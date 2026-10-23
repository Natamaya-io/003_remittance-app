<div class="spk-module">
<?php require __DIR__ . '/_nav.php'; ?>
<div class="spk-hero"><span class="eyebrow dark">ALTERNATIF PENELITIAN</span><h2>Data Alternatif</h2><p>Mode penelitian menggunakan empat perusahaan tetap. Alternatif lain tidak digunakan dalam perhitungan skripsi.</p></div>
<div class="spk-section"><div class="spk-section-head"><div><h3>Empat Alternatif Penelitian</h3><div class="spk-muted">Status aktif diberikan jika perusahaan mempunyai transaksi pada P1 dan P2.</div></div></div><div class="table-responsive"><table class="spk-table"><thead><tr><th>No</th><th>Kode</th><th>Nama Alternatif</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>
<?php foreach($alternatives as $i=>$a):?>
<?php if(!in_array($a['alternative_key'],['FABLE','JURITA','GSB','KAIROS'],true)) continue; ?>
<tr><td><?=$i+1?></td><td><strong><?=e($a['alternative_key'])?></strong></td><td><strong><?=e($a['name'])?></strong></td><td><span class="badge-spk <?=(int)$a['is_active']===1?'active':''?>"><?=(int)$a['is_active']===1?'AKTIF':'NONAKTIF'?></span></td><td class="spk-muted"><?=e($a['source_note']??'')?></td></tr>
<?php endforeach;?></tbody></table></div></div>
</div>
