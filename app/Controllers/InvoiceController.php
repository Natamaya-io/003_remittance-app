<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use App\Services\UploadService;
use PDO;
use Throwable;

final class InvoiceController extends BaseController
{
    public function index(): void
    {
        $this->permission('invoice.view');

        $q = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));

        $sql = 'SELECT i.*, c.name company_name, t.reference_no
                FROM invoices i
                JOIN companies c ON c.id = i.company_id
                LEFT JOIN transactions t ON t.id = i.transaction_id
                WHERE 1=1';
        $parameters = [];

        if ($q !== '') {
            $sql .= ' AND (i.invoice_no LIKE ? OR c.name LIKE ?)';
            $parameters[] = '%' . $q . '%';
            $parameters[] = '%' . $q . '%';
        }

        if ($status !== '') {
            $sql .= ' AND i.status = ?';
            $parameters[] = $status;
        }

        $sql .= ' ORDER BY i.issue_date DESC, i.id DESC';

        $statement = App::db()->prepare($sql);
        $statement->execute($parameters);
        $invoices = $statement->fetchAll(PDO::FETCH_ASSOC);

        $templateCounts = [
            'invoice_asli' => 0,
            'invoice_order' => 0,
            'invoice_terima_uang' => 0,
        ];
        $invoiceTemplates = [];
        $templateTotal = 0;
        $templateWarning = null;

        try {
            $pdo = App::db();

            if ($this->tableExists($pdo, 'invoice_template_documents')) {
                $countStatement = $pdo->query(
                    'SELECT template_type, COUNT(*) total
                     FROM invoice_template_documents
                     GROUP BY template_type'
                );

                foreach ($countStatement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $type = (string) ($row['template_type'] ?? '');
                    if (array_key_exists($type, $templateCounts)) {
                        $templateCounts[$type] = (int) ($row['total'] ?? 0);
                    }
                }

                $templateTotal = array_sum($templateCounts);

                $latestStatement = $pdo->query(
                    'SELECT id, template_type, document_no, invoice_no, invoice_date,
                            currency, amount, company_name, bill_to_name, updated_at
                     FROM invoice_template_documents
                     ORDER BY updated_at DESC, id DESC
                     LIMIT 6'
                );

                $invoiceTemplates = $latestStatement->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $templateWarning = 'Tabel template invoice belum tersedia.';
            }
        } catch (Throwable $exception) {
            $templateWarning = $exception->getMessage();
        }

        $this->view('invoices/index', [
            'title' => 'Invoice',
            'invoices' => $invoices,
            'q' => $q,
            'status' => $status,
            'templateCounts' => $templateCounts,
            'templateTotal' => $templateTotal,
            'invoiceTemplates' => $invoiceTemplates,
            'templateWarning' => $templateWarning,
        ]);
    }

    public function create(): void
    {
        $this->permission('invoice.manage');
        $this->view('invoices/form', ['title' => 'Tambah Invoice', 'invoice' => null] + $this->formData());
    }

    public function store(): void
    {
        $this->permission('invoice.manage');
        $data = $this->validated();
        $data['attachment_path'] = UploadService::store($_FILES['attachment'] ?? [], 'invoices');
        $columns = array_keys($data);

        App::db()->prepare(
            'INSERT INTO invoices(' . implode(',', $columns) . ',created_at,updated_at)
             VALUES(' . implode(',', array_fill(0, count($columns), '?')) . ',NOW(),NOW())'
        )->execute(array_values($data));

        $id = (int) App::db()->lastInsertId();
        AuditService::log('create', 'invoice', $id, null, $data);
        flash('success', 'Invoice disimpan.');
        redirect('/invoices');
    }

    public function edit(string $id): void
    {
        $this->permission('invoice.manage');
        $invoice = $this->find((int) $id);
        $this->view('invoices/form', ['title' => 'Ubah Invoice', 'invoice' => $invoice] + $this->formData());
    }

    public function update(string $id): void
    {
        $this->permission('invoice.manage');
        $before = $this->find((int) $id);
        $data = $this->validated();
        $file = UploadService::store($_FILES['attachment'] ?? [], 'invoices');

        if ($file) {
            $data['attachment_path'] = $file;
        }

        $sets = implode(',', array_map(fn ($column) => "{$column}=?", array_keys($data)));
        App::db()->prepare("UPDATE invoices SET {$sets},updated_at=NOW() WHERE id=?")
            ->execute([...array_values($data), (int) $id]);

        AuditService::log('update', 'invoice', (int) $id, $before, $data);
        flash('success', 'Invoice diperbarui.');
        redirect('/invoices');
    }

    public function destroy(string $id): void
    {
        $this->permission('invoice.manage');
        $before = $this->find((int) $id);
        App::db()->prepare('DELETE FROM invoices WHERE id=?')->execute([(int) $id]);
        AuditService::log('delete', 'invoice', (int) $id, $before, null);
        flash('success', 'Invoice dihapus.');
        redirect('/invoices');
    }

    public function print(string $id): void
    {
        $this->permission('invoice.view');
        $invoice = $this->findJoin((int) $id);
        $this->view('invoices/print', ['title' => 'Invoice ' . $invoice['invoice_no'], 'invoice' => $invoice], 'guest');
    }

    private function validated(): array
    {
        $number = trim((string) ($_POST['invoice_no'] ?? ''));
        $company = (int) ($_POST['company_id'] ?? 0);

        if ($number === '' || $company <= 0) {
            flash('danger', 'Nomor invoice dan perusahaan wajib diisi.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/invoices');
        }

        return [
            'invoice_no' => $number,
            'company_id' => $company,
            'transaction_id' => (int) ($_POST['transaction_id'] ?? 0) ?: null,
            'issue_date' => $_POST['issue_date'] ?? date('Y-m-d'),
            'due_date' => $_POST['due_date'] ?? null,
            'amount' => (float) ($_POST['amount'] ?? 0),
            'currency' => strtoupper(trim((string) ($_POST['currency'] ?? 'USD'))),
            'status' => in_array($_POST['status'] ?? 'draft', ['draft', 'sent', 'paid', 'overdue', 'cancelled'], true)
                ? $_POST['status']
                : 'draft',
            'notes' => trim((string) ($_POST['notes'] ?? '')) ?: null,
            'created_by' => Auth::id(),
        ];
    }

    private function formData(): array
    {
        return [
            'companies' => App::db()->query(
                "SELECT id,name FROM companies WHERE status='active' ORDER BY name"
            )->fetchAll(PDO::FETCH_ASSOC),
            'transactions' => App::db()->query(
                "SELECT t.id,t.reference_no,c.name company_name
                 FROM transactions t
                 JOIN companies c ON c.id=t.payer_company_id
                 WHERE t.deleted_at IS NULL
                 ORDER BY t.id DESC
                 LIMIT 500"
            )->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    private function find(int $id): array
    {
        $statement = App::db()->prepare('SELECT * FROM invoices WHERE id=?');
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException('Invoice tidak ditemukan.');
        }

        return $row;
    }

    private function findJoin(int $id): array
    {
        $statement = App::db()->prepare(
            'SELECT i.*, c.name company_name, c.address company_address,
                    c.email company_email, t.reference_no
             FROM invoices i
             JOIN companies c ON c.id=i.company_id
             LEFT JOIN transactions t ON t.id=i.transaction_id
             WHERE i.id=?'
        );
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException('Invoice tidak ditemukan.');
        }

        return $row;
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
}
