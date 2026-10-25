<div class="page-actions d-flex justify-content-end gap-2 flex-wrap mb-4">
    <a class="btn btn-light" href="<?= url('/invoices') ?>">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali
    </a>
    <a class="btn btn-primary" href="<?= url('/document_template_manager.php?module=invoice') ?>">
        <i class="bi bi-plus-lg me-1"></i>
        Kelola Template
    </a>
</div>

<link rel="stylesheet" href="<?= e(asset('css/invoice-template-flow.css?v=2.0.0')) ?>">

<section class="panel-card invoice-template-picker">
    <?php if ($templates === []): ?>
        <div class="invoice-template-empty">
            <span class="invoice-template-empty-icon"><i class="bi bi-file-earmark-word"></i></span>
            <h3>Belum ada template invoice aktif</h3>
            <p>Upload file Word DOCX melalui menu Kelola Template. Setelah template diaktifkan, template akan muncul di halaman ini.</p>
            <a class="btn btn-primary" href="<?= url('/document_template_manager.php?module=invoice') ?>">
                Upload Template Word
            </a>
        </div>
    <?php else: ?>
        <div class="invoice-template-choice-grid">
            <?php foreach ($templates as $template): ?>
                <article class="invoice-template-choice-card">
                    <div class="invoice-template-choice-icon">
                        <i class="bi bi-file-earmark-word"></i>
                    </div>
                    <div class="invoice-template-choice-body">
                        <span class="invoice-template-choice-type"><?= e($template['type_name'] ?? 'Template Invoice') ?></span>
                        <h3><?= e($template['title']) ?></h3>
                        <?php if (!empty($template['description'])): ?>
                            <p><?= e($template['description']) ?></p>
                        <?php endif; ?>
                        <small><?= e($template['file_original_name'] ?? '') ?></small>
                    </div>
                    <a class="btn btn-primary w-100" href="<?= url('/invoices/create?template_id=' . (int) $template['id']) ?>">
                        Gunakan Template
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
