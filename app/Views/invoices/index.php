<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Data Template Invoice
|--------------------------------------------------------------------------
*/

$templateCounts = is_array($templateCounts ?? null)
    ? $templateCounts
    : [
        'invoice_asli' => 0,
        'invoice_order' => 0,
        'invoice_terima_uang' => 0,
    ];

$templateTotal = (int) ($templateTotal ?? 0);

$invoiceTemplates = is_array($invoiceTemplates ?? null)
    ? $invoiceTemplates
    : [];

$templateWarning = trim((string) ($templateWarning ?? ''));

/*
|--------------------------------------------------------------------------
| Helper tampilan
|--------------------------------------------------------------------------
*/

$templateLabel = static fn (string $type): string => match ($type) {
    'invoice_asli' => 'Invoice Asli',
    'invoice_order' => 'Invoice Order',
    'invoice_terima_uang' => 'Invoice Terima Uang',
    default => 'Template Invoice',
};

$templateIcon = static fn (string $type): string => match ($type) {
    'invoice_asli' => 'bi-receipt',
    'invoice_order' => 'bi-file-earmark-text',
    'invoice_terima_uang' => 'bi-cash-stack',
    default => 'bi-file-earmark',
};

$templateColorClass = static fn (string $type): string => match ($type) {
    'invoice_asli' => 'purple',
    'invoice_order' => 'pink',
    'invoice_terima_uang' => 'lilac',
    default => 'purple',
};
?>

<?php if (can('invoice.manage')): ?>
    <div class="page-actions d-flex justify-content-end mb-4">
        <a
            class="btn-primary"
            href="<?= url('/invoice_templates.php') ?>"
        >
            <i class="bi bi-layout-text-window"></i>
            <span>Kelola Template</span>
        </a>
    </div>
<?php endif; ?>

<section class="panel-card invoice-template-hub mb-4">

    <div class="invoice-template-head" x-data="{ showInfo: false }">
        <div>
            <span class="eyebrow dark">TEMPLATE INVOICE</span>

            <div class="d-inline-flex align-items-center gap-2">
                <h3 class="m-0">Template Invoice</h3>
                <button
                    type="button"
                    @click="showInfo = !showInfo"
                    class="info-toggle-btn"
                    :class="{ 'is-active': showInfo }"
                    :aria-expanded="showInfo.toString()"
                    aria-label="Tampilkan info template"
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
                style="display: none;"
            >
                Pilih jenis template invoice yang digunakan dalam proses administrasi.
            </p>
        </div>

        <div class="invoice-template-head-actions">
            <span class="invoice-template-total">
                <strong><?= $templateTotal ?></strong>
                <small>template tersimpan</small>
            </span>
        </div>
    </div>

    <?php if ($templateWarning !== ''): ?>
        <div class="alert alert-warning mb-3">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <?= e($templateWarning) ?>
        </div>
    <?php endif; ?>

    <div class="invoice-template-types">

        <a
            class="invoice-template-type-card"
            href="<?= url('/invoice_templates.php?type=invoice_asli') ?>"
        >
            <span class="invoice-template-icon purple">
                <i class="bi bi-receipt"></i>
            </span>

            <span class="invoice-template-type-copy">
                <strong>Invoice Asli</strong>
                <small>Template utama invoice.</small>
            </span>

            <span class="invoice-template-count">
                <?= (int) ($templateCounts['invoice_asli'] ?? 0) ?>
            </span>
        </a>

        <a
            class="invoice-template-type-card"
            href="<?= url('/invoice_templates.php?type=invoice_order') ?>"
        >
            <span class="invoice-template-icon pink">
                <i class="bi bi-file-earmark-text"></i>
            </span>

            <span class="invoice-template-type-copy">
                <strong>Invoice Order</strong>
                <small>Template order pembayaran.</small>
            </span>

            <span class="invoice-template-count">
                <?= (int) ($templateCounts['invoice_order'] ?? 0) ?>
            </span>
        </a>

        <a
            class="invoice-template-type-card"
            href="<?= url('/invoice_templates.php?type=invoice_terima_uang') ?>"
        >
            <span class="invoice-template-icon lilac">
                <i class="bi bi-cash-stack"></i>
            </span>

            <span class="invoice-template-type-copy">
                <strong>Invoice Terima Uang</strong>
                <small>Template penerimaan dana.</small>
            </span>

            <span class="invoice-template-count">
                <?= (int) ($templateCounts['invoice_terima_uang'] ?? 0) ?>
            </span>
        </a>

    </div>

    <div class="invoice-template-latest">

        <div class="invoice-template-subhead">
            <h4>Template Terbaru</h4>

            <?php if (can('invoice.manage')): ?>
                <a
                    class="invoice-template-view-all"
                    href="<?= url('/invoice_templates.php') ?>"
                >
                    Lihat semua
                    <i class="bi bi-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table modern-table align-middle invoice-template-table">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Dokumen / Invoice</th>
                        <th>Perusahaan / Bill To</th>
                        <th>Nominal</th>
                        <th>Update</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($invoiceTemplates as $template): ?>
                        <?php
                        $templateId = (int) ($template['id'] ?? 0);
                        $templateType = (string) ($template['template_type'] ?? '');

                        $documentNumber = trim(
                            (string) ($template['invoice_no'] ?? '')
                        );

                        if ($documentNumber === '') {
                            $documentNumber = trim(
                                (string) ($template['document_no'] ?? '')
                            );
                        }

                        $companyName = trim(
                            (string) ($template['company_name'] ?? '')
                        );

                        if ($companyName === '') {
                            $companyName = trim(
                                (string) ($template['bill_to_name'] ?? '')
                            );
                        }

                        $currency = strtoupper(
                            trim((string) ($template['currency'] ?? ''))
                        );

                        $amount = (float) ($template['amount'] ?? 0);

                        $updatedAt = '-';

                        if (!empty($template['updated_at'])) {
                            $timestamp = strtotime(
                                (string) $template['updated_at']
                            );

                            if ($timestamp !== false) {
                                $updatedAt = date('d/m/Y', $timestamp);
                            }
                        }
                        ?>

                        <tr>
                            <td>
                                <span
                                    class="
                                        invoice-template-kind
                                        <?= e($templateColorClass($templateType)) ?>
                                    "
                                >
                                    <i
                                        class="
                                            bi
                                            <?= e($templateIcon($templateType)) ?>
                                            me-1
                                        "
                                    ></i>

                                    <?= e($templateLabel($templateType)) ?>
                                </span>
                            </td>

                            <td>
                                <strong>
                                    <?= e(
                                        $documentNumber !== ''
                                            ? $documentNumber
                                            : '-'
                                    ) ?>
                                </strong>

                                <?php if (
                                    !empty($template['document_no'])
                                    && !empty($template['invoice_no'])
                                ): ?>
                                    <small class="d-block text-muted">
                                        Doc:
                                        <?= e((string) $template['document_no']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= e(
                                    $companyName !== ''
                                        ? $companyName
                                        : '-'
                                ) ?>
                            </td>

                            <td>
                                <?php if ($currency !== '' || $amount > 0): ?>
                                    <?= e($currency) ?>
                                    <?= number_format(
                                        $amount,
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= e($updatedAt) ?>
                            </td>

                            <td class="text-end">
                                <div
                                    class="
                                        d-inline-flex
                                        align-items-center
                                        gap-2
                                    "
                                >
                                    <a
                                        class="action-icon-btn"
                                        title="Preview"
                                        href="<?= url(
                                            '/invoice_templates.php'
                                            . '?preview='
                                            . $templateId
                                        ) ?>"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if (can('invoice.manage')): ?>
                                        <a
                                            class="action-icon-btn"
                                            title="Edit"
                                            href="<?= url(
                                                '/invoice_templates.php'
                                                . '?edit='
                                                . $templateId
                                            ) ?>"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($invoiceTemplates === []): ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <div class="invoice-template-empty">
                                    <i class="bi bi-file-earmark-text"></i>

                                    <strong>
                                        Belum ada template invoice
                                    </strong>

                                    <small>
                                        Gunakan tombol Kelola Template
                                        untuk menambahkan template.
                                    </small>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</section>
