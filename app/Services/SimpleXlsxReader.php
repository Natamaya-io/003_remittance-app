<?php

declare(strict_types=1);

namespace App\Services;

use ZipArchive;

final class SimpleXlsxReader
{
    public static function read(string $file): array
    {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return $ext === 'csv' ? self::readCsv($file) : self::readXlsx($file);
    }

    private static function readCsv(string $file): array
    {
        $rows = [];
        $handle = fopen($file, 'rb');
        if (!$handle) throw new \RuntimeException('CSV tidak dapat dibuka.');
        $first = fgets($handle);
        rewind($handle);
        $delimiter = substr_count((string) $first, ';') > substr_count((string) $first, ',') ? ';' : ',';
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) $rows[] = $row;
        fclose($handle);
        return $rows;
    }

    private static function readXlsx(string $file): array
    {
        if (!class_exists(ZipArchive::class)) {
            throw new \RuntimeException('Ekstensi PHP Zip belum aktif. Aktifkan extension=zip pada php.ini XAMPP.');
        }
        $zip = new ZipArchive();
        if ($zip->open($file) !== true) throw new \RuntimeException('File XLSX tidak dapat dibuka.');
        $shared = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $dom = new \DOMDocument();
            $dom->loadXML($sharedXml);
            foreach ($dom->getElementsByTagName('si') as $si) {
                $text = '';
                foreach ($si->getElementsByTagName('t') as $t) $text .= $t->nodeValue;
                $shared[] = $text;
            }
        }
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) throw new \RuntimeException('Worksheet pertama tidak ditemukan.');
        $dom = new \DOMDocument();
        $dom->loadXML($sheetXml);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];
        foreach ($xpath->query('//x:sheetData/x:row') as $rowNode) {
            $row = [];
            foreach ($xpath->query('x:c', $rowNode) as $cell) {
                $ref = $cell->getAttribute('r');
                preg_match('/([A-Z]+)(\d+)/', $ref, $m);
                $index = self::columnIndex($m[1] ?? 'A');
                $type = $cell->getAttribute('t');
                $valueNode = $xpath->query('x:v', $cell)->item(0);
                $value = $valueNode?->nodeValue ?? '';
                if ($type === 's') $value = $shared[(int) $value] ?? '';
                elseif ($type === 'inlineStr') {
                    $texts = $xpath->query('x:is/x:t', $cell);
                    $value = $texts->length ? $texts->item(0)->nodeValue : '';
                }
                $row[$index] = $value;
            }
            if ($row) {
                $max = max(array_keys($row));
                $normalized = [];
                for ($i = 0; $i <= $max; $i++) $normalized[] = $row[$i] ?? '';
                $rows[] = $normalized;
            }
        }
        return $rows;
    }

    private static function columnIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split($letters) as $letter) $index = $index * 26 + (ord($letter) - 64);
        return $index - 1;
    }
}
