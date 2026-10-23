<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\App;
use App\Core\Auth;


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function si_e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function si_url(string $query = ''): string
{
    $path = $_SERVER['SCRIPT_NAME']
        ?? '/standing_instruction_7.php';

    return $path . $query;
}

function si_app_url(string $path = ''): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/public/standing_instruction_7.php'));
    $base = rtrim(dirname($script), '/');
    return $path === '' || $path === '/'
        ? $base . '/'
        : $base . '/' . ltrim($path, '/');
}

function si_redirect(string $message = ''): void
{
    $url = si_url();

    if ($message !== '') {
        $url .= '?message='
            . rawurlencode($message);
    }

    header('Location: ' . $url);
    exit;
}

function si_csrf(): string
{
    if (empty($_SESSION['si_csrf'])) {
        $_SESSION['si_csrf'] =
            bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['si_csrf'];
}

function si_verify_csrf(): void
{
    $token = (string) (
        $_POST['_token'] ?? ''
    );

    if (
        $token === ''
        || !hash_equals(
            si_csrf(),
            $token
        )
    ) {
        http_response_code(419);
        exit('Token keamanan tidak valid.');
    }
}

function si_date_id(mixed $value): string
{
    if (empty($value)) {
        return '-';
    }

    try {
        $date = new DateTime((string) $value);
    } catch (Throwable) {
        return (string) $value;
    }

    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    return $date->format('d')
        . ' '
        . $months[(int) $date->format('n')]
        . ' '
        . $date->format('Y');
}

function si_money(
    mixed $amount,
    string $currency
): string {
    $currency = strtoupper(trim($currency));
    $amount = (float) $amount;

    $decimals = in_array(
        $currency,
        ['IDR', 'JPY'],
        true
    ) ? 0 : 2;

    $value = number_format(
        $amount,
        $decimals,
        ',',
        '.'
    );

    return $currency === 'IDR'
        ? 'Rp. ' . $value
        : $currency . ' ' . $value;
}

function si_number_words(int $number): string
{
    $words = [
        '',
        'satu',
        'dua',
        'tiga',
        'empat',
        'lima',
        'enam',
        'tujuh',
        'delapan',
        'sembilan',
        'sepuluh',
        'sebelas',
    ];

    if ($number < 12) {
        return $words[$number];
    }

    if ($number < 20) {
        return si_number_words($number - 10)
            . ' belas';
    }

    if ($number < 100) {
        return si_number_words(
            intdiv($number, 10)
        )
            . ' puluh '
            . si_number_words(
                $number % 10
            );
    }

    if ($number < 200) {
        return 'seratus '
            . si_number_words(
                $number - 100
            );
    }

    if ($number < 1000) {
        return si_number_words(
            intdiv($number, 100)
        )
            . ' ratus '
            . si_number_words(
                $number % 100
            );
    }

    if ($number < 2000) {
        return 'seribu '
            . si_number_words(
                $number - 1000
            );
    }

    if ($number < 1000000) {
        return si_number_words(
            intdiv($number, 1000)
        )
            . ' ribu '
            . si_number_words(
                $number % 1000
            );
    }

    if ($number < 1000000000) {
        return si_number_words(
            intdiv($number, 1000000)
        )
            . ' juta '
            . si_number_words(
                $number % 1000000
            );
    }

    if ($number < 1000000000000) {
        return si_number_words(
            intdiv($number, 1000000000)
        )
            . ' miliar '
            . si_number_words(
                $number % 1000000000
            );
    }

    return si_number_words(
        intdiv($number, 1000000000000)
    )
        . ' triliun '
        . si_number_words(
            $number % 1000000000000
        );
}

function si_terbilang(
    mixed $amount,
    string $currency
): string {
    $number = (int) floor((float) $amount);

    $text = trim(
        preg_replace(
            '/\s+/',
            ' ',
            si_number_words($number)
        ) ?? ''
    );

    $currencyName = match (
        strtoupper($currency)
    ) {
        'IDR' => 'rupiah',
        'USD' => 'dolar Amerika',
        'EUR' => 'euro',
        'GBP' => 'pound sterling',
        'CNY' => 'yuan',
        'JPY' => 'yen',
        'SGD' => 'dolar Singapura',
        'AUD' => 'dolar Australia',
        default => strtoupper($currency),
    };

    return ucwords(
        $text . ' ' . $currencyName
    );
}

function si_find_document(
    PDO $pdo,
    int $id
): ?array {
    $stmt = $pdo->prepare(
        'SELECT
            d.*,
            t.code AS template_code,
            t.name AS template_name
         FROM si_documents d
         JOIN si_templates t
            ON t.id = d.template_id
         WHERE d.id = ?
         LIMIT 1'
    );

    $stmt->execute([$id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return is_array($row)
        ? $row
        : null;
}

function si_find_items(
    PDO $pdo,
    int $documentId
): array {
    $stmt = $pdo->prepare(
        'SELECT *
         FROM si_document_items
         WHERE document_id = ?
         ORDER BY item_order, id'
    );

    $stmt->execute([$documentId]);

    return $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
}

if (!Auth::check()) {
    header('Location: login');
    exit;
}

$user = Auth::user();

$role = (string) (
    $user['role_code'] ?? ''
);

if (!in_array($role, ['super_admin', 'superadmin'], true)) {
    http_response_code(403);

    exit(
        'Modul Standing Instruction '
        . 'hanya dapat diakses Super Admin.'
    );
}

$userId = (int) ($user['id'] ?? 0);

$pdo = App::db();

$action = (string) (
    $_GET['action'] ?? 'list'
);

$message = (string) (
    $_GET['message'] ?? ''
);

$errors = [];

$templates = $pdo->query(
    'SELECT *
     FROM si_templates
     WHERE is_active = 1
     ORDER BY sort_order, name'
)->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['_action'] ?? '') === 'delete'
) {
    si_verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);

    $document = si_find_document(
        $pdo,
        $id
    );

    if (!$document) {
        http_response_code(404);
        exit('Dokumen tidak ditemukan.');
    }

    $stmt = $pdo->prepare(
        'DELETE FROM si_documents
         WHERE id = ?'
    );

    $stmt->execute([$id]);

    si_redirect('Dokumen berhasil dihapus.');
}


/*
|--------------------------------------------------------------------------
| SAVE CREATE / UPDATE
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && in_array(
        $_POST['_action'] ?? '',
        ['create', 'update'],
        true
    )
) {
    si_verify_csrf();

    $postAction = (string) $_POST['_action'];

    $id = (int) ($_POST['id'] ?? 0);

    $templateId = (int) (
        $_POST['template_id'] ?? 0
    );

    $documentDate = (string) (
        $_POST['document_date']
        ?? date('Y-m-d')
    );

    $effectiveDate = trim(
        (string) (
            $_POST['effective_date'] ?? ''
        )
    );

    $documentCity = trim(
        (string) (
            $_POST['document_city']
            ?? 'Jakarta'
        )
    );

    $documentNumber = trim(
        (string) (
            $_POST['document_number'] ?? ''
        )
    );

    $companyName = trim(
        (string) (
            $_POST['company_name'] ?? ''
        )
    );

    $companyAddress = trim(
        (string) (
            $_POST['company_address'] ?? ''
        )
    );

    $companyPhone = trim(
        (string) (
            $_POST['company_phone'] ?? ''
        )
    );

    $logoPath = trim(
        (string) (
            $_POST['logo_path'] ?? ''
        )
    );

    $bankName = trim(
        (string) (
            $_POST['bank_name'] ?? ''
        )
    );

    $bankBranch = trim(
        (string) (
            $_POST['bank_branch'] ?? ''
        )
    );

    $bankCity = trim(
        (string) (
            $_POST['bank_city'] ?? ''
        )
    );

    $attachmentText = trim(
        (string) (
            $_POST['attachment_text'] ?? ''
        )
    );

    $subject = trim(
        (string) (
            $_POST['subject'] ?? ''
        )
    );

    $signatoryTitle = trim(
        (string) (
            $_POST['signatory_title']
            ?? 'Pemberi Kuasa'
        )
    );

    $secondSignatoryTitle = trim(
        (string) (
            $_POST['second_signatory_title']
            ?? 'Direktur'
        )
    );

    $notes = trim(
        (string) (
            $_POST['notes'] ?? ''
        )
    );

    $status = (string) (
        $_POST['status'] ?? 'draft'
    );

    if ($templateId < 1) {
        $errors[] = 'Template wajib dipilih.';
    }

    if ($documentDate === '') {
        $errors[] = 'Tanggal surat wajib diisi.';
    }

    if (
        !in_array(
            $status,
            ['draft', 'final', 'cancelled'],
            true
        )
    ) {
        $errors[] = 'Status dokumen tidak valid.';
    }

    $senderNames =
        $_POST['item_sender_name'] ?? [];

    $senderAccounts =
        $_POST['item_sender_account'] ?? [];

    $receiverNames =
        $_POST['item_receiver_name'] ?? [];

    $receiverAccounts =
        $_POST['item_receiver_account'] ?? [];

    $receiverBanks =
        $_POST['item_receiver_bank'] ?? [];

    $currencies =
        $_POST['item_currency'] ?? [];

    $amounts =
        $_POST['item_amount'] ?? [];

    $transactionCounts =
        $_POST['item_transaction_count'] ?? [];

    $itemNotes =
        $_POST['item_notes'] ?? [];

    if ($errors === []) {
        try {
            $pdo->beginTransaction();

            if ($postAction === 'create') {
                $stmt = $pdo->prepare(
                    'INSERT INTO si_documents
                    (
                        template_id,
                        document_number,
                        document_date,
                        effective_date,
                        document_city,
                        company_name,
                        company_address,
                        company_phone,
                        logo_path,
                        bank_name,
                        bank_branch,
                        bank_city,
                        attachment_text,
                        subject,
                        signatory_title,
                        second_signatory_title,
                        notes,
                        status,
                        created_by,
                        updated_by,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?, ?, ?, ?,
                        ?, ?, NOW(), NOW()
                    )'
                );

                $stmt->execute([
                    $templateId,
                    $documentNumber ?: null,
                    $documentDate,
                    $effectiveDate ?: null,
                    $documentCity,
                    $companyName ?: null,
                    $companyAddress ?: null,
                    $companyPhone ?: null,
                    $logoPath ?: null,
                    $bankName ?: null,
                    $bankBranch ?: null,
                    $bankCity ?: null,
                    $attachmentText ?: null,
                    $subject ?: null,
                    $signatoryTitle ?: null,
                    $secondSignatoryTitle ?: null,
                    $notes ?: null,
                    $status,
                    $userId,
                    $userId,
                ]);

                $id = (int) $pdo->lastInsertId();
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE si_documents
                     SET
                        template_id = ?,
                        document_number = ?,
                        document_date = ?,
                        effective_date = ?,
                        document_city = ?,
                        company_name = ?,
                        company_address = ?,
                        company_phone = ?,
                        logo_path = ?,
                        bank_name = ?,
                        bank_branch = ?,
                        bank_city = ?,
                        attachment_text = ?,
                        subject = ?,
                        signatory_title = ?,
                        second_signatory_title = ?,
                        notes = ?,
                        status = ?,
                        updated_by = ?,
                        updated_at = NOW()
                     WHERE id = ?'
                );

                $stmt->execute([
                    $templateId,
                    $documentNumber ?: null,
                    $documentDate,
                    $effectiveDate ?: null,
                    $documentCity,
                    $companyName ?: null,
                    $companyAddress ?: null,
                    $companyPhone ?: null,
                    $logoPath ?: null,
                    $bankName ?: null,
                    $bankBranch ?: null,
                    $bankCity ?: null,
                    $attachmentText ?: null,
                    $subject ?: null,
                    $signatoryTitle ?: null,
                    $secondSignatoryTitle ?: null,
                    $notes ?: null,
                    $status,
                    $userId,
                    $id,
                ]);

                $deleteItems = $pdo->prepare(
                    'DELETE FROM si_document_items
                     WHERE document_id = ?'
                );

                $deleteItems->execute([$id]);
            }

            $insertItem = $pdo->prepare(
                'INSERT INTO si_document_items
                (
                    document_id,
                    item_order,
                    sender_name,
                    sender_account,
                    receiver_name,
                    receiver_account,
                    receiver_bank,
                    currency,
                    amount,
                    transaction_count,
                    notes,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, NOW(), NOW()
                )'
            );

            $maxRows = max(
                count($senderNames),
                count($senderAccounts),
                count($receiverNames),
                count($amounts)
            );

            for ($index = 0; $index < $maxRows; $index++) {
                $senderName = trim(
                    (string) (
                        $senderNames[$index] ?? ''
                    )
                );

                $senderAccount = trim(
                    (string) (
                        $senderAccounts[$index] ?? ''
                    )
                );

                $receiverName = trim(
                    (string) (
                        $receiverNames[$index] ?? ''
                    )
                );

                $receiverAccount = trim(
                    (string) (
                        $receiverAccounts[$index] ?? ''
                    )
                );

                $receiverBank = trim(
                    (string) (
                        $receiverBanks[$index] ?? ''
                    )
                );

                $currency = strtoupper(
                    trim(
                        (string) (
                            $currencies[$index]
                            ?? 'IDR'
                        )
                    )
                );

                $amount = (float) str_replace(
                    [',', ' '],
                    ['', ''],
                    (string) (
                        $amounts[$index] ?? 0
                    )
                );

                $transactionCount = max(
                    1,
                    (int) (
                        $transactionCounts[$index]
                        ?? 1
                    )
                );

                $itemNote = trim(
                    (string) (
                        $itemNotes[$index] ?? ''
                    )
                );

                if (
                    $senderName === ''
                    && $senderAccount === ''
                    && $receiverName === ''
                    && $receiverAccount === ''
                    && $amount <= 0
                ) {
                    continue;
                }

                $insertItem->execute([
                    $id,
                    $index + 1,
                    $senderName ?: null,
                    $senderAccount ?: null,
                    $receiverName ?: null,
                    $receiverAccount ?: null,
                    $receiverBank ?: null,
                    $currency,
                    $amount,
                    $transactionCount,
                    $itemNote ?: null,
                ]);
            }

            $pdo->commit();

            header(
                'Location: '
                . si_url(
                    '?action=print&id=' . $id
                )
            );

            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errors[] = $exception->getMessage();
        }
    }
}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

if ($action === 'print') {
    $id = (int) ($_GET['id'] ?? 0);

    $document = si_find_document(
        $pdo,
        $id
    );

    if (!$document) {
        http_response_code(404);
        exit('Dokumen tidak ditemukan.');
    }

    $items = si_find_items(
        $pdo,
        $id
    );

    $templateCode = (string) (
        $document['template_code'] ?? ''
    );

    $logoUrl = '';

    if (!empty($document['logo_path'])) {
        $logoUrl = url(
            ltrim((string) $document['logo_path'], '/')
        );
    }

    $documentDate = si_date_id(
        $document['document_date']
    );

    $effectiveDate = si_date_id(
        $document['effective_date']
            ?: $document['document_date']
    );

    $totalTransactions = 0;

    foreach ($items as $item) {
        $totalTransactions += max(
            1,
            (int) (
                $item['transaction_count'] ?? 1
            )
        );
    }

    ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta
    name="viewport"
    content="width=device-width,initial-scale=1"
>
<title>
    <?= si_e($document['template_name']) ?>
</title>

<style>
@page {
    size: A4 portrait;
    margin: 16mm 18mm;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #edf1f5;
    color: #000;
    font-family: "Times New Roman", serif;
    font-size: 12px;
    line-height: 1.35;
}

.toolbar {
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 12px;
    background: linear-gradient(135deg,#f5eaff,#ffeaf4);
    text-align: center;
}

.toolbar button,
.toolbar a {
    border: 0;
    border-radius: 7px;
    padding: 10px 16px;
    margin: 0 5px;
    background: linear-gradient(135deg,#7444dc,#eb4f7b);
    color: #fff;
    font: inherit;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.toolbar a {
    background: #fff;
    color: #65437a;
}

.page {
    position: relative;
    width: 210mm;
    min-height: 297mm;
    margin: 18px auto;
    padding: 16mm 18mm;
    background: #fff;
    box-shadow: 0 5px 20px #0002;
}

.watermark {
    position: fixed;
    top: 48%;
    left: 50%;
    transform:
        translate(-50%, -50%)
        rotate(-35deg);
    font: 700 78px Arial;
    color: rgba(170, 0, 0, .08);
    pointer-events: none;
}

.letterhead {
    display: grid;
    grid-template-columns: 72px 1fr;
    align-items: center;
    gap: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #000;
}

.letterhead.no-logo {
    grid-template-columns: 1fr;
}

.logo {
    width: 68px;
    max-height: 65px;
    object-fit: contain;
}

.company {
    text-align: center;
}

.company h1 {
    margin: 0;
    font-size: 17px;
}

.company div {
    margin-top: 2px;
    font-size: 10px;
}

.date {
    margin-top: 12px;
    text-align: right;
}

.recipient {
    margin-top: 28px;
    margin-bottom: 26px;
}

.recipient.right {
    width: 42%;
    margin-left: auto;
}

.paragraph {
    margin: 16px 0;
    text-align: justify;
    text-indent: 37px;
}

.details {
    width: 100%;
    margin: 14px 0;
    border-collapse: collapse;
}

.details td {
    padding: 3px 4px;
    vertical-align: top;
}

.details .label {
    width: 190px;
}

.details .colon {
    width: 12px;
}

.item-block {
    margin: 14px 0 20px;
}

.item-block table {
    width: 100%;
    border-collapse: collapse;
}

.item-block td {
    padding: 3px;
    vertical-align: top;
}

.item-block .label {
    width: 185px;
}

.item-block .colon {
    width: 12px;
}

.debit-table {
    width: 94%;
    margin: 14px auto;
    border-collapse: collapse;
}

.debit-table td {
    border: 1px solid #000;
    padding: 7px 8px;
    vertical-align: top;
}

.debit-table .number {
    width: 45px;
}

.debit-table .label {
    width: 150px;
}

.total {
    margin: 14px 0;
    font-weight: 700;
}

.closing {
    margin-top: 18px;
    text-align: justify;
}

.signature {
    width: 190px;
    margin-top: 25px;
    margin-left: auto;
    text-align: center;
}

.signature-space {
    height: 65px;
}

.second-signature {
    margin-top: 55px;
}

.metadata {
    display: grid;
    grid-template-columns: 75px 12px 1fr;
    gap: 3px;
    margin: 12px 0 25px;
}

@media print {
    body {
        background: #fff;
    }

    .toolbar {
        display: none;
    }

    .page {
        width: auto;
        min-height: auto;
        margin: 0;
        padding: 0;
        box-shadow: none;
    }
}
</style>
<link rel="stylesheet" href="<?= e(asset('css/theme-clean-global.css')) ?>">
</head>

<body>

<div class="toolbar">

<button onclick="window.print()">
    Cetak / Download PDF A4
</button>

<a href="<?= si_e(si_url()) ?>">
    Kembali
</a>

</div>

<div class="page">

<?php if (
    $document['status'] === 'draft'
): ?>

<div class="watermark">
    DRAFT
</div>

<?php endif; ?>


<?php if (
    !empty($document['company_name'])
    || $logoUrl !== ''
): ?>

<header class="letterhead <?= $logoUrl === '' ? 'no-logo' : '' ?>">

<?php if ($logoUrl !== ''): ?>

<img
    class="logo"
    src="<?= si_e($logoUrl) ?>"
    alt="Logo"
>

<?php endif; ?>

<div class="company">

<h1>
    <?= si_e($document['company_name']) ?>
</h1>

<div>

<?= nl2br(
    si_e($document['company_address'])
) ?>

<?php if (
    !empty($document['company_phone'])
): ?>

<br>
Tlp. <?= si_e($document['company_phone']) ?>

<?php endif; ?>

</div>

</div>

</header>

<?php endif; ?>


<div class="date">

<?= si_e($document['document_city']) ?>,
<?= si_e($documentDate) ?>

</div>


<?php if (
    $templateCode === 'permohonan_debet'
): ?>

<div class="recipient right">

Kepada Yth.,<br>

<?= si_e($document['bank_name']) ?><br>

<?= si_e($document['bank_branch']) ?><br>

<?= si_e($document['bank_city']) ?>

</div>

<div class="metadata">

<div>Nomor</div>
<div>:</div>
<div><?= si_e($document['document_number']) ?></div>

<div>Lampiran</div>
<div>:</div>
<div><?= si_e($document['attachment_text']) ?></div>

<div>Hal</div>
<div>:</div>
<div>
<?= si_e(
    $document['subject']
        ?: 'Surat Perintah Pendebetan Rekening'
) ?>
</div>

</div>

<p>Dengan hormat,</p>

<p class="paragraph">

Bersama ini kami mohon bantuan Saudara untuk
mendebet rekening, termasuk biaya yang akan timbul
dalam transaksi kami dengan data terlampir,
dengan tanggal efektif

<strong><?= si_e($effectiveDate) ?></strong>

sebagai berikut:

</p>

<table class="debit-table">

<?php foreach ($items as $index => $item): ?>

<tr>

<td
    class="number"
    rowspan="3"
>
    <?= $index + 1 ?>.
</td>

<td class="label">
    Nama Rek Debet
</td>

<td>
    <?= si_e($item['sender_name']) ?>
</td>

</tr>

<tr>

<td class="label">
    Jumlah transaksi
</td>

<td>
    <?= (int) $item['transaction_count'] ?>
</td>

</tr>

<tr>

<td class="label">
    Nominal total debet
</td>

<td>

<?= si_e(
    si_money(
        $item['amount'],
        $item['currency']
    )
) ?>

(
<?= si_e(
    si_terbilang(
        $item['amount'],
        $item['currency']
    )
) ?>
)

<?php if (
    !empty($item['sender_account'])
): ?>

<br>
No. Rekening:
<?= si_e($item['sender_account']) ?>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</table>

<div class="total">

Total transaksi:
<?= $totalTransactions ?>
transaksi

</div>

<p class="closing">

Demikian surat perintah debet ini kami buat
dengan sebenarnya, dan segala akibat yang timbul
disebabkan oleh hal tersebut di atas akan menjadi
tanggung jawab kami dan membebaskan Bank dari segala
macam risiko atau akibat yang mungkin timbul
di kemudian hari.

</p>


<?php else: ?>

<div class="recipient">

Dengan hormat,

<br><br>

Kepada Yth.<br>

<?= si_e($document['bank_name']) ?><br>

<?= si_e($document['bank_branch']) ?><br>

<?= si_e($document['bank_city']) ?>

</div>


<?php endif; ?>


<?php if ($templateCode === 'dealing_bri'): ?>

<?php $item = $items[0] ?? []; ?>

<p class="paragraph">

Dengan ini kami mengajukan permohonan kepada
Saudara untuk melaksanakan transaksi

<strong>Beli Valuta Asing (Buy)</strong>

sesuai dengan ketentuan dan prosedur yang berlaku
di Bank, termasuk biaya-biaya yang mungkin timbul,
berdasarkan data terlampir, dengan tanggal efektif

<strong><?= si_e($effectiveDate) ?></strong>.

</p>

<p>
Adapun rincian transaksi Standing Instruction
Beli Valuta Asing tersebut adalah sebagai berikut:
</p>

<table class="details">

<tr>
<td class="label">Nama Perusahaan</td>
<td class="colon">:</td>
<td>
<?= si_e(
    $item['sender_name']
        ?: $document['company_name']
) ?>
</td>
</tr>

<tr>
<td class="label">Jenis Transaksi</td>
<td class="colon">:</td>
<td>Beli Valuta Asing (Buy)</td>
</tr>

<tr>
<td class="label">Nomor Rekening</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_account']) ?></td>
</tr>

<tr>
<td class="label">Nominal Valas</td>
<td class="colon">:</td>
<td>
<?= si_e(
    si_money(
        $item['amount'] ?? 0,
        $item['currency'] ?? 'USD'
    )
) ?>
</td>
</tr>

</table>

<p class="closing">

Demikian Surat Beli Valuta Asing ini kami sampaikan
dengan sebenarnya. Segala akibat yang timbul
sehubungan dengan pelaksanaan instruksi ini sepenuhnya
menjadi tanggung jawab kami dan dengan ini kami
membebaskan Bank dari segala tuntutan, risiko,
dan/atau akibat hukum yang mungkin timbul
di kemudian hari.

</p>

<?php endif; ?>


<?php if ($templateCode === 'overbooking'): ?>

<p class="paragraph">

Dengan ini kami mengajukan permohonan kepada Saudara
untuk melaksanakan transaksi

<strong>overbooking</strong>

dari rekening giro pihak pengirim ke rekening pihak
penerima, termasuk biaya-biaya yang mungkin timbul,
dengan tanggal efektif

<strong><?= si_e($effectiveDate) ?></strong>.

</p>

<?php foreach ($items as $item): ?>

<div class="item-block">

<table>

<tr>
<td class="label">Nama Rek Debet</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_name']) ?></td>
</tr>

<tr>
<td class="label">No Rekening</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_account']) ?></td>
</tr>

<tr>
<td class="label">Nama Rek Kredit</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_name']) ?></td>
</tr>

<tr>
<td class="label">No Rekening</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_account']) ?></td>
</tr>

<tr>
<td class="label">Nominal</td>
<td class="colon">:</td>
<td>
<?= si_e(
    si_money(
        $item['amount'],
        $item['currency']
    )
) ?>
</td>
</tr>

</table>

</div>

<?php endforeach; ?>

<div class="total">
Total transaksi:
<?= $totalTransactions ?>
transaksi
</div>

<?php endif; ?>


<?php if ($templateCode === 'pemindah_bukuan'): ?>

<p class="paragraph">

Dengan ini kami menginstruksikan kepada Saudara
untuk melaksanakan pemindahbukuan dari rekening
pengirim kepada rekening penerima, sesuai dengan
ketentuan dan prosedur yang berlaku di Bank,
dengan tanggal efektif

<strong><?= si_e($effectiveDate) ?></strong>.

</p>

<?php foreach ($items as $item): ?>

<div class="item-block">

<table>

<tr>
<td class="label">Nama Pengirim</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_name']) ?></td>
</tr>

<tr>
<td class="label">Nomor Rekening Pengirim</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_account']) ?></td>
</tr>

<tr>
<td class="label">Nama Penerima</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_name']) ?></td>
</tr>

<tr>
<td class="label">Nomor Rekening Penerima</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_account']) ?></td>
</tr>

<tr>
<td class="label">Nominal</td>
<td class="colon">:</td>
<td>
<?= si_e(
    si_money(
        $item['amount'],
        $item['currency']
    )
) ?>
</td>
</tr>

</table>

</div>

<?php endforeach; ?>

<div class="total">
Total:
<?= $totalTransactions ?>
Transaksi
</div>

<?php endif; ?>


<?php if ($templateCode === 'rtgs'): ?>

<?php $item = $items[0] ?? []; ?>

<p class="paragraph">

Dengan ini kami mengajukan permohonan kepada Saudara
untuk melaksanakan pemindahan dana dalam mata uang
Rupiah melalui sistem RTGS, sesuai ketentuan dan
prosedur yang berlaku, dengan tanggal efektif

<strong><?= si_e($effectiveDate) ?></strong>.

</p>

<p>
Adapun rincian Standing Instruction Pemindahan
Dana IDR (RTGS) adalah sebagai berikut:
</p>

<table class="details">

<tr>
<td class="label">Nama Pengirim</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_name']) ?></td>
</tr>

<tr>
<td class="label">Nomor Rekening Pengirim</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_account']) ?></td>
</tr>

<tr>
<td class="label">Nama Penerima</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_name']) ?></td>
</tr>

<tr>
<td class="label">Bank Penerima</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_bank']) ?></td>
</tr>

<tr>
<td class="label">Nomor Rekening Penerima</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_account']) ?></td>
</tr>

<tr>
<td class="label">Jumlah Dana</td>
<td class="colon">:</td>
<td>
<?= si_e(
    si_money(
        $item['amount'] ?? 0,
        $item['currency'] ?? 'IDR'
    )
) ?>
</td>
</tr>

</table>

<?php endif; ?>


<?php if ($templateCode === 'tarik_setor'): ?>

<?php $item = $items[0] ?? []; ?>

<p class="paragraph">

Dengan ini kami mengajukan permohonan kepada Saudara
untuk melaksanakan Tarik-Setor Tunai dalam mata uang
Rupiah, sesuai dengan ketentuan dan prosedur yang
berlaku, dengan tanggal efektif

<strong><?= si_e($effectiveDate) ?></strong>.

</p>

<p>
Adapun rincian Standing Instruction Penarikan
Dana IDR adalah sebagai berikut:
</p>

<table class="details">

<tr>
<td class="label">Nama Pengirim</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_name']) ?></td>
</tr>

<tr>
<td class="label">Nomor rekening Pengirim</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_account']) ?></td>
</tr>

<tr>
<td class="label">Jumlah Dana</td>
<td class="colon">:</td>
<td>
<?= si_e(
    si_money(
        $item['amount'] ?? 0,
        $item['currency'] ?? 'IDR'
    )
) ?>
</td>
</tr>

</table>

<p>Selanjutnya, disetor ke rekening di bawah ini:</p>

<table class="details">

<tr>
<td class="label">Nama Penerima</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_name']) ?></td>
</tr>

<tr>
<td class="label">Nomor rekening Penerima</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_account']) ?></td>
</tr>

<tr>
<td class="label">Jumlah Dana</td>
<td class="colon">:</td>
<td>
<?= si_e(
    si_money(
        $item['amount'] ?? 0,
        $item['currency'] ?? 'IDR'
    )
) ?>
</td>
</tr>

</table>

<?php endif; ?>


<?php if ($templateCode === 'transfer'): ?>

<p class="paragraph">

Bersama ini kami mohon bantuan Saudara untuk
melaksanakan transfer atau pendebetan rekening,
termasuk biaya yang akan timbul dalam transaksi,
dengan tanggal efektif

<strong><?= si_e($effectiveDate) ?></strong>.

</p>

<?php foreach ($items as $index => $item): ?>

<div class="item-block">

<strong><?= $index + 1 ?>.</strong>

<table>

<tr>
<td class="label">Nama Rek Debet</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_name']) ?></td>
</tr>

<tr>
<td class="label">No Rekening</td>
<td class="colon">:</td>
<td><?= si_e($item['sender_account']) ?></td>
</tr>

<tr>
<td class="label">Nama Rek Kredit</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_name']) ?></td>
</tr>

<tr>
<td class="label">No Rekening</td>
<td class="colon">:</td>
<td><?= si_e($item['receiver_account']) ?></td>
</tr>

<tr>
<td class="label">Nominal</td>
<td class="colon">:</td>
<td>
<?= si_e(
    si_money(
        $item['amount'],
        $item['currency']
    )
) ?>
</td>
</tr>

</table>

</div>

<?php endforeach; ?>

<div class="total">
Total transaksi:
<?= $totalTransactions ?>
transaksi
</div>

<?php endif; ?>


<?php if (
    $templateCode !== 'permohonan_debet'
): ?>

<p class="closing">

Demikian surat Standing Instruction ini kami
sampaikan dengan sebenarnya. Segala akibat yang
timbul sehubungan dengan pelaksanaan instruksi ini
sepenuhnya menjadi tanggung jawab kami dan dengan
ini kami membebaskan pihak Bank dari segala tuntutan,
risiko, dan/atau akibat hukum yang mungkin timbul
di kemudian hari.

</p>

<?php endif; ?>


<p>
Atas perhatian dan kerja sama yang baik,
kami ucapkan terima kasih.
</p>


<div class="signature">

Hormat kami,

<div class="signature-space"></div>

<?= si_e(
    $document['signatory_title']
        ?: 'Pemberi Kuasa'
) ?>


<?php if (
    !empty(
        $document['second_signatory_title']
    )
): ?>

<div class="second-signature">

<?= si_e(
    $document[
        'second_signatory_title'
    ]
) ?>

</div>

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
| FORM CREATE / EDIT
|--------------------------------------------------------------------------
*/

$editing = null;
$items = [];

if ($action === 'edit') {
    $editing = si_find_document(
        $pdo,
        (int) ($_GET['id'] ?? 0)
    );

    if (!$editing) {
        http_response_code(404);
        exit('Dokumen tidak ditemukan.');
    }

    $items = si_find_items(
        $pdo,
        (int) $editing['id']
    );
}

$form = $editing ?? [
    'id' => 0,
    'template_id' => '',
    'document_number' => '',
    'document_date' => date('Y-m-d'),
    'effective_date' => date('Y-m-d'),
    'document_city' => 'Jakarta',
    'company_name' => '',
    'company_address' => '',
    'company_phone' => '',
    'logo_path' => '',
    'bank_name' => '',
    'bank_branch' => '',
    'bank_city' => '',
    'attachment_text' => '',
    'subject' => '',
    'signatory_title' => 'Pemberi Kuasa',
    'second_signatory_title' => 'Direktur',
    'notes' => '',
    'status' => 'draft',
];

if ($items === []) {
    $items = [[
        'sender_name' => '',
        'sender_account' => '',
        'receiver_name' => '',
        'receiver_account' => '',
        'receiver_bank' => '',
        'currency' => 'IDR',
        'amount' => '',
        'transaction_count' => 1,
        'notes' => '',
    ]];
}

$showForm = in_array(
    $action,
    ['create', 'edit'],
    true
);


/*
|--------------------------------------------------------------------------
| LIST DATA
|--------------------------------------------------------------------------
*/

$documents = $pdo->query(
    'SELECT
        d.*,
        t.code AS template_code,
        t.name AS template_name,
        u.name AS creator_name
     FROM si_documents d
     JOIN si_templates t
        ON t.id = d.template_id
     LEFT JOIN users u
        ON u.id = d.created_by
     ORDER BY d.document_date DESC, d.id DESC'
)->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta
    name="viewport"
    content="width=device-width,initial-scale=1"
>
<title>Standing Instruction</title>
<link rel="stylesheet" href="<?= si_e(asset('css/modern-banking.css')) ?>">
<link rel="stylesheet" href="<?= si_e(asset('css/remitops-v5.css')) ?>">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f3f6fa;
    color: #172b4d;
    font-family: "Segoe UI", Arial, sans-serif;
}

.header {
    padding: 20px 28px;
    background: linear-gradient(
        120deg,
        #071d35,
        #194b7a
    );
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    margin: 0;
}

.header a {
    color: #fff;
    text-decoration: none;
}

.wrap {
    max-width: 1450px;
    margin: 22px auto;
    padding: 0 18px;
}

.card {
    background: #fff;
    border: 1px solid #dce5ef;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 18px;
    box-shadow: 0 8px 25px #102a4310;
}

.toolbar {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    flex-wrap: wrap;
    align-items: center;
}

.btn {
    display: inline-block;
    border: 0;
    border-radius: 8px;
    padding: 10px 14px;
    background: #2f6fed;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    cursor: pointer;
}

.btn-secondary {
    background: #edf2f8;
    color: #244363;
}

.btn-danger {
    background: #ffecec;
    color: #a92727;
}

.alert {
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 9px;
}

.alert-success {
    background: #e8f7ee;
    color: #176a3a;
}

.alert-error {
    background: #feecec;
    color: #9d2626;
}

.grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}

.full {
    grid-column: 1 / -1;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: 700;
}

input,
select,
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd6e2;
    border-radius: 8px;
    font: inherit;
}

textarea {
    min-height: 85px;
}

.item {
    position: relative;
    border: 1px solid #dce5ef;
    background: #f8fafc;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 12px;
}

.item-grid {
    display: grid;
    grid-template-columns:
        repeat(4, minmax(150px, 1fr));
    gap: 12px;
}

.remove-item {
    position: absolute;
    right: 12px;
    top: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 10px;
    border-bottom: 1px solid #e1e8f0;
    text-align: left;
    vertical-align: top;
}

th {
    background: #f6f8fb;
    font-size: 12px;
    color: #657489;
}

.actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.small {
    padding: 7px 9px;
    font-size: 12px;
}

@media(max-width: 900px) {
    .grid,
    .item-grid {
        grid-template-columns: 1fr;
    }

    .table-wrap {
        overflow: auto;
    }
}
</style>
<link rel="stylesheet" href="<?= si_e(asset('css/standalone-docs.css?v=4.0.0')) ?>">
</head>

<body class="module-page module-standing">

<header class="header">

<div>

<h1>
Standing Instruction
</h1>

<small>
7 Template Dokumen A4
</small>

</div>

<a class="btn-back" href="<?= si_e(si_app_url('/')) ?>">Kembali ke Dashboard</a>

</header>


<main class="wrap">

<?php if ($message !== ''): ?>

<div class="alert alert-success">
<?= si_e($message) ?>
</div>

<?php endif; ?>


<?php foreach ($errors as $error): ?>

<div class="alert alert-error">
<?= si_e($error) ?>
</div>

<?php endforeach; ?>


<?php if ($showForm): ?>

<section class="card">

<div class="toolbar">

<h2>
<?= $editing
    ? 'Edit Standing Instruction'
    : 'Buat Standing Instruction'
?>
</h2>

<a
    class="btn btn-secondary"
    href="<?= si_e(si_url()) ?>"
>
Kembali
</a>

</div>


<form method="post">

<input
    type="hidden"
    name="_token"
    value="<?= si_e(si_csrf()) ?>"
>

<input
    type="hidden"
    name="_action"
    value="<?= $editing
        ? 'update'
        : 'create'
    ?>"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $form['id'] ?>"
>


<div class="grid">

<div>

<label>Template *</label>

<select
    id="template_id"
    name="template_id"
    required
>

<option value="">
Pilih template
</option>

<?php foreach ($templates as $template): ?>

<option
    value="<?= (int) $template['id'] ?>"
    data-company="<?= si_e($template['default_company_name']) ?>"
    data-address="<?= si_e($template['default_company_address']) ?>"
    data-phone="<?= si_e($template['default_company_phone']) ?>"
    data-logo="<?= si_e($template['default_logo_path']) ?>"
    data-bank="<?= si_e($template['default_bank_name']) ?>"
    data-branch="<?= si_e($template['default_bank_branch']) ?>"
    data-city="<?= si_e($template['default_bank_city']) ?>"
    <?= (int) $form['template_id']
        === (int) $template['id']
        ? 'selected'
        : ''
    ?>
>
<?= si_e($template['name']) ?>
</option>

<?php endforeach; ?>

</select>

</div>


<div>

<label>Status</label>

<select name="status">

<option
    value="draft"
    <?= $form['status'] === 'draft'
        ? 'selected'
        : ''
    ?>
>
Draft
</option>

<option
    value="final"
    <?= $form['status'] === 'final'
        ? 'selected'
        : ''
    ?>
>
Final
</option>

<option
    value="cancelled"
    <?= $form['status'] === 'cancelled'
        ? 'selected'
        : ''
    ?>
>
Dibatalkan
</option>

</select>

</div>


<div>

<label>Tanggal Surat *</label>

<input
    type="date"
    name="document_date"
    value="<?= si_e($form['document_date']) ?>"
    required
>

</div>


<div>

<label>Tanggal Efektif</label>

<input
    type="date"
    name="effective_date"
    value="<?= si_e($form['effective_date']) ?>"
>

</div>


<div>

<label>Kota Surat</label>

<input
    name="document_city"
    value="<?= si_e($form['document_city']) ?>"
>

</div>


<div>

<label>Nomor Surat</label>

<input
    name="document_number"
    value="<?= si_e($form['document_number']) ?>"
>

</div>


<div>

<label>Lampiran</label>

<input
    name="attachment_text"
    value="<?= si_e($form['attachment_text']) ?>"
>

</div>


<div>

<label>Hal / Perihal</label>

<input
    name="subject"
    value="<?= si_e($form['subject']) ?>"
>

</div>


<div>

<label>Nama Perusahaan</label>

<input
    id="company_name"
    name="company_name"
    value="<?= si_e($form['company_name']) ?>"
>

</div>


<div>

<label>Telepon Perusahaan</label>

<input
    id="company_phone"
    name="company_phone"
    value="<?= si_e($form['company_phone']) ?>"
>

</div>


<div class="full">

<label>Alamat Perusahaan</label>

<textarea
    id="company_address"
    name="company_address"
><?= si_e($form['company_address']) ?></textarea>

</div>


<div>

<label>Path Logo</label>

<input
    id="logo_path"
    name="logo_path"
    value="<?= si_e($form['logo_path']) ?>"
>

</div>


<div>

<label>Nama Bank</label>

<input
    id="bank_name"
    name="bank_name"
    value="<?= si_e($form['bank_name']) ?>"
>

</div>


<div>

<label>Cabang Bank</label>

<input
    id="bank_branch"
    name="bank_branch"
    value="<?= si_e($form['bank_branch']) ?>"
>

</div>


<div>

<label>Kota Bank</label>

<input
    id="bank_city"
    name="bank_city"
    value="<?= si_e($form['bank_city']) ?>"
>

</div>


<div>

<label>Jabatan Penandatangan</label>

<input
    name="signatory_title"
    value="<?= si_e($form['signatory_title']) ?>"
>

</div>


<div>

<label>Jabatan Kedua</label>

<input
    name="second_signatory_title"
    value="<?= si_e($form['second_signatory_title']) ?>"
>

</div>


<div class="full">

<label>Catatan Internal</label>

<textarea name="notes"><?= si_e($form['notes']) ?></textarea>

</div>

</div>


<hr>

<div class="toolbar">

<h3>Rincian Transaksi</h3>

<button
    type="button"
    class="btn btn-secondary"
    id="addItem"
>
+ Tambah Rincian
</button>

</div>


<div id="itemsContainer">

<?php foreach ($items as $item): ?>

<div class="item">

<button
    type="button"
    class="btn btn-danger small remove-item"
>
Hapus
</button>

<div class="item-grid">

<div>

<label>Nama Pengirim / Rek Debet</label>

<input
    name="item_sender_name[]"
    value="<?= si_e($item['sender_name']) ?>"
>

</div>


<div>

<label>No. Rekening Pengirim</label>

<input
    name="item_sender_account[]"
    value="<?= si_e($item['sender_account']) ?>"
>

</div>


<div>

<label>Nama Penerima / Rek Kredit</label>

<input
    name="item_receiver_name[]"
    value="<?= si_e($item['receiver_name']) ?>"
>

</div>


<div>

<label>No. Rekening Penerima</label>

<input
    name="item_receiver_account[]"
    value="<?= si_e($item['receiver_account']) ?>"
>

</div>


<div>

<label>Bank Penerima</label>

<input
    name="item_receiver_bank[]"
    value="<?= si_e($item['receiver_bank']) ?>"
>

</div>


<div>

<label>Mata Uang</label>

<select name="item_currency[]">

<?php foreach (
    [
        'IDR',
        'USD',
        'EUR',
        'CNY',
        'SGD',
        'JPY',
        'GBP',
        'AUD',
    ] as $currency
): ?>

<option
    value="<?= $currency ?>"
    <?= $item['currency'] === $currency
        ? 'selected'
        : ''
    ?>
>
<?= $currency ?>
</option>

<?php endforeach; ?>

</select>

</div>


<div>

<label>Nominal</label>

<input
    type="number"
    step="0.01"
    min="0"
    name="item_amount[]"
    value="<?= si_e($item['amount']) ?>"
>

</div>


<div>

<label>Jumlah Transaksi</label>

<input
    type="number"
    min="1"
    name="item_transaction_count[]"
    value="<?= (int) $item['transaction_count'] ?>"
>

</div>


<div class="full">

<label>Catatan Rincian</label>

<textarea
    name="item_notes[]"
><?= si_e($item['notes']) ?></textarea>

</div>

</div>

</div>

<?php endforeach; ?>

</div>


<br>

<button class="btn">
Simpan & Buka Cetak
</button>

</form>

</section>


<?php else: ?>


<section class="card">

<div class="toolbar">

<div>

<h2 style="margin:0">
Daftar Standing Instruction
</h2>

<small>
Kelola, edit, cetak, dan download PDF.
</small>

</div>

<a
    class="btn"
    href="<?= si_e(
        si_url('?action=create')
    ) ?>"
>
+ Buat Dokumen
</a>

</div>

</section>


<section class="card table-wrap">

<table>

<thead>

<tr>
<th>Tanggal</th>
<th>Template</th>
<th>Perusahaan</th>
<th>Status</th>
<th>Dibuat Oleh</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php if ($documents === []): ?>

<tr>
<td colspan="6">
Belum ada dokumen.
</td>
</tr>

<?php endif; ?>


<?php foreach ($documents as $document): ?>

<tr>

<td>
<?= si_e(
    date(
        'd/m/Y',
        strtotime(
            $document['document_date']
        )
    )
) ?>
</td>

<td>
<strong>
<?= si_e($document['template_name']) ?>
</strong>
<br>
<small>
<?= si_e($document['document_number']) ?>
</small>
</td>

<td>
<?= si_e($document['company_name']) ?>
</td>

<td>
<?= si_e(strtoupper($document['status'])) ?>
</td>

<td>
<?= si_e($document['creator_name']) ?>
</td>

<td>

<div class="actions">

<a
    class="btn small"
    href="<?= si_e(
        si_url(
            '?action=print&id='
            . (int) $document['id']
        )
    ) ?>"
>
Cetak
</a>

<a
    class="btn btn-secondary small"
    href="<?= si_e(
        si_url(
            '?action=edit&id='
            . (int) $document['id']
        )
    ) ?>"
>
Edit
</a>

<form
    method="post"
    onsubmit="return confirm('Hapus dokumen ini?')"
>

<input
    type="hidden"
    name="_token"
    value="<?= si_e(si_csrf()) ?>"
>

<input
    type="hidden"
    name="_action"
    value="delete"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $document['id'] ?>"
>

<button
    class="btn btn-danger small"
>
Hapus
</button>

</form>

</div>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</section>

<?php endif; ?>

</main>


<template id="itemTemplate">

<div class="item">

<button
    type="button"
    class="btn btn-danger small remove-item"
>
Hapus
</button>

<div class="item-grid">

<div>
<label>Nama Pengirim / Rek Debet</label>
<input name="item_sender_name[]">
</div>

<div>
<label>No. Rekening Pengirim</label>
<input name="item_sender_account[]">
</div>

<div>
<label>Nama Penerima / Rek Kredit</label>
<input name="item_receiver_name[]">
</div>

<div>
<label>No. Rekening Penerima</label>
<input name="item_receiver_account[]">
</div>

<div>
<label>Bank Penerima</label>
<input name="item_receiver_bank[]">
</div>

<div>
<label>Mata Uang</label>
<select name="item_currency[]">
<option>IDR</option>
<option>USD</option>
<option>EUR</option>
<option>CNY</option>
<option>SGD</option>
<option>JPY</option>
<option>GBP</option>
<option>AUD</option>
</select>
</div>

<div>
<label>Nominal</label>
<input
    type="number"
    step="0.01"
    min="0"
    name="item_amount[]"
>
</div>

<div>
<label>Jumlah Transaksi</label>
<input
    type="number"
    min="1"
    value="1"
    name="item_transaction_count[]"
>
</div>

<div class="full">
<label>Catatan Rincian</label>
<textarea name="item_notes[]"></textarea>
</div>

</div>

</div>

</template>


<script>

const templateSelect =
    document.getElementById('template_id');

if (templateSelect) {
    templateSelect.addEventListener(
        'change',
        function () {
            const option =
                this.options[this.selectedIndex];

            const values = {
                company_name:
                    option.dataset.company || '',

                company_address:
                    option.dataset.address || '',

                company_phone:
                    option.dataset.phone || '',

                logo_path:
                    option.dataset.logo || '',

                bank_name:
                    option.dataset.bank || '',

                bank_branch:
                    option.dataset.branch || '',

                bank_city:
                    option.dataset.city || '',
            };

            Object.entries(values).forEach(
                ([id, value]) => {
                    const field =
                        document.getElementById(id);

                    if (
                        field
                        && (
                            field.value.trim() === ''
                            || confirmFieldReplace(field)
                        )
                    ) {
                        field.value = value;
                    }
                }
            );
        }
    );
}

function confirmFieldReplace(field) {
    return field.dataset.autofilled === '1';
}

document.addEventListener(
    'click',
    function (event) {
        if (
            event.target
            && event.target.id === 'addItem'
        ) {
            const container =
                document.getElementById(
                    'itemsContainer'
                );

            const template =
                document.getElementById(
                    'itemTemplate'
                );

            container.appendChild(
                template.content.cloneNode(true)
            );
        }

        if (
            event.target
            && event.target.classList.contains(
                'remove-item'
            )
        ) {
            const items =
                document.querySelectorAll(
                    '#itemsContainer .item'
                );

            if (items.length <= 1) {
                alert(
                    'Minimal harus ada satu rincian.'
                );

                return;
            }

            event.target
                .closest('.item')
                .remove();
        }
    }
);

</script>

<script src="<?= si_e(asset('js/smart-table.js')) ?>"></script>
</body>
</html>
