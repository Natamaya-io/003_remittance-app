<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Flash;

$pageTitle = isset($title)
    ? trim((string) $title)
    : 'Masuk';

$content = isset($content)
    ? (string) $content
    : '';

$appName = trim(
    (string)
    App::config('app.name')
);

if ($appName === '') {
    $appName = 'Fable RemitOps';
}

$flashes = Flash::all();
?>
<!doctype html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="theme-color"
        content="#ffffff"
    >

    <meta
        name="color-scheme"
        content="light"
    >

    <title>
        <?= e($pageTitle) ?>
        ·
        <?= e($appName) ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= e(asset('css/app.css?v=22.0.2')) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= e(asset('css/login.css?v=36.0.0')) ?>"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link rel="stylesheet" href="<?= e(asset('css/theme-clean-global.css')) ?>">
</head>

<body class="login-body">

    <?php if ($flashes !== []): ?>

        <div class="login-flash-stack">

            <?php foreach ($flashes as $flash): ?>

                <?php

                $flashMessage = trim(
                    (string) (
                        $flash['message']
                        ?? ''
                    )
                );

                ?>

                <?php if ($flashMessage !== ''): ?>

                    <div
                        class="login-flash"
                        role="alert"
                    >

                        <span class="login-flash-icon">

                            <i class="bi bi-info-lg"></i>

                        </span>

                        <div class="login-flash-copy">

                            <strong>
                                Informasi
                            </strong>

                            <span>
                                <?= e($flashMessage) ?>
                            </span>

                        </div>

                        <button
                            type="button"
                            class="login-flash-close"
                            data-login-flash-close
                            aria-label="Tutup notifikasi"
                        >
                            ×
                        </button>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <?= $content ?>

    <script
        src="<?= e(asset('js/app.js?v=22.0.1')) ?>"
        defer
    ></script>

    <script>
    document.addEventListener(
        'click',
        function (event) {

            const close =
                event.target.closest(
                    '[data-login-flash-close]'
                );

            if (!close) {
                return;
            }

            close
                .closest('.login-flash')
                ?.remove();
        }
    );
    </script>

</body>

</html>