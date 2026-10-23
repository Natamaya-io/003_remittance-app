<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\App;
use App\Core\Auth;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function inv_escape(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function inv_app_url(string $path = ''): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/public/invoice_templates.php'));
    $base = rtrim(dirname($script), '/');
    return $path === '' || $path === '/'
        ? $base . '/'
        : $base . '/' . ltrim($path, '/');
}

function inv_csrf(): string
{
    if (empty($_SESSION['invoice_templates_csrf'])) {
        $_SESSION['invoice_templates_csrf'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['invoice_templates_csrf'];
}

function inv_verify_csrf(): void
{
    $token = (string) ($_POST['_token'] ?? '');

    if (
        $token === ''
        || !hash_equals(inv_csrf(), $token)
    ) {
        http_response_code(419);
        exit('Token CSRF tidak valid.');
    }
}

function inv_format_money(
    mixed $number,
    string $currency = ''
): string {
    $value = (float) $number;

    $formatted = number_format(
        $value,
        2,
        '.',
        ','
    );

    return trim($currency . ' ' . $formatted);
}

function inv_allowed_roles(): array
{
    return [
        'super_admin',
        'superadmin',
        'supervisor',
        'admin_invoice',
        'invoice',
    ];
}

function inv_template_label(string $type): string
{
    return match ($type) {
        'invoice_asli' => 'Invoice Asli',
        'invoice_order' => 'Invoice Order',
        'invoice_terima_uang' => 'Invoice Terima Uang',
        default => 'Template Invoice',
    };
}

function inv_find_document(
    \PDO $pdo,
    int $id
): ?array {
    $stmt = $pdo->prepare(
        'SELECT *
         FROM invoice_template_documents
         WHERE id = ?
         LIMIT 1'
    );

    $stmt->execute([$id]);

    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $row ?: null;
}

if (!Auth::check()) {
    header(
        'Location: ' . url('/login')
    );
    exit;
}

$user = Auth::user() ?? [];

$roleCode = (string) (
    $user['role_code'] ?? ''
);

if (!in_array($roleCode, inv_allowed_roles(), true)) {
    http_response_code(403);
    exit('Anda tidak memiliki akses ke modul Invoice Template.');
}

$canWrite = in_array($roleCode, ['super_admin', 'superadmin', 'admin_invoice', 'invoice'], true);

$pdo = App::db();

$message = (string) ($_GET['message'] ?? '');
$error = '';

/*
|--------------------------------------------------------------------------
| Aksi simpan / hapus
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    inv_verify_csrf();

    if (!$canWrite) {
        http_response_code(403);
        exit('Anda tidak memiliki akses untuk mengubah template invoice.');
    }

    $action = (string) ($_POST['_action'] ?? '');

    try {
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);

            $data = [
                'template_type' => trim((string) ($_POST['template_type'] ?? 'invoice_asli')),
                'document_no' => trim((string) ($_POST['document_no'] ?? '')),
                'invoice_no' => trim((string) ($_POST['invoice_no'] ?? '')),
                'value_date' => trim((string) ($_POST['value_date'] ?? '')) ?: null,
                'invoice_date' => trim((string) ($_POST['invoice_date'] ?? '')) ?: null,
                'due_date' => trim((string) ($_POST['due_date'] ?? '')) ?: null,
                'currency' => trim((string) ($_POST['currency'] ?? '')),
                'amount' => (float) ($_POST['amount'] ?? 0),
                'qty' => (float) ($_POST['qty'] ?? 1),
                'rate' => (float) ($_POST['rate'] ?? 0),

                'company_name' => trim((string) ($_POST['company_name'] ?? '')),
                'company_address' => trim((string) ($_POST['company_address'] ?? '')),
                'company_phone' => trim((string) ($_POST['company_phone'] ?? '')),
                'company_email' => trim((string) ($_POST['company_email'] ?? '')),

                'bill_to_name' => trim((string) ($_POST['bill_to_name'] ?? '')),
                'bill_to_address' => trim((string) ($_POST['bill_to_address'] ?? '')),

                'beneficiary_name' => trim((string) ($_POST['beneficiary_name'] ?? '')),
                'beneficiary_address' => trim((string) ($_POST['beneficiary_address'] ?? '')),
                'beneficiary_country' => trim((string) ($_POST['beneficiary_country'] ?? '')),
                'beneficiary_account' => trim((string) ($_POST['beneficiary_account'] ?? '')),
                'beneficiary_bank' => trim((string) ($_POST['beneficiary_bank'] ?? '')),
                'beneficiary_bank_address' => trim((string) ($_POST['beneficiary_bank_address'] ?? '')),
                'beneficiary_swift' => trim((string) ($_POST['beneficiary_swift'] ?? '')),

                'bank_name' => trim((string) ($_POST['bank_name'] ?? '')),
                'bank_swift' => trim((string) ($_POST['bank_swift'] ?? '')),
                'bank_account' => trim((string) ($_POST['bank_account'] ?? '')),
                'bank_address' => trim((string) ($_POST['bank_address'] ?? '')),

                'correspondent_bank' => trim((string) ($_POST['correspondent_bank'] ?? '')),
                'correspondent_bank_address' => trim((string) ($_POST['correspondent_bank_address'] ?? '')),
                'correspondent_bank_swift' => trim((string) ($_POST['correspondent_bank_swift'] ?? '')),
                'correspondent_bank_account' => trim((string) ($_POST['correspondent_bank_account'] ?? '')),

                'payment_details' => trim((string) ($_POST['payment_details'] ?? '')),
                'item_description' => trim((string) ($_POST['item_description'] ?? '')),
                'terms' => trim((string) ($_POST['terms'] ?? '')),
                'notes' => trim((string) ($_POST['notes'] ?? '')),
            ];

            if ($data['template_type'] === '') {
                throw new \RuntimeException('Jenis template wajib dipilih.');
            }

            if ($data['invoice_no'] === '' && $data['document_no'] === '') {
                throw new \RuntimeException('Nomor dokumen atau invoice wajib diisi.');
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi kop Invoice Asli
            |--------------------------------------------------------------------------
            |
            | Nama beneficiary menjadi sumber utama kop surat pada preview/print.
            | Dengan validasi ini, dokumen baru tidak lagi memakai nama hardcode.
            |
            */
            if (
                $data['template_type'] === 'invoice_asli'
                && $data['beneficiary_name'] === ''
            ) {
                throw new \RuntimeException(
                    'Beneficiary Name wajib diisi karena digunakan sebagai kop Invoice Asli.'
                );
            }

            if ($id > 0) {
                $sql = 'UPDATE invoice_template_documents SET
                    template_type = :template_type,
                    document_no = :document_no,
                    invoice_no = :invoice_no,
                    value_date = :value_date,
                    invoice_date = :invoice_date,
                    due_date = :due_date,
                    currency = :currency,
                    amount = :amount,
                    qty = :qty,
                    rate = :rate,
                    company_name = :company_name,
                    company_address = :company_address,
                    company_phone = :company_phone,
                    company_email = :company_email,
                    bill_to_name = :bill_to_name,
                    bill_to_address = :bill_to_address,
                    beneficiary_name = :beneficiary_name,
                    beneficiary_address = :beneficiary_address,
                    beneficiary_country = :beneficiary_country,
                    beneficiary_account = :beneficiary_account,
                    beneficiary_bank = :beneficiary_bank,
                    beneficiary_bank_address = :beneficiary_bank_address,
                    beneficiary_swift = :beneficiary_swift,
                    bank_name = :bank_name,
                    bank_swift = :bank_swift,
                    bank_account = :bank_account,
                    bank_address = :bank_address,
                    correspondent_bank = :correspondent_bank,
                    correspondent_bank_address = :correspondent_bank_address,
                    correspondent_bank_swift = :correspondent_bank_swift,
                    correspondent_bank_account = :correspondent_bank_account,
                    payment_details = :payment_details,
                    item_description = :item_description,
                    terms = :terms,
                    notes = :notes,
                    updated_by = :updated_by,
                    updated_at = NOW()
                    WHERE id = :id';

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':template_type' => $data['template_type'],
                    ':document_no' => $data['document_no'] ?: null,
                    ':invoice_no' => $data['invoice_no'] ?: null,
                    ':value_date' => $data['value_date'],
                    ':invoice_date' => $data['invoice_date'],
                    ':due_date' => $data['due_date'],
                    ':currency' => $data['currency'] ?: null,
                    ':amount' => $data['amount'],
                    ':qty' => $data['qty'],
                    ':rate' => $data['rate'],
                    ':company_name' => $data['company_name'] ?: null,
                    ':company_address' => $data['company_address'] ?: null,
                    ':company_phone' => $data['company_phone'] ?: null,
                    ':company_email' => $data['company_email'] ?: null,
                    ':bill_to_name' => $data['bill_to_name'] ?: null,
                    ':bill_to_address' => $data['bill_to_address'] ?: null,
                    ':beneficiary_name' => $data['beneficiary_name'] ?: null,
                    ':beneficiary_address' => $data['beneficiary_address'] ?: null,
                    ':beneficiary_country' => $data['beneficiary_country'] ?: null,
                    ':beneficiary_account' => $data['beneficiary_account'] ?: null,
                    ':beneficiary_bank' => $data['beneficiary_bank'] ?: null,
                    ':beneficiary_bank_address' => $data['beneficiary_bank_address'] ?: null,
                    ':beneficiary_swift' => $data['beneficiary_swift'] ?: null,
                    ':bank_name' => $data['bank_name'] ?: null,
                    ':bank_swift' => $data['bank_swift'] ?: null,
                    ':bank_account' => $data['bank_account'] ?: null,
                    ':bank_address' => $data['bank_address'] ?: null,
                    ':correspondent_bank' => $data['correspondent_bank'] ?: null,
                    ':correspondent_bank_address' => $data['correspondent_bank_address'] ?: null,
                    ':correspondent_bank_swift' => $data['correspondent_bank_swift'] ?: null,
                    ':correspondent_bank_account' => $data['correspondent_bank_account'] ?: null,
                    ':payment_details' => $data['payment_details'] ?: null,
                    ':item_description' => $data['item_description'] ?: null,
                    ':terms' => $data['terms'] ?: null,
                    ':notes' => $data['notes'] ?: null,
                    ':updated_by' => (int) ($user['id'] ?? 0),
                    ':id' => $id,
                ]);

                header(
                    'Location: ' . url('/invoice_templates.php')
                    . '?message=' . rawurlencode('Dokumen invoice berhasil diperbarui.')
                );
                exit;
            }

            $sql = 'INSERT INTO invoice_template_documents (
                template_type,
                document_no,
                invoice_no,
                value_date,
                invoice_date,
                due_date,
                currency,
                amount,
                qty,
                rate,
                company_name,
                company_address,
                company_phone,
                company_email,
                bill_to_name,
                bill_to_address,
                beneficiary_name,
                beneficiary_address,
                beneficiary_country,
                beneficiary_account,
                beneficiary_bank,
                beneficiary_bank_address,
                beneficiary_swift,
                bank_name,
                bank_swift,
                bank_account,
                bank_address,
                correspondent_bank,
                correspondent_bank_address,
                correspondent_bank_swift,
                correspondent_bank_account,
                payment_details,
                item_description,
                terms,
                notes,
                created_by,
                updated_by,
                created_at,
                updated_at
            ) VALUES (
                :template_type,
                :document_no,
                :invoice_no,
                :value_date,
                :invoice_date,
                :due_date,
                :currency,
                :amount,
                :qty,
                :rate,
                :company_name,
                :company_address,
                :company_phone,
                :company_email,
                :bill_to_name,
                :bill_to_address,
                :beneficiary_name,
                :beneficiary_address,
                :beneficiary_country,
                :beneficiary_account,
                :beneficiary_bank,
                :beneficiary_bank_address,
                :beneficiary_swift,
                :bank_name,
                :bank_swift,
                :bank_account,
                :bank_address,
                :correspondent_bank,
                :correspondent_bank_address,
                :correspondent_bank_swift,
                :correspondent_bank_account,
                :payment_details,
                :item_description,
                :terms,
                :notes,
                :created_by,
                :updated_by,
                NOW(),
                NOW()
            )';

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':template_type' => $data['template_type'],
                ':document_no' => $data['document_no'] ?: null,
                ':invoice_no' => $data['invoice_no'] ?: null,
                ':value_date' => $data['value_date'],
                ':invoice_date' => $data['invoice_date'],
                ':due_date' => $data['due_date'],
                ':currency' => $data['currency'] ?: null,
                ':amount' => $data['amount'],
                ':qty' => $data['qty'],
                ':rate' => $data['rate'],
                ':company_name' => $data['company_name'] ?: null,
                ':company_address' => $data['company_address'] ?: null,
                ':company_phone' => $data['company_phone'] ?: null,
                ':company_email' => $data['company_email'] ?: null,
                ':bill_to_name' => $data['bill_to_name'] ?: null,
                ':bill_to_address' => $data['bill_to_address'] ?: null,
                ':beneficiary_name' => $data['beneficiary_name'] ?: null,
                ':beneficiary_address' => $data['beneficiary_address'] ?: null,
                ':beneficiary_country' => $data['beneficiary_country'] ?: null,
                ':beneficiary_account' => $data['beneficiary_account'] ?: null,
                ':beneficiary_bank' => $data['beneficiary_bank'] ?: null,
                ':beneficiary_bank_address' => $data['beneficiary_bank_address'] ?: null,
                ':beneficiary_swift' => $data['beneficiary_swift'] ?: null,
                ':bank_name' => $data['bank_name'] ?: null,
                ':bank_swift' => $data['bank_swift'] ?: null,
                ':bank_account' => $data['bank_account'] ?: null,
                ':bank_address' => $data['bank_address'] ?: null,
                ':correspondent_bank' => $data['correspondent_bank'] ?: null,
                ':correspondent_bank_address' => $data['correspondent_bank_address'] ?: null,
                ':correspondent_bank_swift' => $data['correspondent_bank_swift'] ?: null,
                ':correspondent_bank_account' => $data['correspondent_bank_account'] ?: null,
                ':payment_details' => $data['payment_details'] ?: null,
                ':item_description' => $data['item_description'] ?: null,
                ':terms' => $data['terms'] ?: null,
                ':notes' => $data['notes'] ?: null,
                ':created_by' => (int) ($user['id'] ?? 0),
                ':updated_by' => (int) ($user['id'] ?? 0),
            ]);

            header(
                'Location: ' . url('/invoice_templates.php')
                . '?message=' . rawurlencode('Dokumen invoice berhasil ditambahkan.')
            );
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);

            $stmt = $pdo->prepare(
                'DELETE FROM invoice_template_documents
                 WHERE id = ?'
            );

            $stmt->execute([$id]);

            header(
                'Location: ' . url('/invoice_templates.php')
                . '?message=' . rawurlencode('Dokumen invoice berhasil dihapus.')
            );
            exit;
        }
    } catch (\Throwable $exception) {
        $error = $exception->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/
$editId = (int) ($_GET['edit'] ?? 0);
$editData = null;

$requestedType = trim((string) ($_GET['type'] ?? ''));

if (!in_array(
    $requestedType,
    [
        'invoice_asli',
        'invoice_order',
        'invoice_terima_uang',
    ],
    true
)) {
    $requestedType = '';
}

if ($editId > 0 && $canWrite) {
    $editData = inv_find_document($pdo, $editId);
}

/*
|--------------------------------------------------------------------------
| Preview / Print
|--------------------------------------------------------------------------
*/
$previewId = (int) ($_GET['preview'] ?? 0);

if ($previewId > 0) {
    $doc = inv_find_document($pdo, $previewId);

    if (!$doc) {
        exit('Dokumen tidak ditemukan.');
    }

    $templateType = (string) ($doc['template_type'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | Kop surat dinamis
    |--------------------------------------------------------------------------
    |
    | Prioritas utama menggunakan data beneficiary pada dokumen.
    | Data company hanya digunakan sebagai fallback untuk dokumen lama yang
    | belum memiliki beneficiary.
    |
    */
    $letterheadName = trim(
        (string) ($doc['beneficiary_name'] ?? '')
    );

    $letterheadAddress = trim(
        (string) ($doc['beneficiary_address'] ?? '')
    );

    $letterheadCountry = trim(
        (string) ($doc['beneficiary_country'] ?? '')
    );

    if ($letterheadName === '') {
        $letterheadName = trim(
            (string) ($doc['company_name'] ?? '')
        );
    }

    if ($letterheadAddress === '') {
        $letterheadAddress = trim(
            (string) ($doc['company_address'] ?? '')
        );
    }

    $letterheadAddressLines = array_values(
        array_filter(
            [
                $letterheadAddress,
                $letterheadCountry,
            ],
            static fn (string $value): bool =>
                trim($value) !== ''
        )
    );

    ?>
    <!doctype html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <title><?= inv_escape(inv_template_label($templateType)) ?></title>
        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                background: #fcf8fd;
                font-family:"Segoe UI Variable","Segoe UI",Inter,Arial,sans-serif;
                color: #111;
            }
            .toolbar {
                position: sticky;
                top: 0;
                z-index: 99;
                background: linear-gradient(135deg,#f5eaff,#ffeaf4);
                color: #51385c;
                padding: 14px 20px;
                display: flex;
                gap: 10px;
                align-items: center;
                justify-content: space-between;
            }
            .toolbar a,
            .toolbar button {
                background: linear-gradient(135deg,#7444dc,#eb4f7b);
                color: #fff;
                border: none;
                border-radius: 8px;
                padding: 10px 14px;
                text-decoration: none;
                cursor: pointer;
                font-weight: 700;
            }
            .sheet-wrap {
                padding: 20px;
            }
            .sheet {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
                padding: 14mm 14mm 16mm 14mm;
                box-shadow: 0 0 10px rgba(0,0,0,.10);
            }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .mb-10 { margin-bottom: 10px; }
            .mb-16 { margin-bottom: 16px; }
            .mb-20 { margin-bottom: 20px; }
            .mb-24 { margin-bottom: 24px; }
            .mb-30 { margin-bottom: 30px; }
            .mt-20 { margin-top: 20px; }
            .small { font-size: 12px; }
            .normal { font-size: 14px; }
            .title { font-size: 18px; font-weight: bold; }
            .bold { font-weight: bold; }

            /*
            |--------------------------------------------------------------------------
            | Kop surat dinamis
            |--------------------------------------------------------------------------
            */
            .invoice-letterhead {
                margin-bottom: 24px;
                padding-bottom: 12px;
                border-bottom: 1px solid #eaddeb;
                line-height: 1.4;
            }
            .invoice-letterhead__name {
                margin: 0 0 3px;
                font-size: 15px;
                font-weight: 700;
                color: #51385c;
            }
            .invoice-letterhead__address {
                margin: 0;
                font-size: 13px;
                color: #74647c;
                white-space: normal;
            }
            .invoice-letterhead__empty {
                padding: 9px 11px;
                border: 1px dashed #b98bdc;
                border-radius: 6px;
                background: #fffafd;
                color: #654574;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }
            .bordered td,
            .bordered th {
                border: 1px solid #222;
                padding: 6px;
                vertical-align: top;
                font-size: 13px;
            }
            .line-table td {
                padding: 4px 6px;
                vertical-align: top;
                font-size: 13px;
            }
            .item-table th,
            .item-table td {
                padding: 10px 11px;
                font-size: 12.5px;
                border-bottom: 1px solid #eee4f2;
                vertical-align: top;
            }
            .item-table th {
                text-align: left;
            }
            .grey-head {
                background: linear-gradient(135deg,#7444dc,#eb4f7b);
                color: #fff;
            }
            .light-row {
                background: #fefbff;
            }
            @media print {
                body {
                    background: #fff;
                }
                .toolbar {
                    display: none;
                }
                .sheet-wrap {
                    padding: 0;
                }
                .sheet {
                    width: 210mm;
                    min-height: auto;
                    margin: 0;
                    box-shadow: none;
                    padding: 10mm 12mm 12mm 12mm;
                }
                @page {
                    size: A4;
                    margin: 0;
                }
            }
        </style>
    <link rel="stylesheet" href="<?= e(asset('css/theme-clean-global.css')) ?>">
</head>
    <body>

    <div class="toolbar">
        <div>
            <?= inv_escape(inv_template_label($templateType)) ?>
            - Preview
        </div>
        <div>
            <a href="<?= inv_escape(url('/invoice_templates.php')) ?>">Kembali</a>
            <button onclick="window.print()">Print / Save PDF</button>
        </div>
    </div>

    <div class="sheet-wrap">
        <div class="sheet">

        <?php if ($templateType === 'invoice_asli'): ?>

            <div class="invoice-letterhead">
                <?php if ($letterheadName !== ''): ?>
                    <div class="invoice-letterhead__name">
                        <?= nl2br(inv_escape($letterheadName)) ?>
                    </div>
                <?php endif; ?>

                <?php if ($letterheadAddressLines !== []): ?>
                    <div class="invoice-letterhead__address">
                        <?= nl2br(
                            inv_escape(
                                implode(
                                    "\n",
                                    $letterheadAddressLines
                                )
                            )
                        ) ?>
                    </div>
                <?php endif; ?>

                <?php if (
                    $letterheadName === ''
                    && $letterheadAddressLines === []
                ): ?>
                    <div class="invoice-letterhead__empty">
                        Data beneficiary untuk kop invoice belum diisi.
                        Edit dokumen, lalu isi Beneficiary Name dan Beneficiary Address.
                    </div>
                <?php endif; ?>
            </div>

            <table class="bordered mb-20">
                <tr>
                    <td style="width:28%; font-weight:bold;">BILL TO</td>
                    <td>
                        <div class="bold"><?= inv_escape($doc['bill_to_name'] ?? '') ?></div>
                        <div><?= nl2br(inv_escape($doc['bill_to_address'] ?? '')) ?></div>
                    </td>
                </tr>
            </table>

            <div class="text-right bold mb-30">
                Date: <?= inv_escape(
                    $doc['document_no']
                    ?: ($doc['invoice_date'] ?? '')
                ) ?>
            </div>

            <div class="mb-20 bold">Payment Details :</div>

            <table class="line-table mb-30">
                <tr>
                    <td style="width:24%;">Beneficiary Name</td>
                    <td style="width:2%;">:</td>
                    <td><?= inv_escape($doc['beneficiary_name'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Beneficiary Addres</td>
                    <td>:</td>
                    <td><?= inv_escape($doc['beneficiary_address'] ?? '') ?></td>
                </tr>
            </table>

            <table class="item-table mb-20">
                <thead>
                    <tr>
                        <th>Descriptions</th>
                        <th style="width:18%;">Unit Price</th>
                        <th style="width:22%;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="light-row">
                        <td><?= inv_escape($doc['item_description'] ?? '') ?></td>
                        <td><?= inv_escape(($doc['currency'] ?? '') . ' ' . ($doc['rate'] ?? '0')) ?></td>
                        <td><?= inv_escape(($doc['currency'] ?? '') . ' ' . ($doc['amount'] ?? '0')) ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="text-right bold">Total</td>
                        <td><?= inv_escape(($doc['currency'] ?? '') . ' ' . ($doc['amount'] ?? '0')) ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="text-right mb-20">
                <div class="bold">Total Price :</div>
                <div><?= inv_escape(($doc['currency'] ?? '') . ' ' . ($doc['amount'] ?? '0')) ?></div>
            </div>

            <table class="bordered">
                <tr>
                    <td style="width:33%;">
                        <div class="bold mb-10">Beneficiary Details</div>
                        <div>Beneficiary Name: <?= inv_escape($doc['beneficiary_name'] ?? '') ?></div>
                        <br>
                        <div>Beneficiary address: <?= nl2br(inv_escape($doc['beneficiary_address'] ?? '')) ?></div>
                    </td>
                    <td style="width:34%;">
                        <div class="bold mb-10">Bank Details</div>
                        <div>Bank Name : <?= inv_escape($doc['bank_name'] ?? '') ?></div>
                        <div>Bank SWIFT : <?= inv_escape($doc['bank_swift'] ?? '') ?></div>
                        <br>
                        <div>Account Number : <?= inv_escape($doc['bank_account'] ?? '') ?></div>
                        <div>Currency: <?= inv_escape($doc['currency'] ?? '') ?></div>
                        <div>Bank Address : <?= nl2br(inv_escape($doc['bank_address'] ?? '')) ?></div>
                    </td>
                    <td style="width:33%;">
                        <div class="bold mb-10">Payment Details</div>
                        <div><?= nl2br(inv_escape($doc['payment_details'] ?? '')) ?></div>
                    </td>
                </tr>
            </table>

        <?php elseif ($templateType === 'invoice_order'): ?>

            <div class="text-center title mb-20">PAYMENT ORDER</div>

            <table class="bordered">
                <tr>
                    <td colspan="2" class="text-center">Filled by the customer</td>
                </tr>
                <tr>
                    <td style="width:27%;" class="text-center">VALUE DATE</td>
                    <td><?= inv_escape($doc['value_date'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">AMOUNT *</td>
                    <td><?= inv_escape(number_format((float) ($doc['amount'] ?? 0), 2, '.', ',')) ?></td>
                </tr>
                <tr>
                    <td class="text-center">currency *</td>
                    <td><?= inv_escape($doc['currency'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">BILL TO</td>
                    <td>
                        <div class="bold"><?= inv_escape($doc['bill_to_name'] ?? '') ?></div>
                        <div><?= nl2br(inv_escape($doc['bill_to_address'] ?? '')) ?></div>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">Beneficiary *</td>
                    <td><?= inv_escape($doc['beneficiary_name'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">Beneficiary COUNTRY*</td>
                    <td><?= inv_escape($doc['beneficiary_country'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">Beneficiary address *</td>
                    <td><?= nl2br(inv_escape($doc['beneficiary_address'] ?? '')) ?></td>
                </tr>
                <tr>
                    <td class="text-center">ACCOUNT *<br>or IBAN</td>
                    <td><?= inv_escape($doc['beneficiary_account'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">BENEF.BANK *</td>
                    <td><?= inv_escape($doc['beneficiary_bank'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">ADDRESS</td>
                    <td><?= nl2br(inv_escape($doc['beneficiary_bank_address'] ?? '')) ?></td>
                </tr>
                <tr>
                    <td class="text-center">SWIFT *</td>
                    <td><?= inv_escape($doc['beneficiary_swift'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">BLZ, ABA, SORT CODE</td>
                    <td><?= inv_escape($doc['notes'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">CORR.BANK</td>
                    <td><?= inv_escape($doc['correspondent_bank'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">ADDRESS</td>
                    <td><?= nl2br(inv_escape($doc['correspondent_bank_address'] ?? '')) ?></td>
                </tr>
                <tr>
                    <td class="text-center">SWIFT</td>
                    <td><?= inv_escape($doc['correspondent_bank_swift'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">ACCOUNT</td>
                    <td><?= inv_escape($doc['correspondent_bank_account'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="text-center">PAYMENT DETAILS *</td>
                    <td style="color:red; font-weight:bold;"><?= nl2br(inv_escape($doc['payment_details'] ?? '')) ?></td>
                </tr>
                <tr>
                    <td class="text-center">Commision</td>
                    <td>OUR</td>
                </tr>
            </table>

        <?php elseif ($templateType === 'invoice_terima_uang'): ?>

            <table style="width:100%;" class="mb-20">
                <tr>
                    <td style="width:52%; vertical-align:top;">
                        <div class="bold" style="font-size:18px;">PT Kairos Sinergi Indonesia</div>
                        <div><?= nl2br(inv_escape($doc['company_address'] ?? '')) ?></div>
                        <div><?= inv_escape($doc['company_email'] ?? '') ?></div>
                    </td>
                    <td style="width:48%; vertical-align:top;" class="text-right">
                        <div class="bold" style="font-size:20px;">INVOICE</div>
                        <div class="bold">Invoice # <?= inv_escape($doc['invoice_no'] ?? '') ?></div>
                        <br>
                        <div class="bold">Balance Due</div>
                        <div class="bold"><?= inv_escape(($doc['currency'] ?? '') . ' ' . ($doc['amount'] ?? '0')) ?></div>
                    </td>
                </tr>
            </table>

            <table style="width:100%;" class="mb-20">
                <tr>
                    <td style="width:60%; vertical-align:top;">
                        <div class="bold">Bill To</div>
                        <div>: <?= inv_escape($doc['bill_to_name'] ?? '') ?></div>
                        <div>: <?= nl2br(inv_escape($doc['bill_to_address'] ?? '')) ?></div>
                    </td>
                    <td style="width:40%; vertical-align:top;" class="text-right">
                        <div><span class="bold">Invoice Date :</span> <?= inv_escape($doc['invoice_date'] ?? '') ?></div>
                        <div><span class="bold">Terms :</span> <?= inv_escape($doc['terms'] ?? '') ?></div>
                        <div><span class="bold">Due Date :</span> <?= inv_escape($doc['due_date'] ?? '') ?></div>
                    </td>
                </tr>
            </table>

            <table class="item-table mb-20">
                <thead class="grey-head">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Item & Description</th>
                        <th style="width:10%;">Qty</th>
                        <th style="width:15%;">Rate</th>
                        <th style="width:15%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><?= nl2br(inv_escape($doc['item_description'] ?? '')) ?></td>
                        <td><?= inv_escape((string) ($doc['qty'] ?? '1')) ?></td>
                        <td><?= inv_escape((string) ($doc['rate'] ?? '0')) ?></td>
                        <td><?= inv_escape((string) ($doc['amount'] ?? '0')) ?></td>
                    </tr>
                </tbody>
            </table>

            <table style="width:100%;" class="mb-24">
                <tr>
                    <td style="width:68%;"></td>
                    <td style="width:32%;">
                        <table style="width:100%;">
                            <tr>
                                <td class="bold">Sub Total</td>
                                <td class="text-right"><?= inv_escape((string) ($doc['amount'] ?? '0')) ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Total</td>
                                <td class="text-right"><?= inv_escape((string) ($doc['amount'] ?? '0')) ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Balance Due</td>
                                <td class="text-right bold"><?= inv_escape(($doc['currency'] ?? '') . ' ' . ($doc['amount'] ?? '0')) ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="bordered">
                <tr>
                    <td style="width:33%;">
                        <div class="bold mb-10">Beneficiary Details :</div>
                        <div>Beneficiary Name : <?= inv_escape($doc['beneficiary_name'] ?? '') ?></div>
                        <br>
                        <div>Beneficiary address: <?= nl2br(inv_escape($doc['beneficiary_address'] ?? '')) ?></div>
                    </td>
                    <td style="width:34%;">
                        <div class="bold mb-10">Bank Details :</div>
                        <div>Bank Name : <?= inv_escape($doc['bank_name'] ?? '') ?></div>
                        <div>Bank SWIFT : <?= inv_escape($doc['bank_swift'] ?? '') ?></div>
                        <div>Account Number : <?= inv_escape($doc['bank_account'] ?? '') ?></div>
                        <div>Currency: <?= inv_escape($doc['currency'] ?? '') ?></div>
                        <div>Bank Address : <?= nl2br(inv_escape($doc['bank_address'] ?? '')) ?></div>
                    </td>
                    <td style="width:33%;">
                        <div class="bold mb-10">Payment Details :</div>
                        <div><?= nl2br(inv_escape($doc['payment_details'] ?? '')) ?></div>
                    </td>
                </tr>
            </table>

        <?php endif; ?>

        </div>
    </div>

    </body>
    </html>
    <?php
    exit;
}

/*
|--------------------------------------------------------------------------
| List data
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query(
    'SELECT *
     FROM invoice_template_documents
     ORDER BY id DESC'
);

$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Template Invoice</title>
    <link rel="stylesheet" href="<?= inv_escape(asset('css/modern-banking.css')) ?>">
<link rel="stylesheet" href="<?= inv_escape(asset('css/remitops-v5.css')) ?>">
<link rel="stylesheet" href="<?= inv_escape(asset('css/app.css')) ?>">
    <style>
        :root {
            --inv-purple: #8b5cf6;
            --inv-purple-dark: #6842a5;
            --inv-pink: #ec4899;
            --inv-soft-purple: #f4eaff;
            --inv-soft-pink: #fff0f7;
            --inv-bg: #fcf8fd;
            --inv-card: #ffffff;
            --inv-border: #eadcf0;
            --inv-text: #4f365d;
            --inv-muted: #867589;
            --inv-danger: #c53b5c;
        }

        * {
            box-sizing: border-box;
        }

        body.invoice-template-page {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at 95% 4%,
                    rgba(139, 92, 246, 0.08),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 4% 92%,
                    rgba(236, 72, 153, 0.07),
                    transparent 28%
                ),
                linear-gradient(
                    180deg,
                    #fffafd 0%,
                    #fbf7fd 100%
                );
            color: var(--inv-text);
            font-family:
                "Segoe UI Variable",
                "Segoe UI",
                Inter,
                Arial,
                sans-serif;
            font-size: 14px;
            line-height: 1.45;
        }

        .invoice-template-page .header {
            padding: 18px 26px;
            background:
                linear-gradient(
                    135deg,
                    #fff7fc 0%,
                    #f5eaff 52%,
                    #ffeaf4 100%
                );
            color: var(--inv-text);
            border-bottom: 1px solid var(--inv-border);
            box-shadow: 0 8px 22px rgba(90, 55, 105, 0.06);
        }

        .invoice-template-page .header-inner {
            max-width: 1280px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .invoice-template-page .header h1 {
            margin: 0 0 3px;
            color: var(--inv-text);
            font-size: 22px;
            line-height: 1.2;
            font-weight: 800;
        }

        .invoice-template-page .header-subtitle {
            color: var(--inv-muted);
            font-size: 13px;
        }

        .invoice-template-page .header a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 40px;
            padding: 9px 13px;
            border: 1px solid #decce8;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.82);
            color: #654574;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .invoice-template-page .container {
            max-width: 1280px;
            margin: 20px auto;
            padding: 0 16px 28px;
        }

        .invoice-template-page .card {
            padding: 18px;
            margin-bottom: 16px;
            background: var(--inv-card);
            border: 1px solid var(--inv-border);
            border-radius: 18px;
            box-shadow: 0 9px 24px rgba(94, 59, 108, 0.06);
        }

        .invoice-template-page .section-title {
            margin: 0 0 14px;
            color: var(--inv-text);
            font-size: 18px;
            line-height: 1.25;
            font-weight: 800;
        }

        .invoice-template-page .alert {
            padding: 10px 12px;
            margin-bottom: 14px;
            border-radius: 11px;
            font-size: 13px;
        }

        .invoice-template-page .alert-success {
            background: #eaf9f1;
            color: #19734a;
        }

        .invoice-template-page .alert-error {
            background: #fff0f3;
            color: #b83455;
        }

        .invoice-template-page .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px 14px;
        }

        .invoice-template-page .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 14px;
        }

        .invoice-template-page .full {
            grid-column: 1 / -1;
        }

        .invoice-template-page label {
            display: block;
            margin-bottom: 5px;
            color: var(--inv-text);
            font-size: 13px;
            font-weight: 700;
        }

        .invoice-template-page .field-help {
            display: block;
            margin-top: 5px;
            color: var(--inv-muted);
            font-size: 11px;
            line-height: 1.4;
        }

        .invoice-template-page input,
        .invoice-template-page select,
        .invoice-template-page textarea {
            width: 100%;
            min-height: 40px;
            padding: 8px 11px;
            border: 1px solid #ddcfe5;
            border-radius: 10px;
            background: #ffffff;
            color: var(--inv-text);
            font-family: inherit;
            font-size: 13px;
            line-height: 1.35;
            outline: none;
        }

        .invoice-template-page input:focus,
        .invoice-template-page select:focus,
        .invoice-template-page textarea:focus {
            border-color: #b98bdc;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.08);
        }

        .invoice-template-page textarea {
            min-height: 70px;
            resize: vertical;
        }

        .invoice-template-page hr {
            margin: 16px 0 !important;
            border: 0 !important;
            border-top: 1px solid #eee3f2 !important;
        }

        .invoice-template-page .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 38px;
            padding: 8px 12px;
            border: 0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .invoice-template-page .btn-primary {
            background:
                linear-gradient(
                    135deg,
                    var(--inv-purple),
                    var(--inv-pink)
                );
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(139, 92, 246, 0.14);
        }

        .invoice-template-page .btn-secondary {
            background: var(--inv-soft-purple);
            color: var(--inv-purple-dark);
        }

        .invoice-template-page .btn-danger {
            background: #fff0f3;
            color: var(--inv-danger);
        }

        .invoice-template-page .table-wrap {
            overflow: auto;
        }

        .invoice-template-page table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border: 1px solid var(--inv-border);
            border-radius: 14px;
        }

        .invoice-template-page th,
        .invoice-template-page td {
            padding: 10px 11px;
            border-bottom: 1px solid #eee4f2;
            text-align: left;
            vertical-align: middle;
            font-size: 12.5px;
        }

        .invoice-template-page th {
            background:
                linear-gradient(
                    180deg,
                    #f7effd 0%,
                    #f2e5fb 100%
                );
            color: #64477a;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .invoice-template-page tbody tr:nth-child(even) {
            background: #fefbff;
        }

        .invoice-template-page tbody tr:hover {
            background: #fbf5ff;
        }

        .invoice-template-page .actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
        }

        .invoice-template-page .actions form {
            margin: 0;
        }

        .invoice-template-page small {
            color: var(--inv-muted);
        }

        @media (max-width: 1000px) {
            .invoice-template-page .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .invoice-template-page .header-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .invoice-template-page .grid,
            .invoice-template-page .grid-2 {
                grid-template-columns: 1fr;
            }

            .invoice-template-page .header a {
                width: 100%;
            }
        }
    
/* ===============================
   FINAL INVOICE THEME OVERRIDE
   remove navy / blue inherited css
   =============================== */

body.invoice-template-page,
body.invoice-template-page .header,
body.invoice-template-page header.header {
    background: linear-gradient(135deg,#fff7fc,#f5eaff,#ffeaf4) !important;
    color:#51385c !important;
}

body.invoice-template-page .header h1 {
    color:#6b3f89 !important;
}

body.invoice-template-page .header-subtitle {
    color:#92749d !important;
}

body.invoice-template-page .header a {
    background:#ffffff !important;
    color:#68428a !important;
    border:1px solid #dfc8ed !important;
}

body.invoice-template-page .btn-primary,
body.invoice-template-page button.btn-primary {
    background:linear-gradient(135deg,#8b5cf6,#ec4899) !important;
    color:#ffffff !important;
}

body.invoice-template-page .btn-secondary {
    background:#f4eaff !important;
    color:#6842a5 !important;
}

body.invoice-template-page th {
    background:linear-gradient(135deg,#f1ddff,#ffe5f1) !important;
    color:#624276 !important;
}

body.invoice-template-page td {
    color:#543667 !important;
}

body.invoice-template-page .card {
    background:#ffffff !important;
    border-color:#eadcf0 !important;
}

</style>
</head>
<body class="invoice-template-page">

<header class="header">
    <div class="header-inner">
        <div>
            <h1>Template Invoice</h1>
            <div class="header-subtitle">Invoice Asli, Invoice Order, dan Invoice Terima Uang</div>
        </div>
        <a href="<?= inv_escape(inv_app_url('/invoices')) ?>">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Invoice
        </a>
    </div>
</header>

<main class="container">

    <?php if ($message !== ''): ?>
        <div class="alert alert-success">
            <?= inv_escape($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error">
            <?= inv_escape($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($canWrite): ?>

<section class="card">
        <h2 class="section-title">
            <?= $editData ? 'Edit Template Invoice' : 'Tambah Template Invoice' ?>
        </h2>

        <form method="post">
            <input type="hidden" name="_token" value="<?= inv_escape(inv_csrf()) ?>">
            <input type="hidden" name="_action" value="save">
            <input type="hidden" name="id" value="<?= (int) ($editData['id'] ?? 0) ?>">

            <div class="grid">
                <div>
                    <label>Jenis Template *</label>
                    <select name="template_type" required>
                        <?php
                        $selectedType = (string) ($editData['template_type'] ?? ($requestedType !== '' ? $requestedType : 'invoice_asli'));
                        ?>
                        <option value="invoice_asli" <?= $selectedType === 'invoice_asli' ? 'selected' : '' ?>>Invoice Asli</option>
                        <option value="invoice_order" <?= $selectedType === 'invoice_order' ? 'selected' : '' ?>>Invoice Order</option>
                        <option value="invoice_terima_uang" <?= $selectedType === 'invoice_terima_uang' ? 'selected' : '' ?>>Invoice Terima Uang</option>
                    </select>
                </div>

                <div>
                    <label>Document No</label>
                    <input type="text" name="document_no" value="<?= inv_escape($editData['document_no'] ?? '') ?>">
                </div>

                <div>
                    <label>Invoice No</label>
                    <input type="text" name="invoice_no" value="<?= inv_escape($editData['invoice_no'] ?? '') ?>">
                </div>

                <div>
                    <label>Value Date</label>
                    <input type="date" name="value_date" value="<?= inv_escape($editData['value_date'] ?? '') ?>">
                </div>

                <div>
                    <label>Invoice Date</label>
                    <input type="date" name="invoice_date" value="<?= inv_escape($editData['invoice_date'] ?? '') ?>">
                </div>

                <div>
                    <label>Due Date</label>
                    <input type="date" name="due_date" value="<?= inv_escape($editData['due_date'] ?? '') ?>">
                </div>

                <div>
                    <label>Currency</label>
                    <input type="text" name="currency" value="<?= inv_escape($editData['currency'] ?? '') ?>" placeholder="USD / EUR / CNY">
                </div>

                <div>
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" value="<?= inv_escape($editData['amount'] ?? '0') ?>">
                </div>

                <div>
                    <label>Qty</label>
                    <input type="number" step="0.01" name="qty" value="<?= inv_escape($editData['qty'] ?? '1') ?>">
                </div>

                <div>
                    <label>Rate</label>
                    <input type="number" step="0.01" name="rate" value="<?= inv_escape($editData['rate'] ?? '0') ?>">
                </div>

                <div>
                    <label>Terms</label>
                    <input type="text" name="terms" value="<?= inv_escape($editData['terms'] ?? '') ?>">
                </div>

                <div>
                    <label>Company Phone</label>
                    <input type="text" name="company_phone" value="<?= inv_escape($editData['company_phone'] ?? '') ?>">
                </div>

                <div class="full">
                    <label>Company Name</label>
                    <input type="text" name="company_name" value="<?= inv_escape($editData['company_name'] ?? '') ?>">
                </div>

                <div class="full">
                    <label>Company Address</label>
                    <textarea name="company_address"><?= inv_escape($editData['company_address'] ?? '') ?></textarea>
                </div>

                <div class="full">
                    <label>Company Email</label>
                    <input type="text" name="company_email" value="<?= inv_escape($editData['company_email'] ?? '') ?>">
                </div>
            </div>

            <hr style="margin:20px 0; border:none; border-top:1px solid #e1e8f0;">

            <div class="grid-2">
                <div>
                    <label>Bill To Name</label>
                    <input type="text" name="bill_to_name" value="<?= inv_escape($editData['bill_to_name'] ?? '') ?>">
                </div>

                <div>
                    <label>Beneficiary Name / Nama Kop</label>
                    <input
                        type="text"
                        name="beneficiary_name"
                        value="<?= inv_escape($editData['beneficiary_name'] ?? '') ?>"
                        placeholder="Nama beneficiary yang tampil pada kop invoice"
                    >
                    <small class="field-help">
                        Untuk Invoice Asli, nilai ini ditampilkan sebagai nama pada kop surat.
                    </small>
                </div>

                <div>
                    <label>Bill To Address</label>
                    <textarea name="bill_to_address"><?= inv_escape($editData['bill_to_address'] ?? '') ?></textarea>
                </div>

                <div>
                    <label>Beneficiary Address / Alamat Kop</label>
                    <textarea
                        name="beneficiary_address"
                        placeholder="Alamat beneficiary yang tampil di bawah nama kop"
                    ><?= inv_escape($editData['beneficiary_address'] ?? '') ?></textarea>
                    <small class="field-help">
                        Alamat ini ditampilkan tepat di bawah nama beneficiary pada preview dan PDF.
                    </small>
                </div>

                <div>
                    <label>Beneficiary Country</label>
                    <input type="text" name="beneficiary_country" value="<?= inv_escape($editData['beneficiary_country'] ?? '') ?>">
                </div>

                <div>
                    <label>Beneficiary Account / IBAN</label>
                    <input type="text" name="beneficiary_account" value="<?= inv_escape($editData['beneficiary_account'] ?? '') ?>">
                </div>

                <div>
                    <label>Beneficiary Bank</label>
                    <input type="text" name="beneficiary_bank" value="<?= inv_escape($editData['beneficiary_bank'] ?? '') ?>">
                </div>

                <div>
                    <label>Beneficiary Swift</label>
                    <input type="text" name="beneficiary_swift" value="<?= inv_escape($editData['beneficiary_swift'] ?? '') ?>">
                </div>

                <div class="full">
                    <label>Beneficiary Bank Address</label>
                    <textarea name="beneficiary_bank_address"><?= inv_escape($editData['beneficiary_bank_address'] ?? '') ?></textarea>
                </div>
            </div>

            <hr style="margin:20px 0; border:none; border-top:1px solid #e1e8f0;">

            <div class="grid-2">
                <div>
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" value="<?= inv_escape($editData['bank_name'] ?? '') ?>">
                </div>

                <div>
                    <label>Bank SWIFT</label>
                    <input type="text" name="bank_swift" value="<?= inv_escape($editData['bank_swift'] ?? '') ?>">
                </div>

                <div>
                    <label>Bank Account</label>
                    <input type="text" name="bank_account" value="<?= inv_escape($editData['bank_account'] ?? '') ?>">
                </div>

                <div>
                    <label>Correspondent Bank</label>
                    <input type="text" name="correspondent_bank" value="<?= inv_escape($editData['correspondent_bank'] ?? '') ?>">
                </div>

                <div class="full">
                    <label>Bank Address</label>
                    <textarea name="bank_address"><?= inv_escape($editData['bank_address'] ?? '') ?></textarea>
                </div>

                <div class="full">
                    <label>Correspondent Bank Address</label>
                    <textarea name="correspondent_bank_address"><?= inv_escape($editData['correspondent_bank_address'] ?? '') ?></textarea>
                </div>

                <div>
                    <label>Correspondent Bank Swift</label>
                    <input type="text" name="correspondent_bank_swift" value="<?= inv_escape($editData['correspondent_bank_swift'] ?? '') ?>">
                </div>

                <div>
                    <label>Correspondent Bank Account</label>
                    <input type="text" name="correspondent_bank_account" value="<?= inv_escape($editData['correspondent_bank_account'] ?? '') ?>">
                </div>
            </div>

            <hr style="margin:20px 0; border:none; border-top:1px solid #e1e8f0;">

            <div class="grid">
                <div class="full">
                    <label>Item Description</label>
                    <textarea name="item_description"><?= inv_escape($editData['item_description'] ?? '') ?></textarea>
                </div>

                <div class="full">
                    <label>Payment Details</label>
                    <textarea name="payment_details"><?= inv_escape($editData['payment_details'] ?? '') ?></textarea>
                </div>

                <div class="full">
                    <label>Notes</label>
                    <textarea name="notes"><?= inv_escape($editData['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-save"></i>
                    Simpan
                </button>

                <a
                    class="btn btn-secondary"
                    href="<?= inv_escape(
                        url(
                            '/invoice_templates.php'
                            . (
                                $requestedType !== ''
                                    ? '?type=' . rawurlencode($requestedType)
                                    : ''
                            )
                        )
                    ) ?>"
                >
                    <i class="bi bi-x-circle"></i>
                    Reset
                </a>
            </div>
        </form>
    </section>

    <?php endif; ?>

    <section class="card table-wrap">
        <h2 class="section-title">Daftar Template Invoice</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis Template</th>
                    <th>Invoice / Dokumen</th>
                    <th>Bill To</th>
                    <th>Beneficiary</th>
                    <th>Amount</th>
                    <th style="width:280px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="7">Belum ada data template invoice.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= (int) $row['id'] ?></td>
                        <td><?= inv_escape(inv_template_label((string) $row['template_type'])) ?></td>
                        <td>
                            <strong><?= inv_escape($row['invoice_no'] ?: $row['document_no']) ?></strong><br>
                            <small><?= inv_escape($row['invoice_date'] ?? '') ?></small>
                        </td>
                        <td><?= inv_escape($row['bill_to_name'] ?? '') ?></td>
                        <td><?= inv_escape($row['beneficiary_name'] ?? '') ?></td>
                        <td><?= inv_escape(inv_format_money($row['amount'] ?? 0, (string) ($row['currency'] ?? ''))) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary" href="<?= inv_escape(url('/invoice_templates.php?preview=' . (int) $row['id'])) ?>">
                                    <i class="bi bi-eye"></i>
                                    Preview
                                </a>

                                <?php if ($canWrite): ?>
                                    <a class="btn btn-secondary" href="<?= inv_escape(url('/invoice_templates.php?edit=' . (int) $row['id'])) ?>">
                                        <i class="bi bi-pencil-square"></i>
                                        Edit
                                    </a>

                                    <form method="post" onsubmit="return confirm('Hapus data ini?')" style="display:inline;">
                                        <input type="hidden" name="_token" value="<?= inv_escape(inv_csrf()) ?>">
                                        <input type="hidden" name="_action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button class="btn btn-danger" type="submit">
                                            <i class="bi bi-trash"></i>
                                            Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</main>
<script src="<?= inv_escape(asset('js/smart-table.js')) ?>"></script>
</body>
</html>