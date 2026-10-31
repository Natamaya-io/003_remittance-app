<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;

final class ProfileController extends BaseController
{
    public function index(): void
    {
        $this->permission('dashboard.view');
        $this->view('profile/index', [
            'title' => 'Profil',
            'user' => Auth::user(),
        ]);
    }

    public function update(): void
    {
        $this->permission('dashboard.view');
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($name === '' || $email === '') {
            flash('danger', 'Nama dan email wajib diisi.');
            redirect('/profile');
        }

        App::db()->prepare(
            'UPDATE users SET name = ?, email = ?, updated_at = NOW() WHERE id = ?'
        )->execute([$name, $email, Auth::id()]);

        Auth::refresh();
        AuditService::log('update', 'profile', Auth::id(), null, ['name' => $name, 'email' => $email]);
        flash('success', 'Profil berhasil diperbarui.');
        redirect('/profile');
    }

    public function password(): void
    {
        $this->permission('dashboard.view');
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirmation'] ?? '');
        $returnTo = trim((string) ($_POST['return_to'] ?? ''));
        if ($returnTo === '' || !str_starts_with($returnTo, '/') || str_starts_with($returnTo, '//')) {
            $returnTo = '/profile';
        }

        if (!password_verify($current, (string) (Auth::user()['password'] ?? ''))) {
            flash('danger', 'Password saat ini salah.');
            redirect($returnTo);
        }

        if (strlen($new) < 8 || $new !== $confirm) {
            flash('danger', 'Password baru minimal 8 karakter dan konfirmasi harus sama.');
            redirect($returnTo);
        }

        $wasInitial = (int) (Auth::user()['must_change_password'] ?? 0) === 1;

        App::db()->prepare(
            'UPDATE users SET password = ?, must_change_password = 0, updated_at = NOW() WHERE id = ?'
        )->execute([password_hash($new, PASSWORD_DEFAULT), Auth::id()]);

        Auth::refresh();
        AuditService::log('password_change', 'profile', Auth::id());

        if ($wasInitial) {
            flash('success', 'Kata sandi berhasil diperbarui. Status akun Anda kini aktif penuh dan kata sandi baru telah tersimpan.');
            redirect('/');
        }

        flash('success', 'Password berhasil diubah.');
        redirect($returnTo);
    }
}
