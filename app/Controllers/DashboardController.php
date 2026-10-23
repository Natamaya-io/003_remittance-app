<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\FxService;
use App\Services\BankIndonesiaFxService;
use PDO;

final class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->permission('dashboard.view');

        $db = App::db();
        $teamWhere = '';
        $params = [];

        if (Auth::isTransactionAdmin()) {
            $teamWhere = ' AND t.team_no = ?';
            $params[] = Auth::transactionTeam();
        }

        $todayStatement = $db->prepare(
            "SELECT
                COUNT(*) AS total,
                COALESCE(SUM(t.status IN ('SUCCESS','SUKSES','TERKIRIM')), 0) AS success_count,
                COALESCE(SUM(t.refund_status = 'success'), 0) AS refund_count,
                COALESCE(SUM(t.amount), 0) AS amount_total
             FROM transactions t
             WHERE t.transaction_date = CURDATE()
               AND t.deleted_at IS NULL{$teamWhere}"
        );
        $todayStatement->execute($params);
        $today = $todayStatement->fetch(PDO::FETCH_ASSOC) ?: [];

        $openComplaints = (int) $db->query(
            "SELECT COUNT(*) FROM complaints WHERE status IN ('open','in_progress')"
        )->fetchColumn();

        $pendingInvoices = (int) $db->query(
            "SELECT COUNT(*) FROM invoices WHERE status IN ('draft','sent','overdue')"
        )->fetchColumn();

        $companies = (int) $db->query(
            "SELECT COUNT(*) FROM companies WHERE status='active'"
        )->fetchColumn();

        $trendStatement = $db->prepare(
            "SELECT t.transaction_date AS date, COUNT(*) AS total
             FROM transactions t
             WHERE t.transaction_date >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
               AND t.deleted_at IS NULL{$teamWhere}
             GROUP BY t.transaction_date
             ORDER BY t.transaction_date"
        );
        $trendStatement->execute($params);
        $trend = $trendStatement->fetchAll(PDO::FETCH_ASSOC);

        $statusStatement = $db->prepare(
            "SELECT COALESCE(NULLIF(t.status,''),'UNKNOWN') AS label, COUNT(*) AS total
             FROM transactions t
             WHERE t.transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
               AND t.deleted_at IS NULL{$teamWhere}
             GROUP BY t.status
             ORDER BY total DESC
             LIMIT 8"
        );
        $statusStatement->execute($params);
        $statuses = $statusStatement->fetchAll(PDO::FETCH_ASSOC);

        $recentStatement = $db->prepare(
            "SELECT
                t.id,
                t.transaction_date,
                t.reference_no,
                t.beneficiary_name,
                t.amount,
                t.currency,
                t.status,
                c.name AS company_name
             FROM transactions t
             INNER JOIN companies c ON c.id = t.payer_company_id
             WHERE t.deleted_at IS NULL{$teamWhere}
             ORDER BY t.created_at DESC
             LIMIT 8"
        );
        $recentStatement->execute($params);
        $recent = $recentStatement->fetchAll(PDO::FETCH_ASSOC);

        $fx = [
            'base' => 'IDR',
            'rates' => [],
            'provider' => 'offline',
            'cached' => true,
        ];

        try {
            $fx = (new FxService())->getRates();
        } catch (\Throwable $exception) {
            $fx['error'] = $exception->getMessage();
        }

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'today' => $today,
            'openComplaints' => $openComplaints,
            'pendingInvoices' => $pendingInvoices,
            'companies' => $companies,
            'trend' => $trend,
            'statuses' => $statuses,
            'recent' => $recent,
            'fx' => $fx,
        ]);
    }
}
