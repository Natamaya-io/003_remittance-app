<?php
$editing = (bool) $complaint;
$value = static fn (string $key, mixed $default = ''): mixed => old($key, $complaint[$key] ?? ($_GET[$key] ?? $default));
$selectedReport = (string) $value('report_row_id');
$histories = is_array($histories ?? null) ? $histories : [];
?>
<div class="page-actions d-flex justify-content-end mb-4">
    <a class="btn btn-light" href="<?= url('/complaints') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="two-column-layout complaint-workspace">
<section class="form-card">
<form method="post" enctype="multipart/form-data" action="<?= $editing ? url('/complaints/' . $complaint['id']) : url('/complaints') ?>">
    <?= csrf_field() ?><?php if ($editing) echo method_field('PUT'); ?>

    <div class="section-title"><i class="bi bi-link-45deg"></i><div><h3>Sumber kasus</h3><p>Pilih baris report untuk mengisi perusahaan, subjek, dan detail secara otomatis.</p></div></div>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <label class="form-label">Baris Laporan</label>
            <select class="form-select" name="report_row_id" data-report-row-select>
                <option value="">Complain manual / tidak terkait report</option>
                <?php foreach ($reportRows as $row):
                    $statusText = ($row['delivery_status'] ?? '-') . ' / ' . ($row['result_status'] ?? '-');
                    $amountText = trim((string) ($row['currency'] ?? '')) . ' ' . number_format((float) ($row['amount'] ?? 0), 2, ',', '.');
                ?>
                    <option value="<?= (int) $row['id'] ?>"
                        <?= selected($selectedReport, $row['id']) ?>
                        data-company="<?= e($row['bill_to']) ?>"
                        data-company-id="<?= (int) ($row['company_id'] ?? 0) ?>"
                        data-beneficiary="<?= e($row['beneficiary_name']) ?>"
                        data-amount="<?= e($amountText) ?>"
                        data-swift="<?= e($row['swift']) ?>"
                        data-status="<?= e($statusText) ?>"
                        data-batch="<?= e(($row['batch_name'] ?? '') . ' · ' . ($row['month_key'] ?? '')) ?>">
                        <?= e(($row['month_key'] ?? '') . ' · ' . ($row['bill_to'] ?? '-') . ' · ' . ($row['beneficiary_name'] ?? '-') . ' · ' . $amountText) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="form-text">Hanya 1.000 baris report terbaru ditampilkan. Gunakan tombol “Buat Complain” pada halaman report untuk memilih baris tertentu.</small>
        </div>
        <div class="col-12" data-report-source-preview hidden>
            <div class="source-preview-card">
                <div><span>Bill To</span><strong data-source-company>-</strong></div>
                <div><span>Beneficiary</span><strong data-source-beneficiary>-</strong></div>
                <div><span>Nominal</span><strong data-source-amount>-</strong></div>
                <div><span>Status</span><strong data-source-status>-</strong></div>
            </div>
        </div>
    </div>

    <div class="section-title"><i class="bi bi-headset"></i><div><h3>Informasi complain</h3><p>Tentukan penanggung jawab, prioritas, status, dan tindak lanjut.</p></div></div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Perusahaan *</label><select class="form-select" name="company_id" required><option value="">Pilih perusahaan</option><?php foreach ($companies as $company): ?><option value="<?= $company['id'] ?>" <?= selected($value('company_id'), $company['id']) ?>><?= e($company['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label">Transaksi Terkait</label><select class="form-select" name="transaction_id"><option value="">Tidak ada</option><?php foreach ($transactions as $transaction): ?><option value="<?= $transaction['id'] ?>" <?= selected($value('transaction_id'), $transaction['id']) ?>><?= e(($transaction['reference_no'] ?: 'TRX-' . $transaction['id']) . ' · ' . $transaction['company_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-12"><label class="form-label">Subjek *</label><input class="form-control" name="subject" value="<?= e($value('subject')) ?>" required placeholder="Ringkasan masalah"></div>
        <div class="col-md-3"><label class="form-label">Prioritas</label><select class="form-select" name="priority"><?php foreach (['low','medium','high','critical'] as $status): ?><option value="<?= $status ?>" <?= selected($value('priority', 'medium'), $status) ?>><?= strtoupper($status) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><?php foreach (['open','in_progress','resolved','closed'] as $status): ?><option value="<?= $status ?>" <?= selected($value('status', 'open'), $status) ?>><?= strtoupper($status) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label">Assigned To</label><select class="form-select" name="assigned_to"><?php foreach ($users as $user): ?><option value="<?= $user['id'] ?>" <?= selected($value('assigned_to', auth_user()['id']), $user['id']) ?>><?= e($user['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" rows="6" name="description" placeholder="Kronologi dan informasi pendukung"><?= e($value('description')) ?></textarea></div>
        <div class="col-12"><label class="form-label">Resolusi</label><textarea class="form-control" rows="3" name="resolution" placeholder="Diisi ketika penanganan selesai"><?= e($value('resolution')) ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Lampiran</label><input type="file" class="form-control" name="attachment"><small class="form-text">PDF/JPG/PNG sesuai batas unggahan sistem.</small></div>
    </div>
    <div class="form-actions"><a class="btn btn-light" href="<?= url('/complaints') ?>">Batal</a><button class="btn btn-primary"><i class="bi bi-check2-circle"></i>Simpan Complain</button></div>
</form>
</section>

<aside class="panel-card help-card">
    <h3>Alur penanganan</h3>
    <ol class="steps-list">
        <li><span>1</span><div><strong>Pilih sumber report</strong><p>Data beneficiary, nominal, dan status ditarik otomatis.</p></div></li>
        <li><span>2</span><div><strong>Verifikasi perusahaan</strong><p>Nama Bill To dipadankan dengan Data PT; pilih manual bila belum sama.</p></div></li>
        <li><span>3</span><div><strong>Tetapkan PIC dan prioritas</strong><p>Gunakan status untuk memantau penyelesaian kasus.</p></div></li>
    </ol>
    <?php if ($editing): ?>
        <div class="complaint-history-block">
            <div class="history-block-head"><div><span>HISTORY</span><strong>Riwayat penanganan</strong></div><span class="badge badge-muted"><?= count($histories) ?> aktivitas</span></div>
            <div class="complaint-timeline">
                <?php foreach ($histories as $history): ?>
                    <article>
                        <i></i>
                        <div><strong><?= e(ucwords(str_replace('_', ' ', (string) ($history['action'] ?? 'aktivitas')))) ?></strong><p><?= e((string) ($history['note'] ?? '-')) ?></p><small><?= e((string) ($history['user_name'] ?? 'Sistem')) ?> · <?= format_datetime((string) ($history['created_at'] ?? '')) ?></small></div>
                    </article>
                <?php endforeach; ?>
                <?php if ($histories === []): ?><p class="text-muted small">Histori khusus akan mulai tercatat setelah migrasi V5 dijalankan.</p><?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</aside>
</div>
