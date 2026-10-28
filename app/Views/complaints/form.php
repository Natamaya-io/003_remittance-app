<?php declare(strict_types=1);

$editing = (bool) $complaint;
$value = static fn (string $key, mixed $default = ''): mixed => old($key, $complaint[$key] ?? ($_GET[$key] ?? $default));
$selectedReport = (string) $value('report_row_id');
$histories = is_array($histories ?? null) ? $histories : [];
?>
<div class="page-actions flex items-center justify-end mb-4">
    <a class="btn-secondary" href="<?= url('/complaints') ?>">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>
</div>

<div class="two-column-layout complaint-workspace">
    <section class="form-card">
        <form method="post" enctype="multipart/form-data" action="<?= $editing ? url('/complaints/' . $complaint['id']) : url('/complaints') ?>">
            <?= csrf_field() ?>
            <?php if ($editing) echo method_field('PUT'); ?>

            <div class="section-title flex items-center gap-2 mb-4">
                <i class="bi bi-link-45deg text-lg text-gray-800"></i>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold text-gray-900 m-0">Sumber kasus</h3>
                    <?= render_tooltip(
                        'Pilih baris report untuk mengisi perusahaan, subjek, dan detail secara otomatis.',
                        '<span class="info-toggle-btn cursor-help" aria-label="Info sumber"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                        ['placement' => 'top']
                    ) ?>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="flex items-center gap-1.5 mb-1">
                        <label class="block text-xs font-semibold text-gray-700">Baris Laporan</label>
                        <?= render_tooltip(
                            'Hanya 1.000 baris report terbaru ditampilkan. Gunakan tombol "Buat Complain" pada halaman report untuk memilih baris tertentu.',
                            '<span class="info-toggle-btn cursor-help" aria-label="Info batas baris"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                            ['placement' => 'top']
                        ) ?>
                    </div>
                    <select class="custom-form-select" name="report_row_id" data-report-row-select>
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

            <div class="section-title flex items-center gap-2 mb-4">
                <i class="bi bi-headset text-lg text-gray-800"></i>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold text-gray-900 m-0">Informasi complain</h3>
                    <?= render_tooltip(
                        'Tentukan penanggung jawab, prioritas, status, dan tindak lanjut.',
                        '<span class="info-toggle-btn cursor-help" aria-label="Info complain"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                        ['placement' => 'top']
                    ) ?>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Perusahaan *</label>
                    <select class="custom-form-select" name="company_id" required>
                        <option value="">Pilih perusahaan</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= $company['id'] ?>" <?= selected($value('company_id'), $company['id']) ?>><?= e($company['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Transaksi Terkait</label>
                    <select class="custom-form-select" name="transaction_id">
                        <option value="">Tidak ada</option>
                        <?php foreach ($transactions as $transaction): ?>
                            <option value="<?= $transaction['id'] ?>" <?= selected($value('transaction_id'), $transaction['id']) ?>><?= e(($transaction['reference_no'] ?: 'TRX-' . $transaction['id']) . ' · ' . $transaction['company_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Subjek *</label>
                    <input class="custom-form-input" name="subject" value="<?= e($value('subject')) ?>" required placeholder="Ringkasan masalah">
                </div>
                <div class="col-md-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Prioritas</label>
                    <select class="custom-form-select" name="priority">
                        <?php foreach (['low', 'medium', 'high', 'critical'] as $status): ?>
                            <option value="<?= $status ?>" <?= selected($value('priority', 'medium'), $status) ?>><?= strtoupper($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select class="custom-form-select" name="status">
                        <?php foreach (['open', 'in_progress', 'resolved', 'closed'] as $status): ?>
                            <option value="<?= $status ?>" <?= selected($value('status', 'open'), $status) ?>><?= strtoupper($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Assigned To</label>
                    <select class="custom-form-select" name="assigned_to">
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= selected($value('assigned_to', auth_user()['id']), $user['id']) ?>><?= e($user['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea class="custom-form-textarea" rows="6" name="description" placeholder="Kronologi dan informasi pendukung"><?= e($value('description')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Resolusi</label>
                    <textarea class="custom-form-textarea" rows="3" name="resolution" placeholder="Diisi ketika penanganan selesai"><?= e($value('resolution')) ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="flex items-center gap-1.5 mb-1">
                        <label class="block text-xs font-semibold text-gray-700">Lampiran</label>
                        <?= render_tooltip(
                            'PDF/JPG/PNG sesuai batas unggahan sistem.',
                            '<span class="info-toggle-btn cursor-help" aria-label="Info lampiran"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                            ['placement' => 'top']
                        ) ?>
                    </div>
                    <input type="file" class="custom-form-input" name="attachment">
                </div>
            </div>

            <div class="form-actions flex items-center justify-end gap-3 mt-4 pt-3 border-t border-gray-100">
                <a class="btn-secondary" href="<?= url('/complaints') ?>">
                    <span>Batal</span>
                </a>
                <button class="btn-primary" type="submit">
                    <i class="bi bi-floppy"></i>
                    <span>Simpan Complain</span>
                </button>
            </div>
        </form>
    </section>

    <aside class="panel-card help-card">
        <h3 class="text-base font-semibold text-gray-900 mb-3">Alur penanganan</h3>
        <ol class="steps-list space-y-3 mb-6">
            <li class="flex items-start gap-2.5">
                <span class="size-6 rounded-full border border-gray-300 text-gray-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                <div>
                    <strong class="text-xs font-semibold text-gray-800 block">Pilih sumber report</strong>
                    <span class="text-xs text-gray-500">Data beneficiary, nominal, dan status ditarik otomatis.</span>
                </div>
            </li>
            <li class="flex items-start gap-2.5">
                <span class="size-6 rounded-full border border-gray-300 text-gray-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                <div>
                    <strong class="text-xs font-semibold text-gray-800 block">Verifikasi perusahaan</strong>
                    <span class="text-xs text-gray-500">Nama Bill To dipadankan dengan Data PT; pilih manual bila belum sama.</span>
                </div>
            </li>
            <li class="flex items-start gap-2.5">
                <span class="size-6 rounded-full border border-gray-300 text-gray-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                <div>
                    <strong class="text-xs font-semibold text-gray-800 block">Tetapkan PIC dan prioritas</strong>
                    <span class="text-xs text-gray-500">Gunakan status untuk memantau penyelesaian kasus.</span>
                </div>
            </li>
        </ol>
        <?php if ($editing): ?>
            <div class="complaint-history-block pt-4 border-t border-gray-100">
                <div class="history-block-head flex items-center justify-between mb-3">
                    <strong class="text-xs font-semibold text-gray-800">Riwayat penanganan</strong>
                    <span class="badge badge-muted text-[11px]"><?= count($histories) ?> aktivitas</span>
                </div>
                <div class="complaint-timeline space-y-3">
                    <?php foreach ($histories as $history): ?>
                        <article class="text-xs border-l-2 border-gray-200 pl-3 py-1">
                            <strong class="block text-gray-800 font-medium"><?= e(ucwords(str_replace('_', ' ', (string) ($history['action'] ?? 'aktivitas')))) ?></strong>
                            <p class="text-gray-600 my-0.5"><?= e((string) ($history['note'] ?? '-')) ?></p>
                            <small class="text-gray-400"><?= e((string) ($history['user_name'] ?? 'Sistem')) ?> · <?= format_datetime((string) ($history['created_at'] ?? '')) ?></small>
                        </article>
                    <?php endforeach; ?>
                    <?php if ($histories === []): ?>
                        <p class="text-gray-400 text-xs">Histori khusus akan mulai tercatat setelah migrasi V5 dijalankan.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </aside>
</div>
