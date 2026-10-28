<?php declare(strict_types=1); ?>
<div class="row g-4">
    <div class="col-xl-6">
        <div class="form-card">
            <div class="section-title flex items-center gap-2 mb-4">
                <i class="bi bi-person-circle text-lg text-gray-800"></i>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 m-0">Profil Pengguna</h3>
                    <span class="text-xs text-gray-500"><?= e($user['role_name']) ?></span>
                </div>
            </div>
            <form method="post" action="<?= url('/profile') ?>" class="space-y-4">
                <?= csrf_field() . method_field('PUT') ?>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama</label>
                    <input class="custom-form-input" name="name" value="<?= e($user['name']) ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" class="custom-form-input" name="email" value="<?= e($user['email']) ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
                    <input class="custom-form-input opacity-60 cursor-not-allowed" value="<?= e($user['username']) ?>" disabled>
                </div>
                <div class="pt-2">
                    <button class="btn-primary" type="submit">
                        <i class="bi bi-floppy"></i>
                        <span>Simpan Profil</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="form-card">
            <div class="section-title flex items-center gap-2 mb-4">
                <i class="bi bi-key text-lg text-gray-800"></i>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold text-gray-900 m-0">Ubah Password</h3>
                    <?= render_tooltip(
                        'Gunakan minimal delapan karakter.',
                        '<span class="info-toggle-btn cursor-help" aria-label="Info password"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                        ['placement' => 'top']
                    ) ?>
                </div>
            </div>
            <form method="post" action="<?= url('/profile/password') ?>" class="space-y-4">
                <?= csrf_field() . method_field('PUT') ?>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password Saat Ini</label>
                    <input type="password" class="custom-form-input" name="current_password" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password Baru</label>
                    <input type="password" class="custom-form-input" name="new_password" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" class="custom-form-input" name="new_password_confirmation" required>
                </div>
                <div class="pt-2">
                    <button class="btn-primary" type="submit">
                        <i class="bi bi-shield-check"></i>
                        <span>Ubah Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
