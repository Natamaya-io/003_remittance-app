<?php declare(strict_types=1); ?>
<div class="page-actions flex items-center justify-between gap-3 mb-4">
    <form class="filter-bar flex items-center gap-2" method="get">
        <?= render_search_input('q', (string) $q, ['placeholder' => 'Cari perusahaan...', 'size' => 'sm', 'wrapperClass' => 'w-64']) ?>
        <button class="btn-primary" type="submit">
            <i class="bi bi-search"></i>
            <span>Cari</span>
        </button>
    </form>
    <?php if (can('company.manage')): ?>
        <a class="btn-primary" href="<?= url('/companies/create') ?>">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah PT</span>
        </a>
    <?php endif; ?>
</div>

<div class="panel-card">
    <div class="table-responsive">
        <table class="table modern-table align-middle">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Perusahaan</th>
                    <th>Kontak</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $c): ?>
                    <tr>
                        <td><span class="code-pill"><?= e($c['code']) ?></span></td>
                        <td>
                            <strong><?= e($c['name']) ?></strong>
                            <small class="d-block text-muted text-truncate" style="max-width:360px"><?= e($c['address'] ?? '') ?></small>
                        </td>
                        <td>
                            <?= e($c['contact_name'] ?? '-') ?>
                            <small class="d-block text-muted"><?= e($c['contact_phone'] ?? '') ?></small>
                        </td>
                        <td><?= e($c['email'] ?? '-') ?></td>
                        <td><?= status_badge($c['status']) ?></td>
                        <td class="text-end">
                            <?php if (can('company.manage')): ?>
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <a class="action-icon-btn" href="<?= url('/companies/' . $c['id'] . '/edit') ?>" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form class="inline-block m-0" method="post" action="<?= url('/companies/' . $c['id']) ?>" data-confirm="Hapus perusahaan ini?">
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
                <?php if (!$companies): ?>
                    <tr><td colspan="6" class="empty-state">Data perusahaan belum tersedia.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
