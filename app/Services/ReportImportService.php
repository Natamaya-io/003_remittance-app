<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use RuntimeException;

final class ReportImportService
{
    /**
     * Membaca file report secara ketat.
     *
     * Setiap baris yang tidak memenuhi struktur wajib tidak dimasukkan ke
     * database. Nama klien dari form diterapkan sebagai TEAM pada seluruh baris.
     *
     * @return array{
     *   rows:array<int,array<string,mixed>>,
     *   sheets:array<int,string>,
     *   skipped:int,
     *   total_source_rows:int,
     *   issues:array<int,string>,
     *   reasons:array<string,int>
     * }
     */
    public function parse(
        string $file,
        ?string $expectedMonth = null,
        ?string $fallbackDate = null,
        ?string $clientName = null
    ): array {
        if (
            $expectedMonth !== null
            && preg_match('/^\d{4}-\d{2}$/', $expectedMonth) !== 1
        ) {
            throw new RuntimeException(
                'Format bulan report tidak valid. Gunakan YYYY-MM.'
            );
        }

        if (
            $fallbackDate !== null
            && $fallbackDate !== ''
            && $this->parseDate($fallbackDate) === null
        ) {
            throw new RuntimeException('Tanggal report tidak valid.');
        }

        $clientName = $this->cleanText($clientName);

        if ($clientName === '') {
            throw new RuntimeException(
                'Nama klien wajib diisi. Nama klien otomatis digunakan sebagai TEAM.'
            );
        }
        $workbook = SpreadsheetReader::readWorkbook($file);

        $parsed = [];
        $usedSheets = [];
        $issues = [];
        $reasons = [];
        $skipped = 0;
        $sourceRows = 0;
        $seenHashes = [];

        foreach ($workbook as $sheetName => $rows) {
            try {
                [$headerIndex, $map] = $this->locateHeader($rows);
            } catch (RuntimeException $exception) {
                $this->reason($reasons, 'sheet_tanpa_header');
                $this->issue(
                    $issues,
                    sprintf(
                        'Sheet "%s" dilewati: %s',
                        $sheetName,
                        $exception->getMessage()
                    )
                );
                continue;
            }

            $sheetAccepted = 0;

            foreach (
                array_slice($rows, $headerIndex + 1)
                as $sourceIndex => $row
            ) {
                if ($this->isBlankRow($row)) {
                    continue;
                }

                $sourceRows++;
                $excelRow = $headerIndex + $sourceIndex + 2;

                $billTo = $this->cleanText(
                    $this->cell($row, $map, 'bill_to')
                );
                $beneficiary = $this->cleanText(
                    $this->cell($row, $map, 'beneficiary_name')
                );
                $amount = $this->parseAmount(
                    $this->cell($row, $map, 'amount')
                );
                $itemNo = $this->cleanText(
                    $this->cell($row, $map, 'item_no')
                );

                $currency = strtoupper(
                    $this->cleanText(
                        $this->cell($row, $map, 'currency')
                    )
                );

                /*
                 * Banyak template memakai nama sheet USD/EUR/CNY/AED sebagai
                 * identitas mata uang. Gunakan nama sheet hanya bila kolom
                 * Currency kosong atau tidak valid.
                 */
                $sheetCurrency = strtoupper(trim((string) $sheetName));
                if (
                    preg_match('/^[A-Z]{3}$/', $currency) !== 1
                    && preg_match('/^[A-Z]{3}$/', $sheetCurrency) === 1
                ) {
                    $currency = $sheetCurrency;
                }

                if (
                    $this->isSummaryRow(
                        $row,
                        $billTo,
                        $beneficiary,
                        $itemNo
                    )
                ) {
                    $skipped++;
                    $this->reason($reasons, 'baris_total_ringkasan');
                    $this->issue(
                        $issues,
                        "{$sheetName}!{$excelRow}: "
                        . 'baris total/ringkasan tidak diimpor.'
                    );
                    continue;
                }

                $missing = [];

                if ($billTo === '') {
                    $missing[] = 'Bill To (Payer)';
                }
                if ($beneficiary === '') {
                    $missing[] = 'Beneficiary Name';
                }
                if ($amount <= 0) {
                    $missing[] = 'Amounts > 0';
                }
                if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
                    $missing[] = 'Currency 3 huruf';
                }

                if ($missing !== []) {
                    $skipped++;
                    $this->reason($reasons, 'kolom_wajib_tidak_valid');
                    $this->issue(
                        $issues,
                        "{$sheetName}!{$excelRow}: "
                        . implode(', ', $missing)
                        . ' tidak valid.'
                    );
                    continue;
                }

                $date = $this->parseDate(
                    $this->cell($row, $map, 'date')
                );

                if (
                    $date !== null
                    && $expectedMonth !== null
                    && substr($date, 0, 7) !== $expectedMonth
                ) {
                    $skipped++;
                    $this->reason($reasons, 'tanggal_di_luar_bulan');
                    $this->issue(
                        $issues,
                        "{$sheetName}!{$excelRow}: tanggal {$date} "
                        . "berada di luar bulan {$expectedMonth}."
                    );
                    continue;
                }

                if ($date === null) {
                    $date = $this->parseDate($fallbackDate)
                        ?: (
                            $expectedMonth !== null
                                ? $expectedMonth . '-01'
                                : null
                        );
                }

                /*
                 * Nomor invoice dan kontrak tidak dibersihkan dengan pemisahan
                 * nomor/tanggal. Nilai tampil dari Excel disimpan utuh agar nol
                 * di depan, slash, titik, tanda hubung, dan teks tetap sama.
                 */
                $invoiceNumber = $this->cleanText(
                    $this->cell($row, $map, 'invoice_no')
                );
                $invoiceDate = $this->parseDate(
                    $this->cell($row, $map, 'invoice_date')
                ) ?: $this->extractDateFromText($invoiceNumber);

                $contractNumber = $this->cleanText(
                    $this->cell($row, $map, 'contract_no')
                );
                $contractDate = $this->parseDate(
                    $this->cell($row, $map, 'contract_date')
                ) ?: $this->extractDateFromText($contractNumber);

                $beneficiaryAccount = $this->cleanText(
                    $this->cell($row, $map, 'beneficiary_account')
                );
                $swift = strtoupper(
                    $this->cleanText(
                        $this->cell($row, $map, 'swift')
                    )
                );

                /*
                 * TEAM tidak dibaca dari workbook. Satu file adalah satu batch
                 * milik satu klien, sehingga seluruh baris memakai nama klien
                 * yang diisi pada form upload.
                 */
                $rowTeam = $clientName;

                $hash = hash(
                    'sha256',
                    implode('|', [
                        $expectedMonth ?? substr((string) $date, 0, 7),
                        (string) $date,
                        $this->normalize($rowTeam),
                        $this->normalize($billTo),
                        $this->normalize($beneficiary),
                        preg_replace('/\s+/', '', $beneficiaryAccount)
                            ?? $beneficiaryAccount,
                        number_format($amount, 4, '.', ''),
                        $currency,
                        $swift,
                        $invoiceNumber,
                        $contractNumber,
                    ])
                );

                if (isset($seenHashes[$hash])) {
                    $skipped++;
                    $this->reason($reasons, 'duplikat_dalam_file');
                    $this->issue(
                        $issues,
                        "{$sheetName}!{$excelRow}: "
                        . 'duplikat dengan baris lain di file yang sama.'
                    );
                    continue;
                }

                $seenHashes[$hash] = true;

                $deliveryRaw = strtoupper(
                    $this->cleanText(
                        $this->cell($row, $map, 'delivery_status')
                    )
                );
                $resultRaw = strtoupper(
                    $this->cleanText(
                        $this->cell($row, $map, 'result_status')
                    )
                );

                $parsed[] = [
                    'source_sheet' => $sheetName,
                    'source_row' => $excelRow,
                    'source_hash' => $hash,
                    'team_label' => $rowTeam,
                    'item_no' => $itemNo,
                    'transaction_date' => $date,
                    'bill_to' => $billTo,
                    'beneficiary_name' => $beneficiary,
                    'beneficiary_account' => $beneficiaryAccount,
                    'beneficiary_bank' => $this->cleanText(
                        $this->cell($row, $map, 'beneficiary_bank')
                    ),
                    'beneficiary_bank_address' => $this->cleanText(
                        $this->cell(
                            $row,
                            $map,
                            'beneficiary_bank_address'
                        )
                    ),
                    'amount' => $amount,
                    'beneficiary_country' => $this->cleanText(
                        $this->cell($row, $map, 'beneficiary_country')
                    ),
                    'beneficiary_city' => $this->cleanText(
                        $this->cell($row, $map, 'beneficiary_city')
                    ),
                    'beneficiary_address' => $this->cleanText(
                        $this->cell($row, $map, 'beneficiary_address')
                    ),
                    'swift' => $swift,
                    'payment_details' => $this->cleanText(
                        $this->cell($row, $map, 'payment_details')
                    ),
                    'currency' => $currency,
                    'invoice_no' => $invoiceNumber,
                    'invoice_date' => $invoiceDate,
                    'contract_no' => $contractNumber,
                    'contract_date' => $contractDate,
                    'correspondent_bank' => $this->cleanText(
                        $this->cell($row, $map, 'correspondent_bank')
                    ),
                    'correspondent_swift' => strtoupper(
                        $this->cleanText(
                            $this->cell(
                                $row,
                                $map,
                                'correspondent_swift'
                            )
                        )
                    ),
                    'correspondent_country' => $this->cleanText(
                        $this->cell(
                            $row,
                            $map,
                            'correspondent_country'
                        )
                    ),
                    'bank_of_payer' => $this->cleanText(
                        $this->cell($row, $map, 'bank_of_payer')
                    ),
                    'invoice_link' => $this->cleanText(
                        $this->cell($row, $map, 'invoice_link')
                    ),
                    'processing_bank' => $this->cleanText(
                        $this->cell($row, $map, 'processing_bank')
                    ),
                    'delivery_status' => $this->deliveryStatus(
                        $deliveryRaw
                    ),
                    'result_status' => $this->resultStatus($resultRaw),
                    'refund_status' => $this->cleanText(
                        $this->cell($row, $map, 'refund_status')
                    ),
                    'notes' => $this->cleanText(
                        $this->cell($row, $map, 'notes')
                    ),
                    'source_json' => json_encode(
                        $row,
                        JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    ),
                ];

                $sheetAccepted++;
            }

            if ($sheetAccepted > 0) {
                $usedSheets[] = $sheetName;
            }
        }

        if ($parsed === []) {
            $detail = $issues !== []
                ? ' Contoh masalah: ' . $issues[0]
                : '';

            throw new RuntimeException(
                'Tidak ada baris report valid yang dapat diimpor.' . $detail
            );
        }

        return [
            'rows' => $parsed,
            'sheets' => array_values(array_unique($usedSheets)),
            'skipped' => $skipped,
            'total_source_rows' => $sourceRows,
            'issues' => $issues,
            'reasons' => $reasons,
        ];
    }

    /**
     * @param array<int,array<int,mixed>> $rows
     * @return array{0:int,1:array<string,int>}
     */
    private function locateHeader(array $rows): array
    {
        $aliases = $this->aliases();

        foreach (array_slice($rows, 0, 40, true) as $index => $row) {
            $normalized = array_map(
                fn (mixed $value): string =>
                    $this->normalize((string) $value),
                $row
            );

            $map = [];

            foreach ($aliases as $key => $names) {
                foreach ($normalized as $column => $header) {
                    if (in_array($header, $names, true)) {
                        $map[$key] = $column;
                        break;
                    }
                }
            }

            if (
                !isset(
                    $map['bill_to'],
                    $map['beneficiary_name'],
                    $map['amount']
                )
            ) {
                continue;
            }

            /*
             * Currency boleh tidak berada pada kolom bila nama sheet adalah
             * kode mata uang tiga huruf. Validasi akhirnya dilakukan per baris.
             */
            if (isset($map['invoice_link'])) {
                $afterLink = $map['invoice_link'] + 1;

                foreach (
                    [
                        'processing_bank' => 0,
                        'delivery_status' => 1,
                        'result_status' => 2,
                    ]
                    as $key => $offset
                ) {
                    if (
                        !isset($map[$key])
                        && array_key_exists($afterLink + $offset, $row)
                    ) {
                        $map[$key] = $afterLink + $offset;
                    }
                }
            }

            return [(int) $index, $map];
        }

        throw new RuntimeException(
            'header wajib Bill To, Beneficiary Name, dan Amounts '
            . 'tidak ditemukan'
        );
    }

    /** @return array<string,array<int,string>> */
    private function aliases(): array
    {
        return [
            'team' => [
                'team',
                'tim',
                'nama team',
                'nama tim',
            ],
            'item_no' => [
                'no',
                'no.',
                'nomor',
                'item no',
                'item number',
            ],
            'date' => [
                'date of transaction',
                'tanggal transaksi',
                'transaction date',
                'date',
                'tanggal',
            ],
            'bill_to' => [
                'bill to (payer)',
                'bill to payer',
                'bill to',
                'payer',
            ],
            'beneficiary_name' => [
                'beneficiary name',
                'nama beneficiary',
                'beneficiary',
            ],
            'beneficiary_account' => [
                'account number of beneficiary',
                'number of beneficiary',
                'number of beneficiary bank',
                'beneficiary account',
                'account number',
            ],
            'beneficiary_bank' => [
                'beneficiary bank',
                'bank name',
                'bank beneficiary',
            ],
            'beneficiary_bank_address' => [
                'beneficiary bank address',
                'bank address',
                'city, country of bank beneficiary',
                'city country of bank beneficiary',
            ],
            'amount' => [
                'amounts',
                'amount',
                'nominal',
                'jumlah',
            ],
            'beneficiary_country' => [
                'country of beneficiary',
                'country beneficiary',
                'beneficiary country',
            ],
            'beneficiary_city' => [
                'city of beneficiary',
                'city beneficiary',
                'beneficiary city',
            ],
            'beneficiary_address' => [
                'address of beneficiary',
                'beneficiary address',
                'complete address of beneficiary account',
            ],
            'swift' => [
                'swift',
                'swift code',
                'beneficiary swift',
            ],
            'payment_details' => [
                'payment details',
                'payment details 1',
                'payment detail',
            ],
            'currency' => [
                'currency',
                'mata uang',
                'curr',
            ],
            'invoice_no' => [
                'invoice #',
                'invoice no',
                'invoice no.',
                'invoice number',
                'invoice # date',
                'invoice #date',
                'no invoice',
                'nomor invoice',
            ],
            'invoice_date' => [
                'invoice date',
                'tanggal invoice',
                'date invoice',
            ],
            'contract_no' => [
                'contract #',
                'contract no',
                'contract no.',
                'contract number',
                'contract # date',
                'contract #date',
                'nomor kontrak',
            ],
            'contract_date' => [
                'contract date',
                'tanggal kontrak',
            ],
            'correspondent_bank' => [
                'correspondence',
                'correspondent bank',
                'correspondence bank',
            ],
            'correspondent_swift' => [
                'swift bank corr',
                'swift bank correspondent',
                'correspondent swift',
            ],
            'correspondent_country' => [
                'country bank corr',
                'country bank correspondent',
                'correspondent country',
                'city, country bank corr',
            ],
            'bank_of_payer' => [
                'bank of payer',
                'payer bank',
            ],
            'invoice_link' => [
                'link to invoice',
                'invoice link',
                'link invoice',
            ],
            'processing_bank' => [
                'processing bank',
                'bank process',
                'bank transaksi',
                'bank',
            ],
            'delivery_status' => [
                'delivery status',
                'status kirim',
                'status pengiriman',
                'terkirim',
            ],
            'result_status' => [
                'result status',
                'hasil',
                'status hasil',
                'success',
            ],
            'refund_status' => [
                'refund status',
                'status refund',
            ],
            'notes' => [
                'notes',
                'note',
                'remarks',
                'keterangan',
            ],
        ];
    }

    /** @param array<int,mixed> $row @param array<string,int> $map */
    private function cell(array $row, array $map, string $key): mixed
    {
        return isset($map[$key])
            ? ($row[$map[$key]] ?? null)
            : null;
    }

    private function cleanText(mixed $value): string
    {
        return trim(
            (string) preg_replace('/\s+/u', ' ', (string) $value)
        );
    }

    private function normalize(string $value): string
    {
        $value = preg_replace('/^\x{FEFF}/u', '', $value) ?? $value;
        $value = str_replace("\xEF\xBB\xBF", '', $value);
        $value = strtolower(
            trim(
                str_replace(
                    ["\r", "\n", '_', '-'],
                    ' ',
                    $value
                )
            )
        );

        return trim(
            (string) preg_replace('/\s+/', ' ', $value)
        );
    }

    /** @param array<int,mixed> $row */
    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /** @param array<int,mixed> $row */
    private function isSummaryRow(
        array $row,
        string $billTo,
        string $beneficiary,
        string $itemNo
    ): bool {
        $joined = $this->normalize(
            implode(' ', array_map('strval', $row))
        );
        $marker = $this->normalize(
            $billTo . ' ' . $beneficiary . ' ' . $itemNo
        );

        return preg_match(
            '/(^|\s)(total|grand total|subtotal|jumlah|итого|итог)(\s|:|$)/iu',
            $marker . ' ' . $joined
        ) === 1;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            $numeric = (float) $value;
            $digits = preg_replace('/\D/', '', (string) $value) ?? '';

            if (strlen($digits) === 6) {
                $date = DateTime::createFromFormat('ymd', $digits);

                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            if ($numeric > 20000 && $numeric < 80000) {
                return gmdate(
                    'Y-m-d',
                    (int) (($numeric - 25569) * 86400)
                );
            }
        }

        $text = trim((string) $value);

        foreach (
            [
                'Y-m-d',
                'd/m/Y',
                'd/m/y',
                'm/d/Y',
                'd-m-Y',
                'd-m-y',
                'Y/m/d',
                'd.m.Y',
                'd.m.y',
            ]
            as $format
        ) {
            $date = DateTime::createFromFormat($format, $text);

            if (
                $date !== false
                && $date->format($format) === $text
            ) {
                return $date->format('Y-m-d');
            }
        }

        $timestamp = strtotime($text);

        return $timestamp !== false
            ? date('Y-m-d', $timestamp)
            : null;
    }

    private function parseAmount(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $text = preg_replace(
            '/[^0-9,.-]/',
            '',
            (string) $value
        ) ?? '';

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
            $text = strlen((string) $last) <= 2
                ? str_replace(',', '.', $text)
                : str_replace(',', '', $text);
        }

        return (float) $text;
    }

    /**
     * Mengambil tanggal dari teks tanpa mengubah teks sumber.
     */
    private function extractDateFromText(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        $patterns = [
            '/\b(\d{2}[.\/-]\d{2}[.\/-]\d{4})\b/',
            '/\b(\d{2}[.\/-]\d{2}[.\/-]\d{2})\b/',
            '/\b(\d{4}[.\/-]\d{2}[.\/-]\d{2})\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value, $matches) === 1) {
                return $this->parseDate($matches[1]);
            }
        }

        return null;
    }

    private function deliveryStatus(string $value): string
    {
        return in_array(
            $value,
            ['SENT', 'TERKIRIM', 'KIRIM', 'DONE'],
            true
        )
            ? 'SENT'
            : 'PENDING';
    }

    private function resultStatus(string $value): string
    {
        if (
            in_array(
                $value,
                ['SUCCESS', 'SUKSES', 'BERHASIL'],
                true
            )
        ) {
            return 'SUCCESS';
        }

        if (
            in_array(
                $value,
                ['FAILED', 'FAIL', 'GAGAL', 'REJECTED', 'REJECT'],
                true
            )
        ) {
            return 'FAILED';
        }

        return 'PENDING';
    }

    /** @param array<string,int> $reasons */
    private function reason(array &$reasons, string $key): void
    {
        $reasons[$key] = ($reasons[$key] ?? 0) + 1;
    }

    /** @param array<int,string> $issues */
    private function issue(array &$issues, string $message): void
    {
        if (count($issues) < 40) {
            $issues[] = $message;
        }
    }
}