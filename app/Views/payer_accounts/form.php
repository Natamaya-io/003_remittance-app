<?php declare(strict_types=1);

$isEdit = is_array($account ?? null);
$record = $isEdit ? $account : [];
$value = static fn (string $key, mixed $default = ''): mixed => old($key, $record[$key] ?? $default);
$action = $isEdit ? url('/payer-accounts/' . (int) $record['id']) : url('/payer-accounts');
?>
<div class="page-actions flex items-center justify-end mb-4">
    <a class="btn-secondary" href="<?= url('/payer-accounts') ?>">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>
</div>

<form method="post" action="<?= $action ?>" class="stack-lg space-y-6">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><?= method_field('PUT') ?><?php endif; ?>

    <section class="form-card">
        <div class="section-title flex items-center gap-2 mb-4">
            <i class="bi bi-bank text-lg text-gray-800"></i>
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900 m-0">Identitas Rekening</h3>
                <?= render_tooltip(
                    'Data bank dan rekening yang digunakan sebagai sumber dana.',
                    '<span class="info-toggle-btn cursor-help" aria-label="Info identitas"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                    ['placement' => 'top']
                ) ?>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Perusahaan *</label>
                <select class="custom-form-select" name="company_id" required>
                    <option value="">Pilih PT</option>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?= (int) $company['id'] ?>" <?= selected((int) $value('company_id'), (int) $company['id']) ?>><?= e($company['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select class="custom-form-select" name="status">
                    <option value="active" <?= selected($value('status', 'active'), 'active') ?>>Aktif</option>
                    <option value="inactive" <?= selected($value('status', 'active'), 'inactive') ?>>Nonaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Rekening *</label>
                <input class="custom-form-input" name="account_no" value="<?= e($value('account_no')) ?>" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Rekening</label>
                <input class="custom-form-input" name="account_name" value="<?= e($value('account_name')) ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Mata Uang *</label>
                <input class="custom-form-input uppercase" maxlength="3" name="currency" value="<?= e($value('currency', 'IDR')) ?>" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Bank *</label>
                <input class="custom-form-input" name="bank_name" value="<?= e($value('bank_name')) ?>" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Cabang Bank</label>
                <input class="custom-form-input" name="bank_branch" value="<?= e($value('bank_branch')) ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">SWIFT</label>
                <input class="custom-form-input uppercase" name="swift" value="<?= e($value('swift')) ?>">
            </div>
        </div>
    </section>

    <section class="form-card">
        <div class="section-title flex items-center gap-2 mb-4">
            <i class="bi bi-buildings text-lg text-gray-800"></i>
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900 m-0">Alamat dan Output</h3>
                <?= render_tooltip(
                    'Dipakai untuk melengkapi format file bank dan komunikasi operasional.',
                    '<span class="info-toggle-btn cursor-help" aria-label="Info alamat"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                    ['placement' => 'top']
                ) ?>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Email Output</label>
                <input type="email" class="custom-form-input" name="email" value="<?= e($value('email')) ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kota</label>
                <input class="custom-form-input" name="city" value="<?= e($value('city')) ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Negara</label>
                <input class="custom-form-input" name="country" value="<?= e($value('country', 'INDONESIA')) ?>">
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat</label>
                <textarea class="custom-form-textarea" name="address" rows="3"><?= e($value('address')) ?></textarea>
            </div>
        </div>
        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
            <input type="checkbox" class="rounded border-gray-300 text-[#315B8C] focus:ring-[#315B8C]" name="is_default" id="is_default" value="1" <?= checked((int) $value('is_default', 0) === 1) ?>>
            <div class="flex items-center gap-1.5">
                <label for="is_default" class="text-xs font-semibold text-gray-800 cursor-pointer">Jadikan rekening default</label>
                <?= render_tooltip(
                    'Rekening ini diprioritaskan pada daftar pilihan transaksi untuk perusahaan terkait.',
                    '<span class="info-toggle-btn cursor-help" aria-label="Info default"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                    ['placement' => 'top']
                ) ?>
            </div>
        </div>
    </section>

    <div class="form-actions sticky-actions flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
        <a class="btn-secondary" href="<?= url('/payer-accounts') ?>">
            <span>Batal</span>
        </a>
        <button class="btn-primary" type="submit">
            <i class="bi bi-floppy"></i>
            <span>Simpan Rekening</span>
        </button>
    </div>
</form>
