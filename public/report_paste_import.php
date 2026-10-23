<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\App;
use App\Core\Auth;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function pasteEscape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pasteCsrf(): string
{
    if (empty($_SESSION['report_paste_csrf'])) {
        $_SESSION['report_paste_csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['report_paste_csrf'];
}

function pasteNormalizeDate(mixed $value): ?string
{
    if (is_numeric($value) && (float) $value > 20000 && (float) $value < 80000) {
        return date('Y-m-d', (int) (((float) $value - 25569) * 86400));
    }

    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    foreach (['Y-m-d', 'd/m/Y', 'd/m/y', 'd-m-Y', 'm/d/Y', 'Y/m/d', 'd M Y', 'd F Y'] as $format) {
        $date = \DateTime::createFromFormat($format, $value);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? null : date('Y-m-d', $timestamp);
}

function pasteNormalizeAmount(mixed $value): float
{
    if (is_numeric($value)) {
        return (float) $value;
    }

    $value = preg_replace('/[^0-9,.\-]/', '', trim((string) $value)) ?? '';
    if ($value === '') {
        return 0.0;
    }

    if (str_contains($value, ',') && str_contains($value, '.')) {
        if (strrpos($value, ',') > strrpos($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '', $value);
        }
    } elseif (str_contains($value, ',')) {
        $value = str_replace(',', '.', $value);
    }

    return (float) $value;
}

function pasteNormalizeStatus(mixed $value): string
{
    $status = strtoupper(trim((string) $value));
    return match (true) {
        in_array($status, ['SUCCESS', 'SUKSES', 'TERKIRIM', 'DONE', 'PAID'], true) => 'SUCCESS',
        in_array($status, ['FAILED', 'GAGAL', 'REJECT', 'REJECTED'], true) => 'FAILED',
        in_array($status, ['CANCEL', 'CANCELLED', 'BATAL'], true) => 'CANCELLED',
        default => 'PENDING',
    };
}

function pasteNormalizeRefund(mixed $value): string
{
    $status = strtolower(trim((string) $value));
    return match (true) {
        str_contains($status, 'success') || str_contains($status, 'sukses') => 'success',
        str_contains($status, 'process') || str_contains($status, 'proses') => 'processed',
        str_contains($status, 'request') || str_contains($status, 'aju') => 'requested',
        str_contains($status, 'fail') || str_contains($status, 'gagal') => 'failed',
        default => 'none',
    };
}

function pasteCompanyId(\PDO $pdo, string $name, int $userId): int
{
    $name = trim(preg_replace('/\s+/', ' ', $name) ?? $name);
    if ($name === '') {
        throw new \RuntimeException('Bill To/Payer kosong.');
    }

    $stmt = $pdo->prepare(
        'SELECT id FROM companies
         WHERE UPPER(REPLACE(REPLACE(name, ".", ""), " ", "")) =
               UPPER(REPLACE(REPLACE(?, ".", ""), " ", ""))
         LIMIT 1'
    );
    $stmt->execute([$name]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int) $id;
    }

    $prefix = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $name) ?? 'PT');
    $prefix = substr($prefix !== '' ? $prefix : 'PT', 0, 12);
    $baseCode = $prefix . '-' . strtoupper(substr(hash('sha256', $name), 0, 6));
    $code = $baseCode;
    $counter = 1;

    while (true) {
        $check = $pdo->prepare('SELECT id FROM companies WHERE code = ? LIMIT 1');
        $check->execute([$code]);
        if (!$check->fetchColumn()) {
            break;
        }
        $counter++;
        $code = substr($baseCode, 0, 24) . '-' . $counter;
    }

    $insert = $pdo->prepare(
        "INSERT INTO companies (code, name, status, created_by, created_at, updated_at)
         VALUES (?, ?, 'active', ?, NOW(), NOW())"
    );
    $insert->execute([$code, $name, $userId]);

    return (int) $pdo->lastInsertId();
}

if (!Auth::check()) {
    redirect('/login');
}

$user = Auth::user() ?? [];
$roleCode = (string) ($user['role_code'] ?? '');
$allowed = in_array($roleCode, [
    'super_admin', 'superadmin', 'supervisor',
    'admin_transaksi_1', 'admin_transaksi_2', 'admin_transaksi_3', 'admin_transaksi_4',
], true) || can('transaction.manage');

if (!$allowed) {
    http_response_code(403);
    exit('Anda tidak memiliki akses untuk mengimpor transaksi.');
}

$pdo = App::db();

if (($_GET['action'] ?? '') === 'import') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $payload = json_decode(file_get_contents('php://input') ?: '', true, 512, JSON_THROW_ON_ERROR);
        $token = (string) ($payload['csrf'] ?? '');
        if ($token === '' || !hash_equals(pasteCsrf(), $token)) {
            throw new \RuntimeException('Token keamanan tidak valid. Muat ulang halaman.');
        }

        $rows = $payload['rows'] ?? [];
        if (!is_array($rows)) {
            throw new \RuntimeException('Format data tidak valid.');
        }
        if (count($rows) > 2000) {
            throw new \RuntimeException('Maksimal 2.000 baris per import.');
        }

        $insert = $pdo->prepare(
            'INSERT INTO transactions (
                team_no, invoice_no, reference_no, transaction_date, payer_company_id,
                beneficiary_name, beneficiary_account, beneficiary_bank, beneficiary_bank_location,
                amount, beneficiary_country, beneficiary_city, beneficiary_address, swift,
                payment_details, currency, correspondent_bank, correspondent_swift,
                correspondent_location, payer_bank, invoice_link, status, refund_status,
                notes, source_hash, created_by, created_at, updated_at
             ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
             )'
        );
        $duplicate = $pdo->prepare('SELECT id FROM transactions WHERE source_hash = ? LIMIT 1');

        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $userId = (int) ($user['id'] ?? 0);

        $pdo->beginTransaction();

        foreach ($rows as $rowIndex => $row) {
            if (!is_array($row) || !array_filter($row, static fn ($value): bool => trim((string) $value) !== '')) {
                $skipped++;
                continue;
            }

            try {
                $date = pasteNormalizeDate($row['transaction_date'] ?? null);
                if ($date === null) {
                    throw new \RuntimeException('Tanggal transaksi tidak valid.');
                }

                $payerName = trim((string) ($row['bill_to'] ?? ''));
                $companyId = pasteCompanyId($pdo, $payerName, $userId);
                $amount = pasteNormalizeAmount($row['amount'] ?? 0);
                $currency = strtoupper(substr(trim((string) ($row['currency'] ?? 'USD')), 0, 3));
                if ($currency === '') {
                    $currency = 'USD';
                }

                $team = Auth::isTransactionAdmin()
                    ? (int) (Auth::transactionTeam() ?? 1)
                    : max(1, min(4, (int) preg_replace('/[^0-9]/', '', (string) ($row['team'] ?? '1'))));

                $reference = trim((string) ($row['reference_number'] ?? ''));
                $sourceHash = hash('sha256', implode('|', [
                    $date,
                    $companyId,
                    number_format($amount, 4, '.', ''),
                    $reference,
                    trim((string) ($row['invoice_no'] ?? '')),
                    trim((string) ($row['beneficiary_name'] ?? '')),
                    trim((string) ($row['swift'] ?? '')),
                ]));

                $duplicate->execute([$sourceHash]);
                if ($duplicate->fetchColumn()) {
                    $skipped++;
                    continue;
                }

                $insert->execute([
                    $team,
                    trim((string) ($row['invoice_no'] ?? '')) ?: null,
                    $reference ?: null,
                    $date,
                    $companyId,
                    trim((string) ($row['beneficiary_name'] ?? '')) ?: null,
                    trim((string) ($row['beneficiary_account'] ?? '')) ?: null,
                    trim((string) ($row['beneficiary_bank'] ?? '')) ?: null,
                    trim((string) ($row['beneficiary_bank_location'] ?? '')) ?: null,
                    $amount,
                    trim((string) ($row['beneficiary_country'] ?? '')) ?: null,
                    trim((string) ($row['beneficiary_city'] ?? '')) ?: null,
                    trim((string) ($row['beneficiary_address'] ?? '')) ?: null,
                    strtoupper(trim((string) ($row['swift'] ?? ''))) ?: null,
                    trim((string) ($row['payment_details'] ?? '')) ?: null,
                    $currency,
                    trim((string) ($row['correspondent_bank'] ?? '')) ?: null,
                    strtoupper(trim((string) ($row['correspondent_swift'] ?? ''))) ?: null,
                    trim((string) ($row['correspondent_location'] ?? '')) ?: null,
                    trim((string) ($row['payer_bank'] ?? '')) ?: null,
                    trim((string) ($row['invoice_link'] ?? '')) ?: null,
                    pasteNormalizeStatus($row['status'] ?? ''),
                    pasteNormalizeRefund($row['refund_status'] ?? ''),
                    trim((string) ($row['notes'] ?? '')) ?: null,
                    $sourceHash,
                    $userId,
                ]);
                $inserted++;
            } catch (\Throwable $rowError) {
                $errors[] = 'Baris ' . ($rowIndex + 1) . ': ' . $rowError->getMessage();
            }
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'inserted' => $inserted,
            'skipped' => $skipped,
            'errors' => array_slice($errors, 0, 30),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (\Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => $exception->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

$reportColumns = [
    [
        'key' => 'team',
        'label' => 'TEAM',
    ],
    [
        'key' => 'invoice_no',
        'label' => 'No. Invoice',
    ],
    [
        'key' => 'transaction_date',
        'label' => 'Date of Transaction',
    ],
    [
        'key' => 'bill_to',
        'label' => 'Bill To/Payer',
    ],
    [
        'key' => 'beneficiary_name',
        'label' => 'Beneficiary Name',
    ],
    [
        'key' => 'beneficiary_account',
        'label' =>
            'Account Number of Beneficiary',
    ],
    [
        'key' => 'beneficiary_bank',
        'label' => 'Beneficiary Bank',
    ],
    [
        'key' =>
            'beneficiary_bank_location',
        'label' =>
            'Beneficiary Bank Location',
    ],
    [
        'key' => 'amount',
        'label' => 'Amounts',
    ],
    [
        'key' => 'beneficiary_country',
        'label' => 'Beneficiary Country',
    ],
    [
        'key' => 'beneficiary_city',
        'label' => 'Beneficiary City',
    ],
    [
        'key' => 'beneficiary_address',
        'label' => 'Beneficiary Address',
    ],
    [
        'key' => 'swift',
        'label' => 'SWIFT',
    ],
    [
        'key' => 'payment_details',
        'label' => 'Payment Details',
    ],
    [
        'key' => 'currency',
        'label' => 'Currency',
    ],
    [
        'key' => 'correspondent_bank',
        'label' => 'Correspondent Bank',
    ],
    [
        'key' => 'correspondent_swift',
        'label' =>
            'SWIFT Bank Correspondent',
    ],
    [
        'key' =>
            'correspondent_location',
        'label' =>
            'Correspondent Bank Location',
    ],
    [
        'key' => 'payer_bank',
        'label' => 'Bank of Payer',
    ],
    [
        'key' => 'reference_number',
        'label' => 'Reference Number',
    ],
    [
        'key' => 'invoice_link',
        'label' => 'Invoice Link',
    ],
    [
        'key' => 'status',
        'label' => 'Status',
    ],
    [
        'key' => 'refund_status',
        'label' => 'Refund Status',
    ],
    [
        'key' => 'notes',
        'label' => 'Notes',
    ],
];

?>
<!doctype html>

<html lang="id">

<head>

<meta charset="utf-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>
    Paste Report dari Excel
</title>

<link
    rel="stylesheet"
    href="<?= pasteEscape(asset('css/modern-banking.css')) ?>"
<link rel="stylesheet" href="<?= pasteEscape(asset('css/remitops-v5.css')) ?>">
>

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
>

<style>

body {
    margin: 0;
}

.import-header {
    padding: 28px 32px;

    color: #ffffff;

    background:
        radial-gradient(
            circle at 90% 5%,
            rgba(93, 200, 255, 0.38),
            transparent 27%
        ),
        linear-gradient(
            120deg,
            #072b55,
            #1479cb
        );
}

.import-header-inner,
.import-container {
    max-width: 1450px;
    margin: 0 auto;
}

.import-header-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 18px;
}

.import-header h1 {
    margin: 0 0 6px;
}

.import-header p {
    margin: 0;

    color:
        rgba(255, 255, 255, 0.78);
}

.import-container {
    padding: 25px 20px;
}

.import-card {
    padding: 22px;

    margin-bottom: 18px;

    border:
        1px solid #dce6f1;

    border-radius: 18px;

    background: #ffffff;

    box-shadow:
        var(--bank-shadow);
}

.import-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;

    margin-top: 15px;
}

.import-button {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 11px 15px;

    border: none;
    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #1265d8,
            #3296f4
        );

    color: #ffffff;

    text-decoration: none;
    font: inherit;
    font-weight: 800;

    cursor: pointer;
}

.import-button.secondary {
    background: #edf4fc;
    color: #205289;
}

.import-button.success {
    background:
        linear-gradient(
            135deg,
            #0e9e6c,
            #31bd87
        );
}

.import-button:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.preview-wrapper {
    overflow: auto;

    max-height: 520px;

    border:
        1px solid #dce6f1;

    border-radius: 13px;
}

.preview-table {
    min-width: 2300px;
}

.preview-table td,
.preview-table th {
    white-space: nowrap;
}

.import-message {
    display: none;

    margin-top: 15px;
    padding: 13px;

    border-radius: 11px;
}

.import-message.success {
    display: block;

    background: #e8f8ef;
    color: #146b40;
}

.import-message.error {
    display: block;

    background: #feecec;
    color: #9d2525;
}

@media (
    max-width: 800px
) {
    .import-header-inner {
        flex-direction: column;
        align-items: flex-start;
    }
}

</style>

<link rel="stylesheet" href="<?= e(asset('css/theme-clean-global.css')) ?>">
</head>

<body>


<header class="import-header">

<div class="import-header-inner">

<div>

<h1>
    Import Report Harian dari Excel
</h1>

<p>
    Salin tabel Excel, paste, periksa preview,
    kemudian simpan sekaligus.
</p>

</div>

<a
    class="import-button secondary"
    href="<?= pasteEscape(
        url('/reports/daily')
    ) ?>"
>
    ← Kembali ke Report
</a>

</div>

</header>


<main class="import-container">


<section class="import-card">

<h2>
    1. Paste Data Excel
</h2>

<p>
    Blok data di Excel, tekan
    <strong>Ctrl + C</strong>,
    klik kotak di bawah, lalu tekan
    <strong>Ctrl + V</strong>.
</p>

<textarea
    id="excelPaste"
    class="excel-paste-box"
    style="width:100%"
    placeholder="Paste tabel Excel di sini..."
></textarea>


<div class="import-actions">

<button
    class="import-button"
    type="button"
    id="previewButton"
>
    <i class="bi bi-table"></i>
    Buat Preview
</button>

<button
    class="import-button secondary"
    type="button"
    id="copyHeadersButton"
>
    <i class="bi bi-clipboard"></i>
    Salin Header Excel
</button>

<button
    class="import-button secondary"
    type="button"
    id="clearButton"
>
    Bersihkan
</button>

</div>

<div
    id="messageBox"
    class="import-message"
></div>

</section>


<section
    class="import-card"
    id="previewSection"
    hidden
>

<div
    style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:15px
    "
>

<div>

<h2 style="margin:0">
    2. Preview Data
</h2>

<div class="import-summary">

<div class="import-summary-item">

<span id="rowCount">
    0
</span>
baris

</div>

<div class="import-summary-item">

<span id="columnCount">
    0
</span>
kolom

</div>

</div>

</div>

<button
    class="import-button success"
    type="button"
    id="importButton"
>
    <i class="bi bi-database-check"></i>
    Import ke Database
</button>

</div>


<div class="preview-wrapper">

<table
    class="preview-table"
    data-no-smart-table
>

<thead id="previewHead"></thead>

<tbody id="previewBody"></tbody>

</table>

</div>

</section>


</main>


<script>

(() => {
    'use strict';

    const csrfToken =
        <?= json_encode(
            pasteCsrf(),
            JSON_UNESCAPED_SLASHES
        ) ?>;

    const columns =
        <?= json_encode(
            $reportColumns,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        ) ?>;

    const aliases = {
        team: [
            'team',
            'tim'
        ],

        invoice_no: [
            'no invoice',
            'no. invoice',
            'invoice no',
            'invoice number'
        ],

        transaction_date: [
            'date of transaction',
            'transaction date',
            'tanggal transaksi',
            'date'
        ],

        bill_to: [
            'bill to/payer',
            'bill to (payer)',
            'bill to',
            'payer',
            'perusahaan',
            'nama pt'
        ],

        beneficiary_name: [
            'beneficiary name',
            'nama beneficiary'
        ],

        beneficiary_account: [
            'account number of beneficiary',
            'beneficiary account',
            'account number'
        ],

        beneficiary_bank: [
            'beneficiary bank'
        ],

        beneficiary_bank_location: [
            'beneficiary bank location',
            'city, country of bank beneficiary',
            'city country of bank beneficiary',
            'lokasi beneficiary bank'
        ],

        amount: [
            'amounts',
            'amount',
            'nominal'
        ],

        beneficiary_country: [
            'beneficiary country',
            'country of beneficiary',
            'country',
            'negara beneficiary'
        ],

        beneficiary_city: [
            'beneficiary city',
            'city of beneficiary',
            'city',
            'kota beneficiary'
        ],

        beneficiary_address: [
            'beneficiary address',
            'complete address of beneficiary account',
            'alamat beneficiary'
        ],

        swift: [
            'swift',
            'swift code'
        ],

        payment_details: [
            'payment details',
            'payment details 1',
            'payment detail'
        ],

        currency: [
            'currency',
            'mata uang'
        ],

        correspondent_bank: [
            'correspondent bank',
            'name correspondence bank'
        ],

        correspondent_swift: [
            'swift bank correspondent',
            'swift bank corr',
            'correspondent swift'
        ],

        correspondent_location: [
            'correspondent bank location',
            'city, country bank corr',
            'city country bank corr',
            'correspondent location'
        ],

        payer_bank: [
            'bank of payer',
            'payer bank'
        ],

        reference_number: [
            'reference number',
            'reference no',
            'nomor referensi'
        ],

        invoice_link: [
            'invoice link',
            'link to invoice',
            'link invoice'
        ],

        status: [
            'status'
        ],

        refund_status: [
            'refund status'
        ],

        notes: [
            'notes',
            'note',
            'remarks',
            'keterangan'
        ]
    };

    const input =
        document.getElementById(
            'excelPaste'
        );

    const previewButton =
        document.getElementById(
            'previewButton'
        );

    const importButton =
        document.getElementById(
            'importButton'
        );

    const clearButton =
        document.getElementById(
            'clearButton'
        );

    const copyHeadersButton =
        document.getElementById(
            'copyHeadersButton'
        );

    const previewSection =
        document.getElementById(
            'previewSection'
        );

    const previewHead =
        document.getElementById(
            'previewHead'
        );

    const previewBody =
        document.getElementById(
            'previewBody'
        );

    const rowCount =
        document.getElementById(
            'rowCount'
        );

    const columnCount =
        document.getElementById(
            'columnCount'
        );

    const messageBox =
        document.getElementById(
            'messageBox'
        );

    let parsedRows = [];

    const normalize = (value) => {
        return String(value ?? '')
            .toLocaleLowerCase('id-ID')
            .replace(/[._-]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    };

    const showMessage = (
        message,
        type
    ) => {
        messageBox.className =
            `import-message ${type}`;

        messageBox.textContent =
            message;
    };

    const clearMessage = () => {
        messageBox.className =
            'import-message';

        messageBox.textContent = '';
    };

    const findCanonicalKey = (
        header
    ) => {
        const normalizedHeader =
            normalize(header);

        for (
            const [
                canonical,
                names
            ]
            of Object.entries(aliases)
        ) {
            if (
                names.some(
                    (alias) =>
                        normalize(alias)
                        === normalizedHeader
                )
            ) {
                return canonical;
            }
        }

        return null;
    };

    const parseClipboard = (
        clipboardText
    ) => {
        const lines = clipboardText
            .replace(/\r/g, '')
            .split('\n')
            .filter(
                (line) =>
                    line.trim() !== ''
            );

        if (lines.length === 0) {
            return [];
        }

        const matrix = lines.map(
            (line) =>
                line.split('\t')
        );

        const firstRow =
            matrix[0];

        const detectedHeaderKeys =
            firstRow.map(
                findCanonicalKey
            );

        const recognizedHeaders =
            detectedHeaderKeys.filter(
                Boolean
            ).length;

        const hasHeader =
            recognizedHeaders >= 3;

        const headerKeys =
            hasHeader
                ? detectedHeaderKeys
                : columns.map(
                    (column) =>
                        column.key
                );

        const dataRows =
            hasHeader
                ? matrix.slice(1)
                : matrix;

        return dataRows.map(
            (row) => {
                const result = {};

                headerKeys.forEach(
                    (
                        key,
                        cellIndex
                    ) => {
                        if (!key) {
                            return;
                        }

                        result[key] =
                            String(
                                row[cellIndex]
                                ?? ''
                            ).trim();
                    }
                );

                return result;
            }
        ).filter(
            (row) =>
                Object.values(row)
                    .some(
                        (value) =>
                            String(value)
                                .trim() !== ''
                    )
        );
    };

    const renderPreview = () => {
        clearMessage();

        parsedRows =
            parseClipboard(
                input.value
            );

        if (parsedRows.length === 0) {
            showMessage(
                'Tidak ada data yang dapat diproses.',
                'error'
            );

            previewSection.hidden =
                true;

            return;
        }

        previewHead.innerHTML = '';
        previewBody.innerHTML = '';

        const headRow =
            document.createElement('tr');

        columns.forEach((column) => {
            const th =
                document.createElement('th');

            th.textContent =
                column.label;

            headRow.appendChild(th);
        });

        previewHead.appendChild(
            headRow
        );

        parsedRows
            .slice(0, 100)
            .forEach((row) => {
                const tr =
                    document.createElement('tr');

                columns.forEach(
                    (column) => {
                        const td =
                            document.createElement('td');

                        td.textContent =
                            row[column.key]
                            ?? '';

                        tr.appendChild(td);
                    }
                );

                previewBody.appendChild(tr);
            });

        rowCount.textContent =
            String(parsedRows.length);

        columnCount.textContent =
            String(columns.length);

        previewSection.hidden =
            false;

        showMessage(
            parsedRows.length > 100
                ? 'Preview menampilkan 100 baris pertama.'
                : 'Preview berhasil dibuat.',
            'success'
        );
    };

    previewButton.addEventListener(
        'click',
        renderPreview
    );

    clearButton.addEventListener(
        'click',
        () => {
            input.value = '';
            parsedRows = [];

            previewSection.hidden =
                true;

            clearMessage();

            input.focus();
        }
    );

    copyHeadersButton.addEventListener(
        'click',
        async () => {
            const headerText =
                columns
                    .map(
                        (column) =>
                            column.label
                    )
                    .join('\t');

            try {
                await navigator
                    .clipboard
                    .writeText(
                        headerText
                    );

                showMessage(
                    'Header berhasil disalin. Paste ke baris pertama Excel.',
                    'success'
                );
            } catch (error) {
                input.value =
                    headerText;

                input.select();

                showMessage(
                    'Header ditempatkan pada kotak. Salin secara manual.',
                    'success'
                );
            }
        }
    );

    importButton.addEventListener(
        'click',
        async () => {
            if (
                parsedRows.length === 0
            ) {
                showMessage(
                    'Buat preview terlebih dahulu.',
                    'error'
                );

                return;
            }

            if (
                !window.confirm(
                    `Import ${parsedRows.length} baris ke database?`
                )
            ) {
                return;
            }

            importButton.disabled =
                true;

            importButton.textContent =
                'Mengimpor...';

            clearMessage();

            try {
                const response =
                    await fetch(
                        <?= json_encode(
                            url(
                                '/report_paste_import.php?action=import'
                            ),
                            JSON_UNESCAPED_SLASHES
                        ) ?>,
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type':
                                    'application/json',
                                'Accept':
                                    'application/json'
                            },
                            body:
                                JSON.stringify({
                                    csrf:
                                        csrfToken,
                                    rows:
                                        parsedRows
                                })
                        }
                    );

                const result =
                    await response.json();

                if (
                    !response.ok
                    || !result.success
                ) {
                    throw new Error(
                        result.message
                        || 'Import gagal.'
                    );
                }

                let message =
                    `${result.inserted} baris berhasil diimpor.`;

                if (result.skipped > 0) {
                    message +=
                        ` ${result.skipped} baris dilewati.`;
                }

                if (
                    Array.isArray(
                        result.errors
                    )
                    && result.errors.length > 0
                ) {
                    message +=
                        ` Terdapat ${result.errors.length} kesalahan baris.`;
                }

                showMessage(
                    message,
                    'success'
                );

                if (result.inserted > 0) {
                    input.value = '';
                    parsedRows = [];
                    previewSection.hidden =
                        true;
                }
            } catch (error) {
                showMessage(
                    error.message
                    || 'Import gagal.',
                    'error'
                );
            } finally {
                importButton.disabled =
                    false;

                importButton.innerHTML =
                    '<i class="bi bi-database-check"></i> Import ke Database';
            }
        }
    );
})();

</script>

</body>

</html>
