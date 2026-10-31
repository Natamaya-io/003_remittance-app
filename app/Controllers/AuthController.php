<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;

final class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (Auth::check()) redirect('/');
        $this->view('auth/login', ['title' => 'Masuk'], 'guest');
    }

    public function login(): void
    {
        $login = trim((string) ($_POST['login'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        remember_old(['login' => $login]);
        if ($login === '' || $password === '') {
            flash('danger', 'Username/email dan password wajib diisi.');
            redirect('/login');
        }
        try {
            $authenticated = Auth::attempt($login, $password);
        } catch (\Throwable $e) {
            $logDir = BASE_PATH . '/storage/logs';
            if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
            @file_put_contents($logDir . '/app.log', '[' . date('Y-m-d H:i:s') . '] LOGIN: ' . $e . PHP_EOL, FILE_APPEND);
            $message = 'Login gagal karena konfigurasi database belum lengkap. Buka /public/system_check.php untuk pemeriksaan dan perbaikan.';
            if ((bool) \App\Core\App::config('app.debug', false)) {
                $message .= ' Detail: ' . $e->getMessage();
            }
            flash('danger', $message);
            redirect('/login');
        }
        if (!$authenticated) {
            flash('danger', 'Kredensial tidak valid atau akun tidak aktif. Jalankan system_check.php bila password sudah benar.');
            redirect('/login');
        }
        clear_old();
        AuditService::log('login', 'auth', Auth::id());
        flash('success', 'Selamat datang, ' . Auth::user()['name'] . '.');
        redirect('/');
    }

    public function logout(): void
    {
        AuditService::log('logout', 'auth', Auth::id());
        Auth::logout();
        redirect('/login');
    }
}
