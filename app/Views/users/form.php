<?php declare(strict_types=1);

$e = (bool) $user;
$v = fn($k, $d = '') => old($k, $user[$k] ?? $d);
?>
<div class="form-card">
    <form method="post" action="<?= $e ? url('/users/' . $user['id']) : url('/users') ?>">
        <?= csrf_field() ?>
        <?php if ($e) echo method_field('PUT'); ?>

        <div class="section-title flex items-center gap-2 mb-4">
            <i class="bi bi-person-gear text-lg text-gray-800"></i>
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900 m-0">Akun dan Hak Akses</h3>
                <?= render_tooltip(
                    'Setiap user memperoleh menu sesuai role.',
                    '<span class="info-toggle-btn cursor-help" aria-label="Info hak akses"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                    ['placement' => 'top']
                ) ?>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama *</label>
                <input class="custom-form-input" name="name" value="<?= e($v('name')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Username *</label>
                <input class="custom-form-input" name="username" value="<?= e($v('username')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Email *</label>
                <input type="email" class="custom-form-input" name="email" value="<?= e($v('email')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Role *</label>
                <select class="custom-form-select" name="role_id" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= selected($v('role_id'), $r['id']) ?>><?= e($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tim Transaksi</label>
                <select class="custom-form-select" name="transaction_team">
                    <option value="">Tidak ada</option>
                    <?php foreach ([1, 2, 3, 4] as $n): ?>
                        <option value="<?= $n ?>" <?= selected($v('transaction_team'), $n) ?>>Tim <?= $n ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <div class="flex items-center gap-1.5 mb-1">
                    <label class="block text-xs font-semibold text-gray-700"><?= $e ? 'Password Baru' : 'Password *' ?></label>
                    <?= render_tooltip(
                        $e ? 'Kosongkan bila tidak diubah.' : 'Minimal 8 karakter.',
                        '<span class="info-toggle-btn cursor-help" aria-label="Info password"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                        ['placement' => 'top']
                    ) ?>
                </div>
                <input type="password" class="custom-form-input" name="password" <?= $e ? '' : 'required' ?>>
            </div>
            <div class="col-12">
                <div class="form-check form-switch flex items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_active" id="active" <?= checked((bool) $v('is_active', 1)) ?>>
                    <label class="form-check-label text-xs font-medium text-gray-700" for="active">Akun aktif</label>
                </div>
            </div>
        </div>

        <div class="form-actions flex items-center justify-end gap-3 mt-4 pt-3 border-t border-gray-100">
            <a class="btn-secondary" href="<?= url('/users') ?>">
                <span>Batal</span>
            </a>
            <button class="btn-primary" type="submit">
                <i class="bi bi-floppy"></i>
                <span>Simpan User</span>
            </button>
        </div>
    </form>
</div>
