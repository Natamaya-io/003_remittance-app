<?php

declare(strict_types=1);

use App\Core\App;

$pageTitle = isset($title) ? trim((string) $title) : 'Cetak';
$content = isset($content) ? (string) $content : '';
$appName = trim((string) App::config('app.name')) ?: 'Fable RemitOps';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · <?= e($appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(asset('css/theme-clean-global.css')) ?>">
</head>
<body>
<?= $content ?>
</body>
</html>
