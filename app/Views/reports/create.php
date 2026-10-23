<?php

declare(strict_types=1);

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

<div class="page-heading">
    <div>
        <span class="eyebrow dark">CREATE REPORT</span>
        <h2>Upload Report Harian</h2>
        <p>Upload satu file untuk satu tim dan satu tanggal. Sistem akan menempatkannya otomatis pada kartu bulan dan kartu tanggal.</p>
    </div>
    <a class="btn btn-light" href="<?= url('/reports') ?>"><i class="bi bi-arrow-left"></i>Kembali</a>
</div>

<div class="two-column-layout">
    <section class="form-card">
        <form method="post" action="<?= url('/reports') ?>" enctype="multipart/form-data" data-upload-form>
            <?= csrf_field() ?>
            <div class="form-grid two">
                <label>
                    <span class="form-label">Nama report *</span>
                    <input class="form-control" name="name" required placeholder="Contoh: REPORT HARIAN AGUSTUS 2026 PARK" value="<?= e(old('name', $defaultReportName)) ?>">
                </label>
                <label>
                    <span class="form-label">Bulan report *</span>
                    <input class="form-control" type="month" name="month_key" required value="<?= e(old('month_key', $selectedMonth)) ?>">
                </label>
                <label>
                    <span class="form-label">Tanggal report *</span>
                    <input class="form-control" type="date" name="report_date" required value="<?= e(old('report_date', $selectedDate)) ?>">
                    <small>Tanggal ini membentuk kartu tanggal pada halaman report bulanan.</small>
                </label>
                <label>
                    <span class="form-label">Nama tim *</span>
                    <input class="form-control" name="team_name" required placeholder="Contoh: PARK, GOSYEN, EKAWIRA" value="<?= e(old('team_name', $selectedTeam)) ?>">
                </label>
                <label>
                    <span class="form-label">Nama klien</span>
                    <input class="form-control" name="client_name" placeholder="Opsional" value="<?= e(old('client_name')) ?>">
                </label>
            </div>

            <label class="drop-zone" data-drop-zone>
                <input type="file" name="report_file" accept=".xlsx,.csv" required data-file-input>
                <span class="drop-icon"><i class="bi bi-cloud-arrow-up"></i></span>
                <strong>Drop file Excel di sini</strong>
                <span>atau klik untuk memilih file XLSX atau CSV</span>
                <small data-file-name>Maksimal <?= (int) App\Core\App::config('upload.max_mb', 5) ?> MB</small>
            </label>

            <div class="form-actions">
                <a class="btn btn-light" href="<?= url('/reports') ?>">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="bi bi-database-check"></i>Import Report</button>
            </div>
        </form>
    </section>

    <aside class="panel-card help-card">
        <h3>Alur kartu report</h3>
        <ol class="steps-list">
            <li><span>1</span><div><strong>Pilih bulan</strong><p>File masuk ke kartu bulan, misalnya Agustus 2026.</p></div></li>
            <li><span>2</span><div><strong>Tentukan tanggal</strong><p>Sistem membentuk kartu tanggal, misalnya 5 Agustus 2026.</p></div></li>
            <li><span>3</span><div><strong>Isi nama tim</strong><p>File tampil sebagai kartu report tim pada tanggal tersebut.</p></div></li>
        </ol>
        <div class="notice info"><i class="bi bi-info-circle"></i><span>Header wajib meliputi Bill To, Beneficiary Name, Amounts, dan Currency. Baris tidak valid akan dilewati dan dicatat.</span></div>
        <a class="btn btn-light w-100 mt-3" href="<?= asset('templates/report-import-template.csv') ?>" download><i class="bi bi-download"></i>Download Contoh Header CSV</a>
    </aside>
</div>