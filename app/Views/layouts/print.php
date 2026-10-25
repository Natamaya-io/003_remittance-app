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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(asset('css/theme-clean-global.css')) ?>">
    <style>
        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif !important;
        }
        h1, h2, h3, h4, h5, h6, th, .heading {
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif !important;
            font-weight: 600 !important;
        }
        b, strong, .font-bold {
            font-weight: 600 !important;
        }
    </style>
</head>
<body>
<?= $content ?>
</body>
</html>
