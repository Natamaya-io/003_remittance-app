<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use RuntimeException;

final class TransactionTemplateService
{
    /**
     * Membaca workbook transaksi dan mengubahnya ke struktur internal.
     * Mendukung XLSX, XLS XML, dan CSV. Mampu membaca format mentah (25 kolom)
     * maupun format gabungan BRI MT103 (14 kolom).
     *
     * @return array{
     *     sheet: string,
     *     header_index: int,
     *     map: array<string, int>,
     *     rows: array<int, array<string, mixed>>,
     *     skipped: int,
     *     issues: array<int, string>,
     *     total_source_rows: int
     * }
     */
    public function parse(string $file): array
    {
        $workbook = SpreadsheetReader::readWorkbook($file);
        $preferred = [];

        foreach ($workbook as $sheetName => $rows) {
            $cleanName = strtolower(trim((string) $sheetName));
            if (in_array($cleanName, ['data base bule', 'database bule', 'database', 'data base', 'bule', 'transaksi'], true)) {
                $preferred = [$sheetName => $rows] + $workbook;
                break;
            }
        }

        if ($preferred === []) {
            $preferred = $workbook;
        }

        $allIssues = [];

        foreach ($preferred as $sheetName => $rows) {
            try {
                [$headerIndex, $map, $isCombined] = $this->locateHeader($rows);
            } catch (RuntimeException) {
                continue;
            }

            $sheetCurrency = null;
            $cleanSheetName = strtoupper(trim((string) $sheetName));
            if (preg_match('/^[A-Z]{3}$/', $cleanSheetName) === 1) {
                $sheetCurrency = $cleanSheetName;
            }

            $parsed = [];
            $skipped = 0;
            $issues = [];
            $sourceRows = array_slice($rows, $headerIndex + 1);

            foreach ($sourceRows as $sourceIndex => $row) {
                if ($this->isBlankRow($row) || $this->isSummaryRow($row)) {
                    continue;
                }

                $excelRow = $headerIndex + $sourceIndex + 2;

                if ($isCombined) {
                    $source = $this->unpackCombinedBriRow($row, $map);
                } else {
                    $source = [
                        'sequence_no' => $this->cell($row, $map, 'sequence'),
                        'transaction_date' => $this->cell($row, $map, 'date'),
                        'payer' => $this->cell($row, $map, 'payer'),
                        'beneficiary_name' => $this->cell($row, $map, 'beneficiary'),
                        'beneficiary_account' => $this->cell($row, $map, 'account'),
                        'beneficiary_bank' => $this->cell($row, $map, 'bank'),
                        'beneficiary_bank_location' => $this->cell($row, $map, 'bank_location'),
                        'amount' => $this->cell($row, $map, 'amount'),
                        'beneficiary_country' => $this->cell($row, $map, 'country'),
                        'beneficiary_city' => $this->cell($row, $map, 'city'),
                        'beneficiary_address' => $this->cell($row, $map, 'address'),
                        'swift' => $this->cell($row, $map, 'swift'),
                        'payment_details' => $this->cell($row, $map, 'payment'),
                        'currency' => $this->cell($row, $map, 'currency'),
                        'correspondent_bank' => $this->cell($row, $map, 'corr_bank'),
                        'correspondent_swift' => $this->cell($row, $map, 'corr_swift'),
                        'correspondent_location' => $this->cell($row, $map, 'corr_location'),
                        'payer_account' => $this->cell($row, $map, 'payer_account'),
                        'payer_address' => $this->cell($row, $map, 'payer_address'),
                        'payer_city' => $this->cell($row, $map, 'payer_city'),
                        'payer_country' => $this->cell($row, $map, 'payer_country'),
                        'payer_email' => $this->cell($row, $map, 'payer_email'),
                        'reference_no' => $this->cell($row, $map, 'reference'),
                        'invoice_no' => $this->cell($row, $map, 'invoice'),
                        'team_no' => $this->cell($row, $map, 'team'),
                    ];
                }

                [$normalized, $rowErrors] = $this->normalizeSourceRow($source, (string) ($sourceIndex + 1), $sheetCurrency);

                if ($rowErrors !== []) {
                    $skipped++;
                    if (count($issues) < 30) {
                        $issues[] = 'Baris ' . $excelRow . ': ' . implode(', ', $rowErrors) . '.';
                    }
                    continue;
                }

                $parsed[] = $normalized;
            }

            if ($parsed !== []) {
                return [
                    'sheet' => $sheetName,
                    'header_index' => $headerIndex,
                    'map' => $map,
                    'rows' => $parsed,
                    'skipped' => $skipped,
                    'issues' => $issues,
                    'total_source_rows' => count($sourceRows),
                ];
            }

            $allIssues = array_merge($allIssues, $issues);
        }

        $message = 'Data transaksi valid tidak ditemukan. Pastikan file berisi header template transaksi yang sesuai.';
        if ($allIssues !== []) {
            $message .= ' Contoh kendala: ' . implode(' ', array_slice($allIssues, 0, 3));
        }

        throw new RuntimeException($message);
    }

    /**
     * Membaca data grid manual copy-paste dari browser.
     *
     * @param array<int, array<string|int, mixed>> $inputRows
     * @return array{
     *     sheet: string,
     *     header_index: int,
     *     map: array<string, int>,
     *     rows: array<int, array<string, mixed>>,
     *     skipped: int,
     *     issues: array<int, string>,
     *     total_source_rows: int
     * }
     */
    public function parseManualRows(array $inputRows): array
    {
        $parsed = [];
        $issues = [];
        $skipped = 0;
        $sourceCount = 0;

        $headerMap = null;
        $startIndex = 0;
        $isCombined = false;

        if (!empty($inputRows)) {
            $firstRow = reset($inputRows);
            if (is_array($firstRow)) {
                try {
                    [$_, $map, $comb] = $this->locateHeader([$firstRow]);
                    if (!empty($map)) {
                        $headerMap = $map;
                        $startIndex = 1;
                        $isCombined = $comb;
                    }
                } catch (RuntimeException) {
                    $headerMap = null;
                }
            }
        }

        $sliceRows = $startIndex > 0 ? array_slice($inputRows, $startIndex, null, true) : $inputRows;

        foreach ($sliceRows as $index => $row) {
            if (!is_array($row) || $this->isBlankAssocRow($row) || $this->isSummaryRow($row)) {
                continue;
            }

            $sourceCount++;
            $source = $isCombined
                ? $this->unpackCombinedBriRow($row, $headerMap ?? [])
                : $this->resolveSourceRow($row, $headerMap);

            [$normalized, $rowErrors] = $this->normalizeSourceRow($source, (string) $sourceCount);

            if ($rowErrors !== []) {
                $skipped++;
                if (count($issues) < 30) {
                    $issues[] = 'Baris manual ' . ($index + 1) . ': ' . implode(', ', $rowErrors) . '.';
                }
                continue;
            }

            $parsed[] = $normalized;
        }

        if ($parsed === []) {
            $message = 'Tidak ada data transaksi manual yang valid.';
            if ($issues !== []) {
                $message .= ' Contoh kendala: ' . implode(' ', array_slice($issues, 0, 3));
            }
            throw new RuntimeException($message);
        }

        return [
            'sheet' => 'Manual Grid',
            'header_index' => $startIndex,
            'map' => $headerMap ?? [],
            'rows' => $parsed,
            'skipped' => $skipped,
            'issues' => $issues,
            'total_source_rows' => $sourceCount,
        ];
    }

    /**
     * Membongkar format gabungan BRI (14 kolom MT103) kembali ke field terpisah.
     *
     * @param array<int|string, mixed> $row
     * @param array<string, int> $map
     * @return array<string, mixed>
     */
    private function unpackCombinedBriRow(array $row, array $map): array
    {
        $payerCombo = (string) ($this->cell($row, $map, 'payer_combined') ?? $row[1] ?? '');
        $dateCombo = (string) ($this->cell($row, $map, 'date_combined') ?? $row[2] ?? '');
        $beneficiaryCombo = (string) ($this->cell($row, $map, 'beneficiary_combined') ?? $row[4] ?? '');
        $swiftCombo = (string) ($this->cell($row, $map, 'swift_combined') ?? $row[5] ?? '');
        $paymentCombo = (string) ($this->cell($row, $map, 'payment_combined') ?? $row[7] ?? '');

        // 1. Ekstrak Payer (Payer Account, Name, Address, City, Country)
        $payerParts = explode('|', $payerCombo);
        $payerAcc = trim($payerParts[0] ?? (string) ($row[0] ?? ''));
        $payerName = trim($payerParts[1] ?? '');
        $payerAddress = trim($payerParts[2] ?? '');
        $payerCity = trim($payerParts[3] ?? '');
        $payerCountry = trim($payerParts[4] ?? 'INDONESIA');

        // 2. Ekstrak Date, Currency, Amount
        $dateParts = explode('|', $dateCombo);
        $rawDate = trim($dateParts[0] ?? '');
        $currency = strtoupper(trim($dateParts[1] ?? 'USD'));
        $amount = trim($dateParts[2] ?? '0');

        // 3. Ekstrak Beneficiary Name, Address, City, Country
        $beneficiaryName = '';
        $beneficiaryAddress = '';
        $beneficiaryCity = '';
        $beneficiaryCountry = '';

        if (str_contains($beneficiaryCombo, '|')) {
            [$bName, $bRest] = explode('|', $beneficiaryCombo, 2);
            $beneficiaryName = trim($bName);
            $bHash = explode('#', $bRest);
            $beneficiaryAddress = trim($bHash[0] ?? '');
            $beneficiaryCity = trim($bHash[1] ?? '');
            $beneficiaryCountry = trim($bHash[2] ?? '');
        } else {
            $beneficiaryName = trim($beneficiaryCombo);
        }

        // 4. Ekstrak SWIFT
        $swift = trim(str_replace('A||', '', $swiftCombo));

        // 5. Ekstrak Payment Details
        $cleanPayment = trim(str_replace('###', '', $paymentCombo));
        if ($beneficiaryName !== '' && str_starts_with($cleanPayment, $beneficiaryName . '|')) {
            $cleanPayment = trim(substr($cleanPayment, strlen($beneficiaryName . '|')));
        }

        return [
            'sequence_no' => $row[13] ?? $row[0] ?? null,
            'transaction_date' => $rawDate,
            'payer' => $payerName !== '' ? $payerName : $payerAcc,
            'payer_account' => $payerAcc,
            'payer_address' => $payerAddress,
            'payer_city' => $payerCity,
            'payer_country' => $payerCountry,
            'payer_email' => $this->cell($row, $map, 'payer_email') ?? $row[12] ?? null,
            'beneficiary_name' => $beneficiaryName,
            'beneficiary_account' => $this->cell($row, $map, 'account') ?? $row[3] ?? null,
            'beneficiary_address' => $beneficiaryAddress,
            'beneficiary_city' => $beneficiaryCity,
            'beneficiary_country' => $beneficiaryCountry,
            'beneficiary_bank' => '',
            'beneficiary_bank_location' => '',
            'amount' => $amount,
            'currency' => $currency,
            'swift' => $swift,
            'payment_details' => $cleanPayment,
            'correspondent_bank' => '',
            'correspondent_swift' => '',
            'correspondent_location' => '',
            'reference_no' => '',
            'invoice_no' => $this->cell($row, $map, 'invoice') ?? $row[13] ?? null,
            'team_no' => 1,
        ];
    }

    /**
     * Tahap 2: membentuk struktur gabungan 14 kolom standar BRI.
     *
     * @param array<int, array<string, mixed>> $transactions
     * @return array<int, array<int, string>>
     */
    public function outputRows(array $transactions, string $email): array
    {
        $rows = [];

        foreach ($transactions as $index => $transaction) {
            $date = trim((string) ($transaction['transaction_date'] ?? ''));
            $dateToken = '';
            if ($date !== '') {
                $timestamp = strtotime($date);
                $dateToken = $timestamp !== false ? date('ymd', $timestamp) : $this->stage2Text($date);
            }

            $currency = strtoupper($this->stage2Text((string) ($transaction['currency'] ?? 'USD')));
            $amount = $this->amountToken((float) ($transaction['amount'] ?? 0));

            $payerAccount = $this->stage2Text((string) ($transaction['payer_account'] ?? ''));
            $payerName = $this->stage2Text((string) ($transaction['company_name'] ?? $transaction['payer_name'] ?? $transaction['payer'] ?? ''));
            $payerAddress = $this->stage2Text((string) ($transaction['payer_address'] ?? ''));
            $payerCity = strtoupper($this->stage2Text((string) ($transaction['payer_city'] ?? '')));
            $payerCountry = strtoupper($this->stage2Text((string) ($transaction['payer_country'] ?? 'INDONESIA')));

            $beneficiaryName = $this->stage2Text((string) ($transaction['beneficiary_name'] ?? ''));
            $beneficiaryAddress = $this->stage2Text((string) ($transaction['beneficiary_address'] ?? ''));
            $beneficiaryCity = $this->stage2Text((string) ($transaction['beneficiary_city'] ?? ''));
            $beneficiaryCountry = $this->stage2Text((string) ($transaction['beneficiary_country'] ?? ''));
            $beneficiaryAccount = $this->stage2Text((string) ($transaction['beneficiary_account'] ?? ''));
            $swift = strtoupper($this->stage2Text((string) ($transaction['swift'] ?? '')));
            $paymentDetails = $this->stage2Text((string) ($transaction['payment_details'] ?? ''));

            $rowEmail = trim((string) ($transaction['payer_email'] ?? ''));
            if (!filter_var($rowEmail, FILTER_VALIDATE_EMAIL)) {
                $rowEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'remit.service09@gmail.com';
            }

            $invoice = $this->stage2Text((string) (
                $transaction['invoice_no']
                ?? $transaction['sequence_no']
                ?? ($index + 1)
            ));

            if ($invoice === '') {
                $invoice = $this->stage2Text((string) ($transaction['sequence_no'] ?? ($index + 1)));
            }

            // Remark diawali nama beneficiary jika belum tercantum
            $paymentDetailsCol = $paymentDetails;
            if ($paymentDetailsCol === '') {
                $paymentDetailsCol = $beneficiaryName;
            } elseif ($beneficiaryName !== '' && !str_starts_with($paymentDetailsCol, $beneficiaryName)) {
                $paymentDetailsCol = $beneficiaryName . '|' . $paymentDetailsCol;
            }

            $rows[] = [
                $payerAccount,
                implode('|', [
                    $payerAccount,
                    $payerName,
                    $payerAddress,
                    $payerCity,
                    $payerCountry,
                    '',
                    'ID',
                    '',
                    'ID',
                    '',
                    '',
                ]),
                implode('|', [$dateToken, $currency . ' ', $amount]),
                $beneficiaryAccount,
                $beneficiaryName . '|' . $beneficiaryAddress . '#' . $beneficiaryCity . '#' . $beneficiaryCountry,
                'A||' . $swift,
                'N/A',
                $paymentDetailsCol . ' ###',
                'OUR',
                'E0|N|199',
                'N/A',
                'N/A',
                $rowEmail,
                $invoice,
            ];
        }

        return $rows;
    }

    /**
     * Tahap 3: pembersihan tanda baca teks seperti file KAIROS USD (1 TRX) - GOSYEN.xls.
     * Delimiter |, #, ###, A||, E0|N|199, dan email @ tetap utuh.
     *
     * @param array<int, array<int, mixed>> $stage2Rows
     * @return array<int, array<int, string>>
     */
    public function cleanOutputRows(array $stage2Rows): array
    {
        $result = [];

        foreach ($stage2Rows as $row) {
            $cleaned = [];
            foreach ($row as $colIndex => $cell) {
                $cleaned[] = $this->cleanFinalCell((string) $cell, (int) $colIndex);
            }
            $result[] = $cleaned;
        }

        return $result;
    }

    /** @return array<int, string> */
    public function outputHeaders(): array
    {
        return [
            'Nomor Rekening',
            'Nomor Rekening|Bill To (Payer)|Alamat payer|Kota Payer|Negara Payer||ID||ID||',
            'Date of Transaction|Currency |Amounts',
            'Account number of Beneficiary',
            'Beneficiary Name|Complete Address of Beneficiary Account#City of Beneficiary #Country of Beneficiary',
            'A||Swift',
            'N/A',
            'Payment details 1 ###',
            'OUR',
            'E0|N|199',
            'N/A',
            'N/A',
            'Email',
            'No invoice',
        ];
    }

    /** @return array<int, float> */
    public function outputWidths(): array
    {
        return [20, 55, 32, 30, 55, 24, 12, 42, 12, 16, 12, 12, 32, 14];
    }

    /**
     * Memetakan baris manual numerik atau asosiatif.
     *
     * @param array<string|int, mixed> $row
     * @param array<string, int>|null $headerMap
     * @return array<string, mixed>
     */
    private function resolveSourceRow(array $row, ?array $headerMap = null): array
    {
        if ($headerMap !== null && !empty($headerMap)) {
            $source = [];
            foreach ($this->aliases() as $key => $_) {
                $source[$key] = $this->cell($row, $headerMap, $key);
            }

            return [
                'sequence_no' => $source['sequence'] ?? null,
                'transaction_date' => $source['date'] ?? null,
                'payer' => $source['payer'] ?? null,
                'beneficiary_name' => $source['beneficiary'] ?? null,
                'beneficiary_account' => $source['account'] ?? null,
                'beneficiary_bank' => $source['bank'] ?? null,
                'beneficiary_bank_location' => $source['bank_location'] ?? null,
                'amount' => $source['amount'] ?? null,
                'beneficiary_country' => $source['country'] ?? null,
                'beneficiary_city' => $source['city'] ?? null,
                'beneficiary_address' => $source['address'] ?? null,
                'swift' => $source['swift'] ?? null,
                'payment_details' => $source['payment'] ?? null,
                'currency' => $source['currency'] ?? null,
                'correspondent_bank' => $source['corr_bank'] ?? null,
                'correspondent_swift' => $source['corr_swift'] ?? null,
                'correspondent_location' => $source['corr_location'] ?? null,
                'payer_account' => $source['payer_account'] ?? null,
                'payer_address' => $source['payer_address'] ?? null,
                'payer_city' => $source['payer_city'] ?? null,
                'payer_country' => $source['payer_country'] ?? null,
                'payer_email' => $source['payer_email'] ?? null,
                'reference_no' => $source['reference'] ?? null,
                'invoice_no' => $source['invoice'] ?? null,
                'team_no' => $source['team'] ?? null,
            ];
        }

        $isAssoc = array_keys($row) !== range(0, count($row) - 1);
        if ($isAssoc) {
            $normalizedRow = [];
            foreach ($row as $k => $v) {
                $normalizedRow[$this->normalize((string) $k)] = $v;
            }

            $mapped = [];
            foreach ($this->aliases() as $aliasKey => $aliasNames) {
                foreach ($aliasNames as $aliasName) {
                    if (array_key_exists($aliasName, $normalizedRow)) {
                        $mapped[$aliasKey] = $normalizedRow[$aliasName];
                        break;
                    }
                }
            }

            return [
                'sequence_no' => $mapped['sequence'] ?? $row['sequence_no'] ?? $row['no'] ?? null,
                'transaction_date' => $mapped['date'] ?? $row['transaction_date'] ?? $row['date'] ?? null,
                'payer' => $mapped['payer'] ?? $row['payer'] ?? $row['company_name'] ?? $row['payer_name'] ?? $row['bill_to'] ?? null,
                'beneficiary_name' => $mapped['beneficiary'] ?? $row['beneficiary_name'] ?? $row['beneficiary'] ?? null,
                'beneficiary_account' => $mapped['account'] ?? $row['beneficiary_account'] ?? $row['account'] ?? null,
                'beneficiary_bank' => $mapped['bank'] ?? $row['beneficiary_bank'] ?? $row['bank'] ?? null,
                'beneficiary_bank_location' => $mapped['bank_location'] ?? $row['beneficiary_bank_location'] ?? null,
                'amount' => $mapped['amount'] ?? $row['amount'] ?? $row['amounts'] ?? null,
                'beneficiary_country' => $mapped['country'] ?? $row['beneficiary_country'] ?? null,
                'beneficiary_city' => $mapped['city'] ?? $row['beneficiary_city'] ?? null,
                'beneficiary_address' => $mapped['address'] ?? $row['beneficiary_address'] ?? null,
                'swift' => $mapped['swift'] ?? $row['swift'] ?? $row['swift_code'] ?? null,
                'payment_details' => $mapped['payment'] ?? $row['payment_details'] ?? $row['payment'] ?? null,
                'currency' => $mapped['currency'] ?? $row['currency'] ?? null,
                'correspondent_bank' => $mapped['corr_bank'] ?? $row['correspondent_bank'] ?? null,
                'correspondent_swift' => $mapped['corr_swift'] ?? $row['correspondent_swift'] ?? null,
                'correspondent_location' => $mapped['corr_location'] ?? $row['correspondent_location'] ?? null,
                'payer_account' => $mapped['payer_account'] ?? $row['payer_account'] ?? null,
                'payer_address' => $mapped['payer_address'] ?? $row['payer_address'] ?? null,
                'payer_city' => $mapped['payer_city'] ?? $row['payer_city'] ?? null,
                'payer_country' => $mapped['payer_country'] ?? $row['payer_country'] ?? null,
                'payer_email' => $mapped['payer_email'] ?? $row['payer_email'] ?? $row['email'] ?? null,
                'reference_no' => $mapped['reference'] ?? $row['reference_no'] ?? $row['reference'] ?? null,
                'invoice_no' => $mapped['invoice'] ?? $row['invoice_no'] ?? $row['invoice'] ?? null,
                'team_no' => $mapped['team'] ?? $row['team_no'] ?? $row['team'] ?? null,
            ];
        }

        // Posisi default grid manual browser
        return [
            'sequence_no' => $row[0] ?? null,
            'transaction_date' => $row[1] ?? null,
            'payer' => $row[2] ?? null,
            'beneficiary_name' => $row[3] ?? null,
            'beneficiary_account' => $row[4] ?? null,
            'beneficiary_bank' => $row[5] ?? null,
            'beneficiary_bank_location' => $row[6] ?? null,
            'amount' => $row[7] ?? null,
            'beneficiary_country' => $row[8] ?? null,
            'beneficiary_city' => $row[9] ?? null,
            'beneficiary_address' => $row[10] ?? null,
            'swift' => $row[11] ?? null,
            'payment_details' => $row[12] ?? null,
            'currency' => $row[13] ?? null,
            'correspondent_bank' => $row[14] ?? null,
            'correspondent_swift' => $row[15] ?? null,
            'correspondent_location' => $row[16] ?? null,
            'payer_account' => $row[17] ?? null,
            'payer_address' => $row[18] ?? null,
            'payer_city' => $row[19] ?? null,
            'payer_country' => $row[20] ?? null,
            'payer_email' => $row[21] ?? null,
            'reference_no' => $row[22] ?? null,
            'invoice_no' => $row[23] ?? null,
            'team_no' => $row[24] ?? null,
        ];
    }

    /**
     * @param array<string, mixed> $source
     * @return array{0: array<string, mixed>, 1: array<int, string>}
     */
    private function normalizeSourceRow(array $source, string $defaultSequence, ?string $fallbackCurrency = null): array
    {
        $payer = trim((string) ($source['payer'] ?? $source['payer_name'] ?? $source['company_name'] ?? $source['bill_to'] ?? ''));

        $rawDate = $source['transaction_date'] ?? $source['date'] ?? null;
        $date = $this->parseDate($rawDate);
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $beneficiary = trim((string) ($source['beneficiary_name'] ?? $source['beneficiary'] ?? ''));
        $account = trim((string) ($source['beneficiary_account'] ?? $source['account'] ?? ''));
        $amount = $this->parseAmount($source['amount'] ?? $source['amounts'] ?? $source['nominal'] ?? 0);

        $currencyRaw = trim((string) ($source['currency'] ?? ''));
        if ($currencyRaw === '' && $fallbackCurrency !== null) {
            $currencyRaw = $fallbackCurrency;
        }
        if ($currencyRaw === '') {
            $currencyRaw = 'USD';
        }
        $currency = strtoupper($currencyRaw);

        $teamRaw = $source['team_no'] ?? $source['team'] ?? 1;
        $teamDigits = preg_replace('/\D/', '', (string) $teamRaw) ?? '';
        $teamNo = $teamDigits !== '' ? (int) $teamDigits : 1;
        $teamNo = max(1, min(4, $teamNo));

        $errors = [];
        if ($payer === '') {
            $errors[] = 'Bill To/Payer kosong';
        }
        if ($beneficiary === '') {
            $errors[] = 'Beneficiary Name kosong';
        }
        if ($account === '') {
            $errors[] = 'rekening beneficiary kosong';
        }
        if ($amount <= 0) {
            $errors[] = 'nominal harus lebih dari nol';
        }
        if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
            $errors[] = 'mata uang harus kode 3 huruf';
        }

        return [[
            'sequence_no' => trim((string) ($source['sequence_no'] ?? $source['sequence'] ?? $source['no'] ?? '')) ?: $defaultSequence,
            'transaction_date' => $date,
            'payer' => $payer,
            'beneficiary_name' => $beneficiary,
            'beneficiary_account' => $account,
            'beneficiary_bank' => trim((string) ($source['beneficiary_bank'] ?? $source['bank'] ?? '')),
            'beneficiary_bank_location' => trim((string) ($source['beneficiary_bank_location'] ?? '')),
            'amount' => $amount,
            'beneficiary_country' => trim((string) ($source['beneficiary_country'] ?? $source['country'] ?? '')),
            'beneficiary_city' => trim((string) ($source['beneficiary_city'] ?? $source['city'] ?? '')),
            'beneficiary_address' => trim((string) ($source['beneficiary_address'] ?? $source['address'] ?? '')),
            'swift' => strtoupper(trim((string) ($source['swift'] ?? $source['swift_code'] ?? ''))),
            'payment_details' => trim((string) ($source['payment_details'] ?? $source['payment'] ?? '')),
            'currency' => $currency,
            'correspondent_bank' => trim((string) ($source['correspondent_bank'] ?? $source['corr_bank'] ?? '')),
            'correspondent_swift' => strtoupper(trim((string) ($source['correspondent_swift'] ?? $source['corr_swift'] ?? ''))),
            'correspondent_location' => trim((string) ($source['correspondent_location'] ?? $source['corr_location'] ?? '')),
            'payer_account' => trim((string) ($source['payer_account'] ?? '')),
            'payer_address' => trim((string) ($source['payer_address'] ?? '')),
            'payer_city' => trim((string) ($source['payer_city'] ?? '')),
            'payer_country' => trim((string) ($source['payer_country'] ?? '')) ?: 'INDONESIA',
            'payer_email' => trim((string) ($source['payer_email'] ?? $source['email'] ?? '')),
            'reference_no' => trim((string) ($source['reference_no'] ?? $source['reference'] ?? '')),
            'invoice_no' => trim((string) ($source['invoice_no'] ?? $source['invoice'] ?? '')),
            'team_no' => $teamNo,
        ], $errors];
    }

    /**
     * Mendeteksi header template baik format mentah biasa maupun format gabungan BRI MT103.
     *
     * @param array<int, array<int, mixed>> $rows
     * @return array{0: int, 1: array<string, int>, 2: bool}
     */
    private function locateHeader(array $rows): array
    {
        $aliases = $this->aliases();

        foreach (array_slice($rows, 0, 40, true) as $index => $row) {
            $normalized = array_map(fn ($value): string => $this->normalize((string) $value), $row);

            // Deteksi format gabungan BRI (14 kolom MT103)
            $isCombined = false;
            foreach ($normalized as $colIdx => $hdr) {
                if (
                    str_contains($hdr, 'bill to (payer)')
                    || str_contains($hdr, 'currency |amounts')
                    || str_contains($hdr, 'payment details 1 ###')
                    || str_contains($hdr, 'complete address of beneficiary account#')
                ) {
                    $isCombined = true;
                    break;
                }
            }

            if ($isCombined) {
                $map = [];
                foreach ($normalized as $cIdx => $hdr) {
                    if (str_contains($hdr, 'nomor rekening|bill to')) {
                        $map['payer_combined'] = $cIdx;
                    } elseif (str_contains($hdr, 'currency |amounts') || str_contains($hdr, 'currency|amounts')) {
                        $map['date_combined'] = $cIdx;
                    } elseif (str_contains($hdr, 'account number of beneficiary')) {
                        $map['account'] = $cIdx;
                    } elseif (str_contains($hdr, 'beneficiary name|complete address')) {
                        $map['beneficiary_combined'] = $cIdx;
                    } elseif (str_contains($hdr, 'swift')) {
                        $map['swift_combined'] = $cIdx;
                    } elseif (str_contains($hdr, 'payment details 1')) {
                        $map['payment_combined'] = $cIdx;
                    } elseif (str_contains($hdr, 'email')) {
                        $map['payer_email'] = $cIdx;
                    } elseif (str_contains($hdr, 'invoice')) {
                        $map['invoice'] = $cIdx;
                    }
                }
                return [(int) $index, $map, true];
            }

            // Deteksi format mentah (25 kolom biasa)
            $matches = 0;
            $requiredKeys = ['date', 'payer', 'beneficiary', 'account', 'amount', 'currency', 'swift'];

            foreach ($requiredKeys as $required) {
                foreach ($normalized as $header) {
                    if (in_array($header, $aliases[$required], true)) {
                        $matches++;
                        break;
                    }
                }
            }

            if ($matches < 3) {
                continue;
            }

            $map = [];
            foreach ($aliases as $key => $names) {
                foreach ($normalized as $columnIndex => $header) {
                    if (in_array($header, $names, true)) {
                        $map[$key] = $columnIndex;
                        break;
                    }
                }
            }

            return [(int) $index, $map, false];
        }

        throw new RuntimeException('Header template transaksi tidak ditemukan.');
    }

    /** @return array<string, array<int, string>> */
    private function aliases(): array
    {
        return [
            'sequence' => ['no', 'no.', 'nomor', 'remark', 'item no', 'item_no', 'seq', 'sequence', 'no urut'],
            'date' => [
                'date of transaction', 'tanggal transaksi', 'transaction date', 'tgl transaksi',
                'date', 'tanggal', 'tgl', 'value date', 'val date', 'tgl trf', 'tanggal trf',
            ],
            'payer' => [
                'bill to (payer)', 'bill to payer', 'bill to', 'payer', 'nama perusahaan',
                'company name', 'perusahaan', 'nama pengirim', 'pengirim', 'remitter', 'remitter name',
            ],
            'beneficiary' => [
                'beneficiary name', 'nama beneficiary', 'benef name', 'beneficiary',
                'nama penerima', 'penerima', 'beneficiary customer', 'ben name',
            ],
            'account' => [
                'account number of beneficiary', 'beneficiary account', 'account number',
                'beneficiary account number', 'no rekening beneficiary', 'rekening beneficiary',
                'account no', 'acc no', 'rekening penerima', 'no rek penerima', 'nomor rekening penerima',
            ],
            'bank' => [
                'beneficiary bank', 'bank beneficiary', 'nama bank', 'bank penerima',
                'beneficiary bank name', 'bank of beneficiary', 'receiving bank',
            ],
            'bank_location' => [
                'city, country of bank beneficiary', 'city country of bank beneficiary',
                'beneficiary bank location', 'bank address', 'alamat bank beneficiary',
                'beneficiary bank address', 'bank location', 'alamat bank',
            ],
            'amount' => ['amounts', 'amount', 'nominal', 'total', 'jumlah', 'nilai', 'transfer amount'],
            'country' => [
                'country of beneficiary', 'beneficiary country', 'negara beneficiary',
                'negara penerima', 'beneficiary country name', 'benef country',
            ],
            'city' => [
                'city of beneficiary', 'beneficiary city', 'kota beneficiary',
                'kota penerima', 'benef city',
            ],
            'address' => [
                'complete address of beneficiary account', 'beneficiary address',
                'alamat beneficiary', 'alamat penerima', 'complete address',
                'beneficiary complete address', 'benef address',
            ],
            'swift' => ['swift', 'swift code', 'beneficiary swift', 'bic', 'bic code', 'swift bic'],
            'payment' => [
                'payment details 1', 'payment details', 'payment detail', 'rincian pembayaran',
                'keterangan', 'payment details 1 ###', 'purpose of transaction', 'berita', 'remark',
            ],
            'currency' => ['currency', 'mata uang', 'curr', 'ccy', 'valuta'],
            'corr_bank' => [
                'name correspondence bank', 'correspondence bank', 'correspondent bank',
                'bank korespondensi', 'nama bank korespondensi', 'corr bank',
            ],
            'corr_swift' => [
                'swift bank corr', 'correspondent swift', 'swift bank correspondent',
                'swift korespondensi', 'corr swift', 'correspondent bic',
            ],
            'corr_location' => [
                'city, country bank corr', 'city country bank corr', 'correspondent bank location',
                'lokasi bank korespondensi', 'corr location',
            ],
            'payer_account' => [
                'nomor rekening', 'payer account', 'nomor rekening debet', 'nomorrekening',
                'rekening pengirim', 'no rek pengirim', 'debet account', 'source account', 'rekening debet',
            ],
            'payer_address' => ['alamat payer', 'payer address', 'alamat pengirim'],
            'payer_city' => ['kota payer', 'payer city', 'kota pengirim'],
            'payer_country' => ['negara payer', 'payer country', 'negara pengirim'],
            'payer_email' => ['email', 'payer email', 'email pengirim', 'email payer'],
            'reference' => ['reference number', 'no reference', 'no. reference', 'reference no', 'ref no', 'no ref', 'reference'],
            'invoice' => ['no inv', 'no invoice', 'no. invoice', 'invoice no', 'invoice number', 'nomor invoice', 'no inv.'],
            'team' => ['team', 'tim', 'team no', 'tim no', 'admin team', 'tim transaksi'],
        ];
    }

    private function cell(array $row, array $map, string $key): mixed
    {
        return isset($map[$key]) ? ($row[$map[$key]] ?? null) : null;
    }

    private function normalize(string $value): string
    {
        $value = preg_replace('/^\x{FEFF}/u', '', $value) ?? $value;
        $value = str_replace("\xEF\xBB\xBF", '', $value);
        $value = str_replace(["\r", "\n", "\t"], ' ', $value);
        $value = str_replace(['_', '-'], ' ', $value);
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
        return trim($value);
    }

    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    /** @param array<string|int, mixed> $row */
    private function isBlankAssocRow(array $row): bool
    {
        foreach ($row as $value) {
            if (is_scalar($value) && trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    /** @param array<string|int, mixed> $row */
    private function isSummaryRow(array $row): bool
    {
        $combined = '';
        foreach (array_slice($row, 0, 4) as $val) {
            if (is_scalar($val)) {
                $combined .= ' ' . (string) $val;
            }
        }
        $combined = mb_strtolower(trim($combined), 'UTF-8');

        foreach (['total', 'grand total', 'subtotal', 'jumlah', 'itogo', 'итого'] as $keyword) {
            if (str_contains($combined, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            $numeric = (float) $value;

            // Serial date Excel
            if ($numeric >= 20000 && $numeric <= 80000) {
                $days = (int) floor($numeric - 25569);
                return gmdate('Y-m-d', $days * 86400);
            }

            // Numerik ymd 6 digit (contoh 261013)
            $digits = preg_replace('/\D/', '', (string) $value) ?? '';
            if (strlen($digits) === 6) {
                $dt = DateTime::createFromFormat('ymd', $digits);
                if ($dt !== false) {
                    return $dt->format('Y-m-d');
                }
            }
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $text, $matches) === 1) {
            return $matches[1];
        }

        $formats = [
            'Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y',
            'Y/m/d', 'Y.m.d', 'd/m/y', 'd-m-y', 'd.m.y',
            'm/d/Y', 'm/d/y', 'Ymd', 'ymd',
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $text);
            if ($date !== false) {
                $errors = DateTime::getLastErrors();
                if (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0) {
                    return $date->format('Y-m-d');
                }
            }
        }

        $timestamp = strtotime($text);
        return ($timestamp !== false && $timestamp > 0) ? date('Y-m-d', $timestamp) : null;
    }

    private function parseAmount(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return 0.0;
        }

        $text = preg_replace('/[^\d,.\-]/', '', $text) ?? '';
        if ($text === '' || $text === '-') {
            return 0.0;
        }

        if (str_contains($text, ',') && str_contains($text, '.')) {
            if (strrpos($text, ',') > strrpos($text, '.')) {
                $text = str_replace('.', '', $text);
                $text = str_replace(',', '.', $text);
            } else {
                $text = str_replace(',', '', $text);
            }
        } elseif (str_contains($text, ',')) {
            $parts = explode(',', $text);
            $last = end($parts);
            if (count($parts) > 1 && strlen((string) $last) === 3) {
                $text = implode('', $parts);
            } elseif (count($parts) === 2 && strlen((string) $last) <= 2) {
                $text = $parts[0] . '.' . $last;
            } elseif (count($parts) > 2 && strlen((string) $last) <= 2) {
                $decimal = array_pop($parts);
                $text = implode('', $parts) . '.' . $decimal;
            } else {
                $text = str_replace(',', '', $text);
            }
        }

        return (float) $text;
    }

    private function stage2Text(string $value): string
    {
        $value = str_replace(["\r", "\n", "\t", '|', '#'], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
        return trim($value);
    }

    /**
     * Membersihkan tanda baca internal (titik, koma, slash, strip, kurung, dsb)
     * menjadi spasi rapi, dengan tetap mempertahankan pemisah |, #, ###, A||, E0|N|199, dan @.
     */
    private function cleanFinalCell(string $value, int $colIndex = -1): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        // Kolom 12: Email
        if ($colIndex === 12 || (str_contains($value, '@') && !str_contains($value, '|') && !str_contains($value, '#'))) {
            $cleanedEmail = preg_replace('/[^a-zA-Z0-9._%+\-@]/u', '', $value) ?? $value;
            return trim($cleanedEmail);
        }

        // Kolom N/A
        if (strcasecmp($value, 'N/A') === 0) {
            return 'N/A';
        }

        // Kolom 2: Date|Currency |Amount (contoh: 261013|USD |78000)
        if ($colIndex === 2 && str_contains($value, '|')) {
            $parts = explode('|', $value);
            if (count($parts) === 3) {
                $cDate = preg_replace('/\D/', '', $parts[0]);
                $cCurr = strtoupper(trim(preg_replace('/[^a-zA-Z]/', '', $parts[1])));
                $cAmount = trim($parts[2]);
                return $cDate . '|' . $cCurr . ' |' . $cAmount;
            }
        }

        // Cek penanda akhir ###
        $hasHashEnd = str_ends_with($value, '###');
        $raw = $hasHashEnd ? rtrim(substr($value, 0, -3)) : $value;

        // Split berdasarkan pipe (|)
        $pipeParts = explode('|', $raw);
        $cleanedPipeParts = [];

        foreach ($pipeParts as $pipePart) {
            // Split berdasarkan hash (#)
            $hashParts = explode('#', $pipePart);
            $cleanedHashParts = [];

            foreach ($hashParts as $segment) {
                if ($segment === '') {
                    $cleanedHashParts[] = '';
                    continue;
                }

                if (strcasecmp(trim($segment), 'N/A') === 0) {
                    $cleanedHashParts[] = 'N/A';
                    continue;
                }

                // Ubah tanda baca umum menjadi spasi
                $cleanedSegment = preg_replace('/[.,\/#!$%\^&\*;:{}=\-_`~()?"\'+]/u', ' ', $segment) ?? $segment;
                $cleanedSegment = preg_replace('/\s+/u', ' ', $cleanedSegment) ?? $cleanedSegment;
                $cleanedHashParts[] = trim($cleanedSegment);
            }

            $cleanedPipeParts[] = implode('#', $cleanedHashParts);
        }

        $result = implode('|', $cleanedPipeParts);

        if ($hasHashEnd) {
            $result = rtrim($result) . ' ###';
        }

        return $result;
    }

    private function amountToken(float $amount): string
    {
        if (abs($amount - round($amount)) < 0.00001) {
            return sprintf('%.0f', round($amount));
        }

        return str_replace('.', ',', rtrim(rtrim(number_format($amount, 4, '.', ''), '0'), '.'));
    }
}