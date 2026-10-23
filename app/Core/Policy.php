<?php

declare(strict_types=1);

namespace App\Core;

final class Policy
{
    private const MAP = [
        'dashboard.view' => ['*'],
        'company.view' => ['*'],
        'company.manage' => ['super_admin', 'supervisor'],
        'transaction.view' => ['super_admin', 'supervisor', 'admin_transaksi_1', 'admin_transaksi_2', 'admin_transaksi_3', 'admin_transaksi_4', 'admin_complain', 'admin_invoice'],
        'transaction.manage' => ['super_admin', 'admin_transaksi_1', 'admin_transaksi_2', 'admin_transaksi_3', 'admin_transaksi_4'],
        'payer_account.view' => ['super_admin', 'supervisor', 'admin_transaksi_1', 'admin_transaksi_2', 'admin_transaksi_3', 'admin_transaksi_4'],
        'payer_account.manage' => ['super_admin', 'admin_transaksi_1', 'admin_transaksi_2', 'admin_transaksi_3', 'admin_transaksi_4'],
        'report.view' => ['super_admin', 'supervisor', 'admin_transaksi_1', 'admin_transaksi_2', 'admin_transaksi_3', 'admin_transaksi_4'],
        'report.manage' => ['super_admin', 'supervisor', 'admin_transaksi_1', 'admin_transaksi_2', 'admin_transaksi_3', 'admin_transaksi_4'],
        'complaint.view' => ['super_admin', 'supervisor', 'admin_complain'],
        'complaint.manage' => ['super_admin', 'admin_complain'],
        'invoice.view' => ['super_admin', 'supervisor', 'admin_invoice'],
        'invoice.manage' => ['super_admin', 'admin_invoice'],
        'analysis.view' => ['super_admin', 'supervisor'],
        'analysis.run' => ['super_admin', 'supervisor'],
        'fx.refresh' => ['super_admin', 'supervisor'],
        'user.manage' => ['super_admin'],
        'audit.view' => ['super_admin', 'supervisor'],
    ];

    public static function allows(string $roleCode, string $permission): bool
    {
        $roles = self::MAP[$permission] ?? [];
        return in_array('*', $roles, true) || in_array($roleCode, $roles, true);
    }
}
