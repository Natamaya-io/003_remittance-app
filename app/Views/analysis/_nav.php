<?php
$spkPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$items = [
    ['/analysis', 'bi-speedometer2', 'Dashboard Analisis'],
    ['/analysis/criteria', 'bi-box', 'Data Kriteria'],
    ['/analysis/alternatives', 'bi-people', 'Data Alternatif'],
    ['/analysis/assessment', 'bi-pencil-square', 'Data Penilaian'],
    ['/analysis/calculation', 'bi-calculator', 'Data Perhitungan'],
    ['/analysis/results', 'bi-bar-chart', 'Hasil Akhir'],
];
?>
<link rel="stylesheet" href="<?= asset('css/analysis-spk.css') ?>">
<div class="spk-topnav">
<?php foreach ($items as [$path,$icon,$label]):
    $full = parse_url(url($path), PHP_URL_PATH) ?: $path;
    $active = $path === '/analysis' ? ($spkPath === $full || rtrim($spkPath,'/') === rtrim($full,'/')) : str_starts_with($spkPath,$full);
?>
<a href="<?= url($path) ?>" class="<?= $active ? 'active' : '' ?>"><i class="bi <?= e($icon) ?>"></i><?= e($label) ?></a>
<?php endforeach; ?>
</div>
