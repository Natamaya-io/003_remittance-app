<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - EDIT METADATA (PRELINE & ALPINE NATIVE)
|--------------------------------------------------------------------------
*/

$batch = is_array($batch ?? null) ? $batch : [];
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE ACTIONS
         ========================================================= -->
    <div class="flex items-center justify-end">
        <a class="btn-secondary" href="<?= url('/reports/' . $batch['id']) ?>">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- FORM CARD -->
        <section class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-xs">
            <form method="post" action="<?= url('/reports/' . $batch['id']) ?>">
                <?= csrf_field() ?>
                <?= method_field('PUT') ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama report *</label>
                        <input class="custom-form-input" name="name" required value="<?= e($batch['name'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bulan report *</label>
                        <?= render_datepicker('month_key', (string) ($batch['month_key'] ?? ''), ['mode' => 'month', 'required' => true, 'placeholder' => 'Pilih bulan']) ?>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <label class="block text-xs font-semibold text-gray-700">Tanggal report</label>
                            <?= render_tooltip(
                                'Tanggal harus berada pada bulan report yang dipilih.',
                                '<span class="info-toggle-btn cursor-help" aria-label="Info tanggal report"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                ['placement' => 'top']
                            ) ?>
                        </div>
                        <?= render_datepicker('report_date', (string) ($batch['report_date'] ?? ''), ['placeholder' => 'Pilih tanggal']) ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama tim</label>
                        <input class="custom-form-input" name="team_name" value="<?= e($batch['team_name'] ?? '') ?>" placeholder="Contoh: PARK, GOSYEN, EKAWIRA">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama klien</label>
                        <input class="custom-form-input" name="client_name" value="<?= e($batch['client_name'] ?? '') ?>">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">File sumber</label>
                        <input class="custom-form-input opacity-60 cursor-not-allowed" value="<?= e($batch['source_filename'] ?? '') ?>" disabled>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a class="btn-secondary" href="<?= url('/reports/' . $batch['id']) ?>">
                        Batal
                    </a>
                    <button class="btn-primary" type="submit">
                        <i class="bi bi-floppy"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- ASIDE INFO CARD -->
        <aside class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="bi bi-info-circle text-gray-800"></i>
                    Informasi Report
                </h3>
                <dl class="divide-y divide-gray-100 text-xs mb-6">
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Batch ID</dt><dd class="font-bold text-gray-900 font-mono">#<?= (int) ($batch['id'] ?? 0) ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Total Baris</dt><dd class="font-bold text-gray-900 font-mono"><?= number_format((int) ($batch['total_rows'] ?? 0), 0, ',', '.') ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Terkirim</dt><dd class="font-bold text-emerald-600 font-mono"><?= number_format((int) ($batch['sent_rows'] ?? 0), 0, ',', '.') ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Pending</dt><dd class="font-bold text-amber-600 font-mono"><?= number_format((int) ($batch['pending_rows'] ?? 0), 0, ',', '.') ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Dibuat</dt><dd class="text-gray-700"><?= e(format_datetime($batch['created_at'] ?? null)) ?></dd></div>
                    <div class="py-2.5 flex justify-between items-center"><dt class="text-gray-500 font-medium">Diperbarui</dt><dd class="text-gray-700"><?= e(format_datetime($batch['updated_at'] ?? null)) ?></dd></div>
                </dl>
                <div class="alert alert-warning" role="alert">
                    <div class="alert-content">
                        <span class="alert-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </span>
                        <div class="alert-body">
                            <strong class="alert-title">Pergantian Berkas</strong>
                            <div class="alert-message">Untuk mengganti berkas Excel transaksi, hapus report lama dan upload file baru dengan tanggal yang sama.</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

</div>
