<?php

declare(strict_types=1);

namespace App\Services;

final class LegacyXlsWriter
{
    /**
     * @param array<int,string> $headers
     * @param array<int,array<int,mixed>> $rows
     */
    public static function download(array $headers, array $rows, string $filename, string $sheetName = 'Sheet1'): void
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', trim($filename)) ?: 'export.xls';
        if (!str_ends_with(strtolower($filename), '.xls')) {
            $filename .= '.xls';
        }

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            . 'xmlns:o="urn:schemas-microsoft-com:office:office" '
            . 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
            . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        echo '<Styles>';
        echo '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Top" ss:WrapText="1"/><Font ss:FontName="Calibri" ss:Size="10"/></Style>';
        echo '<Style ss:ID="Header"><Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/><Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#103A65" ss:Pattern="Solid"/></Style>';
        echo '</Styles>';
        echo '<Worksheet ss:Name="' . self::xml(substr($sheetName, 0, 31)) . '"><Table>';

        echo '<Row ss:StyleID="Header">';
        foreach ($headers as $header) {
            echo '<Cell><Data ss:Type="String">' . self::xml((string) $header) . '</Data></Cell>';
        }
        echo '</Row>';

        foreach ($rows as $row) {
            echo '<Row>';
            foreach ($row as $value) {
                if (is_int($value) || is_float($value)) {
                    echo '<Cell><Data ss:Type="Number">' . self::xml((string) $value) . '</Data></Cell>';
                } else {
                    echo '<Cell><Data ss:Type="String">' . self::xml((string) $value) . '</Data></Cell>';
                }
            }
            echo '</Row>';
        }

        echo '</Table><WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel"><FreezePanes/><FrozenNoSplit/><SplitHorizontal>1</SplitHorizontal><TopRowBottomPane>1</TopRowBottomPane></WorksheetOptions></Worksheet></Workbook>';
        exit;
    }

    private static function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
