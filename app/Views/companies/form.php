<?php declare(strict_types=1);

$isEdit = (bool) $company;
?>
<div class="form-card">
    <form method="post" action="<?= $isEdit ? url('/companies/' . $company['id']) : url('/companies') ?>">
        <?= csrf_field() ?>
        <?php if ($isEdit) echo method_field('PUT'); ?>

        <div class="section-title flex items-center gap-2 mb-4">
            <i class="bi bi-buildings text-lg text-gray-800"></i>
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900 m-0">Informasi Perusahaan</h3>
                <?= render_tooltip(
                    'Data PT dapat ditambahkan dan digunakan sebagai alternatif analisis.',
                    '<span class="info-toggle-btn cursor-help" aria-label="Info perusahaan"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                    ['placement' => 'top']
                ) ?>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kode PT *</label>
                <input class="custom-form-input" name="code" value="<?= e(old('code', $company['code'] ?? '')) ?>" required>
            </div>
            <div class="col-md-9">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Perusahaan *</label>
                <input class="custom-form-input" name="name" value="<?= e(old('name', $company['name'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">NPWP / Tax ID</label>
                <input class="custom-form-input" name="tax_id" value="<?= e(old('tax_id', $company['tax_id'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kontak</label>
                <input class="custom-form-input" name="contact_name" value="<?= e(old('contact_name', $company['contact_name'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Telepon</label>
                <input class="custom-form-input" name="contact_phone" value="<?= e(old('contact_phone', $company['contact_phone'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                <input class="custom-form-input" type="email" name="email" value="<?= e(old('email', $company['email'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select class="custom-form-select" name="status">
                    <option value="active" <?= selected(old('status', $company['status'] ?? 'active'), 'active') ?>>Aktif</option>
                    <option value="inactive" <?= selected(old('status', $company['status'] ?? ''), 'inactive') ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-12">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat</label>
                <textarea class="custom-form-textarea" name="address" rows="3"><?= e(old('address', $company['address'] ?? '')) ?></textarea>
            </div>
        </div>

        <div class="form-actions flex items-center justify-end gap-3 mt-4 pt-3 border-t border-gray-100">
            <a class="btn-secondary" href="<?= url('/companies') ?>">
                <span>Batal</span>
            </a>
            <button class="btn-primary" type="submit">
                <i class="bi bi-floppy"></i>
                <span>Simpan</span>
            </button>
        </div>
    </form>
</div>
