<?php declare(strict_types=1); ?>
<?php if (can('complaint.manage')): ?>
    <div class="page-actions flex justify-end mb-4">
        <a class="btn-primary" href="<?= url('/complaints/create') ?>">
            <i class="bi bi-plus-lg"></i>
            <span>Complain Baru</span>
        </a>
    </div>
<?php endif; ?>

<div class="panel-card mb-4">
    <form class="filter-grid flex flex-wrap items-end gap-3" method="get">
        <div class="filter-grow flex-1 min-w-[220px]">
            <span class="block text-xs font-semibold text-gray-700 mb-1">Pencarian</span>
            <?= render_search_input('q', (string) $q, ['placeholder' => 'Ticket, subjek, perusahaan, beneficiary...', 'size' => 'sm']) ?>
        </div>
        <div>
            <span class="block text-xs font-semibold text-gray-700 mb-1">Status</span>
            <select class="custom-form-select" name="status">
                <option value="">Semua status</option>
                <?php foreach (['open', 'in_progress', 'resolved', 'closed'] as $item): ?>
                    <option value="<?= $item ?>" <?= selected($status, $item) ?>><?= strtoupper($item) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-actions flex items-center gap-2">
            <button class="btn-primary" type="submit">
                <i class="bi bi-funnel"></i>
                <span>Terapkan</span>
            </button>
            <a class="btn-secondary" href="<?= url('/complaints') ?>">
                <span>Reset</span>
            </a>
        </div>
    </form>
</div>

<div class="panel-card">
    <div class="table-responsive">
        <table class="table modern-table align-middle">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Perusahaan / Sumber</th>
                    <th>Subjek</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Assigned</th>
                    <th>Dibuat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><span class="code-pill"><?= e($complaint['ticket_no']) ?></span></td>
                        <td>
                            <strong><?= e($complaint['company_name']) ?></strong>
                            <?php if (!empty($complaint['report_row_id'])): ?>
                                <small class="d-block text-[#315B8C]">
                                    <i class="bi bi-table"></i> <?= e(($complaint['report_batch_name'] ?? 'Laporan') . ' · ' . ($complaint['report_month'] ?? '')) ?>
                                </small>
                            <?php else: ?>
                                <small class="d-block text-muted"><?= e($complaint['reference_no'] ?? 'Manual') ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($complaint['subject']) ?></strong>
                            <?php if (!empty($complaint['report_beneficiary'])): ?>
                                <small class="d-block text-muted"><?= e($complaint['report_beneficiary']) ?> · <?= e($complaint['report_currency']) ?> <?= number_format((float) $complaint['report_amount'], 2, ',', '.') ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= status_badge($complaint['priority']) ?></td>
                        <td><?= status_badge($complaint['status']) ?></td>
                        <td><?= e($complaint['assigned_name'] ?? '-') ?></td>
                        <td><?= format_datetime($complaint['created_at']) ?></td>
                        <td class="text-end">
                            <?php if (can('complaint.manage')): ?>
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <a class="action-icon-btn" href="<?= url('/complaints/' . $complaint['id'] . '/edit') ?>" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" class="inline-block m-0" action="<?= url('/complaints/' . $complaint['id']) ?>" data-confirm="Hapus complain?">
                                        <?= csrf_field() . method_field('DELETE') ?>
                                        <button class="action-icon-btn text-red-600" type="submit" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$complaints): ?>
                    <tr><td colspan="8" class="empty-state">Belum ada complain.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
