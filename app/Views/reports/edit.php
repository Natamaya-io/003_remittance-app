<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - EDIT METADATA (PRELINE & ALPINE NATIVE)
|--------------------------------------------------------------------------
*/

$batch = is_array($batch ?? null) ? $batch : [];
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE HEADING
         ========================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Metadata Report</h2>
        </div>
        <div>
            <a class="py-2 px-3.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-2 transition-colors" href="<?= url('/reports/' . $batch['id']) ?>">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
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
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="name" required value="<?= e($batch['name'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bulan report *</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="month" name="month_key" required value="<?= e($batch['month_key'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal report</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="date" name="report_date" value="<?= e($batch['report_date'] ?? '') ?>">
                        <span class="text-[11px] text-gray-400 mt-1 block">Tanggal harus berada pada bulan report yang dipilih.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama tim</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="team_name" value="<?= e($batch['team_name'] ?? '') ?>" placeholder="Contoh: PARK, GOSYEN, EKAWIRA">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama klien</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="client_name" value="<?= e($batch['client_name'] ?? '') ?>">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">File sumber</label>
                        <input class="py-2 px-3 block w-full border border-gray-200 bg-gray-50 rounded-xl text-xs text-gray-500 cursor-not-allowed" value="<?= e($batch['source_filename'] ?? '') ?>" disabled>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a class="py-2 px-4 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors" href="<?= url('/reports/' . $batch['id']) ?>">
                        Batal
                    </a>
                    <button class="btn btn-primary py-2 px-4 text-xs font-medium rounded-lg inline-flex items-center gap-2" type="submit">
                        <i class="bi bi-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        <!-- ASIDE INFO CARD -->
        <aside class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="bi bi-info-circle text-blue-600"></i>
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
                <div class="p-3.5 bg-amber-50/70 border border-amber-100 rounded-xl flex items-start gap-2.5 text-xs text-amber-800">
                    <i class="bi bi-exclamation-triangle text-amber-600 text-base shrink-0 mt-0.5"></i>
                    <span>Untuk mengganti berkas Excel transaksi, hapus report lama dan upload file baru dengan tanggal yang sama.</span>
                </div>
            </div>
        </aside>
    </div>

</div>
