<?php
$criteria = $calculation['criteria'];
$alternatives = $calculation['alternatives'];
$matrix = $calculation['matrix'];
$entropy = $calculation['entropy'];
$topsis = $calculation['topsis'];
?>
<div class="spk-module">
<?php require __DIR__ . '/_nav.php'; ?>
<div class="spk-section"><div class="spk-formula"><strong>P1:</strong> 1 September-31 Oktober 2024 &nbsp; | &nbsp; <strong>P2:</strong> 1 November-31 Desember 2024 &nbsp; | &nbsp; <strong>Semua kriteria:</strong> Benefit</div></div>

<div class="spk-section">
<div class="spk-section-head"><div x-data="{ showInfo: false }"><div class="d-inline-flex align-items-center gap-2"><h3 class="m-0">Metode ENTROPY</h3><button type="button" @click="showInfo = !showInfo" class="info-toggle-btn" :class="{ 'is-active': showInfo }" :aria-expanded="showInfo.toString()" aria-label="Tampilkan info rumus Entropy" title="Informasi alur"><i class="bi bi-info-circle text-xs leading-none"></i></button></div><div x-show="showInfo" x-cloak x-transition class="spk-muted mt-1" style="display: none;">Kij -&gt; aij -&gt; Ej -&gt; Dj -&gt; Wj</div></div></div>
<div class="spk-formula mb-3"><strong>Kij = Xij / max(Xj)</strong>, lalu aij = Kij / Sum(Kij).</div>
<h3 class="mb-2">1. Matriks Keputusan (X)</h3>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Alternatif</th><?php foreach($criteria as $c):?><th><?=e($c['code'])?><div class="spk-muted"><?=strtoupper(e($c['criterion_type']))?></div></th><?php endforeach;?></tr></thead><tbody>
<?php foreach($alternatives as $i=>$a):?><tr><td><strong><?=e($a['name'])?></strong></td><?php foreach($matrix[$i] as $v):?><td class="num"><?=number_format((float)$v,8,',','.')?></td><?php endforeach;?></tr><?php endforeach;?></tbody></table></div>

<h3 class="mt-4 mb-2">2. Matriks Normalisasi Entropy (Kij)</h3>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Alternatif</th><?php foreach($criteria as $c):?><th><?=e($c['code'])?></th><?php endforeach;?></tr></thead><tbody>
<?php foreach($alternatives as $i=>$a):?><tr><td><?=e($a['name'])?></td><?php foreach($entropy['normalized'][$i] as $v):?><td class="num"><?=number_format((float)$v,12,',','.')?></td><?php endforeach;?></tr><?php endforeach;?><tr><td><strong>TOTAL</strong></td><?php foreach($entropy['column_totals'] as $v):?><td class="num"><strong><?=number_format((float)$v,12,',','.')?></strong></td><?php endforeach;?></tr></tbody></table></div>

<h3 class="mt-4 mb-2">3. Matriks aij</h3>
<div class="spk-formula mb-2">aij = Kij / Sum(Kij)</div>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Alternatif</th><?php foreach($criteria as $c):?><th><?=e($c['code'])?></th><?php endforeach;?></tr></thead><tbody>
<?php foreach($alternatives as $i=>$a):?><tr><td><?=e($a['name'])?></td><?php foreach($entropy['aij'][$i] as $v):?><td class="num"><?=number_format((float)$v,12,',','.')?></td><?php endforeach;?></tr><?php endforeach;?></tbody></table></div>

<h3 class="mt-4 mb-2">4-6. Entropy, Dispersi dan Bobot</h3>
<div class="spk-formula mb-2">Ej = -1/ln(m) x Sum[aij ln(aij)] &nbsp; | &nbsp; Dj = 1 - Ej &nbsp; | &nbsp; Wj = Dj / Sum(Dj)</div>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Kriteria</th><th>Jenis</th><th>Entropy (Ej)</th><th>Dispersi (Dj)</th><th>Bobot (Wj)</th></tr></thead><tbody>
<?php foreach($criteria as $j=>$c):?><tr><td><strong><?=e($c['code'])?></strong> - <?=e($c['name'])?></td><td><span class="badge-spk <?=$c['criterion_type']==='cost'?'cost':''?>"><?=strtoupper(e($c['criterion_type']))?></span></td><td class="num"><?=number_format((float)$entropy['entropy'][$j],12,',','.')?></td><td class="num"><?=number_format((float)$entropy['dispersion'][$j],12,',','.')?></td><td class="num"><strong><?=number_format((float)$entropy['weights'][$j],12,',','.')?></strong></td></tr><?php endforeach;?></tbody></table></div>
</div>

<div class="spk-section">
<div class="spk-section-head"><div x-data="{ showInfo: false }"><div class="d-inline-flex align-items-center gap-2"><h3 class="m-0">Metode TOPSIS</h3><button type="button" @click="showInfo = !showInfo" class="info-toggle-btn" :class="{ 'is-active': showInfo }" :aria-expanded="showInfo.toString()" aria-label="Tampilkan info rumus TOPSIS" title="Informasi alur"><i class="bi bi-info-circle text-xs leading-none"></i></button></div><div x-show="showInfo" x-cloak x-transition class="spk-muted mt-1" style="display: none;">Rij -&gt; Yij -&gt; A+ / A- -&gt; D+ / D- -&gt; Vi -&gt; Ranking</div></div></div>
<h3 class="mb-2">1. Matriks Ternormalisasi (R)</h3><div class="spk-formula mb-2">Rij = Xij / sqrt(Sum Xij^2)</div>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Alternatif</th><?php foreach($criteria as $c):?><th><?=e($c['code'])?></th><?php endforeach;?></tr></thead><tbody><?php foreach($alternatives as $i=>$a):?><tr><td><?=e($a['name'])?></td><?php foreach($topsis['normalized'][$i] as $v):?><td class="num"><?=number_format((float)$v,12,',','.')?></td><?php endforeach;?></tr><?php endforeach;?></tbody></table></div>
<h3 class="mt-4 mb-2">2. Matriks Ternormalisasi Terbobot (Y)</h3><div class="spk-formula mb-2">Yij = Wj x Rij</div>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Alternatif</th><?php foreach($criteria as $c):?><th><?=e($c['code'])?></th><?php endforeach;?></tr></thead><tbody><?php foreach($alternatives as $i=>$a):?><tr><td><?=e($a['name'])?></td><?php foreach($topsis['weighted'][$i] as $v):?><td class="num"><?=number_format((float)$v,12,',','.')?></td><?php endforeach;?></tr><?php endforeach;?></tbody></table></div>
<h3 class="mt-4 mb-2">3. Solusi Ideal Positif dan Negatif</h3>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Solusi</th><?php foreach($criteria as $c):?><th><?=e($c['code'])?></th><?php endforeach;?></tr></thead><tbody><tr><td><strong>A+</strong></td><?php foreach($topsis['ideal_positive'] as $v):?><td class="num"><?=number_format((float)$v,12,',','.')?></td><?php endforeach;?></tr><tr><td><strong>A-</strong></td><?php foreach($topsis['ideal_negative'] as $v):?><td class="num"><?=number_format((float)$v,12,',','.')?></td><?php endforeach;?></tr></tbody></table></div>
<h3 class="mt-4 mb-2">4-6. Jarak, Kedekatan Relatif dan Ranking</h3><div class="spk-formula mb-2">Vi = D- / (D+ + D-). Nilai Vi terbesar menjadi ranking terbaik.</div>
<div class="table-responsive"><table class="spk-table"><thead><tr><th>Alternatif</th><th>D+</th><th>D-</th><th>Nilai V</th><th>Ranking</th></tr></thead><tbody>
<?php foreach($alternatives as $i=>$a):?><tr><td><strong><?=e($a['name'])?></strong></td><td class="num"><?=number_format((float)$topsis['distance_positive'][$i],12,',','.')?></td><td class="num"><?=number_format((float)$topsis['distance_negative'][$i],12,',','.')?></td><td class="num"><strong><?=number_format((float)$topsis['preferences'][$i],12,',','.')?></strong></td><td class="num">#<?=(int)$topsis['ranks'][$i]?></td></tr><?php endforeach;?></tbody></table></div>
<form method="post" action="<?=url('/analysis/calculation/save')?>" class="mt-4"><?=csrf_field()?><div class="spk-form-row cols-2"><div><label class="form-label">Nama Hasil</label><input class="form-control" name="name" value="Analisis Entropy-TOPSIS <?=date('d-m-Y H:i')?>"></div><div class="d-flex align-items-end"><button class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i>Simpan ke Hasil Akhir</button></div></div></form>
</div>
</div>
