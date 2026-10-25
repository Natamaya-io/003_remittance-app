<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use ZipArchive;

final class XlsxWriter
{
    /**
     * @param array<string,array{headers:array<int,string>,rows:array<int,array<int,mixed>>,widths?:array<int,float>}> $sheets
     */
    public static function download(array $sheets, string $filename): void
    {
        $temp = self::create($sheets);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . self::safeFilename($filename, 'xlsx') . '"');
        header('Content-Length: ' . filesize($temp));
        header('Cache-Control: no-store, no-cache, must-revalidate');
        readfile($temp);
        @unlink($temp);
        exit;
    }

    /**
     * @param array<string,array{headers:array<int,string>,rows:array<int,array<int,mixed>>,widths?:array<int,float>}> $sheets
     */
    public static function create(array $sheets): string
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP ZIP belum aktif. Aktifkan extension=zip pada php.ini XAMPP.');
        }
        if ($sheets === []) {
            throw new RuntimeException('Tidak ada data untuk diekspor.');
        }

        $temp = tempnam(sys_get_temp_dir(), 'remitgo_xlsx_');
        if ($temp === false) {
            throw new RuntimeException('Gagal membuat file sementara.');
        }

        $zip = new ZipArchive();
        if ($zip->open($temp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat file XLSX.');
        }

        $sheetNames = [];
        $sheetIndex = 1;
        foreach ($sheets as $name => $sheet) {
            $safeName = self::sheetName((string) $name, $sheetIndex);
            while (in_array($safeName, $sheetNames, true)) {
                $safeName = self::sheetName($safeName . '-' . $sheetIndex, $sheetIndex);
            }
            $sheetNames[] = $safeName;
            $zip->addFromString(
                'xl/worksheets/sheet' . $sheetIndex . '.xml',
                self::sheetXml($sheet['headers'] ?? [], $sheet['rows'] ?? [], $sheet['widths'] ?? [])
            );
            $sheetIndex++;
        }

        $zip->addFromString('[Content_Types].xml', self::contentTypes(count($sheetNames)));
        $zip->addFromString('_rels/.rels', self::rootRelations());
        $zip->addFromString('xl/workbook.xml', self::workbookXml($sheetNames));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRelations(count($sheetNames)));
        $zip->addFromString('xl/styles.xml', self::stylesXml());
        $zip->addFromString('docProps/core.xml', self::coreXml());
        $zip->addFromString('docProps/app.xml', self::appXml($sheetNames));
        $zip->close();

        return $temp;
    }

    /**
     * @param array<int,string> $headers
     * @param array<int,array<int,mixed>> $rows
     * @param array<int,float> $widths
     */
    private static function sheetXml(array $headers, array $rows, array $widths): string
    {
        $columnCount = max(count($headers), ...array_map('count', $rows ?: [[]]));
        $columnCount = max(1, $columnCount);
        $rowCount = count($rows) + 1;
        $lastCell = self::columnLetters($columnCount) . $rowCount;

        $cols = '';
        for ($index = 1; $index <= $columnCount; $index++) {
            $width = $widths[$index - 1] ?? 18;
            $width = max(8, min(55, (float) $width));
            $cols .= '<col min="' . $index . '" max="' . $index . '" width="' . $width . '" customWidth="1"/>';
        }

        $sheetData = '<row r="1" ht="24" customHeight="1">';
        foreach ($headers as $column => $header) {
            $reference = self::columnLetters($column + 1) . '1';
            $sheetData .= self::cellXml($reference, (string) $header, 1);
        }
        $sheetData .= '</row>';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 2;
            $sheetData .= '<row r="' . $excelRow . '">';
            for ($column = 0; $column < $columnCount; $column++) {
                $reference = self::columnLetters($column + 1) . $excelRow;
                $sheetData .= self::cellXml($reference, $row[$column] ?? '', 0);
            }
            $sheetData .= '</row>';
        }

        $autoFilter = $headers !== [] ? '<autoFilter ref="A1:' . self::columnLetters(count($headers)) . $rowCount . '"/>' : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<dimension ref="A1:' . $lastCell . '"/>'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18"/>'
            . '<cols>' . $cols . '</cols>'
            . '<sheetData>' . $sheetData . '</sheetData>'
            . $autoFilter
            . '<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
            . '</worksheet>';
    }

    private static function cellXml(string $reference, mixed $value, int $style): string
    {
        if (is_int($value) || is_float($value)) {
            return '<c r="' . $reference . '" s="' . $style . '" t="n"><v>' . self::xml((string) $value) . '</v></c>';
        }

        if (is_bool($value)) {
            return '<c r="' . $reference . '" s="' . $style . '" t="b"><v>' . ($value ? '1' : '0') . '</v></c>';
        }

        $text = (string) $value;
        return '<c r="' . $reference . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">'
            . self::xml($text) . '</t></is></c>';
    }

    private static function contentTypes(int $sheetCount): string
    {
        $overrides = '';
        for ($index = 1; $index <= $sheetCount; $index++) {
            $overrides .= '<Override PartName="/xl/worksheets/sheet' . $index . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . $overrides . '</Types>';
    }

    private static function rootRelations(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    /** @param array<int,string> $sheetNames */
    private static function workbookXml(array $sheetNames): string
    {
        $sheets = '';
        foreach ($sheetNames as $index => $name) {
            $sheetId = $index + 1;
            $sheets .= '<sheet name="' . self::xml($name) . '" sheetId="' . $sheetId . '" r:id="rId' . $sheetId . '"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<bookViews><workbookView xWindow="0" yWindow="0" windowWidth="24000" windowHeight="14000"/></bookViews>'
            . '<sheets>' . $sheets . '</sheets><calcPr calcId="191029"/></workbook>';
    }

    private static function workbookRelations(int $sheetCount): string
    {
        $relationships = '';
        for ($index = 1; $index <= $sheetCount; $index++) {
            $relationships .= '<Relationship Id="rId' . $index . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $index . '.xml"/>';
        }
        $relationships .= '<Relationship Id="rId' . ($sheetCount + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $relationships . '</Relationships>';
    }

    private static function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="10"/><name val="Aptos"/><family val="2"/></font>'
            . '<font><b/><color rgb="FFFFFFFF"/><sz val="10"/><name val="Aptos"/><family val="2"/></font>'
            . '</fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF103A65"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFD9E2EC"/></left><right style="thin"><color rgb="FFD9E2EC"/></right><top style="thin"><color rgb="FFD9E2EC"/></top><bottom style="thin"><color rgb="FFD9E2EC"/></bottom><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFill="1" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private static function coreXml(): string
    {
        $time = gmdate('Y-m-d\TH:i:s\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>RemitGo!</dc:creator><cp:lastModifiedBy>RemitGo!</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $time . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $time . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    /** @param array<int,string> $sheetNames */
    private static function appXml(array $sheetNames): string
    {
        $titles = '';
        foreach ($sheetNames as $name) {
            $titles .= '<vt:lpstr>' . self::xml($name) . '</vt:lpstr>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>RemitGo!</Application><DocSecurity>0</DocSecurity><ScaleCrop>false</ScaleCrop>'
            . '<HeadingPairs><vt:vector size="2" baseType="variant"><vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant><vt:variant><vt:i4>' . count($sheetNames) . '</vt:i4></vt:variant></vt:vector></HeadingPairs>'
            . '<TitlesOfParts><vt:vector size="' . count($sheetNames) . '" baseType="lpstr">' . $titles . '</vt:vector></TitlesOfParts>'
            . '</Properties>';
    }

    private static function columnLetters(int $number): string
    {
        $letters = '';
        while ($number > 0) {
            $number--;
            $letters = chr(65 + ($number % 26)) . $letters;
            $number = intdiv($number, 26);
        }
        return $letters;
    }

    private static function sheetName(string $name, int $fallback): string
    {
        $name = preg_replace('/[\\\/?*\[\]:]/', '-', trim($name)) ?: '';
        $name = substr($name, 0, 31);
        return $name !== '' ? $name : 'Sheet' . $fallback;
    }

    private static function safeFilename(string $filename, string $extension): string
    {
        $name = preg_replace('/[^A-Za-z0-9._-]+/', '-', trim($filename)) ?: 'export';
        if (!str_ends_with(strtolower($name), '.' . $extension)) {
            $name .= '.' . $extension;
        }
        return $name;
    }

    private static function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
