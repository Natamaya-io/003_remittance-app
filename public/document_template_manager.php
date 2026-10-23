<?php



declare(strict_types=1);



require_once dirname(__DIR__) . '/app/bootstrap.php';



use App\Core\App;

use App\Core\Auth;



if (session_status() !== PHP_SESSION_ACTIVE) {

    session_start();

}



function managerEscape(mixed $value): string

{

    return htmlspecialchars(

        (string) $value,

        ENT_QUOTES,

        'UTF-8'

    );

}



function managerAppUrl(string $path = ''): string

{

    $script = str_replace('\\\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/public/document_template_manager.php'));

    $base = rtrim(dirname($script), '/');

    return $path === '' || $path === '/'

        ? $base . '/'

        : $base . '/' . ltrim($path, '/');

}



function managerUrl(

    string $module,

    string $parameters = ''

): string {

    return url(

        '/document_template_manager.php'

        . '?module='

        . rawurlencode($module)

        . $parameters

    );

}



function managerCsrf(): string

{

    if (empty($_SESSION['document_manager_csrf'])) {

        $_SESSION['document_manager_csrf'] =

            bin2hex(random_bytes(32));

    }



    return (string) $_SESSION[

        'document_manager_csrf'

    ];

}



function managerVerifyCsrf(): void

{

    $token = (string) (

        $_POST['_token']

        ?? ''

    );



    if (

        $token === ''

        || !hash_equals(

            managerCsrf(),

            $token

        )

    ) {

        http_response_code(419);

        exit('Token keamanan tidak valid.');

    }

}



function managerTableExists(

    PDO $pdo,

    string $table

): bool {

    $stmt = $pdo->prepare(

        'SELECT COUNT(*)

         FROM information_schema.TABLES

         WHERE TABLE_SCHEMA = DATABASE()

           AND TABLE_NAME = ?'

    );



    $stmt->execute([$table]);



    return (int) $stmt->fetchColumn() > 0;

}



function managerInstallTables(PDO $pdo): void

{

    $pdo->exec(

        "CREATE TABLE IF NOT EXISTS

        document_template_types

        (

            id BIGINT UNSIGNED

                NOT NULL AUTO_INCREMENT,



            module_code VARCHAR(50)

                NOT NULL,



            type_code VARCHAR(80)

                NOT NULL,



            type_name VARCHAR(150)

                NOT NULL,



            owner_role VARCHAR(80)

                NOT NULL,



            description VARCHAR(255)

                NULL,



            sort_order INT UNSIGNED

                NOT NULL DEFAULT 0,



            is_active TINYINT(1)

                NOT NULL DEFAULT 1,



            created_at DATETIME

                NOT NULL DEFAULT CURRENT_TIMESTAMP,



            updated_at DATETIME

                NOT NULL DEFAULT CURRENT_TIMESTAMP

                ON UPDATE CURRENT_TIMESTAMP,



            PRIMARY KEY (id),



            UNIQUE KEY

                uq_document_template_type_code

                (type_code),



            KEY idx_document_type_module

                (module_code)



        ) ENGINE=InnoDB

          DEFAULT CHARSET=utf8mb4

          COLLATE=utf8mb4_unicode_ci"

    );



    $pdo->exec(

        "CREATE TABLE IF NOT EXISTS

        document_templates

        (

            id BIGINT UNSIGNED

                NOT NULL AUTO_INCREMENT,



            type_id BIGINT UNSIGNED

                NOT NULL,



            title VARCHAR(180)

                NOT NULL,



            description TEXT NULL,



            file_path VARCHAR(500)

                NULL,



            file_original_name VARCHAR(255)

                NULL,



            file_mime VARCHAR(150)

                NULL,



            file_size BIGINT UNSIGNED

                NULL,



            version_label VARCHAR(20)

                NOT NULL DEFAULT '1.0',



            status ENUM(

                'draft',

                'active',

                'inactive'

            ) NOT NULL DEFAULT 'draft',



            created_by BIGINT UNSIGNED

                NOT NULL,



            updated_by BIGINT UNSIGNED

                NULL,



            created_at DATETIME

                NOT NULL DEFAULT CURRENT_TIMESTAMP,



            updated_at DATETIME

                NOT NULL DEFAULT CURRENT_TIMESTAMP

                ON UPDATE CURRENT_TIMESTAMP,



            PRIMARY KEY (id),



            KEY idx_document_template_type

                (type_id),



            KEY idx_document_template_status

                (status)



        ) ENGINE=InnoDB

          DEFAULT CHARSET=utf8mb4

          COLLATE=utf8mb4_unicode_ci"

    );



    $seedSql = "

        INSERT INTO document_template_types

        (

            module_code,

            type_code,

            type_name,

            owner_role,

            description,

            sort_order,

            is_active

        )

        VALUES

        (

            'invoice',

            'invoice_order',

            'Invoice Order',

            'admin_invoice',

            'Template Invoice Order',

            10,

            1

        ),

        (

            'invoice',

            'invoice_asli',

            'Invoice Asli',

            'admin_invoice',

            'Template Invoice Asli',

            20,

            1

        ),

        (

            'invoice',

            'invoice_terima_uang',

            'Invoice Terima Uang',

            'admin_invoice',

            'Template Invoice Terima Uang',

            30,

            1

        ),

        (

            'complain',

            'complain_surat_refund',

            'Surat Refund',

            'admin_complain',

            'Template Surat Refund',

            10,

            1

        ),

        (

            'complain',

            'complain_surat_amend',

            'Surat Amend',

            'admin_complain',

            'Template Surat Amend',

            20,

            1

        ),

        (

            'complain',

            'complain_mt199',

            'MT 199',

            'admin_complain',

            'Template MT 199',

            30,

            1

        ),

        (

            'complain',

            'complain_mt103',

            'MT 103',

            'admin_complain',

            'Template MT 103',

            40,

            1

        )

        ON DUPLICATE KEY UPDATE

            type_name = VALUES(type_name),

            owner_role = VALUES(owner_role),

            description = VALUES(description),

            sort_order = VALUES(sort_order),

            is_active = 1,

            updated_at = CURRENT_TIMESTAMP

    ";



    $pdo->exec($seedSql);

}



function managerCanWrite(

    string $role,

    string $module

): bool {

    if (in_array(

        $role,

        ['super_admin', 'superadmin'],

        true

    )) {

        return true;

    }



    if (

        in_array(

            $role,

            ['admin_invoice', 'invoice'],

            true

        )

        && $module === 'invoice'

    ) {

        return true;

    }



    if (

        in_array(

            $role,

            ['admin_complain', 'complain'],

            true

        )

        && $module === 'complain'

    ) {

        return true;

    }



    return false;

}



if (!Auth::check()) {

    header('Location: login');

    exit;

}



$user = Auth::user() ?? [];



$role = (string) (

    $user['role_code']

    ?? ''

);



$userId = (int) (

    $user['id']

    ?? 0

);



$module = (string) (

    $_GET['module']

    ?? 'invoice'

);



if (!in_array(

    $module,

    ['invoice', 'complain'],

    true

)) {

    $module = 'invoice';

}



$canRead = in_array(

    $role,

    [

        'super_admin',

        'superadmin',

        'supervisor',

        'admin_invoice',

        'invoice',

        'admin_complain',

        'complain',

    ],

    true

);



if (!$canRead) {

    http_response_code(403);

    exit('Akses ditolak.');

}



if (

    in_array(

        $role,

        ['admin_invoice', 'invoice'],

        true

    )

    && $module !== 'invoice'

) {

    http_response_code(403);

    exit('Akses ditolak.');

}



if (

    in_array(

        $role,

        ['admin_complain', 'complain'],

        true

    )

    && $module !== 'complain'

) {

    http_response_code(403);

    exit('Akses ditolak.');

}



$pdo = App::db();



managerInstallTables($pdo);



$canWrite = managerCanWrite(

    $role,

    $module

);



$message = (string) (

    $_GET['message']

    ?? ''

);



$error = '';



/*

|--------------------------------------------------------------------------

| Upload dan simpan

|--------------------------------------------------------------------------

*/



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    managerVerifyCsrf();



    $action = (string) (

        $_POST['_action']

        ?? ''

    );



    try {

        if ($action === 'delete') {

            if (!$canWrite) {

                throw new RuntimeException(

                    'Anda tidak memiliki akses hapus.'

                );

            }



            $id = (int) (

                $_POST['id']

                ?? 0

            );



            $stmt = $pdo->prepare(

                'SELECT file_path

                 FROM document_templates

                 WHERE id = ?

                 LIMIT 1'

            );



            $stmt->execute([$id]);



            $oldPath =

                $stmt->fetchColumn();



            $delete = $pdo->prepare(

                'DELETE

                 FROM document_templates

                 WHERE id = ?'

            );



            $delete->execute([$id]);



            if ($oldPath) {

                $absolute =

                    dirname(__DIR__)

                    . '/public/'

                    . $oldPath;



                if (is_file($absolute)) {

                    @unlink($absolute);

                }

            }



            header(

                'Location: '

                . managerUrl(

                    $module,

                    '&message='

                    . rawurlencode(

                        'Template berhasil dihapus.'

                    )

                )

            );



            exit;

        }



        if ($action === 'save') {

            if (!$canWrite) {

                throw new RuntimeException('Anda tidak memiliki akses simpan.');

            }



            $id = (int) ($_POST['id'] ?? 0);

            $typeId = (int) ($_POST['type_id'] ?? 0);

            $title = trim((string) ($_POST['title'] ?? ''));

            $description = trim((string) ($_POST['description'] ?? ''));

            $version = trim((string) ($_POST['version_label'] ?? '1.0'));

            $status = (string) ($_POST['status'] ?? 'draft');



            if ($typeId < 1 || $title === '') {

                throw new RuntimeException('Jenis dan judul wajib diisi.');

            }

            if (!in_array($status, ['draft', 'active', 'inactive'], true)) {

                throw new RuntimeException('Status template tidak valid.');

            }



            $typeCheck = $pdo->prepare(

                'SELECT id FROM document_template_types WHERE id = ? AND module_code = ? LIMIT 1'

            );

            $typeCheck->execute([$typeId, $module]);

            if (!$typeCheck->fetchColumn()) {

                throw new RuntimeException('Jenis template tidak sesuai modul.');

            }



            $existing = null;

            if ($id > 0) {

                $existingStmt = $pdo->prepare(

                    'SELECT dt.* FROM document_templates dt

                     INNER JOIN document_template_types dtt ON dtt.id = dt.type_id

                     WHERE dt.id = ? AND dtt.module_code = ? LIMIT 1'

                );

                $existingStmt->execute([$id, $module]);

                $existing = $existingStmt->fetch(PDO::FETCH_ASSOC);

                if (!$existing) {

                    throw new RuntimeException('Template yang akan diedit tidak ditemukan.');

                }

            }



            $filePath = $existing['file_path'] ?? null;

            $originalName = $existing['file_original_name'] ?? null;

            $mime = $existing['file_mime'] ?? null;

            $size = $existing['file_size'] ?? null;

            $newUploadedFile = null;



            $file = $_FILES['template_file'] ?? null;

            if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {

                if (($file['error'] ?? 1) !== UPLOAD_ERR_OK) {

                    throw new RuntimeException('Upload file gagal.');

                }



                $size = (int) ($file['size'] ?? 0);

                if ($size <= 0 || $size > 10 * 1024 * 1024) {

                    throw new RuntimeException('Ukuran file maksimal 10 MB.');

                }



                $originalName = basename((string) ($file['name'] ?? 'template'));

                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($extension, ['doc', 'docx', 'pdf', 'xls', 'xlsx'], true)) {

                    throw new RuntimeException('Format harus DOC, DOCX, PDF, XLS, atau XLSX.');

                }



                $relativeDirectory = 'uploads/document_templates/' . date('Y/m');

                $absoluteDirectory = dirname(__DIR__) . '/public/' . $relativeDirectory;

                if (!is_dir($absoluteDirectory) && !mkdir($absoluteDirectory, 0775, true) && !is_dir($absoluteDirectory)) {

                    throw new RuntimeException('Folder upload tidak dapat dibuat.');

                }



                $storedName = bin2hex(random_bytes(16)) . '.' . $extension;

                $absoluteFile = $absoluteDirectory . '/' . $storedName;

                if (!move_uploaded_file((string) $file['tmp_name'], $absoluteFile)) {

                    throw new RuntimeException('File gagal disimpan.');

                }



                $newUploadedFile = $absoluteFile;

                $filePath = $relativeDirectory . '/' . $storedName;

                $mime = function_exists('mime_content_type')

                    ? (mime_content_type($absoluteFile) ?: 'application/octet-stream')

                    : 'application/octet-stream';

            }



            if ($id > 0) {

                $stmt = $pdo->prepare(

                    'UPDATE document_templates SET

                        type_id = ?, title = ?, description = ?, file_path = ?,

                        file_original_name = ?, file_mime = ?, file_size = ?,

                        version_label = ?, status = ?, updated_by = ?, updated_at = NOW()

                     WHERE id = ?'

                );

                $stmt->execute([

                    $typeId, $title, $description ?: null, $filePath, $originalName,

                    $mime, $size, $version ?: '1.0', $status, $userId, $id,

                ]);



                if ($newUploadedFile !== null && !empty($existing['file_path']) && $existing['file_path'] !== $filePath) {

                    $oldAbsolute = dirname(__DIR__) . '/public/' . $existing['file_path'];

                    if (is_file($oldAbsolute)) {

                        @unlink($oldAbsolute);

                    }

                }



                $successMessage = 'Template berhasil diperbarui.';

            } else {

                $stmt = $pdo->prepare(

                    'INSERT INTO document_templates

                    (type_id,title,description,file_path,file_original_name,file_mime,file_size,version_label,status,created_by,updated_by,created_at,updated_at)

                    VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())'

                );

                $stmt->execute([

                    $typeId, $title, $description ?: null, $filePath, $originalName,

                    $mime, $size, $version ?: '1.0', $status, $userId, $userId,

                ]);

                $successMessage = 'Template berhasil ditambahkan.';

            }



            header('Location: ' . managerUrl($module, '&message=' . rawurlencode($successMessage)));

            exit;

        }

    } catch (Throwable $exception) {

        $error = $exception->getMessage();

    }

}



/*

|--------------------------------------------------------------------------

| Download

|--------------------------------------------------------------------------

*/



if (

    ($_GET['action'] ?? '')

    === 'download'

) {

    $id = (int) (

        $_GET['id']

        ?? 0

    );



    $stmt = $pdo->prepare(

        'SELECT

            dt.file_path,

            dt.file_original_name,

            dt.file_mime

         FROM document_templates dt

         INNER JOIN document_template_types dtt

            ON dtt.id = dt.type_id

         WHERE dt.id = ?

           AND dtt.module_code = ?

         LIMIT 1'

    );



    $stmt->execute([

        $id,

        $module,

    ]);



    $file = $stmt->fetch(

        PDO::FETCH_ASSOC

    );



    if (

        !$file

        || empty($file['file_path'])

    ) {

        http_response_code(404);

        exit('File tidak ditemukan.');

    }



    $absolute =

        dirname(__DIR__)

        . '/public/'

        . $file['file_path'];



    if (!is_file($absolute)) {

        http_response_code(404);

        exit('File tidak ditemukan.');

    }



    header(

        'Content-Type: '

        . (

            $file['file_mime']

            ?: 'application/octet-stream'

        )

    );



    header(

        'Content-Disposition: attachment; filename="'

        . basename(

            $file['file_original_name']

        )

        . '"'

    );



    header(

        'Content-Length: '

        . filesize($absolute)

    );



    readfile($absolute);

    exit;

}



/*

|--------------------------------------------------------------------------

| Ambil data

|--------------------------------------------------------------------------

*/



$typeStmt = $pdo->prepare(

    'SELECT *

     FROM document_template_types

     WHERE module_code = ?

       AND is_active = 1

     ORDER BY sort_order'

);



$typeStmt->execute([$module]);



$types = $typeStmt->fetchAll(

    PDO::FETCH_ASSOC

);



$listStmt = $pdo->prepare(

    'SELECT

        dt.*,

        dtt.type_name

     FROM document_templates dt

     INNER JOIN document_template_types dtt

        ON dtt.id = dt.type_id

     WHERE dtt.module_code = ?

     ORDER BY dt.updated_at DESC'

);



$listStmt->execute([$module]);



$templates = $listStmt->fetchAll(

    PDO::FETCH_ASSOC

);



$editData = null;

$editId = (int) ($_GET['edit'] ?? 0);

if ($editId > 0) {

    $editStmt = $pdo->prepare(

        'SELECT dt.* FROM document_templates dt

         INNER JOIN document_template_types dtt ON dtt.id = dt.type_id

         WHERE dt.id = ? AND dtt.module_code = ? LIMIT 1'

    );

    $editStmt->execute([$editId, $module]);

    $editData = $editStmt->fetch(PDO::FETCH_ASSOC) ?: null;

}



$moduleTitle =

    $module === 'invoice'

    ? 'Template Invoice'

    : 'Template Complain';



?>

<!doctype html>



<html lang="id">



<head>



<meta charset="utf-8">



<meta

    name="viewport"

    content="width=device-width, initial-scale=1"

>



<title><?= managerEscape($moduleTitle) ?></title>

<link rel="stylesheet" href="<?= managerEscape(asset('css/modern-banking.css')) ?>">

<link rel="stylesheet" href="<?= managerEscape(asset('css/remitops-v5.css')) ?>">



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

    padding: 22px 30px;

    background: #0c2b4d;

    color: #ffffff;

}



.header-content {

    max-width: 1250px;

    margin: auto;

    display: flex;

    justify-content: space-between;

    align-items: center;

}



.header h1 {

    margin: 0;

}



.header a {

    color: #ffffff;

    text-decoration: none;

}



.container {

    max-width: 1250px;

    margin: 22px auto;

    padding: 0 18px;

}



.card {

    padding: 20px;

    margin-bottom: 18px;

    background: #ffffff;

    border: 1px solid #dce5ef;

    border-radius: 15px;

}



.alert {

    padding: 12px;

    margin-bottom: 15px;

    border-radius: 9px;

}



.success {

    background: #e8f7ee;

    color: #176a3a;

}



.error {

    background: #feecec;

    color: #9d2626;

}



.grid {

    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 13px;

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



.button {

    display: inline-block;

    border: 0;

    border-radius: 8px;

    padding: 9px 13px;

    background: #ec2ed9;

    color: #ffffff;

    text-decoration: none;

    font-weight: 700;

    cursor: pointer;

}



.button.secondary {

    background: #edf2f8;

    color: #63245f;

}



.button.danger {

    background: #feecec;

    color: #9d269d;

}



table {

    width: 100%;

    border-collapse: collapse;

}



th,

td {

    padding: 11px;

    border-bottom: 1px solid #e1e8f0;

    text-align: left;

}



th {

    background: #f6f8fb;

}



.actions {

    display: flex;

    gap: 7px;

}



@media(max-width: 800px) {

    .grid {

        grid-template-columns: 1fr;

    }



    .table-wrap {

        overflow: auto;

    }

}







/* THEME OVERRIDE - PURPLE PINK */

.header{background:linear-gradient(135deg,#f4e7ff,#ffeaf4)!important;color:#673d83!important;border-bottom:1px solid #ead7f0!important;}

.header h1{color:#59366e!important;}

.header a{color:#68428a!important;background:#fff!important;border:1px solid #dfc8ed!important;border-radius:14px!important;}

body{background:#fff7fc!important;color:#51365f!important;}

.card{background:#fff!important;border:1px solid #eadcf0!important;border-radius:20px!important;box-shadow:0 10px 30px rgba(130,70,160,.08)!important;}

label{color:#624276!important;}

input,select,textarea{border:1px solid #dfcbea!important;border-radius:12px!important;color:#55366d!important;}

.button{background:linear-gradient(135deg,#8b5cf6,#ec4899)!important;color:#fff!important;border-radius:12px!important;}

.button.secondary{background:#f3e7ff!important;color:#68428a!important;}

.button.danger{background:#fff0f4!important;color:#d14b6a!important;}

table{border:1px solid #eadcf0!important;border-radius:16px!important;}

th{background:linear-gradient(135deg,#f0ddff,#ffe4f0)!important;color:#624276!important;}

td{border-bottom:1px solid #f1e5f5!important;color:#543667!important;}


/* ===== FINAL THEME OVERRIDE REMOVE BLUE/NAVY ===== */
body.module-page{background:#fff7fc!important;color:#51365f!important;}
.header{background:linear-gradient(135deg,#f4e7ff,#ffeaf4)!important;color:#673d83!important;border-bottom:1px solid #ead7f0!important;}
.header h1{color:#59366e!important;}
.header a{background:#fff!important;color:#68428a!important;border:1px solid #dfc8ed!important;border-radius:14px!important;}
.card{background:#fff!important;border:1px solid #eadcf0!important;border-radius:20px!important;}
input,select,textarea{background:#fff!important;border:1px solid #dfcbea!important;color:#55366d!important;}
.button,button.button,a.button,body .button{
background:linear-gradient(135deg,#8b5cf6,#ec4899)!important;
color:#fff!important;border:none!important;border-radius:12px!important;
box-shadow:0 8px 18px rgba(139,92,246,.25)!important;
}
.button.secondary,button.button.secondary,a.button.secondary{
background:#f3e7ff!important;color:#68428a!important;box-shadow:none!important;border:1px solid #dfc8ed!important;
}
.button.danger,button.button.danger{background:#fff0f4!important;color:#d14b6a!important;box-shadow:none!important;}
table{background:#fff!important;border:1px solid #eadcf0!important;}
th{background:linear-gradient(135deg,#f0ddff,#ffe4f0)!important;color:#624276!important;}
td{border-bottom:1px solid #f1e5f5!important;color:#543667!important;}
</style>



<link rel="stylesheet" href="<?= managerEscape(asset('css/standalone-docs.css?v=4.0.0')) ?>">

</head>



<body class="module-page module-complain">



<header class="header">



<div class="header-content">



<h1>

    <?= managerEscape($moduleTitle) ?>

</h1>



<a href="<?= managerEscape(managerAppUrl('/documents')) ?>">

    Kembali ke Dokumen & Template

</a>



</div>



</header>





<main class="container">



<?php if ($message !== ''): ?>



<div class="alert success">

    <?= managerEscape($message) ?>

</div>



<?php endif; ?>





<?php if ($error !== ''): ?>



<div class="alert error">

    <?= managerEscape($error) ?>

</div>



<?php endif; ?>





<?php if ($canWrite): ?>



<section class="card">



<h2>

    <?= $editData ? 'Edit Template' : 'Tambah Template' ?>

</h2>



<form

    method="post"

    enctype="multipart/form-data"

>



<input

    type="hidden"

    name="_token"

    value="<?= managerEscape(

        managerCsrf()

    ) ?>"

>



<input

    type="hidden"

    name="_action"

    value="save"

>



<input type="hidden" name="id" value="<?= (int) ($editData['id'] ?? 0) ?>">



<div class="grid">



<div>



<label>Jenis Template *</label>



<select

    name="type_id"

    required

>



<option value="">

    Pilih jenis

</option>



<?php foreach ($types as $type): ?>



<option value="<?= (int) $type['id'] ?>" <?= (int) ($editData['type_id'] ?? 0) === (int) $type['id'] ? 'selected' : '' ?>>



<?= managerEscape(

    $type['type_name']

) ?>



</option>



<?php endforeach; ?>



</select>



</div>





<div>



<label>Judul *</label>



<input

    name="title"

    required

    value="<?= managerEscape($editData['title'] ?? '') ?>"

>



</div>





<div>



<label>Versi</label>



<input

    name="version_label"

    value="<?= managerEscape($editData['version_label'] ?? '1.0') ?>"

>



</div>





<div>



<label>Status</label>



<select name="status">



<option value="draft" <?= ($editData['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>

    Draft

</option>



<option value="active" <?= ($editData['status'] ?? '') === 'active' ? 'selected' : '' ?>>

    Aktif

</option>



<option value="inactive" <?= ($editData['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>

    Nonaktif

</option>



</select>



</div>





<div class="full">



<label>Deskripsi</label>



<textarea name="description"><?= managerEscape($editData['description'] ?? '') ?></textarea>



</div>





<div class="full">



<label>File Template</label>



<input

    type="file"

    name="template_file"

    accept=".doc,.docx,.pdf,.xls,.xlsx"

>



</div>



</div>



<br>



<button class="button">

    <?= $editData ? 'Simpan Perubahan' : 'Simpan Template' ?>

</button>

<?php if ($editData): ?>

<a class="button secondary" href="<?= managerUrl($module) ?>">Batal Edit</a>

<?php endif; ?>



</form>



</section>



<?php endif; ?>





<section class="card table-wrap">



<h2>

    Daftar Template

</h2>



<table>



<thead>



<tr>

    <th>Jenis</th>

    <th>Judul</th>

    <th>Versi</th>

    <th>Status</th>

    <th>File</th>

    <th>Aksi</th>

</tr>



</thead>



<tbody>



<?php if ($templates === []): ?>



<tr>



<td colspan="6">

    Belum ada template.

</td>



</tr>



<?php endif; ?>





<?php foreach ($templates as $template): ?>



<tr>



<td>

    <?= managerEscape(

        $template['type_name']

    ) ?>

</td>



<td>



<strong>

    <?= managerEscape(

        $template['title']

    ) ?>

</strong>



<br>



<small>

    <?= managerEscape(

        $template['description']

        ?? ''

    ) ?>

</small>



</td>



<td>

    <?= managerEscape(

        $template['version_label']

    ) ?>

</td>



<td>

    <?= managerEscape(

        strtoupper(

            $template['status']

        )

    ) ?>

</td>



<td>

    <?= managerEscape(

        $template['file_original_name']

        ?: 'Belum ada file'

    ) ?>

</td>



<td>



<div class="actions">



<?php if (

    !empty($template['file_path'])

): ?>



<a

    class="button secondary"

    href="<?= managerUrl(

        $module,

        '&action=download&id='

        . (int) $template['id']

    ) ?>"

>

    Download

</a>



<?php endif; ?>





<?php if ($canWrite): ?>



<a class="button secondary" href="<?= managerUrl($module, '&edit=' . (int) $template['id']) ?>">Edit</a>



<form

    method="post"

    onsubmit="return confirm('Hapus template ini?')"

>



<input

    type="hidden"

    name="_token"

    value="<?= managerEscape(

        managerCsrf()

    ) ?>"

>



<input

    type="hidden"

    name="_action"

    value="delete"

>



<input

    type="hidden"

    name="id"

    value="<?= (int) $template['id'] ?>"

>



<button class="button danger">

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



<script src="<?= managerEscape(asset('js/smart-table.js')) ?>"></script>

</body>



</html>
