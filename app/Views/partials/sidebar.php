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
            <a class="flex items-center gap-x-3 text-gray-900 no-underline focus:outline-none" href="<?= url('/') ?>">
                <div class="size-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-base shadow-sm">
                    <i class="bi bi-bank2"></i>
                </div>
                <div>
                    <span class="block text-base font-bold text-gray-900 leading-tight tracking-tight">Fable RemitOps</span>
                </div>
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
                    <span>Report Harian</span>
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

    <!-- User Profile Pill Footer -->
    <div class="p-3 border-t border-gray-200/80 bg-gray-50/60">
        <div class="flex items-center gap-x-3 p-2 rounded-xl bg-white border border-gray-200 shadow-xs">
            <div class="size-8 rounded-lg bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                <?= e($userInitial) ?>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-gray-800 truncate leading-tight"><?= e($userName) ?></p>
                <p class="text-[10px] text-gray-500 font-medium truncate"><?= e($userRoleName) ?></p>
            </div>
        </div>
    </div>
</aside>
