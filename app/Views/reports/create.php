<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - UPLOAD EXCEL (PRELINE & ALPINE NATIVE)
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
$defaultReportName = 'LAPORAN - ' . ($monthNames[$monthNumber] ?? strtoupper(date('F'))) . ' ' . substr($selectedMonth, 0, 4);
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
                        <input class="custom-form-input" name="name" required placeholder="Contoh: LAPORAN AGUSTUS 2026 PARK" value="<?= e(old('name', $defaultReportName)) ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bulan report *</label>
                        <?= render_datepicker('month_key', (string) old('month_key', $selectedMonth), ['mode' => 'month', 'required' => true, 'placeholder' => 'Pilih bulan']) ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal report *</label>
                        <?= render_datepicker('report_date', (string) old('report_date', $selectedDate), ['required' => true, 'placeholder' => 'Pilih tanggal']) ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama tim *</label>
                        <input class="custom-form-input" name="team_name" required placeholder="Contoh: PARK, GOSYEN, EKAWIRA" value="<?= e(old('team_name', $selectedTeam)) ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama klien</label>
                        <input class="custom-form-input" name="client_name" placeholder="Opsional (misal: PARK)" value="<?= e(old('client_name')) ?>">
                    </div>
                </div>

                <!-- ALPINE DRAG & DROP FILE ZONE -->
                <div class="mb-6">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <label class="block text-xs font-semibold text-gray-700">Berkas Excel (XLSX / CSV) *</label>
                        <?= render_tooltip(
                            'Header berkas wajib mencakup Bill To, Beneficiary Name, Amounts, dan Currency.',
                            '<span class="info-toggle-btn cursor-help" aria-label="Info format kolom berkas"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                            ['placement' => 'top']
                        ) ?>
                    </div>
                    <div
                        class="relative border-2 border-dashed rounded-2xl p-8 text-center transition-all cursor-pointer"
                        :class="dragging ? 'border-[#315B8C] bg-blue-50/50' : 'border-gray-300 bg-[#F5F2F2]/50 hover:bg-[#F5F2F2] hover:border-gray-400'"
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
                            <i class="bi bi-cloud-arrow-up text-3xl text-gray-700 mb-2"></i>
                            <template x-if="!fileName">
                                <div>
                                    <strong class="text-sm font-semibold text-gray-800 block">Pilih atau letakkan berkas Excel di sini</strong>
                                    <span class="text-[11px] text-gray-400 mt-1 block" data-file-name>Maksimal <?= (int) App\Core\App::config('upload.max_mb', 20) ?> MB</span>
                                </div>
                            </template>
                            <template x-if="fileName">
                                <div class="space-y-3 pointer-events-auto">
                                    <div class="bg-white border border-gray-200 rounded-xl py-2 px-4 inline-flex items-center gap-2 shadow-xs">
                                        <i class="bi bi-file-earmark-spreadsheet text-gray-800 text-lg"></i>
                                        <span class="text-xs font-semibold text-gray-900" x-text="fileName"></span>
                                    </div>
                                    <div>
                                        <button
                                            type="button"
                                            @click.stop="$refs.fileInput.value = ''; fileName = '';"
                                            class="btn-danger"
                                        >
                                            <i class="bi bi-trash"></i>
                                            <span>Hapus &amp; Pilih Berkas Lain</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a class="btn-secondary" href="<?= url('/reports') ?>">
                        Batal
                    </a>
                    <button class="btn-primary" type="submit">
                        <i class="bi bi-database-check"></i>
                        <span>Import Report</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- ASIDE GUIDANCE CARD -->
        <aside class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="bi bi-diagram-3 text-gray-800"></i>
                    Struktur Data Report
                </h3>
                <ol class="space-y-4 mb-6">
                    <li class="flex items-start gap-3">
                        <span class="size-6 rounded-full border border-gray-300 text-gray-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <div class="inline-flex items-center gap-1.5 pt-0.5">
                            <strong class="text-xs font-semibold text-gray-800">Pilih bulan</strong>
                            <?= render_tooltip(
                                'File otomatis dikelompokkan ke kartu bulan (contoh: Agustus 2026).',
                                '<span class="info-toggle-btn cursor-help" aria-label="Info pilih bulan"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                ['placement' => 'top']
                            ) ?>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="size-6 rounded-full border border-gray-300 text-gray-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <div class="inline-flex items-center gap-1.5 pt-0.5">
                            <strong class="text-xs font-semibold text-gray-800">Tentukan tanggal</strong>
                            <?= render_tooltip(
                                'Sistem membentuk kartu tanggal transaksi (contoh: 5 Agustus 2026).',
                                '<span class="info-toggle-btn cursor-help" aria-label="Info tentukan tanggal"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                ['placement' => 'top']
                            ) ?>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="size-6 rounded-full border border-gray-300 text-gray-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <div class="inline-flex items-center gap-1.5 pt-0.5">
                            <strong class="text-xs font-semibold text-gray-800">Isi nama tim</strong>
                            <?= render_tooltip(
                                'File tampil sebagai kartu batch tim independen pada tanggal tersebut.',
                                '<span class="info-toggle-btn cursor-help" aria-label="Info nama tim"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                ['placement' => 'top']
                            ) ?>
                        </div>
                    </li>
                </ol>
            </div>

            <a class="btn-secondary w-full" href="<?= asset('templates/report-import-template.csv') ?>" download>
                <i class="bi bi-download"></i>
                <span>Download Contoh Header CSV</span>
            </a>
        </aside>
    </div>
</div>
