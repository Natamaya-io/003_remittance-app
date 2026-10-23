<?php

declare(strict_types=1);

$batches = is_array($batches ?? null) ? $batches : [];

$columns = [
    ['key'=>'transaction_date','label'=>'Tanggal'],
    ['key'=>'beneficiary_name','label'=>'Nama Penerima'],
    ['key'=>'beneficiary_address','label'=>'Alamat Penerima'],
    ['key'=>'beneficiary_phone','label'=>'Telepon Penerima'],
    ['key'=>'beneficiary_city','label'=>'Kota Penerima'],
    ['key'=>'beneficiary_country','label'=>'Negara Penerima'],
    ['key'=>'beneficiary_bank','label'=>'Bank Penerima'],
    ['key'=>'beneficiary_bank_city','label'=>'Kota Bank'],
    ['key'=>'beneficiary_bank_country','label'=>'Negara Bank'],
    ['key'=>'beneficiary_account','label'=>'No Rekening Penerima'],
    ['key'=>'remitter_name','label'=>'Nama Pengirim'],
    ['key'=>'remitter_alias','label'=>'Nama Alias'],
    ['key'=>'remitter_id','label'=>'No ID'],
    ['key'=>'remitter_address','label'=>'Alamat Pengirim'],
    ['key'=>'remitter_phone','label'=>'Telepon Pengirim'],
    ['key'=>'remitter_city','label'=>'Kota Pengirim'],
    ['key'=>'remitter_country','label'=>'Negara Pengirim'],
    ['key'=>'transaction_purpose','label'=>'Tujuan Transaksi'],
    ['key'=>'message','label'=>'Berita'],
    ['key'=>'transfer_type','label'=>'Jenis Pengiriman'],
    ['key'=>'source_of_fund','label'=>'Sumber Dana'],
    ['key'=>'currency','label'=>'Currency'],
    ['key'=>'amount','label'=>'Amount'],
    ['key'=>'exchange_rate','label'=>'Kurs'],
    ['key'=>'commission','label'=>'Komisi'],
    ['key'=>'handling','label'=>'Handling'],
    ['key'=>'correspondent_bank','label'=>'Bank Koresponden'],
    ['key'=>'other_charge','label'=>'Biaya Lain'],
    ['key'=>'amount_words','label'=>'Terbilang'],
];
?>
<div class="bni-import-page">
    <section class="bank-page-heading">
        <div>
            <span class="eyebrow dark">BANK BNI</span>
            <h2>Import Template Transaksi BNI</h2>
            <p>Copy paste data Excel, bentuk tabel, lalu simpan untuk membuat formulir Kiriman Uang BNI.</p>
        </div>
        <a class="btn btn-light" href="<?= url('/transactions') ?>"><i class="bi bi-arrow-left"></i> Pilihan Bank</a>
    </section>

    <section class="bank-workflow-card bni-flow">
        <div class="bank-workflow-step active"><b>1</b><span>Copy Paste Excel</span></div>
        <div class="bank-workflow-line"></div>
        <div class="bank-workflow-step"><b>2</b><span>Bentuk Tabel</span></div>
        <div class="bank-workflow-line"></div>
        <div class="bank-workflow-step"><b>3</b><span>Form BNI</span></div>
        <div class="bank-workflow-line"></div>
        <div class="bank-workflow-step"><b>4</b><span>Cetak</span></div>
    </section>

    <section class="panel-card bni-source-card">
        <div class="bni-source-grid">
            <div>
                <h3>1. Copy Paste Excel</h3>
                <p>Copy tabel dari Excel lalu paste ke kotak di bawah. Baris pertama boleh berupa header.</p>
                <textarea id="bniPaste" class="form-control bni-paste" placeholder="Paste data Excel di sini..."></textarea>
                <div class="bni-source-actions">
                    <button type="button" class="btn btn-primary" id="buildBniTable">
                        <i class="bi bi-table me-1"></i> Bentuk Tabel
                    </button>
                    <button type="button" class="btn btn-light" id="clearBniPaste">Bersihkan</button>
                </div>
            </div>
            <div class="bni-reference-card">
                <h3>Referensi Form BNI</h3>
                <img src="<?= asset('img/bni-remittance-reference.png') ?>" alt="Referensi Form Kiriman Uang BNI">
                <p>Data tabel akan ditempatkan ke field formulir sesuai posisi cetak.</p>
            </div>
        </div>

        <div class="bni-columns-help">
            <strong>Urutan kolom jika data tidak memiliki header:</strong>
            <div class="bni-column-chips">
                <?php foreach ($columns as $index => $column): ?>
                    <span><?= ($index + 1) ?>. <?= e($column['label']) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <form method="post" action="<?= url('/transactions/bni') ?>" id="bniSaveForm" class="panel-card bni-preview-card d-none">
        <?= csrf_field() ?>
        <input type="hidden" name="rows_json" id="bniRowsJson">
        <div class="bni-preview-head">
            <div>
                <span class="eyebrow dark">TAHAP 2</span>
                <h3>Hasil Bentuk Tabel</h3>
                <p>Periksa data sebelum disimpan menjadi formulir BNI.</p>
            </div>
            <label class="bni-batch-name">
                <span>Nama Batch</span>
                <input class="form-control" name="batch_name" value="BNI <?= date('d-m-Y H.i') ?>">
            </label>
        </div>

        <div class="table-responsive bni-preview-table-wrap">
            <table class="table modern-table bni-preview-table">
                <thead>
                    <tr>
                        <th>No</th><th>Tanggal</th><th>Penerima</th><th>Rekening</th><th>Bank</th><th>Pengirim</th><th>Jenis</th><th>Currency</th><th>Amount</th>
                    </tr>
                </thead>
                <tbody id="bniPreviewBody"></tbody>
            </table>
        </div>
        <div class="bni-save-actions">
            <span id="bniRowCount">0 baris</span>
            <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Simpan & Buat Form BNI</button>
        </div>
    </form>

    <section class="panel-card bni-history-card">
        <div class="bni-preview-head">
            <div><span class="eyebrow dark">RIWAYAT</span><h3>Batch BNI Terbaru</h3></div>
        </div>
        <div class="table-responsive">
            <table class="table modern-table">
                <thead><tr><th>Batch</th><th>Jumlah Form</th><th>Dibuat Oleh</th><th>Tanggal</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                        <tr>
                            <td><strong><?= e((string) ($batch['name'] ?? '')) ?></strong></td>
                            <td><?= (int) ($batch['row_count'] ?? $batch['total_rows'] ?? 0) ?></td>
                            <td><?= e((string) ($batch['creator_name'] ?? '-')) ?></td>
                            <td><?= !empty($batch['created_at']) ? e(date('d/m/Y H:i', strtotime((string) $batch['created_at']))) : '-' ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-light" href="<?= url('/transactions/bni/batches/' . (int) $batch['id']) ?>"><i class="bi bi-eye"></i></a>
                                <?php if (can('transaction.manage')): ?>
                                    <form class="d-inline" method="post" action="<?= url('/transactions/bni/batches/' . (int) $batch['id']) ?>" data-confirm="Hapus batch BNI ini?">
                                        <?= csrf_field() . method_field('DELETE') ?>
                                        <button class="btn btn-sm btn-light text-danger" type="submit"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($batches === []): ?><tr><td colspan="5" class="empty-state">Belum ada batch BNI.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
(() => {
    const columns = <?= json_encode(array_column($columns, 'key'), JSON_UNESCAPED_UNICODE) ?>;
    const alias = {
        transactiondate:'transaction_date', tanggal:'transaction_date', date:'transaction_date', tanggaltransaksi:'transaction_date',
        namapenerima:'beneficiary_name', beneficiary:'beneficiary_name', beneficiaryname:'beneficiary_name', penerima:'beneficiary_name',
        alamatpenerima:'beneficiary_address', beneficiaryaddress:'beneficiary_address',
        teleponpenerima:'beneficiary_phone', phonebeneficiary:'beneficiary_phone',
        kotapenerima:'beneficiary_city', beneficiarycity:'beneficiary_city',
        negarapenerima:'beneficiary_country', beneficiarycountry:'beneficiary_country',
        bankpenerima:'beneficiary_bank', beneficiarybank:'beneficiary_bank', bankbeneficiary:'beneficiary_bank',
        kotabank:'beneficiary_bank_city', cityofbank:'beneficiary_bank_city',
        negarabank:'beneficiary_bank_country', countryofbank:'beneficiary_bank_country',
        norekeningpenerima:'beneficiary_account', rekeningpenerima:'beneficiary_account', beneficiaryaccount:'beneficiary_account', accountnumberofbeneficiary:'beneficiary_account',
        namapengirim:'remitter_name', pengirim:'remitter_name', remitter:'remitter_name', payer:'remitter_name', billtopayer:'remitter_name',
        namaalias:'remitter_alias', aliasname:'remitter_alias',
        noid:'remitter_id', idno:'remitter_id', passport:'remitter_id',
        alamatpengirim:'remitter_address', remitteraddress:'remitter_address', payeraddress:'remitter_address',
        teleponpengirim:'remitter_phone', remitterphone:'remitter_phone',
        kotapengirim:'remitter_city', remittercity:'remitter_city', payercity:'remitter_city',
        negarapengirim:'remitter_country', remittercountry:'remitter_country', payercountry:'remitter_country',
        tujuantransaksi:'transaction_purpose', transactionpurpose:'transaction_purpose', paymentdetails:'transaction_purpose',
        berita:'message', message:'message',
        jenispengiriman:'transfer_type', typeoftransfer:'transfer_type', transfertype:'transfer_type',
        sumberdana:'source_of_fund', sourceoffund:'source_of_fund',
        currency:'currency', matauang:'currency',
        amount:'amount', amounts:'amount', jumlahdana:'amount', nominal:'amount',
        kurs:'exchange_rate', exchangerate:'exchange_rate',
        komisi:'commission', commission:'commission',
        handling:'handling',
        bankkoresponden:'correspondent_bank', correspondentbank:'correspondent_bank',
        biayalain:'other_charge', othercharge:'other_charge',
        terbilang:'amount_words', amountinwords:'amount_words'
    };

    const paste = document.getElementById('bniPaste');
    const build = document.getElementById('buildBniTable');
    const clear = document.getElementById('clearBniPaste');
    const form = document.getElementById('bniSaveForm');
    const body = document.getElementById('bniPreviewBody');
    const jsonField = document.getElementById('bniRowsJson');
    const count = document.getElementById('bniRowCount');

    const normalize = value => String(value ?? '').toLowerCase().replace(/[^a-z0-9]/g, '');
    const esc = value => String(value ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));

    function parse() {
        const raw = paste.value.trim();
        if (!raw) { alert('Paste data Excel terlebih dahulu.'); return; }
        const lines = raw.split(/\r?\n/).filter(line => line.trim() !== '');
        if (!lines.length) return;
        const matrix = lines.map(line => line.split('\t'));
        const first = matrix[0].map(normalize);
        const mappedHeaders = first.map(h => alias[h] || (columns.includes(h) ? h : ''));
        const recognized = mappedHeaders.filter(Boolean).length;
        const hasHeader = recognized >= Math.min(3, first.length);
        const headerMap = {};
        if (hasHeader) mappedHeaders.forEach((key, i) => { if (key) headerMap[i] = key; });
        const source = hasHeader ? matrix.slice(1) : matrix;
        const rows = source.map(cells => {
            const row = {};
            columns.forEach(k => row[k] = '');
            cells.forEach((value, i) => {
                const key = hasHeader ? headerMap[i] : columns[i];
                if (key) row[key] = String(value ?? '').trim();
            });
            return row;
        }).filter(row => Object.values(row).some(v => String(v).trim() !== ''));

        if (!rows.length) { alert('Tidak ada baris data yang terbaca.'); return; }
        jsonField.value = JSON.stringify(rows);
        body.innerHTML = rows.map((r, i) => `<tr>
            <td>${i+1}</td><td>${esc(r.transaction_date)}</td><td>${esc(r.beneficiary_name)}</td>
            <td>${esc(r.beneficiary_account)}</td><td>${esc(r.beneficiary_bank)}</td><td>${esc(r.remitter_name)}</td>
            <td>${esc(r.transfer_type)}</td><td>${esc(r.currency)}</td><td>${esc(r.amount)}</td>
        </tr>`).join('');
        count.textContent = `${rows.length} baris`;
        form.classList.remove('d-none');
        form.scrollIntoView({behavior:'smooth', block:'start'});
    }

    build.addEventListener('click', parse);
    clear.addEventListener('click', () => { paste.value=''; body.innerHTML=''; jsonField.value=''; form.classList.add('d-none'); });
})();
</script>
