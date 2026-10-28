<?php declare(strict_types=1); ?>
<div class="page-actions flex items-center justify-end gap-2 flex-wrap mb-4">
    <a class="btn-secondary" href="<?= url('/invoices') ?>">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>
    <a class="btn-primary" href="<?= url('/document_template_manager.php?module=invoice') ?>">
        <i class="bi bi-plus-lg"></i>
        <span>Kelola Template</span>
    </a>
</div>

<link rel="stylesheet" href="<?= e(asset('css/invoice-template-flow.css?v=2.0.0')) ?>">

<section class="panel-card invoice-template-picker">
    <?php if ($templates === []): ?>
        <div class="invoice-template-empty text-center py-10">
            <span class="invoice-template-empty-icon text-4xl text-gray-400 mb-3 block"><i class="bi bi-file-earmark-word"></i></span>
            <h3 class="text-base font-semibold text-gray-800 mb-2">Belum ada template invoice aktif</h3>
            <p class="text-xs text-gray-500 max-w-md mx-auto mb-4">Upload file Word DOCX melalui menu Kelola Template. Setelah template diaktifkan, template akan muncul di halaman ini.</p>
            <a class="btn-primary" href="<?= url('/document_template_manager.php?module=invoice') ?>">
                <i class="bi bi-cloud-arrow-up"></i>
                <span>Upload Template Word</span>
            </a>
        </div>
    <?php else: ?>
        <div class="invoice-template-choice-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($templates as $template): ?>
                <article class="invoice-template-choice-card bg-white border border-gray-200 rounded-2xl p-5 shadow-xs flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="invoice-template-choice-icon size-10 rounded-xl bg-blue-50 text-[#315B8C] flex items-center justify-center text-lg shadow-xs">
                            <i class="bi bi-file-earmark-word"></i>
                        </div>
                        <div>
                            <span class="invoice-template-choice-type text-[11px] font-semibold text-gray-500 uppercase block"><?= e($template['type_name'] ?? 'Template Invoice') ?></span>
                            <h3 class="text-sm font-semibold text-gray-900 mt-0.5"><?= e($template['title']) ?></h3>
                        </div>
                        <?php if (!empty($template['description'])): ?>
                            <p class="text-xs text-gray-500"><?= e($template['description']) ?></p>
                        <?php endif; ?>
                        <small class="text-[11px] text-gray-400 block"><?= e($template['file_original_name'] ?? '') ?></small>
                    </div>
                    <a class="btn-primary w-full justify-center" href="<?= url('/invoices/create?template_id=' . (int) $template['id']) ?>">
                        <span>Gunakan Template</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
