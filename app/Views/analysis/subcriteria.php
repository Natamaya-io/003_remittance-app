<div class="spk-module">
<?php require __DIR__ . '/_nav.php'; ?>
<div class="spk-section"><div class="spk-section-head"><div><h3>Tambah Sub Kriteria</h3><div class="spk-muted">Contoh: “Sangat Baik” = 5.</div></div></div>
<form method="post" action="<?=url('/analysis/subcriteria/save')?>"><?=csrf_field()?>
<div class="spk-form-row cols-3"><div><label class="form-label">Kriteria</label><select class="form-select" name="criterion_id" required><?php foreach($criteria as $c):?><option value="<?=(int)$c['id']?>"><?=e($c['code'].' - '.$c['name'])?></option><?php endforeach;?></select></div><div><label class="form-label">Nama Sub Kriteria</label><input class="form-control" name="name" required></div><div><label class="form-label">Nilai</label><input class="form-control" type="number" step="0.000001" min="0" name="value" required></div></div><input type="hidden" name="sort_order" value="99"><button class="btn btn-primary mt-3">Simpan</button></form></div>
<?php foreach($criteria as $c): ?><div class="spk-section"><div class="spk-section-head"><div><h3><?=e($c['name'])?> (<?=e($c['code'])?>)</h3><div class="spk-muted"><?=strtoupper(e($c['criterion_type']))?></div></div></div>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>No</th><th>Nama Sub Kriteria</th><th>Nilai</th><th>Aksi</th></tr></thead><tbody>
<?php foreach(($grouped[(int)$c['id']]??[]) as $i=>$s):?><tr><td><?=$i+1?></td><td><?=e($s['name'])?></td><td class="num"><?=number_format((float)$s['value'],4,',','.')?></td><td><form method="post" action="<?=url('/analysis/subcriteria/'.(int)$s['id'].'/delete')?>" onsubmit="return confirm('Hapus sub kriteria?')"><?=csrf_field()?><button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr><?php endforeach;?>
<?php if(empty($grouped[(int)$c['id']])):?><tr><td colspan="4" class="spk-muted">Belum ada sub kriteria.</td></tr><?php endif;?></tbody></table></div></div><?php endforeach;?>
</div>
