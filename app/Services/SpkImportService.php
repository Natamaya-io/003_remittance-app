<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use DateTimeImmutable;
use RuntimeException;

/**
 * Importer sumber data khusus modul SPK Entropy-TOPSIS (Report 2024 + Actual Refund).
 * Tidak pernah membaca/menulis tabel transactions, report_rows, companies,
 * invoices, complaints, atau tabel operasional aplikasi lain.
 */
final class SpkImportService
{
    public const SOURCE_TYPES = [
        'report_2024' => 'Report Sukses 2024',
        'actual' => 'Actual LIST MT199 (Refund BNI + BRI)',
    ];

    /** @return array<string,mixed> */
    public function parse(string $absolutePath, string $sourceType): array
    {
        if (!isset(self::SOURCE_TYPES[$sourceType])) {
            throw new RuntimeException('Jenis sumber analisis tidak dikenali.');
        }

        $workbook = SpreadsheetReader::readWorkbook($absolutePath);
        if ($workbook === []) {
            throw new RuntimeException('Workbook tidak memiliki worksheet yang dapat dibaca.');
        }

        return match ($sourceType) {
            'report_2024' => $this->parseReport2024($workbook),
            'actual' => $this->parseActual($workbook),
            default => throw new RuntimeException('Jenis sumber tidak didukung.'),
        };
    }

    /**
     * @param array<string,mixed> $parsed
     */
    public function persist(
        array $parsed,
        string $sourceType,
        string $name,
        string $sourceFilename,
        string $sourcePath,
        int $userId,
        string $origin = 'upload'
    ): int {
        $db = App::db();
        $db->beginTransaction();

        try {
            // Satu sumber aktif per tipe agar upload ulang tidak menggandakan skor.
            $deactivate = $db->prepare('UPDATE spk_sources SET is_active=0 WHERE source_type=? AND is_active=1');
            $deactivate->execute([$sourceType]);

            $statement = $db->prepare(
                'INSERT INTO spk_sources
                (source_type,name,source_filename,source_path,origin,total_rows,imported_rows,ignored_rows,summary_json,is_active,created_by,created_at)
                VALUES (?,?,?,?,?,?,?,?,?,1,?,NOW())'
            );
            $statement->execute([
                $sourceType,
                $name,
                $sourceFilename,
                $sourcePath,
                $origin,
                (int) ($parsed['total_rows'] ?? 0),
                (int) ($parsed['imported_rows'] ?? 0),
                (int) ($parsed['ignored_rows'] ?? 0),
                json_encode($parsed['summary'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $userId,
            ]);
            $sourceId = (int) $db->lastInsertId();

            $insert = $db->prepare(
                'INSERT IGNORE INTO spk_records
                (source_id,sheet_name,source_row_no,payer_key,payer_name,transaction_date,reference_no,event_type,
                 beneficiary_country,beneficiary_bank,swift,currency,amount,status_text,resolved,source_hash,created_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())'
            );

            foreach ((array) ($parsed['records'] ?? []) as $row) {
                $insert->execute([
                    $sourceId,
                    $row['sheet_name'],
                    $row['source_row_no'],
                    $row['payer_key'],
                    $row['payer_name'],
                    $row['transaction_date'],
                    $row['reference_no'],
                    $row['event_type'],
                    $row['beneficiary_country'],
                    $row['beneficiary_bank'],
                    $row['swift'],
                    $row['currency'],
                    $row['amount'],
                    $row['status_text'],
                    $row['resolved'],
                    $row['source_hash'],
                ]);
            }

            // Hasil analisis tidak lagi valid jika salah satu sumber Excel diganti.
            // Riwayat run penelitian lama dihapus; pengguna menghitung ulang dari sumber terbaru.
            $db->exec('DELETE FROM spk_results');
            $db->exec('DELETE FROM spk_weights');
            $db->exec('DELETE FROM spk_runs');
            $db->commit();
            return $sourceId;
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    /** @param array<string,array<int,array<int,mixed>>> $workbook @return array<string,mixed> */
    private function parseReport2024(array $workbook): array
    {
        $records = [];
        $total = 0;
        $ignored = 0;
        $payerCounts = [];
        $usedSheet = null;

        foreach ($workbook as $sheetName => $rows) {
            $headerIndex = $this->findHeader($rows, ['date of transaction', 'bill to payer']);
            if ($headerIndex === null) {
                continue;
            }
            $usedSheet = $sheetName;
            $map = $this->headerMap($rows[$headerIndex]);
            $dateIndex = $this->firstIndex($map, ['date of transaction', 'transaction date', 'date']);
            $payerIndex = $this->firstIndex($map, ['bill to payer', 'payer', 'bill to']);
            $countryIndex = $this->firstIndex($map, ['country of beneficiary', 'beneficiary country']);
            $bankIndex = $this->firstIndex($map, ['beneficiary bank']);
            $swiftIndex = $this->firstIndex($map, ['swift']);
            $currencyIndex = $this->firstIndex($map, ['currency']);
            $amountIndex = $this->firstIndex($map, ['amounts', 'amount']);

            if ($dateIndex === null || $payerIndex === null) {
                throw new RuntimeException('Kolom Date of Transaction / Bill To (Payer) tidak ditemukan.');
            }

            for ($i = $headerIndex + 1, $count = count($rows); $i < $count; $i++) {
                $row = $rows[$i];
                if ($this->rowIsEmpty($row)) {
                    continue;
                }
                $total++;
                $date = $this->parseDate($row[$dateIndex] ?? null);
                $payer = $this->canonicalPayer((string) ($row[$payerIndex] ?? ''));
                if ($date === null || $payer === null) {
                    $ignored++;
                    continue;
                }

                $statusText = $this->statusTextFromTrailingColumns($row);
                if ($this->isFailureStatus($statusText)) {
                    $ignored++;
                    continue;
                }

                $records[] = $this->record([
                    'sheet_name' => $sheetName,
                    'source_row_no' => $i + 1,
                    'payer_key' => $payer['key'],
                    'payer_name' => $payer['name'],
                    'transaction_date' => $date,
                    'reference_no' => '',
                    'event_type' => 'SUCCESS',
                    'beneficiary_country' => $countryIndex !== null ? $this->cleanUpper((string) ($row[$countryIndex] ?? '')) : '',
                    'beneficiary_bank' => $bankIndex !== null ? $this->cleanText((string) ($row[$bankIndex] ?? '')) : '',
                    'swift' => $swiftIndex !== null ? $this->normalizeSwift((string) ($row[$swiftIndex] ?? '')) : '',
                    'currency' => $currencyIndex !== null ? $this->normalizeCurrency((string) ($row[$currencyIndex] ?? '')) : '',
                    'amount' => $amountIndex !== null ? $this->parseAmount($row[$amountIndex] ?? null) : 0,
                    'status_text' => $statusText !== '' ? $statusText : 'SUCCESS',
                    'resolved' => 1,
                ]);
                $payerCounts[$payer['key']] = ($payerCounts[$payer['key']] ?? 0) + 1;
            }
            break;
        }

        if ($usedSheet === null) {
            throw new RuntimeException('Format Report 2024 tidak dikenali.');
        }

        return [
            'total_rows' => $total,
            'imported_rows' => count($records),
            'ignored_rows' => $ignored,
            'records' => $records,
            'summary' => [
                'sheet' => $usedSheet,
                'payer_counts' => $payerCounts,
                'rule' => 'Baris bertanggal valid + payer valid; status CANCEL/REJECT/FAILED dilewati.',
            ],
        ];
    }

    /** @param array<string,array<int,array<int,mixed>>> $workbook @return array<string,mixed> */
    private function parseActual(array $workbook): array
    {
        $records = [];
        $total = 0;
        $ignored = 0;
        $usedSheets = [];

        foreach (['REFUND BNI', 'REFUND BRI'] as $wanted) {
            $sheet = $this->sheetByTrimmedName($workbook, $wanted);
            if ($sheet === null) {
                continue;
            }
            [$sheetName, $rows] = $sheet;
            if ($rows === []) {
                continue;
            }
            $map = $this->headerMap($rows[0]);
            $payerIndex = $this->firstIndex($map, ['payer']);
            $refIndex = $this->firstIndex($map, ['no referensi', 'no ref']);
            $dateIndex = $this->firstIndex($map, ['tanggal transaksi', 'transaction date']);
            $returnIndex = $this->firstIndex($map, ['tanggal retur', 'date refund']);
            $statusIndex = $this->firstIndex($map, ['status', 'status refund']);
            $amountIndex = $this->firstIndex($map, ['nominal retur', 'amount return', 'amount tt']);
            $currencyIndex = $this->firstIndex($map, ['debet currency', 'credit currency']);

            if ($payerIndex === null) {
                continue;
            }
            $usedSheets[] = $sheetName;

            for ($i = 1, $count = count($rows); $i < $count; $i++) {
                $row = $rows[$i];
                if ($this->rowIsEmpty($row)) {
                    continue;
                }
                $total++;
                $payer = $this->canonicalPayer((string) ($row[$payerIndex] ?? ''));
                if ($payer === null) {
                    $ignored++;
                    continue;
                }

                $reference = $refIndex !== null ? $this->cleanText((string) ($row[$refIndex] ?? '')) : '';

                /*
                 * Periode refund mengikuti tanggal transaksi asal jika tersedia.
                 * Khusus REFUND BNI, banyak baris tidak mengisi Tanggal Transaksi,
                 * sehingga Tanggal ACK dipakai sebagai fallback pertama dan Tanggal
                 * Retur sebagai fallback terakhir.
                 */
                $transactionDate = $dateIndex !== null ? $this->parseDate($row[$dateIndex] ?? null) : null;
                $ackIndex = $this->firstIndex($map, ['tanggal ack', 'ack date']);
                $ackDate = $ackIndex !== null ? $this->parseDate($row[$ackIndex] ?? null) : null;
                $returnDate = $returnIndex !== null ? $this->parseDate($row[$returnIndex] ?? null) : null;
                $date = $transactionDate ?? $ackDate ?? $returnDate;
                $dateBasis = $transactionDate !== null ? 'TRANSACTION_DATE'
                    : ($ackDate !== null ? 'ACK_DATE' : ($returnDate !== null ? 'REFUND_DATE' : 'NO_DATE'));

                $returnValue = $returnIndex !== null ? $this->cleanText((string) ($row[$returnIndex] ?? '')) : '';
                $status = $statusIndex !== null ? $this->cleanText((string) ($row[$statusIndex] ?? '')) : '';
                $statusWithBasis = trim($status . ' | DATE_BASIS=' . $dateBasis);
                $resolved = $this->looksResolved($returnValue . ' ' . $status) ? 1 : 0;

                $records[] = $this->record([
                    'sheet_name' => $sheetName,
                    'source_row_no' => $i + 1,
                    'payer_key' => $payer['key'],
                    'payer_name' => $payer['name'],
                    'transaction_date' => $date,
                    'reference_no' => $reference,
                    'event_type' => 'REFUND',
                    'beneficiary_country' => '',
                    'beneficiary_bank' => '',
                    'swift' => '',
                    'currency' => $currencyIndex !== null ? $this->normalizeCurrency((string) ($row[$currencyIndex] ?? '')) : '',
                    'amount' => $amountIndex !== null ? $this->parseAmount($row[$amountIndex] ?? null) : 0,
                    'status_text' => $statusWithBasis,
                    'resolved' => $resolved,
                ]);
            }
        }

        if ($usedSheets === []) {
            throw new RuntimeException('Sheet REFUND BNI / REFUND BRI tidak ditemukan pada file Actual.');
        }

        return [
            'total_rows' => $total,
            'imported_rows' => count($records),
            'ignored_rows' => $ignored,
            'records' => $this->deduplicate($records),
            'summary' => [
                'sheets' => $usedSheets,
                'note' => 'Sheet tanpa identitas payer tetap tidak dipakai dalam skor agar tidak terjadi atribusi yang keliru.',
            ],
        ];
    }

    /** @param array<string,mixed> $row @return array<string,mixed> */
    private function record(array $row): array
    {
        $fingerprint = implode('|', [
            (string) ($row['sheet_name'] ?? ''),
            (string) ($row['source_row_no'] ?? ''),
            (string) ($row['payer_key'] ?? ''),
            (string) ($row['transaction_date'] ?? ''),
            (string) ($row['reference_no'] ?? ''),
            (string) ($row['event_type'] ?? ''),
        ]);

        return [
            'sheet_name' => (string) ($row['sheet_name'] ?? ''),
            'source_row_no' => (int) ($row['source_row_no'] ?? 0),
            'payer_key' => (string) ($row['payer_key'] ?? ''),
            'payer_name' => (string) ($row['payer_name'] ?? ''),
            'transaction_date' => $row['transaction_date'] ?: null,
            'reference_no' => (string) ($row['reference_no'] ?? ''),
            'event_type' => (string) ($row['event_type'] ?? 'OTHER'),
            'beneficiary_country' => (string) ($row['beneficiary_country'] ?? ''),
            'beneficiary_bank' => (string) ($row['beneficiary_bank'] ?? ''),
            'swift' => (string) ($row['swift'] ?? ''),
            'currency' => (string) ($row['currency'] ?? ''),
            'amount' => (float) ($row['amount'] ?? 0),
            'status_text' => (string) ($row['status_text'] ?? ''),
            'resolved' => (int) ($row['resolved'] ?? 0),
            'source_hash' => hash('sha256', $fingerprint),
        ];
    }

    /** @param array<int,array<string,mixed>> $records @return array<int,array<string,mixed>> */
    private function deduplicate(array $records): array
    {
        $unique = [];
        foreach ($records as $row) {
            $businessKey = implode('|', [
                $row['event_type'],
                $row['payer_key'],
                trim((string) $row['reference_no']),
                (string) $row['transaction_date'],
            ]);
            // Jika reference kosong, gunakan source hash agar dua kasus berbeda tidak digabung.
            if (trim((string) $row['reference_no']) === '') {
                $businessKey = (string) $row['source_hash'];
            }
            $unique[hash('sha256', $businessKey)] = $row;
        }
        return array_values($unique);
    }

    /** @param array<string,array<int,array<int,mixed>>> $workbook @return array{0:string,1:array<int,array<int,mixed>>}|null */
    private function sheetByTrimmedName(array $workbook, string $wanted): ?array
    {
        foreach ($workbook as $name => $rows) {
            if (strcasecmp(trim($name), trim($wanted)) === 0) {
                return [$name, $rows];
            }
        }
        return null;
    }

    /** @param array<int,array<int,mixed>> $rows @param array<int,string> $required */
    private function findHeader(array $rows, array $required): ?int
    {
        foreach (array_slice($rows, 0, 25, true) as $index => $row) {
            $map = $this->headerMap($row);
            $ok = true;
            foreach ($required as $header) {
                if (!array_key_exists($this->normalizeHeader($header), $map)) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                return (int) $index;
            }
        }
        return null;
    }

    /** @param array<int,mixed> $header @return array<string,int> */
    private function headerMap(array $header): array
    {
        $map = [];
        foreach ($header as $index => $value) {
            $normalized = $this->normalizeHeader((string) $value);
            if ($normalized !== '' && !isset($map[$normalized])) {
                $map[$normalized] = (int) $index;
            }
        }
        return $map;
    }

    /** @param array<string,int> $map @param array<int,string> $aliases */
    private function firstIndex(array $map, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            $key = $this->normalizeHeader($alias);
            if (isset($map[$key])) {
                return (int) $map[$key];
            }
        }
        return null;
    }

    private function normalizeHeader(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;
        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }

    /** @param array<int,mixed> $row */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            $number = (float) $value;
            if ($number >= 30000 && $number <= 60000) {
                return (new DateTimeImmutable('1899-12-30'))
                    ->modify('+' . (int) $number . ' days')
                    ->format('Y-m-d');
            }
            $digits = (string) (int) $number;
            if (preg_match('/^\d{8}$/', $digits)) {
                foreach (['!Ymd', '!dmY'] as $format) {
                    $date = DateTimeImmutable::createFromFormat($format, $digits);
                    if ($date instanceof DateTimeImmutable) {
                        $year = (int) $date->format('Y');
                        if ($year >= 2000 && $year <= 2100) {
                            return $date->format('Y-m-d');
                        }
                    }
                }
            }
            if (preg_match('/^\d{6}$/', $digits)) {
                $date = DateTimeImmutable::createFromFormat('!ymd', $digits);
                if ($date instanceof DateTimeImmutable) {
                    return $date->format('Y-m-d');
                }
            }
        }

        $text = trim((string) $value);
        $text = preg_replace('/\b(mon|tue|wed|thu|fri|sat|sun)\b/i', '', $text) ?? $text;
        if (preg_match('/(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})/', $text, $m)) {
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }
            $candidate = sprintf('%02d/%02d/%04d', (int) $m[1], (int) $m[2], $year);
            $date = DateTimeImmutable::createFromFormat('!d/m/Y', $candidate);
            if ($date instanceof DateTimeImmutable) {
                return $date->format('Y-m-d');
            }
        }

        $monthMap = [
            'JAN' => 'Jan', 'JANUARI' => 'Jan', 'FEB' => 'Feb', 'FEBRUARI' => 'Feb', 'MAR' => 'Mar', 'MARET' => 'Mar',
            'APR' => 'Apr', 'MEI' => 'May', 'MAY' => 'May', 'JUN' => 'Jun', 'JUNI' => 'Jun', 'JUL' => 'Jul', 'JULI' => 'Jul',
            'AGU' => 'Aug', 'AGUSTUS' => 'Aug', 'AUG' => 'Aug', 'SEP' => 'Sep', 'SEPT' => 'Sep', 'OKT' => 'Oct', 'OCT' => 'Oct',
            'NOV' => 'Nov', 'DES' => 'Dec', 'DEC' => 'Dec',
        ];
        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{2,4})/', $text, $m)) {
            $month = $monthMap[strtoupper($m[2])] ?? $m[2];
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }
            try {
                return (new DateTimeImmutable($m[1] . ' ' . $month . ' ' . $year))->format('Y-m-d');
            } catch (\Throwable) {
                // continue fallback
            }
        }

        try {
            $date = new DateTimeImmutable($text);
            $year = (int) $date->format('Y');
            return ($year >= 2000 && $year <= 2100) ? $date->format('Y-m-d') : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /** @return array{key:string,name:string}|null */
    private function canonicalPayer(string $raw): ?array
    {
        $raw = $this->cleanText(str_replace(['РТ', 'РT', 'PТ'], 'PT', $raw));
        if ($raw === '') {
            return null;
        }
        $upper = strtoupper($raw);
        $compact = preg_replace('/[^A-Z0-9]+/', ' ', $upper) ?? $upper;
        $compact = trim(preg_replace('/\s+/', ' ', $compact) ?? $compact);
        if (!preg_match('/[A-Z]{3,}/', $compact) || preg_match('/^INVO\b/', $compact) || preg_match('/^[0-9 ]+$/', $compact)) {
            return null;
        }

        if (str_contains($compact, 'FABLE')) {
            return ['key' => 'FABLE', 'name' => 'PT FABLE TEKNOLOGI NUSANTARA'];
        }
        if (str_contains($compact, 'JURITA')) {
            return ['key' => 'JURITA', 'name' => 'PT JURITA AGROTAMA INDONESIA'];
        }
        if (str_contains($compact, 'GEMA') && str_contains($compact, 'SAPTA') && str_contains($compact, 'BINTANG')) {
            return ['key' => 'GSB', 'name' => 'PT GEMA SAPTA BINTANG'];
        }
        if (str_contains($compact, 'KAIROS')) {
            return ['key' => 'KAIROS', 'name' => 'PT KAIROS SINERGI INDONESIA'];
        }
        if (str_contains($compact, 'GOSYEN')) {
            return ['key' => 'GOSYEN', 'name' => 'PT GOSYEN TRADE INTERNATIONAL'];
        }
        if (str_contains($compact, 'MULTOTEC')) {
            return ['key' => 'MULTOTEC', 'name' => 'MULTOTEC PROGRESS EQUIPMENT LTD'];
        }
        if (str_contains($compact, 'MERAPI')) {
            return ['key' => 'MERAPI', 'name' => 'MERAPI / PAK PATAR'];
        }
        if (str_contains($compact, 'EKAWIRA')) {
            return ['key' => 'EKAWIRA', 'name' => 'EKAWIRA'];
        }

        return [
            'key' => 'OTHER_' . strtoupper(substr(hash('sha256', $compact), 0, 16)),
            'name' => $compact,
        ];
    }

    private function statusTextFromTrailingColumns(array $row): string
    {
        foreach ([19, 18, 23, 24] as $index) {
            if (isset($row[$index]) && trim((string) $row[$index]) !== '') {
                $candidate = $this->cleanText((string) $row[$index]);
                if (preg_match('/SUKSES|SUCCESS|TERKIRIM|TERKRIIM|JALAN|CANCEL|REJECT|FAILED|GAGAL/i', $candidate)) {
                    return $candidate;
                }
            }
        }
        return '';
    }

    private function isFailureStatus(string $text): bool
    {
        return (bool) preg_match('/CANCEL|REJECT|FAILED|GAGAL/i', $text);
    }

    private function looksResolved(string $text): bool
    {
        $text = strtoupper($this->cleanText($text));
        if ($text === '' || in_array($text, ['BLM', 'BELUM', '-', 'N/A'], true)) {
            return false;
        }
        return (bool) preg_match('/SUCCESS|SUKSES|RETUR|RETURN|REFUND|FINISH|DONE|CLOSED|SELESAI|TERKIRIM|SUDAH|ALREADY|MASUK/i', $text)
            || (bool) preg_match('/\d{1,2}[\/\-.\s][A-Z0-9]{2,9}[\/\-.\s]\d{2,4}/i', $text);
    }

    private function parseAmount(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        $text = preg_replace('/[^0-9,.-]/', '', $this->cleanText((string) $value)) ?? '';
        if ($text === '') {
            return 0.0;
        }
        if (substr_count($text, ',') === 1 && !str_contains($text, '.')) {
            $text = str_replace(',', '.', $text);
        } else {
            $text = str_replace(',', '', $text);
        }
        return is_numeric($text) ? (float) $text : 0.0;
    }

    private function normalizeCurrency(string $value): string
    {
        $value = strtoupper(preg_replace('/[^A-Za-z]/', '', $value) ?? '');
        return preg_match('/^[A-Z]{3}$/', $value) ? $value : '';
    }

    private function normalizeSwift(string $value): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value) ?? '');
    }

    private function cleanUpper(string $value): string
    {
        return strtoupper($this->cleanText($value));
    }

    private function cleanText(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n", "\t"], ' ', $value)) ?? $value);
    }
}
