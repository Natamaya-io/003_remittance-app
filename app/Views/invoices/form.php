<?php declare(strict_types=1);

$e = (bool) $invoice;
$v = fn($k, $d = '') => old($k, $invoice[$k] ?? ($_GET[$k] ?? $d));
?>
<div class="form-card">
    <form method="post" enctype="multipart/form-data" action="<?= $e ? url('/invoices/' . $invoice['id']) : url('/invoices') ?>">
        <?= csrf_field() ?>
        <?php if ($e) echo method_field('PUT'); ?>

        <div class="section-title flex items-center gap-2 mb-4">
            <i class="bi bi-receipt text-lg text-gray-800"></i>
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900 m-0">Informasi Invoice</h3>
                <?= render_tooltip(
                    'Kelola invoice dan keterkaitannya dengan transaksi.',
                    '<span class="info-toggle-btn cursor-help" aria-label="Info invoice"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                    ['placement' => 'top']
                ) ?>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Invoice *</label>
                <input class="custom-form-input" name="invoice_no" value="<?= e($v('invoice_no')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Perusahaan *</label>
                <select class="custom-form-select" name="company_id" required>
                    <option value="">Pilih perusahaan</option>
                    <?php foreach ($companies as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= selected($v('company_id'), $c['id']) ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Transaksi Terkait</label>
                <select class="custom-form-select" name="transaction_id">
                    <option value="">Tidak ada</option>
                    <?php foreach ($transactions as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= selected($v('transaction_id'), $t['id']) ?>><?= e(($t['reference_no'] ?: 'TRX-' . $t['id']) . ' · ' . $t['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Issue Date</label>
                <?= render_datepicker('issue_date', (string) $v('issue_date', date('Y-m-d')), ['placeholder' => 'Pilih tanggal issue']) ?>
            </div>
            <div class="col-md-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Due Date</label>
                <?= render_datepicker('due_date', (string) $v('due_date'), ['placeholder' => 'Pilih tanggal jatuh tempo']) ?>
            </div>
            <div class="col-md-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal</label>
                <input type="number" step="0.01" class="custom-form-input" name="amount" value="<?= e($v('amount', 0)) ?>">
            </div>
            <div class="col-md-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Mata Uang</label>
                <input class="custom-form-input uppercase" name="currency" maxlength="3" value="<?= e($v('currency', 'USD')) ?>">
            </div>
            <div class="col-md-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select class="custom-form-select" name="status">
                    <?php foreach (['draft', 'sent', 'paid', 'overdue', 'cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= selected($v('status', 'draft'), $s) ?>><?= strtoupper($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Lampiran</label>
                <input type="file" class="custom-form-input" name="attachment">
            </div>
            <div class="col-12">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                <textarea class="custom-form-textarea" rows="3" name="notes"><?= e($v('notes')) ?></textarea>
            </div>
        </div>

        <div class="form-actions flex items-center justify-end gap-3 mt-4 pt-3 border-t border-gray-100">
            <a class="btn-secondary" href="<?= url('/invoices') ?>">
                <span>Batal</span>
            </a>
            <button class="btn-primary" type="submit">
                <i class="bi bi-floppy"></i>
                <span>Simpan</span>
            </button>
        </div>
    </form>
</div>
