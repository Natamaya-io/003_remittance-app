<?php

declare(strict_types=1);

$dayNames = [
    1 => 'Senin',
    2 => 'Selasa',
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu',
];

$monthNames = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

$todayLabel = (
    $dayNames[(int) date('N')] ?? ''
)
    . ', '
    . date('d')
    . ' '
    . (
        $monthNames[(int) date('n')]
        ?? ''
    )
    . ' '
    . date('Y');

$currentUser = auth_user() ?? [];

$currentName = trim(
    (string) (
        $currentUser['name']
        ?? 'Pengguna'
    )
);

$currentRoleName = trim(
    (string) (
        $currentUser['role_name']
        ?? $currentUser['role_code']
        ?? 'Pengguna Sistem'
    )
);

$initial = strtoupper(
    substr(
        $currentName,
        0,
        1
    )
);
?>

<!-- ========== PRELINE NATIVE APPLICATION HEADER ========== -->
<header class="sticky top-0 inset-x-0 z-[48] flex flex-wrap md:justify-start md:flex-nowrap w-full bg-white border-b border-gray-200 text-sm py-2.5 sm:py-3.5 lg:ps-64">
    <div class="px-4 sm:px-6 flex items-center justify-between w-full">
        <!-- Left: Mobile Trigger & Title/Breadcrumbs -->
        <div class="flex items-center gap-x-3">
            <!-- Mobile Toggle Navigation Button -->
            <button
                type="button"
                class="py-2 px-2.5 inline-flex items-center gap-x-1.5 text-xs rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 lg:hidden"
                data-hs-overlay="#hs-application-sidebar"
                aria-controls="hs-application-sidebar"
                aria-label="Buka navigasi"
            >
                <i class="bi bi-list text-lg leading-none"></i>
                <span class="sr-only">Buka navigasi</span>
            </button>

            <!-- Page Title & Date -->
            <div>
                <h1 class="text-base sm:text-lg font-bold text-gray-800 leading-tight">
                    <?= e($title ?? 'Dashboard') ?>
                </h1>
                <p class="text-[11px] sm:text-xs text-gray-500 font-medium">
                    <?= e($todayLabel) ?>
                </p>
            </div>
        </div>

        <!-- Right: Status & User Menu -->
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- System Status Indicator -->
            <div class="hidden sm:inline-flex items-center gap-x-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                <span class="size-1.5 inline-block rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem aktif
            </div>

            <!-- User Dropdown (Preline hs-dropdown + Alpine fallback) -->
            <div class="hs-dropdown [--placement:bottom-right] relative inline-flex" x-data="{ open: false }">
                <button
                    id="hs-dropdown-topbar-user"
                    type="button"
                    class="hs-dropdown-toggle inline-flex items-center gap-x-2 py-1.5 px-2.5 text-sm font-semibold rounded-xl border border-gray-200 bg-white text-gray-800 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    aria-label="User menu"
                    @click="open = !open"
                >
                    <div class="size-7 rounded-lg bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        <?= e($initial) ?>
                    </div>
                    <span class="text-gray-700 text-xs font-semibold max-w-[130px] truncate hidden md:inline-block">
                        <?= e($currentName) ?>
                    </span>
                    <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                </button>

                <div
                    class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-60 bg-white shadow-lg rounded-xl p-2 border border-gray-200 z-[60]"
                    :class="{ 'opacity-100 !block': open }"
                    @click.outside="open = false"
                    @keydown.escape.window="open = false"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="hs-dropdown-topbar-user"
                >
                    <div class="py-2.5 px-3 border-b border-gray-100 mb-1">
                        <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider">Akun Terautentikasi</p>
                        <p class="text-sm font-bold text-gray-800 truncate mt-0.5"><?= e($currentName) ?></p>
                        <p class="text-xs text-blue-600 font-semibold mt-0.5"><?= e($currentRoleName) ?></p>
                    </div>

                    <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition-colors" href="<?= url('/profile') ?>">
                        <i class="bi bi-person text-base text-gray-500"></i>
                        Profil pengguna
                    </a>

                    <div class="my-1 border-t border-gray-100"></div>

                    <form method="post" action="<?= url('/logout') ?>" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50 transition-colors text-left" data-no-loading="1">
                            <i class="bi bi-box-arrow-right text-base text-red-500"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
