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

$active = static function (bool $condition): string {
    return $condition ? 'active' : '';
};

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
?>


<aside
    class="sidebar"
    id="sidebar"
    aria-label="Navigasi utama"
>
    <div class="brand">

        <div class="brand-mark">
            <i class="bi bi-bank2"></i>
        </div>

        <div>
            <strong>
                Fable RemitOps
            </strong>

            <small>
                Operations &amp; Decision Platform
            </small>
        </div>

    </div>


    <nav class="sidebar-nav">

        <a
            class="nav-item <?= $active($isDashboard) ?>"
            href="<?= url('/') ?>"
            <?= $isDashboard
                ? 'aria-current="page"'
                : ''
            ?>
        >
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>


        <?php if (can('transaction.view')): ?>

            <a
                class="nav-item <?= $active($isTransactions) ?>"
                href="<?= url('/transactions') ?>"
                <?= $isTransactions
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-arrow-left-right"></i>
                <span>Transaksi</span>
            </a>

        <?php endif; ?>


        <?php if (can('payer_account.view')): ?>

            <a
                class="nav-item <?= $active($isPayerAccounts) ?>"
                href="<?= url('/payer-accounts') ?>"
                <?= $isPayerAccounts
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-bank"></i>
                <span>Rekening Payer</span>
            </a>

        <?php endif; ?>


        <?php if (can('report.view')): ?>

            <a
                class="nav-item <?= $active($isReports) ?>"
                href="<?= url('/reports') ?>"
                <?= $isReports
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Report Harian</span>
            </a>

        <?php endif; ?>


        <?php if (can('company.view')): ?>

            <a
                class="nav-item <?= $active($isCompanies) ?>"
                href="<?= url('/companies') ?>"
                <?= $isCompanies
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-buildings"></i>
                <span>Data PT</span>
            </a>

        <?php endif; ?>


        <?php if (can('complaint.view')): ?>

            <a
                class="nav-item <?= $active($isComplaints) ?>"
                href="<?= url('/complaints') ?>"
                <?= $isComplaints
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-headset"></i>
                <span>Complain</span>
            </a>

        <?php endif; ?>


        <?php if (can('invoice.view')): ?>

            <a
                class="nav-item <?= $active($isInvoices) ?>"
                href="<?= url('/invoices') ?>"
                <?= $isInvoices
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-receipt"></i>
                <span>Invoice</span>
            </a>

        <?php endif; ?>


        <?php if ($canOpenDocuments): ?>

            <div class="nav-label">
                DOKUMEN
            </div>

            <a
                class="nav-item <?= $active($isDocuments) ?>"
                href="<?= url('/documents') ?>"
                <?= $isDocuments
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-folder2-open"></i>

                <span>
                    Dokumen &amp; Template
                </span>
            </a>

        <?php endif; ?>


        <?php if (can('analysis.view')): ?>

            <div class="nav-label">
                ANALITIK
            </div>

            <a
                class="nav-item <?= $active($isAnalysis) ?>"
                href="<?= url('/analysis') ?>"
                <?= $isAnalysis
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-diagram-3"></i>

                <span>
                    Entropy–TOPSIS
                </span>
            </a>

        <?php endif; ?>


        <?php if (
            can('user.manage')
            || can('audit.view')
        ): ?>

            <div class="nav-label">
                ADMINISTRASI
            </div>

        <?php endif; ?>


        <?php if (can('user.manage')): ?>

            <a
                class="nav-item <?= $active($isUsers) ?>"
                href="<?= url('/users') ?>"
                <?= $isUsers
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-people"></i>
                <span>User &amp; Role</span>
            </a>

        <?php endif; ?>


        <?php if (can('audit.view')): ?>

            <a
                class="nav-item <?= $active($isAudit) ?>"
                href="<?= url('/audit') ?>"
                <?= $isAudit
                    ? 'aria-current="page"'
                    : ''
                ?>
            >
                <i class="bi bi-shield-check"></i>
                <span>Audit Log</span>
            </a>

        <?php endif; ?>

    </nav>


    <div class="sidebar-footer">

        <div class="avatar-sm">
            <?= e($userInitial) ?>
        </div>

        <div class="min-w-0">

            <strong
                class="text-truncate d-block"
            >
                <?= e($userName) ?>
            </strong>

            <small>
                <?= e($userRoleName) ?>
            </small>

        </div>

    </div>

</aside>

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>