<?php

declare(strict_types=1);

// Local-only repair and diagnostic page for XAMPP development.
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Halaman ini hanya dapat dibuka dari komputer lokal.');
}

$base = dirname(__DIR__);
$messages = [];
$errors = [];

function h(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }

function loadEnvFile(string $file): array
{
    $env = [];
    if (!is_file($file)) return $env;
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (($value[0] ?? '') === '"' && str_ends_with($value, '"')) $value = substr($value, 1, -1);
        if (($value[0] ?? '') === "'" && str_ends_with($value, "'")) $value = substr($value, 1, -1);
        $env[$key] = $value;
    }
    return $env;
}

function ensureRuntime(string $base, array &$messages, array &$errors): void
{
    $dirs = [
        $base . '/storage/logs',
        $base . '/public/uploads',
        $base . '/public/uploads/complaints',
        $base . '/public/uploads/invoices',
        $base . '/public/uploads/reports',
        $base . '/public/uploads/transaction-jobs',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
            $errors[] = "Gagal membuat folder: {$dir}";
        }
    }
    $log = $base . '/storage/logs/app.log';
    if (!is_file($log) && @file_put_contents($log, '') === false) {
        $errors[] = 'Gagal membuat storage/logs/app.log.';
    }
    if (!is_file($base . '/.env') && is_file($base . '/.env.example')) {
        if (@copy($base . '/.env.example', $base . '/.env')) {
            $messages[] = 'File .env dibuat dari .env.example.';
        } else {
            $errors[] = 'Gagal membuat file .env. Salin .env.example menjadi .env secara manual.';
        }
    }
    if (!$errors) $messages[] = 'Folder runtime dan file log siap digunakan.';
}

function serverPdo(array $env): PDO
{
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $user = $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASSWORD'] ?? '';
    return new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function dbPdo(array $env): PDO
{
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_DATABASE'] ?? 'AplikasiRemmitance';
    $user = $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASSWORD'] ?? '';
    return new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS '
        . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.TABLES '
        . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
    );
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function repairLogin(array $env, array &$messages): void
{
    $database = $env['DB_DATABASE'] ?? 'AplikasiRemmitance';
    $server = serverPdo($env);
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = dbPdo($env);

    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        description VARCHAR(255) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    $pdo->exec("INSERT INTO roles (code,name,description) VALUES
        ('super_admin','Super Admin','Akses penuh dan pengelolaan user')
        ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        role_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(150) NOT NULL,
        username VARCHAR(80) NOT NULL UNIQUE,
        email VARCHAR(190) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        transaction_team TINYINT UNSIGNED NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        must_change_password TINYINT(1) NOT NULL DEFAULT 1,
        last_login_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_users_role (role_id),
        INDEX idx_users_team (transaction_team)
    ) ENGINE=InnoDB");

    $columns = [
        'transaction_team' => 'TINYINT UNSIGNED NULL AFTER password',
        'is_active' => 'TINYINT(1) NOT NULL DEFAULT 1 AFTER transaction_team',
        'must_change_password' => 'TINYINT(1) NOT NULL DEFAULT 1 AFTER is_active',
        'last_login_at' => 'DATETIME NULL AFTER must_change_password',
        'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER last_login_at',
        'updated_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at',
    ];
    foreach ($columns as $name => $definition) {
        if (!columnExists($pdo, 'users', $name)) {
            $pdo->exec("ALTER TABLE users ADD COLUMN `{$name}` {$definition}");
        }
    }
    $pdo->exec('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');

    $roleId = (int)$pdo->query("SELECT id FROM roles WHERE code='super_admin' LIMIT 1")->fetchColumn();
    $hash = password_hash('Password123!', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username=? OR email=? LIMIT 1');
    $stmt->execute(['superadmin', 'superadmin@fable.local']);
    $id = $stmt->fetchColumn();
    if ($id) {
        $update = $pdo->prepare('UPDATE users SET role_id=?, name=?, username=?, email=?, password=?, is_active=1, must_change_password=1 WHERE id=?');
        $update->execute([$roleId, 'Super Administrator', 'superadmin', 'superadmin@fable.local', $hash, $id]);
    } else {
        $insert = $pdo->prepare('INSERT INTO users (role_id,name,username,email,password,is_active,must_change_password) VALUES (?,?,?,?,?,1,1)');
        $insert->execute([$roleId, 'Super Administrator', 'superadmin', 'superadmin@fable.local', $hash]);
    }
    $messages[] = 'Struktur login diperbaiki dan password superadmin direset menjadi Password123!.';
}

function freshInstall(array $env, string $base, array &$messages): void
{
    $sqlFile = $base . '/database/install_all.sql';
    if (!is_file($sqlFile)) throw new RuntimeException('database/install_all.sql tidak ditemukan.');
    $server = serverPdo($env);
    // PDO MySQL can execute this installer as multi-statements when the attribute is enabled.
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $user = $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASSWORD'] ?? '';
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);
    $pdo->exec((string)file_get_contents($sqlFile));
    $messages[] = 'Instalasi database baru selesai. Seluruh tabel lama telah dibuat ulang.';
}


function migrateModules(array $env, string $base, array &$messages): void
{
    $sqlFiles = [
        $base . '/database/migration_modules.sql',
        $base . '/database/migration_report_transaction_v3.sql',
        $base . '/database/migration_upgrade_v4.sql',
        $base . '/database/migration_upgrade_v5.sql',
    ];
    foreach ($sqlFiles as $sqlFile) {
        if (!is_file($sqlFile)) {
            throw new RuntimeException(basename($sqlFile) . ' tidak ditemukan.');
        }
    }

    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $user = $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASSWORD'] ?? '';
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);
    foreach ($sqlFiles as $sqlFile) {
        $pdo->exec((string) file_get_contents($sqlFile));
    }
    $messages[] = 'Seluruh tabel Dokumen, Report Bulanan, konverter Transaksi, histori kurs, master rekening payer, dan histori complain berhasil dilengkapi tanpa menghapus data utama.';
}

ensureRuntime($base, $messages, $errors);
$env = loadEnvFile($base . '/.env');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'runtime') {
            ensureRuntime($base, $messages, $errors);
        } elseif ($action === 'repair_login') {
            repairLogin($env, $messages);
        } elseif ($action === 'migrate_modules') {
            migrateModules($env, $base, $messages);
        } elseif ($action === 'fresh_install' && ($_POST['confirm'] ?? '') === 'HAPUS') {
            freshInstall($env, $base, $messages);
            repairLogin($env, $messages);
        } else {
            $errors[] = 'Aksi tidak valid atau konfirmasi instalasi baru belum tepat.';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
        @file_put_contents($base . '/storage/logs/app.log', '[' . date('Y-m-d H:i:s') . '] SETUP: ' . $e . PHP_EOL, FILE_APPEND);
    }
}

$checks = [];
$checks[] = ['Versi PHP minimal 8.0', version_compare(PHP_VERSION, '8.0.0', '>='), PHP_VERSION];
$checks[] = ['Ekstensi PDO MySQL', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'aktif' : 'tidak aktif'];
$checks[] = ['Ekstensi ZIP untuk XLSX', extension_loaded('zip'), extension_loaded('zip') ? 'aktif' : 'aktifkan extension=zip di php.ini'];
$checks[] = ['Ekstensi DOM untuk XLSX', extension_loaded('dom'), extension_loaded('dom') ? 'aktif' : 'aktifkan extension=dom di php.ini'];
$checks[] = ['Ekstensi cURL untuk Kurs BI', extension_loaded('curl'), extension_loaded('curl') ? 'aktif' : 'aktifkan extension=curl di php.ini'];
$checks[] = ['Ekstensi OpenSSL untuk HTTPS BI', extension_loaded('openssl'), extension_loaded('openssl') ? 'aktif' : 'aktifkan extension=openssl di php.ini'];
$checks[] = ['File .env', is_file($base . '/.env'), $base . '/.env'];
$checks[] = ['Folder cache kurs', is_dir($base . '/storage/cache'), $base . '/storage/cache'];
$checks[] = ['Folder log', is_dir($base . '/storage/logs'), $base . '/storage/logs'];
$checks[] = ['File app.log', is_file($base . '/storage/logs/app.log'), $base . '/storage/logs/app.log'];

try {
    $pdo = dbPdo($env);
    $checks[] = ['Koneksi database', true, $env['DB_DATABASE'] ?? 'AplikasiRemmitance'];
    foreach (['roles','users','companies','transactions','complaints','invoices','audit_logs','document_template_types','document_templates','invoice_template_documents','si_templates','si_documents','si_document_items','report_batches','report_rows','transaction_jobs','payer_accounts','complaint_histories','fx_rate_history','analysis_runs','analysis_weights','analysis_results','spk_sources','spk_records','spk_criteria','spk_subcriteria','spk_alternatives','spk_assessments','spk_runs','spk_weights','spk_results'] as $table) {
        $checks[] = ["Tabel {$table}", tableExists($pdo, $table), tableExists($pdo, $table) ? 'ada' : 'belum ada'];
    }
    if (tableExists($pdo, 'users') && tableExists($pdo, 'roles')) {
        $stmt = $pdo->prepare('SELECT u.username,u.password,u.is_active,r.code role_code FROM users u JOIN roles r ON r.id=u.role_id WHERE u.username=? LIMIT 1');
        $stmt->execute(['superadmin']);
        $user = $stmt->fetch();
        $checks[] = ['Akun superadmin', (bool)$user, $user ? 'ditemukan' : 'belum ada'];
        if ($user) {
            $checks[] = ['Akun aktif', (int)$user['is_active'] === 1, (string)$user['is_active']];
            $checks[] = ['Password Password123!', password_verify('Password123!', $user['password']), password_verify('Password123!', $user['password']) ? 'cocok' : 'tidak cocok'];
            $checks[] = ['Role super_admin', $user['role_code'] === 'super_admin', (string)$user['role_code']];
        }
    }
} catch (Throwable $e) {
    $checks[] = ['Koneksi database', false, $e->getMessage()];
}

$logFile = $base . '/storage/logs/app.log';
$log = is_file($logFile) ? (string)file_get_contents($logFile) : '';
if (strlen($log) > 20000) $log = substr($log, -20000);
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>System Check RemitGo!</title>
<style>body{font-family:Arial,sans-serif;background:#f3f6fb;color:#172b4d;padding:24px}.wrap{max-width:1100px;margin:auto}.card{background:#fff;border-radius:16px;padding:22px;margin-bottom:18px;box-shadow:0 8px 25px #12233b12}h1{margin-top:0}table{width:100%;border-collapse:collapse}th,td{padding:11px;border-bottom:1px solid #e5ebf3;text-align:left}.ok{color:#087a3d;font-weight:700}.bad{color:#c62828;font-weight:700}.btn{display:inline-block;border:0;border-radius:9px;padding:11px 16px;background:#2367e8;color:#fff;font-weight:700;cursor:pointer}.danger{background:#c62828}.msg{padding:11px;border-radius:8px;background:#e8f6ec;margin:8px 0}.err{padding:11px;border-radius:8px;background:#fdeaea;color:#9b1c1c;margin:8px 0}code,pre{background:#eef3f8;padding:8px;border-radius:8px;overflow:auto}.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}@media(max-width:800px){.grid{grid-template-columns:1fr}}</style><link rel="stylesheet" href="/AplikasiRemmitance/public/assets/css/theme-clean-global.css">
</head><body><div class="wrap">
<div class="card"><h1>System Check & Repair — RemitGo!</h1><p>Halaman ini hanya aktif pada localhost. Hapus file <code>public/system_check.php</code> setelah aplikasi berjalan.</p>
<?php foreach($messages as $m): ?><div class="msg"><?=h($m)?></div><?php endforeach; ?>
<?php foreach($errors as $m): ?><div class="err"><?=h($m)?></div><?php endforeach; ?></div>
<div class="grid"><div class="card"><h2>Status Sistem</h2><table><tr><th>Pemeriksaan</th><th>Status</th><th>Detail</th></tr><?php foreach($checks as [$name,$ok,$detail]): ?><tr><td><?=h($name)?></td><td class="<?=$ok?'ok':'bad'?>"><?=$ok?'OK':'GAGAL'?></td><td><?=h($detail)?></td></tr><?php endforeach; ?></table></div>
<div><div class="card"><h2>Perbaikan Aman</h2><form method="post"><input type="hidden" name="action" value="runtime"><button class="btn">Buat .env, Folder Log & Upload</button></form><br><form method="post"><input type="hidden" name="action" value="repair_login"><button class="btn">Perbaiki Login & Reset Superadmin</button></form><br><form method="post"><input type="hidden" name="action" value="migrate_modules"><button class="btn">Lengkapi Tabel Modul</button></form><p>Login setelah perbaikan: <b>superadmin</b> / <b>Password123!</b></p></div>
<div class="card"><h2>Instalasi Baru</h2><p><b>PERINGATAN:</b> menghapus dan membuat ulang seluruh tabel aplikasi.</p><form method="post"><input type="hidden" name="action" value="fresh_install"><input name="confirm" placeholder="Ketik HAPUS" required> <button class="btn danger">Instal Database Baru</button></form></div></div></div>
<div class="card"><h2>Log Aplikasi</h2><p>File: <code><?=h($logFile)?></code></p><pre><?=h($log === '' ? 'Log masih kosong.' : $log)?></pre></div>
<div class="card"><a href="<?=h(($env['APP_URL'] ?? 'http://localhost/AplikasiRemmitance/public') . '/login')?>">Kembali ke halaman login</a></div>
</div></body></html>
