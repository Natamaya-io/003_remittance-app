<div class="spk-module">
<?php require __DIR__ . '/_nav.php'; ?>
<div class="spk-section"><div class="spk-section-head"><div x-data="{ showInfo: false }"><div class="d-inline-flex align-items-center gap-2"><h3 class="m-0">Empat Alternatif Penelitian</h3><button type="button" @click="showInfo = !showInfo" class="info-toggle-btn" :class="{ 'is-active': showInfo }" :aria-expanded="showInfo.toString()" aria-label="Tampilkan info alternatif" title="Informasi bantuan"><i class="bi bi-info-circle text-xs leading-none"></i></button></div><div x-show="showInfo" x-cloak x-transition class="spk-muted mt-1" style="display: none;">Status aktif diberikan jika perusahaan mempunyai transaksi pada P1 dan P2.</div></div></div><div class="table-responsive"><table class="spk-table"><thead><tr><th>No</th><th>Kode</th><th>Nama Alternatif</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>
<?php foreach($alternatives as $i=>$a):?>
<?php if(!in_array($a['alternative_key'],['FABLE','JURITA','GSB','KAIROS'],true)) continue; ?>
<tr><td><?=$i+1?></td><td><strong><?=e($a['alternative_key'])?></strong></td><td><strong><?=e($a['name'])?></strong></td><td><span class="badge-spk <?=(int)$a['is_active']===1?'active':''?>"><?=(int)$a['is_active']===1?'AKTIF':'NONAKTIF'?></span></td><td class="spk-muted"><?=e($a['source_note']??'')?></td></tr>
<?php endforeach;?></tbody></table></div></div>
</div>
