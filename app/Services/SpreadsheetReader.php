<?php

declare(strict_types=1);

namespace App\Services;

use DOMDocument;
use DOMXPath;
use RuntimeException;
use ZipArchive;

final class SpreadsheetReader
{
    /**
     * @return array<string,array<int,array<int,mixed>>>
     */
    public static function readWorkbook(string $file): array
    {
        if (!is_file($file)) {
            throw new RuntimeException('File spreadsheet tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            return ['CSV' => self::readCsv($file)];
        }

        if ($extension === 'xlsx' || $extension === 'xlsm') {
            return self::readXlsx($file);
        }

        if ($extension === 'xls') {
            return self::readXmlSpreadsheet($file);
        }

        throw new RuntimeException('Format file tidak didukung. Gunakan XLSX atau CSV.');
    }

    /**
     * @return array<int,array<int,mixed>>
     */
    private static function readCsv(string $file): array
    {
        $handle = fopen($file, 'rb');
        if ($handle === false) {
            throw new RuntimeException('CSV tidak dapat dibuka.');
        }

        $sample = (string) fgets($handle);
        rewind($handle);
        $delimiter = substr_count($sample, ';') > substr_count($sample, ',') ? ';' : ',';
        $rows = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = array_map(static fn ($value): string => trim((string) $value), $row);
        }

        fclose($handle);
        return $rows;
    }

    /**
     * @return array<string,array<int,array<int,mixed>>>
     */
    private static function readXlsx(string $file): array
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP ZIP belum aktif. Aktifkan extension=zip pada php.ini XAMPP.');
        }
        if (!class_exists(DOMDocument::class)) {
            throw new RuntimeException('Ekstensi PHP DOM belum aktif. Aktifkan extension=dom pada php.ini XAMPP.');
        }

        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            throw new RuntimeException('File XLSX tidak dapat dibuka.');
        }

        try {
            $sharedStrings = self::sharedStrings($zip);
            $sheetDefinitions = self::sheetDefinitions($zip);
            $result = [];

            foreach ($sheetDefinitions as $definition) {
                $xml = $zip->getFromName($definition['path']);
                if ($xml === false) {
                    continue;
                }
                $result[$definition['name']] = self::parseSheet($xml, $sharedStrings);
            }

            if ($result === []) {
                throw new RuntimeException('Tidak ada worksheet yang dapat dibaca.');
            }

            return $result;
        } finally {
            $zip->close();
        }
    }

    /**
     * @return array<int,string>
     */
    private static function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $dom = new DOMDocument();
        if (!@$dom->loadXML($xml)) {
            return [];
        }

        $values = [];
        foreach ($dom->getElementsByTagName('si') as $item) {
            $text = '';
            foreach ($item->getElementsByTagName('t') as $node) {
                $text .= $node->nodeValue;
            }
            $values[] = $text;
        }
        return $values;
    }

    /**
     * @return array<int,array{name:string,path:string}>
     */
    private static function sheetDefinitions(ZipArchive $zip): array
    {
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relationsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($workbookXml === false || $relationsXml === false) {
            throw new RuntimeException('Struktur workbook XLSX tidak lengkap.');
        }

        $relationsDom = new DOMDocument();
        $relationsDom->loadXML($relationsXml);
        $relationMap = [];
        foreach ($relationsDom->getElementsByTagName('Relationship') as $relation) {
            $relationMap[$relation->getAttribute('Id')] = $relation->getAttribute('Target');
        }

        $workbookDom = new DOMDocument();
        $workbookDom->loadXML($workbookXml);
        $definitions = [];

        foreach ($workbookDom->getElementsByTagName('sheet') as $sheet) {
            $relationId = $sheet->getAttributeNS(
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                'id'
            );
            $target = $relationMap[$relationId] ?? '';
            if ($target === '') {
                continue;
            }
            $target = ltrim($target, '/');
            if (!str_starts_with($target, 'xl/')) {
                $target = 'xl/' . $target;
            }
            $definitions[] = [
                'name' => $sheet->getAttribute('name') ?: 'Sheet',
                'path' => $target,
            ];
        }

        return $definitions;
    }

    /**
     * @param array<int,string> $sharedStrings
     * @return array<int,array<int,mixed>>
     */
    private static function parseSheet(string $xml, array $sharedStrings): array
    {
        $dom = new DOMDocument();
        if (!@$dom->loadXML($xml)) {
            return [];
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];

        foreach ($xpath->query('//x:sheetData/x:row') as $rowNode) {
            $indexed = [];
            foreach ($xpath->query('x:c', $rowNode) as $cellNode) {
                $reference = $cellNode->getAttribute('r');
                $columnIndex = self::columnIndex($reference);
                $type = $cellNode->getAttribute('t');
                $valueNode = $xpath->query('x:v', $cellNode)->item(0);
                $value = $valueNode ? (string) $valueNode->nodeValue : '';

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $parts = [];
                    foreach ($xpath->query('x:is//x:t', $cellNode) as $textNode) {
                        $parts[] = (string) $textNode->nodeValue;
                    }
                    $value = implode('', $parts);
                } elseif ($type === 'b') {
                    $value = $value === '1';
                }

                $indexed[$columnIndex] = $value;
            }

            if ($indexed === []) {
                continue;
            }

            $max = max(array_keys($indexed));
            $row = [];
            for ($index = 0; $index <= $max; $index++) {
                $row[] = $indexed[$index] ?? '';
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private static function columnIndex(string $cellReference): int
    {
        if (!preg_match('/^([A-Z]+)/i', $cellReference, $matches)) {
            return 0;
        }

        $index = 0;
        foreach (str_split(strtoupper($matches[1])) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }
        return max(0, $index - 1);
    }

    /**
     * Membaca SpreadsheetML 2003 (.xls berbasis XML). Binary BIFF8 tidak dibaca.
     *
     * @return array<string,array<int,array<int,mixed>>>
     */
    private static function readXmlSpreadsheet(string $file): array
    {
        $prefix = (string) file_get_contents($file, false, null, 0, 256);
        if (!str_contains($prefix, '<?xml') && !str_contains($prefix, '<Workbook')) {
            throw new RuntimeException(
                'File XLS lama berbentuk binary belum dapat diimpor langsung. ' .
                'Buka file tersebut di Excel lalu Save As menjadi XLSX.'
            );
        }

        $dom = new DOMDocument();
        if (!@$dom->load($file)) {
            throw new RuntimeException('File XLS XML tidak dapat dibaca.');
        }
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
        $result = [];

        foreach ($xpath->query('//ss:Worksheet') as $worksheet) {
            $name = $worksheet->getAttributeNS('urn:schemas-microsoft-com:office:spreadsheet', 'Name') ?: 'Sheet';
            $rows = [];
            foreach ($xpath->query('.//ss:Table/ss:Row', $worksheet) as $rowNode) {
                $row = [];
                $currentIndex = 1;
                foreach ($xpath->query('ss:Cell', $rowNode) as $cellNode) {
                    $explicitIndex = $cellNode->getAttributeNS('urn:schemas-microsoft-com:office:spreadsheet', 'Index');
                    if ($explicitIndex !== '') {
                        $currentIndex = (int) $explicitIndex;
                    }
                    while (count($row) < $currentIndex - 1) {
                        $row[] = '';
                    }
                    $dataNode = $xpath->query('ss:Data', $cellNode)->item(0);
                    $row[] = $dataNode ? (string) $dataNode->nodeValue : '';
                    $currentIndex++;
                }
                $rows[] = $row;
            }
            $result[$name] = $rows;
        }

        if ($result === []) {
            throw new RuntimeException('Worksheet XLS XML tidak ditemukan.');
        }
        return $result;
    }
}
