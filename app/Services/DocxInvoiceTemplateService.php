<?php

declare(strict_types=1);

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use RuntimeException;
use ZipArchive;

final class DocxInvoiceTemplateService
{
    public static function validate(string $absolutePath): void
    {
        if (!is_file($absolutePath)) {
            throw new RuntimeException('File template Word tidak ditemukan.');
        }

        if (strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) !== 'docx') {
            throw new RuntimeException('Template invoice harus menggunakan format DOCX.');
        }

        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP ZIP belum aktif. Aktifkan extension=zip pada php.ini XAMPP.');
        }

        if (!class_exists(DOMDocument::class)) {
            throw new RuntimeException('Ekstensi PHP DOM/XML belum aktif. Aktifkan ekstensi XML pada PHP XAMPP.');
        }

        $zip = new ZipArchive();
        if ($zip->open($absolutePath) !== true) {
            throw new RuntimeException('File DOCX tidak dapat dibuka atau rusak.');
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!is_string($documentXml) || trim($documentXml) === '') {
            throw new RuntimeException('Struktur DOCX tidak valid: word/document.xml tidak ditemukan.');
        }
    }

    public static function toHtml(string $absolutePath): string
    {
        self::validate($absolutePath);

        $zip = new ZipArchive();
        $zip->open($absolutePath);

        $documentXml = (string) $zip->getFromName('word/document.xml');
        $relations = self::relationMap($zip);

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        if (@$dom->loadXML($documentXml) === false) {
            $zip->close();
            throw new RuntimeException('XML dokumen Word tidak dapat diproses.');
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

        $body = $xpath->query('//w:body')->item(0);
        if (!$body instanceof DOMElement) {
            $zip->close();
            throw new RuntimeException('Body dokumen Word tidak ditemukan.');
        }

        $parts = [];
        foreach ($body->childNodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }

            $local = $node->localName;
            if ($local === 'p') {
                $parts[] = self::paragraphHtml($node, $xpath, $zip, $relations);
            } elseif ($local === 'tbl') {
                $parts[] = self::tableHtml($node, $xpath, $zip, $relations);
            }
        }

        $zip->close();

        $content = trim(implode("\n", array_filter($parts)));
        if ($content === '') {
            $content = '<p class="docx-empty">Template Word tidak memiliki konten yang dapat dirender.</p>';
        }

        return '<div class="docx-template">' . $content . '</div>';
    }

    public static function render(string $absolutePath, array $data): string
    {
        $html = self::toHtml($absolutePath);

        $replacements = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $safe = htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
            $replacements['{{' . $key . '}}'] = $safe;
            $replacements['{' . $key . '}'] = $safe;
        }

        return strtr($html, $replacements);
    }

    public static function placeholderList(): array
    {
        return [
            'invoice_no',
            'company_name',
            'company_address',
            'company_email',
            'issue_date',
            'due_date',
            'amount',
            'amount_with_currency',
            'currency',
            'status',
            'reference_no',
            'notes',
            'transaction_date',
            'beneficiary_name',
            'beneficiary_account',
            'beneficiary_bank',
            'beneficiary_country',
            'beneficiary_city',
            'beneficiary_address',
            'swift',
            'payment_details',
            'correspondent_bank',
            'correspondent_swift',
        ];
    }

    private static function paragraphHtml(
        DOMElement $paragraph,
        DOMXPath $xpath,
        ZipArchive $zip,
        array $relations
    ): string {
        $alignment = '';
        $jc = $xpath->query('./w:pPr/w:jc', $paragraph)->item(0);
        if ($jc instanceof DOMElement) {
            $val = $jc->getAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'val');
            $align = match ($val) {
                'center' => 'center',
                'right', 'end' => 'right',
                'both', 'distribute' => 'justify',
                default => 'left',
            };
            $alignment = ' style="text-align:' . $align . '"';
        }

        $text = self::paragraphText($paragraph, $xpath);
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        $images = [];
        foreach ($xpath->query('.//a:blip', $paragraph) as $blip) {
            if (!$blip instanceof DOMElement) {
                continue;
            }
            $rid = $blip->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
            if ($rid === '' || !isset($relations[$rid])) {
                continue;
            }
            $img = self::imageDataUri($zip, $relations[$rid]);
            if ($img !== null) {
                $images[] = '<img class="docx-image" src="' . $img . '" alt="">';
            }
        }

        $content = $escaped;
        if ($content === '' && $images === []) {
            return '<div class="docx-spacer"></div>';
        }

        return '<p' . $alignment . '>' . implode('', $images) . $content . '</p>';
    }

    private static function tableHtml(
        DOMElement $table,
        DOMXPath $xpath,
        ZipArchive $zip,
        array $relations
    ): string {
        $rowsHtml = [];

        foreach ($xpath->query('./w:tr', $table) as $row) {
            if (!$row instanceof DOMElement) {
                continue;
            }

            $cellsHtml = [];
            foreach ($xpath->query('./w:tc', $row) as $cell) {
                if (!$cell instanceof DOMElement) {
                    continue;
                }

                $cellParts = [];
                foreach ($cell->childNodes as $child) {
                    if (!$child instanceof DOMElement) {
                        continue;
                    }
                    if ($child->localName === 'p') {
                        $cellParts[] = self::paragraphHtml($child, $xpath, $zip, $relations);
                    } elseif ($child->localName === 'tbl') {
                        $cellParts[] = self::tableHtml($child, $xpath, $zip, $relations);
                    }
                }

                $cellsHtml[] = '<td>' . implode('', $cellParts) . '</td>';
            }

            $rowsHtml[] = '<tr>' . implode('', $cellsHtml) . '</tr>';
        }

        return '<table class="docx-table"><tbody>' . implode('', $rowsHtml) . '</tbody></table>';
    }

    private static function paragraphText(DOMElement $paragraph, DOMXPath $xpath): string
    {
        $parts = [];
        foreach ($xpath->query('.//w:t|.//w:tab|.//w:br', $paragraph) as $node) {
            if (!$node instanceof DOMNode) {
                continue;
            }
            if ($node->localName === 'tab') {
                $parts[] = "\t";
            } elseif ($node->localName === 'br') {
                $parts[] = "\n";
            } else {
                $parts[] = $node->textContent;
            }
        }

        return implode('', $parts);
    }

    private static function relationMap(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('word/_rels/document.xml.rels');
        if (!is_string($xml) || trim($xml) === '') {
            return [];
        }

        $dom = new DOMDocument();
        if (@$dom->loadXML($xml) === false) {
            return [];
        }

        $map = [];
        foreach ($dom->getElementsByTagName('Relationship') as $relation) {
            if (!$relation instanceof DOMElement) {
                continue;
            }
            $id = $relation->getAttribute('Id');
            $target = $relation->getAttribute('Target');
            if ($id === '' || $target === '') {
                continue;
            }
            $map[$id] = 'word/' . ltrim(str_replace('../', '', $target), '/');
        }

        return $map;
    }

    private static function imageDataUri(ZipArchive $zip, string $path): ?string
    {
        $binary = $zip->getFromName($path);
        if (!is_string($binary) || $binary === '') {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        return 'data:' . $mime . ';base64,' . base64_encode($binary);
    }
}
