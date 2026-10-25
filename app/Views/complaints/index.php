<?php if (can('complaint.manage')): ?>
<div class="page-actions d-flex justify-content-end mb-4">
    <a class="btn btn-primary" href="<?= url('/complaints/create') ?>"><i class="bi bi-plus-lg me-1"></i>Complain Baru</a>
</div>
<?php endif; ?>
<div class="panel-card mb-4"><form class="filter-grid"><label class="filter-grow"><span>Pencarian</span><input class="form-control" name="q" placeholder="Ticket, subjek, perusahaan, beneficiary..." value="<?= e($q) ?>"></label><label><span>Status</span><select class="form-select" name="status"><option value="">Semua status</option><?php foreach (['open','in_progress','resolved','closed'] as $item): ?><option value="<?= $item ?>" <?= selected($status, $item) ?>><?= strtoupper($item) ?></option><?php endforeach; ?></select></label><div class="filter-actions"><button class="btn btn-primary">Terapkan</button><a class="btn btn-light" href="<?= url('/complaints') ?>">Reset</a></div></form></div>
<div class="panel-card"><div class="table-responsive"><table class="table modern-table align-middle"><thead><tr><th>Ticket</th><th>Perusahaan / Sumber</th><th>Subjek</th><th>Prioritas</th><th>Status</th><th>Assigned</th><th>Dibuat</th><th class="text-end">Aksi</th></tr></thead><tbody>
<?php foreach ($complaints as $complaint): ?><tr>
<td><span class="code-pill"><?= e($complaint['ticket_no']) ?></span></td>
<td><strong><?= e($complaint['company_name']) ?></strong><?php if (!empty($complaint['report_row_id'])): ?><small class="d-block text-primary"><i class="bi bi-table"></i> <?= e(($complaint['report_batch_name'] ?? 'Report') . ' · ' . ($complaint['report_month'] ?? '')) ?></small><?php else: ?><small class="d-block text-muted"><?= e($complaint['reference_no'] ?? 'Manual') ?></small><?php endif; ?></td>
<td><strong><?= e($complaint['subject']) ?></strong><?php if (!empty($complaint['report_beneficiary'])): ?><small class="d-block text-muted"><?= e($complaint['report_beneficiary']) ?> · <?= e($complaint['report_currency']) ?> <?= number_format((float) $complaint['report_amount'], 2, ',', '.') ?></small><?php endif; ?></td>
<td><?= status_badge($complaint['priority']) ?></td><td><?= status_badge($complaint['status']) ?></td><td><?= e($complaint['assigned_name'] ?? '-') ?></td><td><?= format_datetime($complaint['created_at']) ?></td>
<td class="text-end"><?php if (can('complaint.manage')): ?><a class="btn btn-sm btn-light" href="<?= url('/complaints/' . $complaint['id'] . '/edit') ?>"><i class="bi bi-pencil"></i></a><form method="post" class="d-inline" action="<?= url('/complaints/' . $complaint['id']) ?>" data-confirm="Hapus complain?"><?= csrf_field() . method_field('DELETE') ?><button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button></form><?php endif; ?></td>
</tr><?php endforeach; ?>
<?php if (!$complaints): ?><tr><td colspan="8" class="empty-state">Belum ada complain.</td></tr><?php endif; ?>
</tbody></table></div></div>
