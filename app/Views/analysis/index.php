<div class="spk-module">
<?php require __DIR__ . '/_nav.php'; ?>

<div class="spk-section mb-4">
    <div class="spk-section-head">
        <div>
            <h3><i class="bi bi-calendar-range me-2"></i>Periode Penelitian</h3>
            <div class="spk-muted">Periode dikunci agar hasil dapat direproduksi secara konsisten.</div>
        </div>
    </div>
    <div class="spk-grid">
        <div class="spk-card"><span class="badge-spk active">P1</span><h3 class="mt-2">1 September - 31 Oktober 2024</h3><p>Periode pembanding.</p></div>
        <div class="spk-card"><span class="badge-spk active">P2</span><h3 class="mt-2">1 November - 31 Desember 2024</h3><p>Periode berjalan.</p></div>
    </div>
</div>

<div class="spk-grid spk-grid-kpi mb-4">
    <a class="spk-card text-decoration-none" href="<?= url('/analysis/criteria') ?>"><div class="spk-stat"><?= (int) $stats['criteria'] ?></div><h3>Data Kriteria</h3><p>Kriteria penelitian C1-C6.</p></a>
    <a class="spk-card text-decoration-none" href="<?= url('/analysis/alternatives') ?>"><div class="spk-stat"><?= (int) $stats['alternatives'] ?></div><h3>Data Alternatif</h3><p>Alternatif perusahaan.</p></a>
    <a class="spk-card text-decoration-none" href="<?= url('/analysis/assessment') ?>"><div class="spk-stat"><?= (int) $stats['assessments'] ?></div><h3>Data Penilaian</h3><p>Matriks keputusan penelitian.</p></a>
    <a class="spk-card text-decoration-none" href="<?= url('/analysis/calculation') ?>"><div class="spk-stat"><i class="bi bi-calculator"></i></div><h3>Data Perhitungan</h3><p>Proses perhitungan metode.</p></a>
    <a class="spk-card text-decoration-none" href="<?= url('/analysis/results') ?>"><div class="spk-stat"><?= (int) $stats['runs'] ?></div><h3>Hasil Akhir</h3><p>Bobot, preferensi, dan ranking.</p></a>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="spk-section h-100">
            <div class="spk-section-head">
                <div x-data="{ showInfo: false }"><div class="d-inline-flex align-items-center gap-2"><h3 class="m-0"><i class="bi bi-database me-2"></i>Sumber Data Analisis</h3><button type="button" @click="showInfo = !showInfo" class="info-toggle-btn" :class="{ 'is-active': showInfo }" :aria-expanded="showInfo.toString()" aria-label="Tampilkan info sumber" title="Informasi bantuan"><i class="bi bi-info-circle text-xs leading-none"></i></button></div><div x-show="showInfo" x-cloak x-transition class="spk-muted mt-1" style="display: none;">Hanya dua sumber berikut yang digunakan dalam perhitungan.</div></div>
            </div>
            <div class="spk-source">
                <div class="spk-source-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                <div class="flex-grow-1"><strong>Laporan Sukses 2024</strong><div class="spk-muted">Sumber C1-C5: transaksi, hari aktif, negara, bank/SWIFT, dan mata uang.</div></div>
                <span class="badge-spk <?= !empty($sourceReadiness['report_2024']) ? 'active' : '' ?>"><?= !empty($sourceReadiness['report_2024']) ? 'SIAP' : 'BELUM DIIMPORT' ?></span>
            </div>
            <div class="spk-source">
                <div class="spk-source-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                <div class="flex-grow-1"><strong>Actual LIST MT199</strong><div class="spk-muted">Sumber C6: refund dari sheet REFUND BNI dan REFUND BRI.</div></div>
                <span class="badge-spk <?= !empty($sourceReadiness['actual']) ? 'active' : '' ?>"><?= !empty($sourceReadiness['actual']) ? 'SIAP' : 'BELUM DIIMPORT' ?></span>
            </div>

            <?php if ($sources): ?>
                <hr>
                <div class="spk-muted mb-2">Riwayat import dua sumber penelitian:</div>
                <?php foreach ($sources as $source): ?>
                <div class="spk-source">
                    <div class="spk-source-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="flex-grow-1">
                        <strong><?= e($source['name']) ?></strong>
                        <div class="spk-muted"><?= e($source['source_filename']) ?> · <?= number_format((int) $source['imported_rows'],0,',','.') ?> baris dipakai · <?= number_format((int) $source['ignored_rows'],0,',','.') ?> dilewati</div>
                    </div>
                    <span class="badge-spk <?= (int) $source['is_active'] === 1 ? 'active' : '' ?>"><?= (int) $source['is_active'] === 1 ? 'AKTIF' : 'ARSIP' ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="spk-section h-100">
            <div class="spk-section-head"><div x-data="{ showInfo: false }"><div class="d-inline-flex align-items-center gap-2"><h3 class="m-0"><i class="bi bi-cloud-arrow-up me-2"></i>Import / Ganti Sumber</h3><button type="button" @click="showInfo = !showInfo" class="info-toggle-btn" :class="{ 'is-active': showInfo }" :aria-expanded="showInfo.toString()" aria-label="Tampilkan info upload" title="Informasi bantuan"><i class="bi bi-info-circle text-xs leading-none"></i></button></div><div x-show="showInfo" x-cloak x-transition class="spk-muted mt-1" style="display: none;">Upload hanya memengaruhi modul Entropy-TOPSIS.</div></div></div>
            <form method="post" action="<?= url('/analysis/sources/upload') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Jenis sumber</label>
                    <select class="custom-form-select" name="source_type" required>
                        <option value="">Pilih sumber</option>
                        <?php foreach ($sourceTypes as $key => $label): ?><option value="<?= e($key) ?>"><?= e($label) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Nama sumber</label><input class="custom-form-input" name="source_name" placeholder="Opsional"></div>
                <div class="mb-3"><label class="form-label">File Excel</label><input class="custom-form-input" type="file" name="analysis_file" accept=".xlsx,.xls,.csv" required></div>
                <button class="btn-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><span>Import dan Bangun Penilaian</span></button>
            </form>
        </div>
    </div>
</div>

<div class="spk-section">
    <div class="spk-section-head">
        <div x-data="{ showInfo: false }"><div class="d-inline-flex align-items-center gap-2"><h3 class="m-0">Alur Metode</h3><button type="button" @click="showInfo = !showInfo" class="info-toggle-btn" :class="{ 'is-active': showInfo }" :aria-expanded="showInfo.toString()" aria-label="Tampilkan info alur" title="Informasi bantuan"><i class="bi bi-info-circle text-xs leading-none"></i></button></div><div x-show="showInfo" x-cloak x-transition class="spk-muted mt-1" style="display: none;">Entropy dan TOPSIS mengikuti prosedur pada modul PDF yang digunakan sebagai rujukan metode.</div></div>
        <a class="btn-primary" href="<?= url('/analysis/calculation') ?>">
            <i class="bi bi-play-fill"></i>
            <span>Lihat Perhitungan</span>
        </a>
    </div>
    <div class="spk-formula mb-3"><strong>Pembentukan matriks:</strong> Laporan 2024 + Actual -> P1/P2 -> C1-C6 -> Matriks X</div>
    <div class="spk-formula mb-3"><strong>Entropy:</strong> Benefit Kij = Xij / max(Xj) -> aij = Kij / Sum(Kij) -> Ej -> Dj = 1 - Ej -> Wj</div>
    <div class="spk-formula"><strong>TOPSIS:</strong> Rij -> Yij = Wj x Rij -> A+ / A- -> D+ / D- -> Vi = D- / (D+ + D-) -> Ranking</div>
</div>

<?php if ($latestResults): ?>
<div class="spk-section">
    <div class="spk-section-head"><div><h3>Hasil Terakhir</h3><div class="spk-muted"><?= e($latestRun['name'] ?? '') ?> · <?= e(format_datetime($latestRun['created_at'] ?? null)) ?></div></div><a class="btn-secondary" href="<?= url('/analysis/results/' . (int) $latestRun['id']) ?>"><span>Detail</span></a></div>
    <div class="spk-rank-grid">
        <?php foreach (array_slice($latestResults,0,4) as $row): ?>
        <div class="spk-rank-card"><div class="rank">#<?= (int) $row['rank_position'] ?></div><strong><?= e($row['alternative_name']) ?></strong><small>V = <?= number_format((float) $row['preference_value'],12,',','.') ?></small></div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
</div>
