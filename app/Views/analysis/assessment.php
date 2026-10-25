<div class="spk-module">
<?php require __DIR__ . '/_nav.php'; ?>
<div class="spk-section">
    <div class="spk-section-head">
        <div><h3>Matriks C1-C6</h3><div class="spk-muted">P1: 1 September-31 Oktober 2024 · P2: 1 November-31 Desember 2024.</div></div>
        <form method="post" action="<?=url('/analysis/assessment/reset')?>" onsubmit="return confirm('Kembalikan semua nilai ke otomatis dari Report 2024 + Actual?')"><?=csrf_field()?><button class="btn btn-light">Reset ke Otomatis</button></form>
    </div>

    <div class="table-responsive"><table class="spk-table"><thead><tr><th>Alternatif</th><?php foreach($criteria as $c):?><th><?=e($c['code'])?><div class="spk-muted">Benefit</div></th><?php endforeach;?></tr></thead><tbody>
    <?php foreach($alternatives as $a):?><tr><td><strong><?=e($a['name'])?></strong></td><?php foreach($criteria as $c):$row=$assessmentMatrix[(int)$a['id']][(int)$c['id']]??null;$auto=(float)($row['auto_value']??0);$final=$auto;?><td><div><strong><?=number_format($final,8,',','.')?></strong></div><small class="spk-muted">Sumber: Excel olahan</small></td><?php endforeach;?></tr><?php endforeach;?></tbody></table></div>

</div>

<div class="spk-section">
    <div class="spk-section-head"><div><h3>Detail Pembentuk Nilai Auto</h3><div class="spk-muted">Klik alternatif untuk melihat indikator P1 dan P2 yang menjadi dasar C1-C6.</div></div></div>
    <?php foreach($alternatives as $a):
        $firstCriterion = $criteria[0] ?? null;
        $row = $firstCriterion ? ($assessmentMatrix[(int)$a['id']][(int)$firstCriterion['id']] ?? null) : null;
        $detail = $row && !empty($row['source_detail_json']) ? json_decode((string)$row['source_detail_json'], true) : [];
        $p1 = $detail['p1'] ?? [];
        $p2 = $detail['p2'] ?? [];
    ?>
    <details class="mb-3">
        <summary class="btn btn-light w-100 text-start"><strong><?=e($a['name'])?></strong></summary>
        <div class="table-responsive mt-2"><table class="spk-table"><thead><tr><th>Periode</th><th>Transaksi</th><th>Hari Aktif</th><th>Tx/Hari</th><th>Negara</th><th>Bank/SWIFT</th><th>Mata Uang</th><th>Refund</th></tr></thead><tbody>
        <tr><td><strong>P1</strong></td><td class="num"><?=number_format((int)($p1['transactions']??0),0,',','.')?></td><td class="num"><?=number_format((int)($p1['active_days']??0),0,',','.')?></td><td class="num"><?=number_format((float)($p1['transactions_per_active_day']??0),6,',','.')?></td><td class="num"><?=(int)($p1['countries']??0)?></td><td class="num"><?=(int)($p1['bank_swift']??0)?></td><td class="num"><?=(int)($p1['currencies']??0)?></td><td class="num"><?=(int)($p1['refunds']??0)?></td></tr>
        <tr><td><strong>P2</strong></td><td class="num"><?=number_format((int)($p2['transactions']??0),0,',','.')?></td><td class="num"><?=number_format((int)($p2['active_days']??0),0,',','.')?></td><td class="num"><?=number_format((float)($p2['transactions_per_active_day']??0),6,',','.')?></td><td class="num"><?=(int)($p2['countries']??0)?></td><td class="num"><?=(int)($p2['bank_swift']??0)?></td><td class="num"><?=(int)($p2['currencies']??0)?></td><td class="num"><?=(int)($p2['refunds']??0)?></td></tr>
        </tbody></table></div>
    </details>
    <?php endforeach; ?>
</div>
</div>
