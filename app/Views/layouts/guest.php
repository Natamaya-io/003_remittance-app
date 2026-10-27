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
    $appName = 'RemitGo!';
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

    <!-- =====================================================
         GOOGLE FONTS: DM SANS (HEADINGS/CTA) & ROBOTO (BODY)
    ====================================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link rel="stylesheet" href="<?= e(asset('css/tailwind.css?v=1.0.0')) ?>">

    <style>
        :root {
            --font-app: 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'DM Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --font-cta: 'DM Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }
        html {
            font-size: 20px;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        @media (max-width: 639px) {
            html {
                font-size: 16px;
            }
        }
        @media (min-width: 640px) and (max-width: 1023px) {
            html {
                font-size: 18px;
            }
        }
        @media (min-width: 1024px) {
            html {
                font-size: 20px;
            }
        }
        html, body.login-body {
            height: 100%;
            max-height: 100vh;
            overflow: hidden;
        }
        body, body.login-body {
            font-family: var(--font-app) !important;
            font-size: 0.95rem;
            line-height: 1.6;
            background: #F5F2F2 !important;
            color: #111827;
        }
        h1, h2, h3, h4, h5, h6, .brand-title {
            font-family: var(--font-heading) !important;
            font-weight: 600 !important;
            letter-spacing: -0.015em;
        }
        button, .btn, .btn-primary, [role="button"] {
            font-family: var(--font-cta) !important;
        }
        b, strong, .font-bold {
            font-weight: 600 !important;
        }
    </style>
</head>

<body class="login-body h-screen max-h-screen bg-[#F5F2F2] text-gray-900 antialiased font-sans overflow-hidden">

    <?php if ($flashes !== []): ?>
        <div class="fixed top-5 left-1/2 -translate-x-1/2 z-50 w-full max-w-md px-4 space-y-3">
            <?php foreach ($flashes as $flash): ?>
                <?php
                $flashType = trim((string) ($flash['type'] ?? 'info'));
                $flashMessage = trim((string) ($flash['message'] ?? ''));
                if ($flashMessage === '') continue;
                $alertClass = match($flashType) {
                    'success' => 'alert-success',
                    'danger', 'error' => 'alert-danger',
                    'warning' => 'alert-warning',
                    default => 'alert-info',
                };
                $alertIcon = match($flashType) {
                    'success' => 'bi-check-circle-fill',
                    'danger', 'error' => 'bi-x-circle-fill',
                    'warning' => 'bi-exclamation-triangle-fill',
                    default => 'bi-info-circle-fill',
                };
                ?>
                <div
                    class="alert <?= $alertClass ?>"
                    role="alert"
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div class="alert-content">
                        <span class="alert-icon">
                            <i class="bi <?= $alertIcon ?>"></i>
                        </span>
                        <div class="alert-body">
                            <div class="alert-message"><?= e($flashMessage) ?></div>
                        </div>
                    </div>
                    <button
                        class="alert-close"
                        type="button"
                        @click="show = false"
                        aria-label="Tutup notifikasi"
                    >
                        <i class="bi bi-x text-lg leading-none"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $content ?>

    <script
        src="<?= e(asset('js/app.js?v=22.0.1')) ?>"
        defer
    ></script>

    <script
        src="<?= e(asset('vendor/alpine/alpine.min.js')) ?>"
        defer
    ></script>

    <script
        src="<?= e(asset('vendor/preline/preline.min.js')) ?>"
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