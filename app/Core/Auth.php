<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

/**
 * AUTH FIX V5 - POSITIONAL PLACEHOLDERS
 *
 * Seluruh query memakai placeholder tanda tanya (?) agar kompatibel
 * dengan PHP 8.0, PDO MySQL, dan MariaDB pada XAMPP.
 */
final class Auth
{
    private static ?array $cachedUser = null;

    /**
     * Mencoba login menggunakan username atau email.
     */
    public static function attempt(string $login, string $password): bool
    {
        $login = trim($login);

        if ($login === '' || $password === '') {
            return false;
        }

        /*
         * Dua placeholder dan dua parameter.
         * Jangan menggunakan placeholder bernama yang sama dua kali.
         */
        $sql = '
            SELECT
                u.id,
                u.role_id,
                u.name,
                u.username,
                u.email,
                u.password,
                u.transaction_team,
                u.is_active,
                u.must_change_password,
                u.last_login_at,
                u.created_at,
                u.updated_at,
                r.code AS role_code,
                r.name AS role_name
            FROM users AS u
            INNER JOIN roles AS r
                ON r.id = u.role_id
            WHERE
                (u.username = ? OR u.email = ?)
                AND u.is_active = 1
            LIMIT 1
        ';

        try {
            $pdo = App::db();
            $stmt = $pdo->prepare($sql);

            /*
             * Query memiliki dua tanda tanya.
             * Execute juga harus memiliki tepat dua nilai.
             */
            $stmt->execute([
                $login,
                $login,
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            self::writeLog(
                'AUTH_QUERY_ERROR: '
                . $exception->getMessage()
                . ' | File: '
                . $exception->getFile()
                . ':'
                . $exception->getLine()
            );

            throw $exception;
        }

        if (!is_array($user)) {
            return false;
        }

        if (!isset($user['password'])) {
            self::writeLog('Password hash user tidak ditemukan.');

            return false;
        }

        if (!password_verify($password, (string) $user['password'])) {
            return false;
        }

        /*
         * Regenerasi session untuk mencegah session fixation.
         */
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role_code'] = (string) $user['role_code'];
        $_SESSION['login_time'] = time();

        self::$cachedUser = $user;

        /*
         * Update last login menggunakan satu placeholder dan satu parameter.
         * Kegagalan update metadata tidak membatalkan login.
         */
        try {
            $update = App::db()->prepare(
                'UPDATE users
                 SET last_login_at = NOW()
                 WHERE id = ?'
            );

            $update->execute([
                (int) $user['id'],
            ]);
        } catch (Throwable $exception) {
            self::writeLog(
                'LAST_LOGIN_UPDATE_ERROR: '
                . $exception->getMessage()
            );
        }

        return true;
    }

    /**
     * Mengambil data pengguna yang sedang login.
     */
    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $sql = '
            SELECT
                u.id,
                u.role_id,
                u.name,
                u.username,
                u.email,
                u.password,
                u.transaction_team,
                u.is_active,
                u.must_change_password,
                u.last_login_at,
                u.created_at,
                u.updated_at,
                r.code AS role_code,
                r.name AS role_name
            FROM users AS u
            INNER JOIN roles AS r
                ON r.id = u.role_id
            WHERE
                u.id = ?
                AND u.is_active = 1
            LIMIT 1
        ';

        try {
            $stmt = App::db()->prepare($sql);

            /*
             * Query memiliki satu tanda tanya.
             * Execute memiliki tepat satu nilai.
             */
            $stmt->execute([
                (int) $_SESSION['user_id'],
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            self::$cachedUser = is_array($user)
                ? $user
                : null;
        } catch (Throwable $exception) {
            self::writeLog(
                'AUTH_USER_QUERY_ERROR: '
                . $exception->getMessage()
            );

            self::logout();

            return null;
        }

        return self::$cachedUser;
    }

    /**
     * Mendapatkan ID pengguna.
     */
    public static function id(): ?int
    {
        $user = self::user();

        if ($user === null) {
            return null;
        }

        return (int) $user['id'];
    }

    /**
     * Memeriksa status login.
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Menghapus session login.
     */
    public static function logout(): void
    {
        self::$cachedUser = null;
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                (bool) ($params['secure'] ?? false),
                (bool) ($params['httponly'] ?? true)
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Memeriksa hak akses.
     */
    public static function can(string $permission): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        return Policy::allows(
            (string) $user['role_code'],
            $permission
        );
    }

    /**
     * Memastikan pengguna memiliki hak akses.
     */
    public static function requirePermission(string $permission): void
    {
        if (!self::check()) {
            redirect('/login');
            exit;
        }

        if (!self::can($permission)) {
            http_response_code(403);

            View::render(
                'errors/403',
                [
                    'title' => 'Akses Ditolak',
                ]
            );

            exit;
        }
    }

    /**
     * Mendapatkan nomor tim transaksi pengguna.
     */
    public static function transactionTeam(): ?int
    {
        $user = self::user();

        if (
            $user === null
            || !array_key_exists('transaction_team', $user)
            || $user['transaction_team'] === null
            || $user['transaction_team'] === ''
        ) {
            return null;
        }

        return (int) $user['transaction_team'];
    }

    /**
     * Memeriksa apakah user adalah admin transaksi.
     */
    public static function isTransactionAdmin(): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        $roleCode = (string) ($user['role_code'] ?? '');

        return str_starts_with(
            $roleCode,
            'admin_transaksi_'
        );
    }

    /**
     * Menulis error ke storage/logs/app.log.
     */
    private static function writeLog(string $message): void
    {
        $basePath = defined('BASE_PATH')
            ? BASE_PATH
            : dirname(__DIR__, 2);

        $logDirectory = $basePath . '/storage/logs';
        $logFile = $logDirectory . '/app.log';

        if (!is_dir($logDirectory)) {
            @mkdir(
                $logDirectory,
                0775,
                true
            );
        }

        $content = sprintf(
            "[%s] AUTH FIX V5: %s%s",
            date('Y-m-d H:i:s'),
            $message,
            PHP_EOL
        );

        @file_put_contents(
            $logFile,
            $content,
            FILE_APPEND
        );
    }
}