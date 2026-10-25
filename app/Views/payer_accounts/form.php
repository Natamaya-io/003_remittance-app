<?php

declare(strict_types=1);

$isEdit = is_array($account ?? null);
$record = $isEdit ? $account : [];
$value = static fn (string $key, mixed $default = ''): mixed => old($key, $record[$key] ?? $default);
$action = $isEdit ? url('/payer-accounts/' . (int) $record['id']) : url('/payer-accounts');
?>
<div class="page-actions d-flex justify-content-end mb-4">
    <a class="btn btn-light" href="<?= url('/payer-accounts') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<form method="post" action="<?= $action ?>" class="stack-lg">
    <?= csrf_field() ?><?php if ($isEdit): ?><?= method_field('PUT') ?><?php endif; ?>
    <section class="form-card">
        <div class="section-title"><i class="bi bi-bank"></i><div><h3>Identitas Rekening</h3><p>Data bank dan rekening yang digunakan sebagai sumber dana.</p></div></div>
        <div class="form-grid three">
            <label class="span-2"><span class="form-label">Perusahaan *</span><select class="form-select" name="company_id" required><option value="">Pilih PT</option><?php foreach ($companies as $company): ?><option value="<?= (int) $company['id'] ?>" <?= selected((int) $value('company_id'), (int) $company['id']) ?>><?= e($company['name']) ?></option><?php endforeach; ?></select></label>
            <label><span class="form-label">Status</span><select class="form-select" name="status"><option value="active" <?= selected($value('status', 'active'), 'active') ?>>Aktif</option><option value="inactive" <?= selected($value('status', 'active'), 'inactive') ?>>Nonaktif</option></select></label>
            <label><span class="form-label">Nomor Rekening *</span><input class="form-control" name="account_no" value="<?= e($value('account_no')) ?>" required></label>
            <label><span class="form-label">Nama Rekening</span><input class="form-control" name="account_name" value="<?= e($value('account_name')) ?>"></label>
            <label><span class="form-label">Mata Uang *</span><input class="form-control text-uppercase" maxlength="3" name="currency" value="<?= e($value('currency', 'IDR')) ?>" required></label>
            <label><span class="form-label">Nama Bank *</span><input class="form-control" name="bank_name" value="<?= e($value('bank_name')) ?>" required></label>
            <label><span class="form-label">Cabang Bank</span><input class="form-control" name="bank_branch" value="<?= e($value('bank_branch')) ?>"></label>
            <label><span class="form-label">SWIFT</span><input class="form-control text-uppercase" name="swift" value="<?= e($value('swift')) ?>"></label>
        </div>
    </section>

    <section class="form-card">
        <div class="section-title"><i class="bi bi-buildings"></i><div><h3>Alamat dan Output</h3><p>Dipakai untuk melengkapi format file bank dan komunikasi operasional.</p></div></div>
        <div class="form-grid three">
            <label><span class="form-label">Email Output</span><input type="email" class="form-control" name="email" value="<?= e($value('email')) ?>"></label>
            <label><span class="form-label">Kota</span><input class="form-control" name="city" value="<?= e($value('city')) ?>"></label>
            <label><span class="form-label">Negara</span><input class="form-control" name="country" value="<?= e($value('country', 'INDONESIA')) ?>"></label>
            <label class="span-3"><span class="form-label">Alamat</span><textarea class="form-control" name="address" rows="3"><?= e($value('address')) ?></textarea></label>
        </div>
        <label class="switch-field mt-3"><input type="checkbox" name="is_default" value="1" <?= checked((int) $value('is_default', 0) === 1) ?>><span></span><div><strong>Jadikan rekening default</strong><small>Rekening ini diprioritaskan pada daftar pilihan transaksi untuk perusahaan terkait.</small></div></label>
    </section>

    <div class="form-actions sticky-actions"><a class="btn btn-light" href="<?= url('/payer-accounts') ?>">Batal</a><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i>Simpan Rekening</button></div>
</form>
