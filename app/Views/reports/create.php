<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - UPLOAD EXCEL (PRELINE & ALPINE NATIVE)
|--------------------------------------------------------------------------
*/

$monthNames = [
    1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
    5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
    9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER',
];
$selectedMonth = (string) ($defaultMonth ?? date('Y-m'));
$selectedDate = (string) ($defaultDate ?? '');
$selectedTeam = (string) ($defaultTeam ?? '');
$monthNumber = (int) substr($selectedMonth, 5, 2);
$defaultReportName = 'REPORT HARIAN - ' . ($monthNames[$monthNumber] ?? strtoupper(date('F'))) . ' ' . substr($selectedMonth, 0, 4);
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE ACTIONS
         ========================================================= -->
    <div class="flex items-center justify-end">
        <a class="py-2 px-3.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 transition-colors" href="<?= url('/reports') ?>">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- FORM CARD (2 KOLOM DESKTOP) -->
        <section class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-xs">
            <form method="post" action="<?= url('/reports') ?>" enctype="multipart/form-data" data-upload-form x-data="{ dragging: false, fileName: '' }">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama report *</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="name" required placeholder="Contoh: REPORT HARIAN AGUSTUS 2026 PARK" value="<?= e(old('name', $defaultReportName)) ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bulan report *</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="month" name="month_key" required value="<?= e(old('month_key', $selectedMonth)) ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal report *</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="date" name="report_date" required value="<?= e(old('report_date', $selectedDate)) ?>">
                        <span class="text-[11px] text-gray-400 mt-1 block">Membentuk kartu tanggal pada bulan report.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama tim *</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="team_name" required placeholder="Contoh: PARK, GOSYEN, EKAWIRA" value="<?= e(old('team_name', $selectedTeam)) ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama klien</label>
                        <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="client_name" placeholder="Opsional (misal: PARK)" value="<?= e(old('client_name')) ?>">
                    </div>
                </div>

                <!-- ALPINE DRAG & DROP FILE ZONE -->
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Berkas Excel (XLSX / CSV) *</label>
                    <div
                        class="relative border-2 border-dashed rounded-2xl p-8 text-center transition-all cursor-pointer"
                        :class="dragging ? 'border-blue-500 bg-blue-50/50' : 'border-gray-300 bg-gray-50/50 hover:bg-gray-50 hover:border-gray-400'"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="dragging = false; if ($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0].name; }"
                    >
                        <input
                            type="file"
                            name="report_file"
                            accept=".xlsx,.csv"
                            required
                            data-file-input
                            x-ref="fileInput"
                            @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        >
                        <div class="flex flex-col items-center pointer-events-none">
                            <div class="size-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-3 shadow-xs">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>
                            <template x-if="!fileName">
                                <div>
                                    <strong class="text-sm font-bold text-gray-800 block">Drop berkas Excel di sini</strong>
                                    <span class="text-xs text-gray-500 block mt-1">atau klik untuk memilih berkas dari komputer</span>
                                    <span class="text-[11px] text-gray-400 mt-2 block" data-file-name>Maksimal <?= (int) App\Core\App::config('upload.max_mb', 20) ?> MB</span>
                                </div>
                            </template>
                            <template x-if="fileName">
                                <div class="space-y-3 pointer-events-auto">
                                    <div class="bg-blue-50 border border-blue-200 rounded-xl py-2 px-4 inline-flex items-center gap-2 shadow-xs">
                                        <i class="bi bi-file-earmark-spreadsheet text-blue-600 text-lg"></i>
                                        <span class="text-xs font-bold text-blue-800" x-text="fileName"></span>
                                    </div>
                                    <div>
                                        <button
                                            type="button"
                                            @click.stop="$refs.fileInput.value = ''; fileName = '';"
                                            class="py-1.5 px-3 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 focus:outline-hidden focus:bg-red-100 transition-colors"
                                        >
                                            <i class="bi bi-trash"></i>
                                            Hapus &amp; Pilih Berkas Lain
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a class="py-2 px-3.5 inline-flex items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors focus:outline-hidden focus:bg-gray-50" href="<?= url('/reports') ?>">
                        Batal
                    </a>
                    <button class="btn-primary py-2 px-4 inline-flex items-center gap-x-2 text-xs font-semibold rounded-lg shadow-xs focus:outline-hidden" type="submit">
                        <i class="bi bi-database-check"></i>
                        Import Report
                    </button>
                </div>
            </form>
        </section>

        <!-- ASIDE GUIDANCE CARD -->
        <aside class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="bi bi-diagram-3 text-blue-600"></i>
                    Struktur Data Report
                </h3>
                <ol class="space-y-4 mb-6">
                    <li class="flex items-start gap-3">
                        <span class="size-6 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <div>
                            <strong class="text-xs font-bold text-gray-800 block">Pilih bulan</strong>
                            <span class="text-xs text-gray-500 mt-0.5 block">File otomatis dikelompokkan ke kartu bulan (contoh: Agustus 2026).</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="size-6 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <div>
                            <strong class="text-xs font-bold text-gray-800 block">Tentukan tanggal</strong>
                            <span class="text-xs text-gray-500 mt-0.5 block">Sistem membentuk kartu tanggal transaksi (contoh: 5 Agustus 2026).</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="size-6 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <div>
                            <strong class="text-xs font-bold text-gray-800 block">Isi nama tim</strong>
                            <span class="text-xs text-gray-500 mt-0.5 block">File tampil sebagai kartu batch tim independen pada tanggal tersebut.</span>
                        </div>
                    </li>
                </ol>

                <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl flex items-start gap-2.5 text-xs text-blue-800 mb-4">
                    <i class="bi bi-info-circle text-blue-600 text-base shrink-0 mt-0.5"></i>
                    <span>Header berkas wajib mencakup <b>Bill To</b>, <b>Beneficiary Name</b>, <b>Amounts</b>, dan <b>Currency</b>.</span>
                </div>
            </div>

            <a class="py-2 px-3.5 inline-flex items-center justify-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 text-center mt-auto transition-colors focus:outline-hidden focus:bg-gray-100" href="<?= asset('templates/report-import-template.csv') ?>" download>
                <i class="bi bi-download"></i>
                Download Contoh Header CSV
            </a>
        </aside>
    </div>

</div>
