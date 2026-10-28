<?php
$backUrl = url('/invoices');
$statusText = strtoupper((string)($invoice['status'] ?? 'DRAFT'));
$dueDate = !empty($invoice['due_date']) ? format_date($invoice['due_date']) : '-';
?>
<style>
body.login-body{
    display:block!important;
    min-height:100vh!important;
    margin:0!important;
    background:#F5F2F2!important;
    font-family:"Segoe UI Variable","Segoe UI",Inter,Arial,sans-serif!important;
    color:#111827!important;
}
.invoice-print-shell{min-height:100vh;padding:28px 18px 48px}
.invoice-print-toolbar{
    width:min(980px,100%);margin:0 auto 16px;padding:12px 14px;
    display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
    background:#ffffff;border:1px solid #e5e7eb;border-radius:18px;
    box-shadow:0 1px 3px rgba(0,0,0,.05)
}
.invoice-print-toolbar .left{display:flex;align-items:center;gap:9px;font-weight:700;color:#111827}
.invoice-print-actions{display:flex;gap:9px;flex-wrap:wrap}
.invoice-print-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    min-height:40px;padding:9px 16px;border:0;border-radius:9px;
    text-decoration:none;cursor:pointer;font:inherit;font-weight:600
}
.invoice-print-btn.primary{
    color:#fff;background:#315B8C;
    box-shadow:0 1px 2px rgba(0,0,0,.15)
}
.invoice-print-btn.secondary{color:#413333;background:#F5F2F2;border:1px solid #d1d5db}
.invoice-sheet{
    width:min(900px,100%);min-height:1120px;margin:0 auto;padding:54px;
    background:#fff;border:1px solid #e5e7eb;border-radius:22px;
    box-shadow:0 4px 16px rgba(0,0,0,.06);overflow:hidden
}
.invoice-accent{
    height:8px;margin:-54px -54px 42px;
    background:#315B8C;
}
.invoice-head{
    display:flex;justify-content:space-between;gap:30px;align-items:flex-start;
    padding-bottom:24px;border-bottom:1px solid #e5e7eb
}
.invoice-brand{display:flex;gap:14px;align-items:flex-start}
.invoice-brand-mark{
    width:48px;height:48px;display:grid;place-items:center;border-radius:15px;
    color:#fff;background:#315B8C;font-size:22px
}
.invoice-brand h2{margin:0 0 5px;font-size:24px;color:#111827}
.invoice-brand p{margin:0;color:#6b7280}
.invoice-number{text-align:right}
.invoice-number .label{font-size:12px;letter-spacing:.14em;font-weight:700;color:#6b7280}
.invoice-number h1{margin:7px 0 10px;font-size:34px;letter-spacing:-.03em;color:#111827}
.invoice-status{
    display:inline-flex;padding:6px 10px;border-radius:999px;
    background:#fff1df;color:#9a6418;font-size:11px;font-weight:850
}
.invoice-meta{
    display:grid;grid-template-columns:1.5fr .8fr;gap:28px;
    margin-top:34px;padding:24px;border:1px solid #e5e7eb;border-radius:18px;background:#ffffff
}
.invoice-meta small{display:block;margin-bottom:5px;color:#6b7280;font-size:10px;font-weight:700;letter-spacing:.08em}
.invoice-meta strong{display:block;color:#111827;font-size:15px}
.invoice-meta p{margin:6px 0 0;color:#4b5563}
.invoice-meta .dates{display:grid;gap:18px}
.invoice-total{
    margin-top:24px;padding:22px 24px;border-radius:18px;
    background:#F5F2F2;
    border:1px solid #e5e7eb
}
.invoice-total span{display:block;color:#6b7280;font-size:12px}
.invoice-total strong{display:inline-block;margin-top:5px;color:#111827;font-size:28px}
.invoice-total small{margin-left:7px;color:#6b7280}
.invoice-notes{
    margin-top:24px;padding:22px;border:1px solid #e5e7eb;border-radius:18px;background:#fff
}
.invoice-notes h4{margin:0 0 12px;color:#111827}
.invoice-notes p{margin:6px 0;color:#4b5563;line-height:1.65}

.invoice-table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #e5e7eb;border-radius:14px}
.invoice-table th{background:#315B8C;color:#fff;font-size:12px;font-weight:700;padding:10px;text-transform:uppercase}
.invoice-table td{padding:10px;font-size:12.5px;color:#111827;border-bottom:1px solid #e5e7eb}
.invoice-table tr:nth-child(even){background:#F5F2F2}

.invoice-footer{
    margin-top:56px;padding-top:18px;border-top:1px solid #e5e7eb;text-align:center;color:#6b7280
}
.invoice-footer strong{color:#315B8C}
@media(max-width:700px){
    .invoice-sheet{min-height:auto;padding:26px;border-radius:0}
    .invoice-accent{margin:-26px -26px 28px}
    .invoice-head,.invoice-meta{grid-template-columns:1fr;display:grid}
    .invoice-number{text-align:left}
}
@media print{
    @page{size:A4 portrait;margin:12mm}
    body.login-body{background:#fff!important}
    .invoice-print-shell{padding:0!important;min-height:auto!important}
    .invoice-print-toolbar{display:none!important}
    .invoice-sheet{
        width:auto!important;max-width:none!important;min-height:auto!important;
        margin:0!important;padding:0!important;border:0!important;border-radius:0!important;
        box-shadow:none!important;overflow:visible!important
    }
    .invoice-accent{height:5mm;margin:0 0 10mm}
    .invoice-head{break-inside:avoid;page-break-inside:avoid}
    .invoice-meta,.invoice-total,.invoice-notes,
.invoice-table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #e5e7eb;border-radius:14px}
.invoice-table th{background:#315B8C;color:#fff;font-size:12px;font-weight:700;padding:10px;text-transform:uppercase}
.invoice-table td{padding:10px;font-size:12.5px;color:#111827;border-bottom:1px solid #e5e7eb}
.invoice-table tr:nth-child(even){background:#F5F2F2}

.invoice-footer{
        break-inside:avoid;page-break-inside:avoid
    }
    .invoice-meta{margin-top:9mm;padding:6mm}
    .invoice-total{margin-top:6mm;padding:5mm 6mm}
    .invoice-notes{margin-top:6mm;padding:5mm 6mm}
    
.invoice-table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #e5e7eb;border-radius:14px}
.invoice-table th{background:#315B8C;color:#fff;font-size:12px;font-weight:700;padding:10px;text-transform:uppercase}
.invoice-table td{padding:10px;font-size:12.5px;color:#111827;border-bottom:1px solid #e5e7eb}
.invoice-table tr:nth-child(even){background:#F5F2F2}

.invoice-footer{margin-top:12mm}
}
</style>

<div class="invoice-print-shell">
    <div class="invoice-print-toolbar">
        <div class="left">
            <i class="bi bi-receipt"></i>
            <span>Preview Invoice <?= e($invoice['invoice_no']) ?></span>
        </div>
        <div class="invoice-print-actions">
            <a class="invoice-print-btn secondary" href="<?= e($backUrl) ?>">
                <i class="bi bi-arrow-left"></i> Kembali ke Invoice
            </a>
            <button class="invoice-print-btn primary" type="button" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <article class="invoice-sheet">
        <div class="invoice-accent"></div>

        <header class="invoice-head">
            <div class="invoice-brand">
                <div class="invoice-brand-mark"><i class="bi bi-bank2"></i></div>
                <div>
                    <h2>RemitGo!</h2>
                    <p>International Remittance Operations</p>
                </div>
            </div>
            <div class="invoice-number">
                <div class="label">INVOICE</div>
                <h1><?= e($invoice['invoice_no']) ?></h1>
                <span class="invoice-status"><?= e($statusText) ?></span>
            </div>
        </header>

        <section class="invoice-meta">
            <div>
                <small>DITAGIHKAN KEPADA</small>
                <strong><?= e($invoice['company_name']) ?></strong>
                <?php if (!empty($invoice['company_address'])): ?>
                    <p><?= nl2br(e($invoice['company_address'])) ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['company_email'])): ?>
                    <p><?= e($invoice['company_email']) ?></p>
                <?php endif; ?>
            </div>
            <div class="dates">
                <div>
                    <small>TANGGAL TERBIT</small>
                    <strong><?= e(format_date($invoice['issue_date'])) ?></strong>
                </div>
                <div>
                    <small>JATUH TEMPO</small>
                    <strong><?= e($dueDate) ?></strong>
                </div>
            </div>
        </section>

        <section class="invoice-total">
            <span>Total Invoice</span>
            <strong><?= e(format_money($invoice['amount'], $invoice['currency'])) ?></strong>
            <small><?= e($invoice['currency']) ?></small>
        </section>

        <section class="invoice-notes">
            <h4>Rincian dan Catatan</h4>
            <p><?= nl2br(e($invoice['notes'] ?? '-')) ?></p>
            <?php if (!empty($invoice['reference_no'])): ?>
                <p>Referensi transaksi: <strong><?= e($invoice['reference_no']) ?></strong></p>
            <?php endif; ?>
        </section>

        <footer class="invoice-footer">
            <strong>RemitGo!</strong><br>
            <small>Dokumen dibuat secara elektronik dan dapat dicetak pada kertas A4.</small>
        </footer>
    </article>
</div>
