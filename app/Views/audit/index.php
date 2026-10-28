<?php declare(strict_types=1); ?>
<div class="page-actions flex items-center justify-between gap-3 mb-4">
    <form class="filter-bar flex items-center gap-2" method="get">
        <?= render_search_input('q', (string) $q, ['placeholder' => 'Aksi, entitas, user...', 'size' => 'sm', 'wrapperClass' => 'w-64']) ?>
        <button class="btn-primary" type="submit">
            <i class="bi bi-search"></i>
            <span>Cari</span>
        </button>
    </form>
    <div class="flex items-center gap-1.5 text-xs text-gray-500">
        <i class="bi bi-info-circle text-gray-400"></i>
        <span>Maksimal 500 log terbaru</span>
    </div>
</div>

<div class="panel-card">
    <div class="table-responsive">
        <table class="table modern-table align-middle">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Entitas</th>
                    <th>ID</th>
                    <th>IP</th>
                    <th>Perubahan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?= format_datetime($l['created_at']) ?></td>
                        <td><?= e($l['user_name'] ?? 'System') ?></td>
                        <td><span class="code-pill"><?= e(strtoupper($l['action'])) ?></span></td>
                        <td><?= e($l['entity_type']) ?></td>
                        <td><?= e($l['entity_id'] ?? '-') ?></td>
                        <td><?= e($l['ip_address'] ?? '-') ?></td>
                        <td>
                            <details class="cursor-pointer">
                                <summary class="text-xs text-[#315B8C] font-medium">Lihat JSON</summary>
                                <pre class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded-lg text-[11px] overflow-x-auto"><?= e($l['after_data'] ?? $l['before_data'] ?? '-') ?></pre>
                            </details>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$logs): ?>
                    <tr><td colspan="7" class="empty-state">Audit log kosong.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
