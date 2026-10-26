<?php

declare(strict_types=1);

$path = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?: '/';

$currentUser = auth_user() ?? [];
$currentRole = (string) ($currentUser['role_code'] ?? '');

$documentRoles = [
    'super_admin',
    'superadmin',
    'supervisor',
    'admin_invoice',
    'invoice',
    'admin_complain',
    'complain',
];

$canOpenDocuments = in_array(
    $currentRole,
    $documentRoles,
    true
);

$isDashboard = $path === parse_url(url('/'), PHP_URL_PATH);
$isTransactions = str_contains($path, '/transactions');
$isPayerAccounts = str_contains($path, '/payer-accounts');
$isReports = str_contains($path, '/reports');
$isCompanies = str_contains($path, '/companies');
$isComplaints = str_contains($path, '/complaints');
$isInvoices = str_contains($path, '/invoices');

$isDocuments = str_contains($path, '/documents')
    || str_contains($path, '/document_templates.php')
    || str_contains($path, '/document_template_manager.php')
    || str_contains($path, '/invoice_templates.php')
    || str_contains($path, '/standing_instruction_7.php');

$isAnalysis = str_contains($path, '/analysis');
$isUsers = str_contains($path, '/users');
$isAudit = str_contains($path, '/audit');
$isProfile = str_contains($path, '/profile');

$isMoreActive = $isPayerAccounts
    || $isCompanies
    || $isComplaints
    || $isInvoices
    || $isDocuments
    || $isAnalysis
    || $isUsers
    || $isAudit;

$userName = trim(
    (string) ($currentUser['name'] ?? 'Pengguna')
);

$userInitial = strtoupper(
    substr($userName, 0, 1)
);

$userRoleName = trim(
    (string) (
        $currentUser['role_name']
        ?? $currentRole
        ?: 'Pengguna Sistem'
    )
);
?>

<!-- =======================================================================
     SPA FLOATING BOTTOM DOCK (TABLET & DESKTOP: sm and up)
     ======================================================================= -->
<nav
    class="hidden sm:inline-flex fixed bottom-5 left-1/2 -translate-x-1/2 z-40 items-center gap-1.5 p-1.5 bg-white/95 backdrop-blur-md border border-gray-200/90 rounded-2xl shadow-[0_12px_36px_-6px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.06)]"
    x-data="{ openMore: false, openProfile: false }"
    aria-label="Navigasi Utama SPA"
>
    <!-- Core Nav Links -->
    <a
        href="<?= url('/') ?>"
        class="spa-dock-tab <?= $isDashboard ? 'is-active' : '' ?>"
        <?= $isDashboard ? 'aria-current="page"' : '' ?>
        title="Dashboard"
    >
        <i class="bi bi-grid-1x2-fill <?= $isDashboard ? 'text-blue-600' : 'text-gray-400' ?>"></i>
        <span>Dashboard</span>
    </a>

    <?php if (can('transaction.view')): ?>
        <a
            href="<?= url('/transactions') ?>"
            class="spa-dock-tab <?= $isTransactions ? 'is-active' : '' ?>"
            <?= $isTransactions ? 'aria-current="page"' : '' ?>
            title="Transaksi"
        >
            <i class="bi bi-arrow-left-right <?= $isTransactions ? 'text-blue-600' : 'text-gray-400' ?>"></i>
            <span>Transaksi</span>
        </a>
    <?php endif; ?>

    <?php if (can('report.view')): ?>
        <a
            href="<?= url('/reports') ?>"
            class="spa-dock-tab <?= $isReports ? 'is-active' : '' ?>"
            <?= $isReports ? 'aria-current="page"' : '' ?>
            title="Laporan"
        >
            <i class="bi bi-file-earmark-bar-graph <?= $isReports ? 'text-blue-600' : 'text-gray-400' ?>"></i>
            <span>Laporan</span>
        </a>
    <?php endif; ?>

    <!-- Secondary Modules Popover (Upward) -->
    <div class="relative" @click.outside="openMore = false">
        <button
            type="button"
            @click="openMore = !openMore; openProfile = false"
            class="spa-dock-tab <?= $isMoreActive ? 'is-active' : '' ?>"
            :aria-expanded="openMore"
            aria-haspopup="true"
            title="Menu Lainnya"
        >
            <i class="bi bi-grid-3x3-gap-fill <?= $isMoreActive ? 'text-blue-600' : 'text-gray-400' ?>"></i>
            <span>Lainnya</span>
            <i class="bi bi-chevron-up text-[10px] text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openMore }"></i>
        </button>

        <div
            x-show="openMore"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            x-cloak
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 max-h-[75vh] overflow-y-auto bg-white rounded-2xl border border-gray-200 shadow-[0_16px_40px_-8px_rgba(0,0,0,0.18)] p-2 z-50 divide-y divide-gray-100"
        >
            <!-- Operasional -->
            <div class="py-1">
                <span class="dropdown-header">Operasional</span>
                <?php if (can('payer_account.view')): ?>
                    <a href="<?= url('/payer-accounts') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isPayerAccounts ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                        <i class="bi bi-bank <?= $isPayerAccounts ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                        <span>Rekening Payer</span>
                    </a>
                <?php endif; ?>
                <?php if (can('company.view')): ?>
                    <a href="<?= url('/companies') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isCompanies ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                        <i class="bi bi-buildings <?= $isCompanies ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                        <span>Data PT</span>
                    </a>
                <?php endif; ?>
                <?php if (can('complaint.view')): ?>
                    <a href="<?= url('/complaints') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isComplaints ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                        <i class="bi bi-headset <?= $isComplaints ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                        <span>Complain</span>
                    </a>
                <?php endif; ?>
                <?php if (can('invoice.view')): ?>
                    <a href="<?= url('/invoices') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isInvoices ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                        <i class="bi bi-receipt <?= $isInvoices ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                        <span>Invoice</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Dokumen & Analitik -->
            <?php if ($canOpenDocuments || can('analysis.view')): ?>
                <div class="py-1">
                    <span class="dropdown-header">Dokumen &amp; Analitik</span>
                    <?php if ($canOpenDocuments): ?>
                        <a href="<?= url('/documents') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isDocuments ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-folder2-open <?= $isDocuments ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>Dokumen &amp; Template</span>
                        </a>
                    <?php endif; ?>
                    <?php if (can('analysis.view')): ?>
                        <a href="<?= url('/analysis') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isAnalysis ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-diagram-3 <?= $isAnalysis ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>Entropy–TOPSIS</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Administrasi -->
            <?php if (can('user.manage') || can('audit.view')): ?>
                <div class="py-1">
                    <span class="dropdown-header">Administrasi</span>
                    <?php if (can('user.manage')): ?>
                        <a href="<?= url('/users') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isUsers ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-people <?= $isUsers ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>User &amp; Role</span>
                        </a>
                    <?php endif; ?>
                    <?php if (can('audit.view')): ?>
                        <a href="<?= url('/audit') ?>" class="dropdown-item flex items-center gap-2.5 py-1.5 px-3 rounded-xl text-xs <?= $isAudit ? 'active font-semibold text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-shield-check <?= $isAudit ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>Audit Log</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="h-5 w-px bg-gray-200 shrink-0"></div>

    <!-- User Profile & Session Pill -->
    <div class="relative" @click.outside="openProfile = false">
        <button
            type="button"
            @click="openProfile = !openProfile; openMore = false"
            class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-xl bg-gray-50 border border-gray-200/80 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors cursor-pointer shrink-0"
            :aria-expanded="openProfile"
            aria-haspopup="true"
            title="<?= e($userName) ?> (<?= e($userRoleName) ?>)"
        >
            <div class="size-6 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                <?= e($userInitial) ?>
            </div>
            <span class="text-xs font-semibold text-gray-800 max-w-[100px] truncate"><?= e($userName) ?></span>
            <i class="bi bi-chevron-up text-[10px] text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openProfile }"></i>
        </button>

        <!-- Upward Profile Popover -->
        <div
            x-show="openProfile"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            x-cloak
            class="absolute bottom-full right-0 mb-3 w-64 bg-white rounded-2xl border border-gray-200 shadow-[0_16px_40px_-8px_rgba(0,0,0,0.18)] p-2 z-50"
        >
            <div class="px-3 py-2.5 border-b border-gray-100 mb-1">
                <p class="text-xs font-bold text-gray-900 truncate"><?= e($userName) ?></p>
                <p class="text-[11px] text-gray-500 font-medium truncate"><?= e($userRoleName) ?></p>
            </div>
            <a href="<?= url('/profile') ?>" class="dropdown-item flex items-center gap-2 py-2 px-3 rounded-xl text-xs font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                <i class="bi bi-person text-base text-gray-400"></i>
                <span>Pengaturan Akun</span>
            </a>
            <div class="dropdown-divider my-1"></div>
            <form method="post" action="<?= url('/logout') ?>" class="m-0">
                <?= csrf_field() ?>
                <button
                    type="submit"
                    class="w-full flex items-center gap-2 py-2 px-3 rounded-xl text-xs font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors text-left border-none bg-transparent cursor-pointer"
                    data-no-loading="1"
                >
                    <i class="bi bi-box-arrow-right text-base text-red-500"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- =======================================================================
     SPA MOBILE BOTTOM BAR & DRAWER (MOBILE ONLY: sm:hidden)
     ======================================================================= -->
<div x-data="{ openMobileSheet: false }" class="sm:hidden">
    <!-- Bottom Tab Bar -->
    <nav
        class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200/90 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-2 py-1 flex items-center justify-around pb-[calc(0.25rem+env(safe-area-inset-bottom,0px))]"
        aria-label="Navigasi Bawah Mobile"
    >
        <!-- Dashboard -->
        <a
            href="<?= url('/') ?>"
            class="spa-mobile-tab <?= $isDashboard ? 'is-active text-blue-600' : 'text-gray-600' ?>"
            <?= $isDashboard ? 'aria-current="page"' : '' ?>
        >
            <i class="bi bi-grid-1x2-fill text-lg"></i>
            <span class="text-[10px] font-medium leading-none">Dashboard</span>
        </a>

        <!-- Transaksi -->
        <?php if (can('transaction.view')): ?>
            <a
                href="<?= url('/transactions') ?>"
                class="spa-mobile-tab <?= $isTransactions ? 'is-active text-blue-600' : 'text-gray-600' ?>"
                <?= $isTransactions ? 'aria-current="page"' : '' ?>
            >
                <i class="bi bi-arrow-left-right text-lg"></i>
                <span class="text-[10px] font-medium leading-none">Transaksi</span>
            </a>
        <?php endif; ?>

        <!-- Laporan -->
        <?php if (can('report.view')): ?>
            <a
                href="<?= url('/reports') ?>"
                class="spa-mobile-tab <?= $isReports ? 'is-active text-blue-600' : 'text-gray-600' ?>"
                <?= $isReports ? 'aria-current="page"' : '' ?>
            >
                <i class="bi bi-file-earmark-bar-graph text-lg"></i>
                <span class="text-[10px] font-medium leading-none">Laporan</span>
            </a>
        <?php endif; ?>

        <!-- Menu / Lainnya -->
        <button
            type="button"
            @click="openMobileSheet = true"
            class="spa-mobile-tab <?= $isMoreActive ? 'is-active text-blue-600' : 'text-gray-600' ?>"
            aria-label="Buka semua menu"
        >
            <div class="relative inline-flex">
                <i class="bi bi-grid-3x3-gap-fill text-lg"></i>
                <?php if ($isMoreActive): ?>
                    <span class="absolute -top-0.5 -right-0.5 size-2 bg-blue-600 rounded-full"></span>
                <?php endif; ?>
            </div>
            <span class="text-[10px] font-medium leading-none">Menu</span>
        </button>

        <!-- Akun / Profil -->
        <a
            href="<?= url('/profile') ?>"
            class="spa-mobile-tab <?= $isProfile ? 'is-active text-blue-600' : 'text-gray-600' ?>"
            <?= $isProfile ? 'aria-current="page"' : '' ?>
        >
            <div class="size-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[9px]">
                <?= e($userInitial) ?>
            </div>
            <span class="text-[10px] font-medium leading-none">Akun</span>
        </a>
    </nav>

    <!-- Mobile Drawer / Bottom Sheet -->
    <div
        x-show="openMobileSheet"
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="mobile-sheet-title"
    >
        <!-- Backdrop -->
        <div
            x-show="openMobileSheet"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs"
            @click="openMobileSheet = false"
        ></div>

        <!-- Sheet Panel -->
        <div
            x-show="openMobileSheet"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed bottom-0 inset-x-0 max-h-[85vh] bg-white rounded-t-3xl shadow-2xl border-t border-gray-200/80 flex flex-col overflow-hidden"
        >
            <!-- Handle & Header -->
            <div class="pt-3 pb-2.5 px-5 flex flex-col items-center border-b border-gray-100">
                <div class="w-10 h-1 bg-gray-300 rounded-full mb-2"></div>
                <div class="w-full flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-grid-3x3-gap-fill text-lg text-gray-900"></i>
                        <span id="mobile-sheet-title" class="font-bold text-sm text-gray-900">Menu Aplikasi</span>
                    </div>
                    <button
                        type="button"
                        @click="openMobileSheet = false"
                        class="size-8 inline-flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200"
                        aria-label="Tutup menu"
                    >
                        <i class="bi bi-x text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-4 overflow-y-auto space-y-4 pb-[calc(1.5rem+env(safe-area-inset-bottom,0px))]">
                <!-- User Profile Card -->
                <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50 border border-gray-200/80">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="size-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                            <?= e($userInitial) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate leading-tight"><?= e($userName) ?></p>
                            <p class="text-[11px] text-gray-500 font-medium truncate"><?= e($userRoleName) ?></p>
                        </div>
                    </div>
                    <form method="post" action="<?= url('/logout') ?>" class="m-0 shrink-0">
                        <?= csrf_field() ?>
                        <button
                            type="submit"
                            class="size-9 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 shadow-xs hover:bg-red-50 hover:text-red-600 hover:border-red-200 focus:outline-none transition-colors"
                            title="Keluar"
                            aria-label="Keluar sistem"
                            data-no-loading="1"
                        >
                            <i class="bi bi-box-arrow-right text-base leading-none"></i>
                        </button>
                    </form>
                </div>

                <!-- Operasional Links -->
                <div class="space-y-1">
                    <div class="px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                        Operasional
                    </div>
                    <?php if (can('payer_account.view')): ?>
                        <a href="<?= url('/payer-accounts') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isPayerAccounts ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-bank text-base <?= $isPayerAccounts ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>Rekening Payer</span>
                        </a>
                    <?php endif; ?>
                    <?php if (can('company.view')): ?>
                        <a href="<?= url('/companies') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isCompanies ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-buildings text-base <?= $isCompanies ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>Data PT</span>
                        </a>
                    <?php endif; ?>
                    <?php if (can('complaint.view')): ?>
                        <a href="<?= url('/complaints') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isComplaints ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-headset text-base <?= $isComplaints ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>Complain</span>
                        </a>
                    <?php endif; ?>
                    <?php if (can('invoice.view')): ?>
                        <a href="<?= url('/invoices') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isInvoices ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="bi bi-receipt text-base <?= $isInvoices ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                            <span>Invoice</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Dokumen & Analitik Links -->
                <?php if ($canOpenDocuments || can('analysis.view')): ?>
                    <div class="space-y-1">
                        <div class="px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Dokumen &amp; Analitik
                        </div>
                        <?php if ($canOpenDocuments): ?>
                            <a href="<?= url('/documents') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isDocuments ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                                <i class="bi bi-folder2-open text-base <?= $isDocuments ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                                <span>Dokumen &amp; Template</span>
                            </a>
                        <?php endif; ?>
                        <?php if (can('analysis.view')): ?>
                            <a href="<?= url('/analysis') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isAnalysis ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                                <i class="bi bi-diagram-3 text-base <?= $isAnalysis ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                                <span>Entropy–TOPSIS</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Administrasi Links -->
                <?php if (can('user.manage') || can('audit.view')): ?>
                    <div class="space-y-1">
                        <div class="px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Administrasi
                        </div>
                        <?php if (can('user.manage')): ?>
                            <a href="<?= url('/users') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isUsers ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                                <i class="bi bi-people text-base <?= $isUsers ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                                <span>User &amp; Role</span>
                            </a>
                        <?php endif; ?>
                        <?php if (can('audit.view')): ?>
                            <a href="<?= url('/audit') ?>" class="flex items-center gap-3 p-2.5 rounded-xl text-xs <?= $isAudit ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                                <i class="bi bi-shield-check text-base <?= $isAudit ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                                <span>Audit Log</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
