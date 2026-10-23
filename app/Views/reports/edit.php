<?php

declare(strict_types=1);

$batch = is_array($batch ?? null) ? $batch : [];
?>

<div class="page-heading">
    <div>
        <span class="eyebrow dark">CRUD REPORT</span>
        <h2>Edit Metadata Report</h2>
        <p>Perubahan ini tidak mengubah isi transaksi di dalam file. Gunakan halaman detail untuk mengubah status setiap baris.</p>
    </div>
    <a class="btn btn-light" href="<?= url('/reports/' . $batch['id']) ?>"><i class="bi bi-arrow-left"></i>Kembali</a>
</div>

<div class="two-column-layout">
    <section class="form-card">
        <form method="post" action="<?= url('/reports/' . $batch['id']) ?>">
            <?= csrf_field() ?>
            <?= method_field('PUT') ?>

            <div class="form-grid two">
                <label>
                    <span class="form-label">Nama report *</span>
                    <input class="form-control" name="name" required value="<?= e($batch['name'] ?? '') ?>">
                </label>
                <label>
                    <span class="form-label">Bulan report *</span>
                    <input class="form-control" type="month" name="month_key" required value="<?= e($batch['month_key'] ?? '') ?>">
                </label>
                <label>
                    <span class="form-label">Tanggal report</span>
                    <input class="form-control" type="date" name="report_date" value="<?= e($batch['report_date'] ?? '') ?>">
                    <small>Tanggal harus berada pada bulan report yang dipilih.</small>
                </label>
                <label>
                    <span class="form-label">Nama tim</span>
                    <input class="form-control" name="team_name" value="<?= e($batch['team_name'] ?? '') ?>" placeholder="Contoh: PARK, GOSYEN, EKAWIRA">
                </label>
                <label>
                    <span class="form-label">Nama klien</span>
                    <input class="form-control" name="client_name" value="<?= e($batch['client_name'] ?? '') ?>">
                </label>
                <label>
                    <span class="form-label">File sumber</span>
                    <input class="form-control" value="<?= e($batch['source_filename'] ?? '') ?>" disabled>
                </label>
            </div>

            <div class="form-actions">
                <a class="btn btn-light" href="<?= url('/reports/' . $batch['id']) ?>">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i>Simpan Perubahan</button>
            </div>
        </form>
    </section>

    <aside class="panel-card help-card">
        <h3>Informasi report</h3>
        <dl class="report-meta-list">
            <div><dt>Batch</dt><dd>#<?= (int) ($batch['id'] ?? 0) ?></dd></div>
            <div><dt>Total baris</dt><dd><?= number_format((int) ($batch['total_rows'] ?? 0), 0, ',', '.') ?></dd></div>
            <div><dt>Terkirim</dt><dd><?= number_format((int) ($batch['sent_rows'] ?? 0), 0, ',', '.') ?></dd></div>
            <div><dt>Pending</dt><dd><?= number_format((int) ($batch['pending_rows'] ?? 0), 0, ',', '.') ?></dd></div>
            <div><dt>Dibuat</dt><dd><?= e(format_datetime($batch['created_at'] ?? null)) ?></dd></div>
            <div><dt>Diperbarui</dt><dd><?= e(format_datetime($batch['updated_at'] ?? null)) ?></dd></div>
        </dl>
        <div class="notice info"><i class="bi bi-info-circle"></i><span>Untuk mengganti file Excel, hapus report lama lalu upload file baru pada tanggal yang sama.</span></div>
    </aside>
</div>

