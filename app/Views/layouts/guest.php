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

    <!-- =========================================================
         FLOATING TOAST NOTIFICATION STACK (GEMINI.MD SECTION 16)
         ========================================================= -->
    <div
        class="fixed bottom-20 sm:bottom-6 right-0 sm:right-6 left-0 sm:left-auto z-[9999] w-full sm:max-w-md pointer-events-none space-y-3 px-4 sm:px-0"
        id="remitToastContainer"
        x-data="remitToastStack"
        x-cloak
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                class="toast-item pointer-events-auto rounded-xl border p-4 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.08),0_8px_10px_-6px_rgba(0,0,0,0.04)] transition-all duration-200 ease-out"
                :class="toastClasses(toast.type)"
                role="alert"
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-[8px]"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-[8px]"
                @mouseenter="pauseTimer(toast)"
                @mouseleave="resumeTimer(toast)"
            >
                <div class="flex items-start gap-3">
                    <!-- Ikon Kustom Telanjang -->
                    <span class="flex-shrink-0 text-base leading-none pt-0.5">
                        <i :class="toastIcon(toast.type)"></i>
                    </span>

                    <!-- Badan Pesan -->
                    <div class="flex-1 min-w-0">
                        <strong
                            x-show="toast.title"
                            x-text="toast.title"
                            class="block font-heading font-semibold text-sm leading-tight mb-1"
                        ></strong>
                        <div
                            x-text="toast.message"
                            class="font-sans text-xs sm:text-sm leading-relaxed break-words"
                        ></div>
                    </div>

                    <!-- Tombol Tutup Kustom (.toast-close) -->
                    <button
                        type="button"
                        class="toast-close"
                        @click="dismiss(toast.id)"
                        aria-label="Tutup notifikasi"
                        title="Tutup notifikasi"
                    >
                        <i class="bi bi-x-lg text-xs leading-none"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- BOOTSTRAP DATA FLASH SESSION BACKEND -->
    <?php if ($flashes !== []): ?>
        <script type="application/json" id="remit-flashes-data">
            <?= json_encode($flashes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        </script>
    <?php endif; ?>

    <?= $content ?>

    <script
        src="<?= e(asset('js/app.js?v=22.0.1')) ?>"
        defer
    ></script>

    <script
        src="<?= e(asset('js/custom-dialogs.js?v=1.0.0')) ?>"
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