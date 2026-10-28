<?php declare(strict_types=1); ?>
<div class="page-actions flex items-center justify-between gap-3 mb-4">
    <div class="flex items-center gap-2">
        <h2 class="text-lg font-semibold text-gray-900 m-0">Pengguna</h2>
        <?= render_tooltip(
            'Kelola akun, peran, dan pembagian tim transaksi.',
            '<span class="info-toggle-btn cursor-help" aria-label="Info pengguna"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
            ['placement' => 'top']
        ) ?>
    </div>
    <a class="btn-primary" href="<?= url('/users/create') ?>">
        <i class="bi bi-person-plus"></i>
        <span>Tambah User</span>
    </a>
</div>

<div class="panel-card">
    <div class="table-responsive">
        <table class="table modern-table align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tim</th>
                    <th>Status</th>
                    <th>Login Terakhir</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm"><?= e(strtoupper(substr($u['name'], 0, 1))) ?></div>
                                <strong><?= e($u['name']) ?></strong>
                            </div>
                        </td>
                        <td><?= e($u['username']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="code-pill"><?= e($u['role_name']) ?></span></td>
                        <td><?= $u['transaction_team'] ? 'Tim ' . e((string) $u['transaction_team']) : '-' ?></td>
                        <td><?= status_badge($u['is_active'] ? 'active' : 'inactive') ?></td>
                        <td><?= format_datetime($u['last_login_at']) ?></td>
                        <td class="text-end">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <a class="action-icon-btn" href="<?= url('/users/' . $u['id'] . '/edit') ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ((int) $u['id'] !== (int) auth_user()['id']): ?>
                                    <form method="post" class="inline-block m-0" action="<?= url('/users/' . $u['id']) ?>" data-confirm="Hapus user ini?">
                                        <?= csrf_field() . method_field('DELETE') ?>
                                        <button class="action-icon-btn text-red-600" type="submit" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
