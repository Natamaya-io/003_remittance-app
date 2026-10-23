<div class="spk-module">
<?php require __DIR__ . '/_nav.php'; ?>
<div class="spk-hero">
    <span class="eyebrow dark">KRITERIA PENELITIAN</span>
    <h2>Data Kriteria C1-C6</h2>
    <p>Enam kriteria penelitian dikunci agar definisi pada aplikasi, perhitungan manual, dan naskah skripsi tetap konsisten. Seluruh kriteria bertipe <strong>Benefit</strong>.</p>
</div>

<div class="spk-section">
    <div class="spk-section-head">
        <div><h3>Daftar Kriteria Tetap</h3><div class="spk-muted">C1-C5 berasal dari Report Sukses 2024. C6 menggunakan Report Sukses 2024 dan refund dari Actual LIST MT199.</div></div>
    </div>
    <div class="table-responsive">
        <table class="spk-table">
            <thead><tr><th>No</th><th>Kode</th><th>Nama Kriteria</th><th>Jenis</th><th>Rumus / Sumber Nilai</th><th>Deskripsi</th></tr></thead>
            <tbody>
            <?php foreach($criteria as $i=>$c): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><strong><?=e($c['code'])?></strong></td>
                    <td><?=e($c['name'])?></td>
                    <td><span class="badge-spk active">BENEFIT</span></td>
                    <td><?=e($rules[$c['data_rule']]??$c['data_rule'])?></td>
                    <td><?=e($c['description']??'')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="spk-section">
    <h3 class="mb-3">Definisi Operasional</h3>
    <div class="spk-grid">
        <div class="spk-card"><span class="badge-spk active">C1</span><h3 class="mt-2">Pertumbuhan Jumlah Transaksi</h3><p>C1 = Transaksi P2 / Transaksi P1.</p></div>
        <div class="spk-card"><span class="badge-spk active">C2</span><h3 class="mt-2">Pertumbuhan Transaksi per Hari Aktif</h3><p>C2 = (Tx P2 / Hari Aktif P2) / (Tx P1 / Hari Aktif P1).</p></div>
        <div class="spk-card"><span class="badge-spk active">C3</span><h3 class="mt-2">Pertumbuhan Cakupan Negara</h3><p>C3 = Negara unik P2 / Negara unik P1.</p></div>
        <div class="spk-card"><span class="badge-spk active">C4</span><h3 class="mt-2">Pertumbuhan Cakupan Bank/SWIFT</h3><p>C4 = Bank/SWIFT unik P2 / Bank/SWIFT unik P1.</p></div>
        <div class="spk-card"><span class="badge-spk active">C5</span><h3 class="mt-2">Pertumbuhan Keragaman Mata Uang</h3><p>C5 = Mata uang unik P2 / Mata uang unik P1.</p></div>
        <div class="spk-card"><span class="badge-spk active">C6</span><h3 class="mt-2">Tingkat Transaksi Tanpa Refund</h3><p>C6 = 1 - (Refund P2 / Transaksi P2).</p></div>
    </div>
</div>
</div>
