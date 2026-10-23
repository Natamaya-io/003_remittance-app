<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use PDO;
use Throwable;

final class DocumentCenterController extends BaseController
{
    public function index(): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        $user = Auth::user() ?? [];
        $roleCode = (string) ($user['role_code'] ?? '');

        $isSuperAdmin = in_array($roleCode, ['super_admin', 'superadmin'], true);
        $isSupervisor = $roleCode === 'supervisor';
        $isInvoiceAdmin = in_array($roleCode, ['admin_invoice', 'invoice'], true);
        $isComplainAdmin = in_array($roleCode, ['admin_complain', 'complain'], true);

        if (!$isSuperAdmin && !$isSupervisor && !$isInvoiceAdmin && !$isComplainAdmin) {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Akses Ditolak']);
            return;
        }

        if ($isInvoiceAdmin && !$isSuperAdmin && !$isSupervisor) {
            redirect('/invoices');
        }

        $showStandingInstruction = $isSuperAdmin;
        $showInvoice = false;
        $showComplain = $isSuperAdmin || $isSupervisor || $isComplainAdmin;

        $counts = [
            'standing_instruction' => 0,
            'invoice' => 0,
            'complain' => 0,
        ];
        $databaseWarning = null;

        try {
            $pdo = App::db();

            if ($this->tableExists($pdo, 'si_documents')) {
                $counts['standing_instruction'] = $this->countRows($pdo, 'SELECT COUNT(*) FROM si_documents');
            } elseif ($this->tableExists($pdo, 'standing_instructions')) {
                $counts['standing_instruction'] = $this->countRows($pdo, 'SELECT COUNT(*) FROM standing_instructions');
            }

            if ($this->tableExists($pdo, 'invoice_template_documents')) {
                $counts['invoice'] = $this->countRows($pdo, 'SELECT COUNT(*) FROM invoice_template_documents');
            } elseif ($this->tableExists($pdo, 'document_templates') && $this->tableExists($pdo, 'document_template_types')) {
                $counts['invoice'] = $this->countRows(
                    $pdo,
                    'SELECT COUNT(*) FROM document_templates dt
                     INNER JOIN document_template_types dtt ON dtt.id = dt.type_id
                     WHERE dtt.module_code = ?',
                    ['invoice']
                );
            }

            if ($this->tableExists($pdo, 'document_templates') && $this->tableExists($pdo, 'document_template_types')) {
                $counts['complain'] = $this->countRows(
                    $pdo,
                    'SELECT COUNT(*) FROM document_templates dt
                     INNER JOIN document_template_types dtt ON dtt.id = dt.type_id
                     WHERE dtt.module_code = ?',
                    ['complain']
                );
            }
        } catch (Throwable $exception) {
            $databaseWarning = $exception->getMessage();
        }

        $this->view('documents/index', [
            'title' => 'Dokumen & Template',
            'user' => $user,
            'roleCode' => $roleCode,
            'counts' => $counts,
            'showStandingInstruction' => $showStandingInstruction,
            'showInvoice' => $showInvoice,
            'showComplain' => $showComplain,
            'databaseWarning' => $databaseWarning,
        ]);
    }

    private function tableExists(PDO $pdo, string $tableName): bool
    {
        $statement = $pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?'
        );
        $statement->execute([$tableName]);

        return (int) $statement->fetchColumn() > 0;
    }

    private function countRows(PDO $pdo, string $sql, array $parameters = []): int
    {
        $statement = $pdo->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn();
    }
}
