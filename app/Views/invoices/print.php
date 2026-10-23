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
    background:
      radial-gradient(circle at 90% 5%,rgba(116,68,220,.08),transparent 28%),
      radial-gradient(circle at 10% 90%,rgba(235,79,123,.07),transparent 30%),
      #fbf6fb!important;
    font-family:"Segoe UI Variable","Segoe UI",Inter,Arial,sans-serif!important;
    color:#51385c!important;
}
.invoice-print-shell{min-height:100vh;padding:28px 18px 48px}
.invoice-print-toolbar{
    width:min(980px,100%);margin:0 auto 16px;padding:12px 14px;
    display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
    background:rgba(255,255,255,.92);border:1px solid #eaddeb;border-radius:18px;
    box-shadow:0 12px 28px rgba(101,59,116,.08)
}
.invoice-print-toolbar .left{display:flex;align-items:center;gap:9px;font-weight:800;color:#51385c}
.invoice-print-actions{display:flex;gap:9px;flex-wrap:wrap}
.invoice-print-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    min-height:42px;padding:9px 14px;border:0;border-radius:13px;
    text-decoration:none;cursor:pointer;font:inherit;font-weight:800
}
.invoice-print-btn.primary{
    color:#fff;background:linear-gradient(135deg,#7444dc,#eb4f7b);
    box-shadow:0 7px 18px rgba(116,68,220,.14)
}
.invoice-print-btn.secondary{color:#68459e;background:#f3eaff;border:1px solid #e3d4ef}
.invoice-sheet{
    width:min(900px,100%);min-height:1120px;margin:0 auto;padding:54px;
    background:#fff;border:1px solid #eaddeb;border-radius:22px;
    box-shadow:0 22px 54px rgba(101,59,116,.11);overflow:hidden
}
.invoice-accent{
    height:8px;margin:-54px -54px 42px;
    background:linear-gradient(90deg,#7444dc 0%,#a94dde 50%,#eb4f7b 100%)
}
.invoice-head{
    display:flex;justify-content:space-between;gap:30px;align-items:flex-start;
    padding-bottom:24px;border-bottom:1px solid #eaddeb
}
.invoice-brand{display:flex;gap:14px;align-items:flex-start}
.invoice-brand-mark{
    width:48px;height:48px;display:grid;place-items:center;border-radius:15px;
    color:#fff;background:linear-gradient(135deg,#7444dc,#eb4f7b);font-size:22px
}
.invoice-brand h2{margin:0 0 5px;font-size:24px;color:#51385c}
.invoice-brand p{margin:0;color:#847488}
.invoice-number{text-align:right}
.invoice-number .label{font-size:12px;letter-spacing:.14em;font-weight:800;color:#8d7596}
.invoice-number h1{margin:7px 0 10px;font-size:34px;letter-spacing:-.03em;color:#51385c}
.invoice-status{
    display:inline-flex;padding:6px 10px;border-radius:999px;
    background:#fff1df;color:#9a6418;font-size:11px;font-weight:850
}
.invoice-meta{
    display:grid;grid-template-columns:1.5fr .8fr;gap:28px;
    margin-top:34px;padding:24px;border:1px solid #eee3f3;border-radius:18px;background:#fffafd
}
.invoice-meta small{display:block;margin-bottom:5px;color:#927f9a;font-size:10px;font-weight:850;letter-spacing:.08em}
.invoice-meta strong{display:block;color:#51385c;font-size:15px}
.invoice-meta p{margin:6px 0 0;color:#74647c}
.invoice-meta .dates{display:grid;gap:18px}
.invoice-total{
    margin-top:24px;padding:22px 24px;border-radius:18px;
    background:linear-gradient(135deg,#f5eaff 0%,#ffeaf4 100%);
    border:1px solid #eaddeb
}
.invoice-total span{display:block;color:#847488;font-size:12px}
.invoice-total strong{display:inline-block;margin-top:5px;color:#51385c;font-size:28px}
.invoice-total small{margin-left:7px;color:#847488}
.invoice-notes{
    margin-top:24px;padding:22px;border:1px solid #eaddeb;border-radius:18px;background:#fff
}
.invoice-notes h4{margin:0 0 12px;color:#51385c}
.invoice-notes p{margin:6px 0;color:#6f6077;line-height:1.65}

.invoice-table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #eaddeb;border-radius:14px}
.invoice-table th{background:linear-gradient(135deg,#7444dc,#eb4f7b);color:#fff;font-size:12px;font-weight:800;padding:10px;text-transform:uppercase}
.invoice-table td{padding:10px;font-size:12.5px;color:#51385c;border-bottom:1px solid #eee4f2}
.invoice-table tr:nth-child(even){background:#fffafd}

.invoice-footer{
    margin-top:56px;padding-top:18px;border-top:1px solid #eee4f3;text-align:center;color:#8a778f
}
.invoice-footer strong{color:#65437a}
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
.invoice-table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #eaddeb;border-radius:14px}
.invoice-table th{background:linear-gradient(135deg,#7444dc,#eb4f7b);color:#fff;font-size:12px;font-weight:800;padding:10px;text-transform:uppercase}
.invoice-table td{padding:10px;font-size:12.5px;color:#51385c;border-bottom:1px solid #eee4f2}
.invoice-table tr:nth-child(even){background:#fffafd}

.invoice-footer{
        break-inside:avoid;page-break-inside:avoid
    }
    .invoice-meta{margin-top:9mm;padding:6mm}
    .invoice-total{margin-top:6mm;padding:5mm 6mm}
    .invoice-notes{margin-top:6mm;padding:5mm 6mm}
    
.invoice-table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #eaddeb;border-radius:14px}
.invoice-table th{background:linear-gradient(135deg,#7444dc,#eb4f7b);color:#fff;font-size:12px;font-weight:800;padding:10px;text-transform:uppercase}
.invoice-table td{padding:10px;font-size:12.5px;color:#51385c;border-bottom:1px solid #eee4f2}
.invoice-table tr:nth-child(even){background:#fffafd}

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
                    <h2>Fable RemitOps</h2>
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
            <strong>Fable RemitOps</strong><br>
            <small>Dokumen dibuat secara elektronik dan dapat dicetak pada kertas A4.</small>
        </footer>
    </article>
</div>
