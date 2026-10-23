<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
$base = dirname(__DIR__);
require_once $base . '/app/Core/Env.php';
App\Core\Env::load($base . '/.env');

function envv(string $key, mixed $default = null): mixed {
    return App\Core\Env::get($key, $default);
}

$checks = [];
$checks[] = ['PHP version >= 8.0', version_compare(PHP_VERSION, '8.0.0', '>='), PHP_VERSION];
$checks[] = ['PDO extension', extension_loaded('pdo'), extension_loaded('pdo') ? 'aktif' : 'tidak aktif'];
$checks[] = ['PDO MySQL extension', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'aktif' : 'tidak aktif'];
$checks[] = ['File .env', is_file($base . '/.env'), is_file($base . '/.env') ? 'ditemukan' : 'belum dibuat'];
$checks[] = ['Folder log writable', is_writable($base . '/storage/logs'), $base . '/storage/logs'];

$dbError = null;
$userData = null;
try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', envv('DB_HOST','127.0.0.1'), envv('DB_PORT','3306'), envv('DB_DATABASE','AplikasiRemmitance'));
    $pdo = new PDO($dsn, (string) envv('DB_USERNAME','root'), (string) envv('DB_PASSWORD',''), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $checks[] = ['Koneksi database', true, (string) envv('DB_DATABASE','AplikasiRemmitance')];
    foreach (['roles','users','companies','transactions','complaints','invoices','audit_logs'] as $table) {
        $exists = (bool) $pdo->query("SHOW TABLES LIKE " . $pdo->quote($table))->fetchColumn();
        $checks[] = ["Tabel {$table}", $exists, $exists ? 'ada' : 'tidak ada'];
    }
    $cols = $pdo->query('SHOW COLUMNS FROM users')->fetchAll();
    $colNames = array_column($cols, 'Field');
    foreach (['role_id','username','email','password','is_active','must_change_password','last_login_at'] as $col) {
        $checks[] = ["Kolom users.{$col}", in_array($col,$colNames,true), in_array($col,$colNames,true)?'ada':'tidak ada'];
    }
    $stmt = $pdo->prepare('SELECT u.id,u.username,u.password,u.is_active,u.must_change_password,r.code role_code FROM users u JOIN roles r ON r.id=u.role_id WHERE u.username=? LIMIT 1');
    $stmt->execute(['superadmin']);
    $userData = $stmt->fetch();
    $checks[] = ['Akun superadmin', (bool)$userData, $userData ? 'ditemukan' : 'tidak ditemukan'];
    if ($userData) {
        $checks[] = ['Akun aktif', (int)$userData['is_active']===1, (string)$userData['is_active']];
        $checks[] = ['Password123! cocok', password_verify('Password123!', $userData['password']), password_verify('Password123!', $userData['password'])?'cocok':'tidak cocok'];
        $checks[] = ['Role super_admin', $userData['role_code']==='super_admin', (string)$userData['role_code']];
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
    $checks[] = ['Koneksi/struktur database', false, $dbError];
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Diagnostik Login</title><style>body{font-family:Arial,sans-serif;background:#f4f7fb;padding:30px;color:#172b4d}.wrap{max-width:1000px;margin:auto;background:#fff;padding:26px;border-radius:16px;box-shadow:0 8px 25px #0001}table{width:100%;border-collapse:collapse}th,td{padding:12px;border-bottom:1px solid #e6edf5;text-align:left}.ok{color:#087a3d;font-weight:700}.bad{color:#c62828;font-weight:700}code{background:#eef3f8;padding:3px 6px;border-radius:5px}</style><link rel="stylesheet" href="/AplikasiRemmitance/public/assets/css/theme-clean-global.css">
</head><body><div class="wrap"><h1>Diagnostik Login Fable RemitOps</h1><p>Hapus atau pindahkan file ini setelah masalah selesai.</p><table><thead><tr><th>Pemeriksaan</th><th>Status</th><th>Detail</th></tr></thead><tbody><?php foreach($checks as [$name,$ok,$detail]): ?><tr><td><?=htmlspecialchars($name)?></td><td class="<?=$ok?'ok':'bad'?>"><?=$ok?'OK':'GAGAL'?></td><td><?=htmlspecialchars((string)$detail)?></td></tr><?php endforeach; ?></tbody></table><p>Log aplikasi: <code><?=htmlspecialchars($base . '/storage/logs/app.log')?></code></p></div></body></html>
