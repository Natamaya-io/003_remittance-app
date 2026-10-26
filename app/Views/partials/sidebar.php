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

$navClass = static function (bool $condition): string {
    return $condition
        ? 'flex items-center gap-x-3 py-2 px-2.5 bg-blue-50 text-xs font-semibold text-blue-700 rounded-lg group shadow-xs transition-colors'
        : 'flex items-center gap-x-3 py-2 px-2.5 text-xs font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 group transition-colors';
};

$iconClass = static function (bool $condition): string {
    return $condition
        ? 'text-base text-blue-600'
        : 'text-base text-gray-400 group-hover:text-gray-600 transition-colors';
};
?>

<!-- ========== PRELINE NATIVE APPLICATION SIDEBAR ========== -->
<aside
    id="hs-application-sidebar"
    class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform fixed top-0 start-0 bottom-0 z-[60] w-64 bg-white border-e border-gray-200 flex flex-col justify-between overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300"
    tabindex="-1"
    aria-label="Navigasi Utama"
>
    <div>
        <!-- Brand Header -->
        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-200/80">
            <a class="flex items-center gap-x-2.5 text-gray-900 no-underline focus:outline-none" href="<?= url('/') ?>">
                <i class="bi bi-bank2 text-2xl text-blue-600"></i>
                <span class="text-base font-bold text-gray-900 leading-tight tracking-tight">RemitGo!</span>
            </a>
            <!-- Close Button on Mobile -->
            <button
                type="button"
                class="py-1 px-1.5 inline-flex justify-center items-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 focus:outline-none lg:hidden"
                data-hs-overlay="#hs-application-sidebar"
                aria-label="Tutup navigasi"
            >
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Quick Search Bar in Sidebar -->
        <div class="px-3 pt-3">
            <button
                type="button"
                x-data
                @click.stop="$dispatch('open-search-modal')"
                onclick="event.stopPropagation(); window.openSearchModal ? window.openSearchModal() : null; return false;"
                class="w-full flex items-center justify-between py-2 px-3 text-xs font-medium text-gray-500 bg-gray-50 hover:bg-gray-100 hover:text-gray-900 border border-gray-200 rounded-xl transition-colors shadow-xs group"
                title="Pencarian (Tekan / atau Ctrl+K)"
                aria-label="Buka pencarian global"
            >
                <span class="flex items-center gap-x-2.5">
                    <i class="bi bi-search text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                    <span>Cari transaksi...</span>
                </span>
                <kbd class="py-0.5 px-1.5 text-[10px] bg-white border border-gray-200 rounded text-gray-400 font-mono shadow-xs">/</kbd>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="p-3 w-full flex flex-col space-y-1">
            <a
                class="<?= $navClass($isDashboard) ?>"
                href="<?= url('/') ?>"
                <?= $isDashboard ? 'aria-current="page"' : '' ?>
            >
                <i class="bi bi-grid-1x2-fill <?= $iconClass($isDashboard) ?>"></i>
                <span>Dashboard</span>
            </a>

            <?php if (can('transaction.view')): ?>
                <a
                    class="<?= $navClass($isTransactions) ?>"
                    href="<?= url('/transactions') ?>"
                    <?= $isTransactions ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-arrow-left-right <?= $iconClass($isTransactions) ?>"></i>
                    <span>Transaksi</span>
                </a>
            <?php endif; ?>

            <?php if (can('payer_account.view')): ?>
                <a
                    class="<?= $navClass($isPayerAccounts) ?>"
                    href="<?= url('/payer-accounts') ?>"
                    <?= $isPayerAccounts ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-bank <?= $iconClass($isPayerAccounts) ?>"></i>
                    <span>Rekening Payer</span>
                </a>
            <?php endif; ?>

            <?php if (can('report.view')): ?>
                <a
                    class="<?= $navClass($isReports) ?>"
                    href="<?= url('/reports') ?>"
                    <?= $isReports ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-file-earmark-bar-graph <?= $iconClass($isReports) ?>"></i>
                    <span>Laporan</span>
                </a>
            <?php endif; ?>

            <?php if (can('company.view')): ?>
                <a
                    class="<?= $navClass($isCompanies) ?>"
                    href="<?= url('/companies') ?>"
                    <?= $isCompanies ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-buildings <?= $iconClass($isCompanies) ?>"></i>
                    <span>Data PT</span>
                </a>
            <?php endif; ?>

            <?php if (can('complaint.view')): ?>
                <a
                    class="<?= $navClass($isComplaints) ?>"
                    href="<?= url('/complaints') ?>"
                    <?= $isComplaints ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-headset <?= $iconClass($isComplaints) ?>"></i>
                    <span>Complain</span>
                </a>
            <?php endif; ?>

            <?php if (can('invoice.view')): ?>
                <a
                    class="<?= $navClass($isInvoices) ?>"
                    href="<?= url('/invoices') ?>"
                    <?= $isInvoices ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-receipt <?= $iconClass($isInvoices) ?>"></i>
                    <span>Invoice</span>
                </a>
            <?php endif; ?>

            <?php if ($canOpenDocuments): ?>
                <div class="px-2.5 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    Dokumen
                </div>
                <a
                    class="<?= $navClass($isDocuments) ?>"
                    href="<?= url('/documents') ?>"
                    <?= $isDocuments ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-folder2-open <?= $iconClass($isDocuments) ?>"></i>
                    <span>Dokumen &amp; Template</span>
                </a>
            <?php endif; ?>

            <?php if (can('analysis.view')): ?>
                <div class="px-2.5 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    Analitik
                </div>
                <a
                    class="<?= $navClass($isAnalysis) ?>"
                    href="<?= url('/analysis') ?>"
                    <?= $isAnalysis ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-diagram-3 <?= $iconClass($isAnalysis) ?>"></i>
                    <span>Entropy–TOPSIS</span>
                </a>
            <?php endif; ?>

            <?php if (can('user.manage') || can('audit.view')): ?>
                <div class="px-2.5 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    Administrasi
                </div>
            <?php endif; ?>

            <?php if (can('user.manage')): ?>
                <a
                    class="<?= $navClass($isUsers) ?>"
                    href="<?= url('/users') ?>"
                    <?= $isUsers ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-people <?= $iconClass($isUsers) ?>"></i>
                    <span>User &amp; Role</span>
                </a>
            <?php endif; ?>

            <?php if (can('audit.view')): ?>
                <a
                    class="<?= $navClass($isAudit) ?>"
                    href="<?= url('/audit') ?>"
                    <?= $isAudit ? 'aria-current="page"' : '' ?>
                >
                    <i class="bi bi-shield-check <?= $iconClass($isAudit) ?>"></i>
                    <span>Audit Log</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- User Profile & Session Actions Footer -->
    <div class="p-3 border-t border-gray-200/80 bg-gray-50/60">
        <div class="flex items-center gap-2">
            <a
                href="<?= url('/profile') ?>"
                class="flex-1 flex items-center gap-x-2.5 p-2 rounded-xl bg-white border border-gray-200 shadow-xs hover:border-blue-300 hover:bg-blue-50/40 transition-colors group no-underline text-inherit min-w-0"
                title="Lihat profil pengguna"
            >
                <div class="size-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs shrink-0">
                    <?= e($userInitial) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-800 truncate leading-tight group-hover:text-blue-700 transition-colors"><?= e($userName) ?></p>
                    <p class="text-[10px] text-gray-500 font-medium truncate"><?= e($userRoleName) ?></p>
                </div>
            </a>

            <!-- Logout Form Button -->
            <form method="post" action="<?= url('/logout') ?>" class="m-0 shrink-0">
                <?= csrf_field() ?>
                <button
                    type="submit"
                    class="size-10 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 shadow-xs hover:bg-red-50 hover:text-red-600 hover:border-red-200 focus:outline-none transition-colors"
                    title="Keluar dari sistem"
                    aria-label="Keluar dari sistem"
                    data-no-loading="1"
                >
                    <i class="bi bi-box-arrow-right text-base leading-none"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
