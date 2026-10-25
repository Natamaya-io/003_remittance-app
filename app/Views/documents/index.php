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
            <article class="panel-card h-100 document-module-card" x-data="{ showInfo: false }">
                <div class="document-module-icon blue"><i class="bi bi-file-earmark-check-fill"></i></div>
                <div class="d-inline-flex align-items-center gap-2 mt-3">
                    <h3 class="fs-5 m-0">Standing Instruction</h3>
                    <button
                        type="button"
                        @click="showInfo = !showInfo"
                        class="info-toggle-btn"
                        :class="{ 'is-active': showInfo }"
                        :aria-expanded="showInfo.toString()"
                        aria-label="Tampilkan info Standing Instruction"
                        title="Informasi bantuan"
                    >
                        <i class="bi bi-info-circle text-xs leading-none"></i>
                    </button>
                </div>
                <p
                    x-show="showInfo"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="text-muted mt-2"
                    style="display: none;"
                >
                    Tujuh template dokumen operasional siap digunakan.
                </p>
                <div class="d-flex justify-content-between align-items-end mt-4 gap-3">
                    <div><strong class="display-6 d-block"><?= (int) $counts['standing_instruction'] ?></strong><small class="text-muted">dokumen tersimpan</small></div>
                    <a class="btn btn-primary" href="<?= url('/standing_instruction_7.php') ?>">Buka Modul <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </article>
        </div>
    <?php endif; ?>

    <?php if ($showComplain): ?>
        <div class="col-12 col-xl-6">
            <article class="panel-card h-100 document-module-card" x-data="{ showInfo: false }">
                <div class="document-module-icon orange"><i class="bi bi-chat-left-text-fill"></i></div>
                <div class="d-inline-flex align-items-center gap-2 mt-3">
                    <h3 class="fs-5 m-0">Template Complain</h3>
                    <button
                        type="button"
                        @click="showInfo = !showInfo"
                        class="info-toggle-btn"
                        :class="{ 'is-active': showInfo }"
                        :aria-expanded="showInfo.toString()"
                        aria-label="Tampilkan info Template Complain"
                        title="Informasi bantuan"
                    >
                        <i class="bi bi-info-circle text-xs leading-none"></i>
                    </button>
                </div>
                <p
                    x-show="showInfo"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="text-muted mt-2"
                    style="display: none;"
                >
                    Kelola template Refund, Amend, MT 199, dan MT 103.
                </p>
                <div class="d-flex justify-content-between align-items-end mt-4 gap-3">
                    <div><strong class="display-6 d-block"><?= (int) $counts['complain'] ?></strong><small class="text-muted">template tersimpan</small></div>
                    <a class="btn btn-primary" href="<?= url('/document_template_manager.php?module=complain') ?>">Buka Complain <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </article>
        </div>
    <?php endif; ?>
</div>
