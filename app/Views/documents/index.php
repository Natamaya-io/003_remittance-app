<?php

declare(strict_types=1);

$user = is_array($user ?? null) ? $user : [];
$roleCode = (string) ($roleCode ?? '');
$counts = array_merge([
    'standing_instruction' => 0,
    'invoice' => 0,
    'complain' => 0,
], is_array($counts ?? null) ? $counts : []);
$showStandingInstruction = (bool) ($showStandingInstruction ?? false);
$showInvoice = (bool) ($showInvoice ?? false);
$showComplain = (bool) ($showComplain ?? false);
$databaseWarning = trim((string) ($databaseWarning ?? ''));
?>

<section class="bank-hero mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="eyebrow">DOCUMENT OPERATION CENTER</span>
            <h2 class="mt-2 mb-2">Dokumen & Template</h2>
            <p class="mb-0">Kelola Standing Instruction dan template Complain dalam satu pusat dokumen.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="opacity-75 small">Hak akses aktif</div>
            <strong class="fs-5"><?= e($user['role_name'] ?? $roleCode) ?></strong>
        </div>
    </div>
</section>

<?php if ($databaseWarning !== ''): ?>
    <div class="alert alert-warning shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Sebagian tabel dokumen belum tersedia: <?= e($databaseWarning) ?>
        <div class="small mt-1">Impor <code>database/migration_modules.sql</code> untuk melengkapi tabel tanpa menghapus data lama.</div>
    </div>
<?php endif; ?>

<div class="row g-4 document-module-grid">
    <?php if ($showStandingInstruction): ?>
        <div class="col-12 col-xl-6">
            <article class="panel-card h-100 document-module-card">
                <div class="document-module-icon blue"><i class="bi bi-file-earmark-check-fill"></i></div>
                <h3 class="fs-5 mt-3">Standing Instruction</h3>
                <p class="text-muted">Tujuh template dokumen operasional siap digunakan.</p>
                <div class="d-flex justify-content-between align-items-end mt-4 gap-3">
                    <div><strong class="display-6 d-block"><?= (int) $counts['standing_instruction'] ?></strong><small class="text-muted">dokumen tersimpan</small></div>
                    <a class="btn btn-primary" href="<?= url('/standing_instruction_7.php') ?>">Buka Modul <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </article>
        </div>
    <?php endif; ?>

    <?php if ($showComplain): ?>
        <div class="col-12 col-xl-6">
            <article class="panel-card h-100 document-module-card">
                <div class="document-module-icon orange"><i class="bi bi-chat-left-text-fill"></i></div>
                <h3 class="fs-5 mt-3">Template Complain</h3>
                <p class="text-muted">Kelola template Refund, Amend, MT 199, dan MT 103.</p>
                <div class="d-flex justify-content-between align-items-end mt-4 gap-3">
                    <div><strong class="display-6 d-block"><?= (int) $counts['complain'] ?></strong><small class="text-muted">template tersimpan</small></div>
                    <a class="btn btn-primary" href="<?= url('/document_template_manager.php?module=complain') ?>">Buka Complain <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </article>
        </div>
    <?php endif; ?>
</div>
