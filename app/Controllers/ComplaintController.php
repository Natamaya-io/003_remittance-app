<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use App\Services\UploadService;
use PDO;
use RuntimeException;

final class ComplaintController extends BaseController
{
    public function index(): void
    {
        $this->permission('complaint.view');
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $sql = "SELECT cp.*, c.name company_name, t.reference_no, u.name assigned_name,
                       rr.beneficiary_name report_beneficiary, rr.amount report_amount,
                       rr.currency report_currency, rb.name report_batch_name, rb.month_key report_month
                FROM complaints cp
                JOIN companies c ON c.id=cp.company_id
                LEFT JOIN transactions t ON t.id=cp.transaction_id
                LEFT JOIN users u ON u.id=cp.assigned_to
                LEFT JOIN report_rows rr ON rr.id=cp.report_row_id
                LEFT JOIN report_batches rb ON rb.id=rr.batch_id
                WHERE 1=1";
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (cp.ticket_no LIKE ? OR cp.subject LIKE ? OR c.name LIKE ? OR rr.beneficiary_name LIKE ? OR rb.name LIKE ?)';
            $search = '%' . $q . '%';
            $params = [$search, $search, $search, $search, $search];
        }
        if ($status !== '') {
            $sql .= ' AND cp.status=?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY cp.created_at DESC';
        $stmt = App::db()->prepare($sql);
        $stmt->execute($params);
        $this->view('complaints/index', [
            'title' => 'Complain',
            'complaints' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function create(): void
    {
        $this->permission('complaint.manage');
        $this->view('complaints/form', ['title' => 'Tambah Complain', 'complaint' => null] + $this->formData());
    }

    public function store(): void
    {
        $this->permission('complaint.manage');
        $data = $this->validated(true);
        $data['ticket_no'] = 'CMP-' . date('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $data['attachment_path'] = UploadService::store($_FILES['attachment'] ?? [], 'complaints');
        $data['created_by'] = Auth::id();
        $cols = array_keys($data);
        $stmt = App::db()->prepare('INSERT INTO complaints(' . implode(',', $cols) . ',created_at,updated_at) VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ',NOW(),NOW())');
        $stmt->execute(array_values($data));
        $id = (int) App::db()->lastInsertId();
        AuditService::log('create', 'complaint', $id, null, $data);
        $this->logHistory($id, 'created', 'Complain dibuat dari ' . ($data['report_row_id'] ? 'Report Harian' : 'input manual') . '.', null, (string) $data['status']);
        flash('success', 'Complain berhasil dibuat dan sumber report tersimpan.');
        redirect('/complaints');
    }

    public function edit(string $id): void
    {
        $this->permission('complaint.manage');
        $complaint = $this->find((int) $id);
        $histories = $this->histories((int) $id);
        $this->view('complaints/form', ['title' => 'Ubah Complain', 'complaint' => $complaint, 'histories' => $histories] + $this->formData());
    }

    public function update(string $id): void
    {
        $this->permission('complaint.manage');
        $before = $this->find((int) $id);
        $data = $this->validated(false);
        $attachment = UploadService::store($_FILES['attachment'] ?? [], 'complaints');
        if ($attachment !== null) {
            $data['attachment_path'] = $attachment;
        }
        $sets = implode(',', array_map(static fn (string $column): string => "{$column}=?", array_keys($data)));
        App::db()->prepare("UPDATE complaints SET {$sets},updated_at=NOW() WHERE id=?")
            ->execute([...array_values($data), (int) $id]);
        AuditService::log('update', 'complaint', (int) $id, $before, $data);
        $changes = [];
        foreach (['status' => 'status', 'priority' => 'prioritas', 'assigned_to' => 'PIC'] as $field => $label) {
            if ((string) ($before[$field] ?? '') !== (string) ($data[$field] ?? '')) {
                $changes[] = $label . ': ' . (string) ($before[$field] ?? '-') . ' → ' . (string) ($data[$field] ?? '-');
            }
        }
        if (trim((string) ($data['resolution'] ?? '')) !== '' && trim((string) ($before['resolution'] ?? '')) !== trim((string) $data['resolution'])) {
            $changes[] = 'Resolusi diperbarui';
        }
        $this->logHistory((int) $id, 'updated', $changes !== [] ? implode('; ', $changes) : 'Informasi complain diperbarui.', (string) ($before['status'] ?? ''), (string) ($data['status'] ?? ''));
        flash('success', 'Complain diperbarui.');
        redirect('/complaints');
    }

    public function destroy(string $id): void
    {
        $this->permission('complaint.manage');
        $before = $this->find((int) $id);
        App::db()->prepare('DELETE FROM complaints WHERE id=?')->execute([(int) $id]);
        AuditService::log('delete', 'complaint', (int) $id, $before, null);
        flash('success', 'Complain dihapus.');
        redirect('/complaints');
    }

    /** @return array<string,mixed> */
    private function validated(bool $isCreate): array
    {
        $reportRowId = (int) ($_POST['report_row_id'] ?? 0) ?: null;
        $companyId = (int) ($_POST['company_id'] ?? 0);
        $description = trim((string) ($_POST['description'] ?? '')) ?: null;

        if ($reportRowId !== null) {
            $stmt = App::db()->prepare(
                "SELECT rr.*, rb.name batch_name, rb.month_key, c.id matched_company_id
                 FROM report_rows rr
                 JOIN report_batches rb ON rb.id=rr.batch_id
                 LEFT JOIN companies c ON REPLACE(REPLACE(UPPER(TRIM(c.name)),'.',''),' ','')=REPLACE(REPLACE(UPPER(TRIM(rr.bill_to)),'.',''),' ','')
                 WHERE rr.id=?
                   AND (LOWER(COALESCE(rr.delivery_status,'')) IN ('sent','processed','delivered','success','terkirim','sukses')
                        OR LOWER(COALESCE(rr.result_status,'')) IN ('success','processed','sukses'))"
            );
            $stmt->execute([$reportRowId]);
            $source = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$source) {
                flash('danger', 'Baris report yang dipilih tidak ditemukan.');
                redirect($_SERVER['HTTP_REFERER'] ?? '/complaints/create');
            }
            if ($companyId <= 0 && (int) ($source['matched_company_id'] ?? 0) > 0) {
                $companyId = (int) $source['matched_company_id'];
            }
            if ($description === null) {
                $description = sprintf(
                    "Sumber report: %s (%s)\nBeneficiary: %s\nNominal: %s %s\nBank/SWIFT: %s / %s\nStatus: %s / %s",
                    (string) $source['batch_name'],
                    (string) $source['month_key'],
                    (string) $source['beneficiary_name'],
                    number_format((float) $source['amount'], 2, ',', '.'),
                    (string) $source['currency'],
                    (string) $source['beneficiary_bank'],
                    (string) $source['swift'],
                    (string) $source['delivery_status'],
                    (string) $source['result_status']
                );
            }
        }

        $subject = trim((string) ($_POST['subject'] ?? ''));
        if ($subject === '' || $companyId <= 0) {
            flash('danger', 'Perusahaan dan subjek wajib diisi. Pilih perusahaan secara manual apabila nama payer report belum cocok dengan Data PT.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/complaints');
        }

        return [
            'company_id' => $companyId,
            'transaction_id' => (int) ($_POST['transaction_id'] ?? 0) ?: null,
            'report_row_id' => $reportRowId,
            'subject' => $subject,
            'description' => $description,
            'priority' => in_array($_POST['priority'] ?? 'medium', ['low', 'medium', 'high', 'critical'], true) ? $_POST['priority'] : 'medium',
            'status' => in_array($_POST['status'] ?? 'open', ['open', 'in_progress', 'resolved', 'closed'], true) ? $_POST['status'] : 'open',
            'assigned_to' => (int) ($_POST['assigned_to'] ?? Auth::id()) ?: Auth::id(),
            'resolution' => trim((string) ($_POST['resolution'] ?? '')) ?: null,
        ];
    }

    /** @return array<string,mixed> */
    private function formData(): array
    {
        $reportRows = App::db()->query(
            "SELECT rr.id, rr.bill_to, rr.beneficiary_name, rr.amount, rr.currency, rr.swift,
                    rr.beneficiary_bank, rr.delivery_status, rr.result_status,
                    rb.id batch_id, rb.name batch_name, rb.month_key,
                    c.id company_id
             FROM report_rows rr
             JOIN report_batches rb ON rb.id=rr.batch_id
             LEFT JOIN companies c ON REPLACE(REPLACE(UPPER(TRIM(c.name)),'.',''),' ','')=REPLACE(REPLACE(UPPER(TRIM(rr.bill_to)),'.',''),' ','')
             WHERE LOWER(COALESCE(rr.delivery_status,'')) IN ('sent','processed','delivered','success','terkirim','sukses')
                OR LOWER(COALESCE(rr.result_status,'')) IN ('success','processed','sukses')
             ORDER BY rb.month_key DESC, rr.id DESC LIMIT 1000"
        )->fetchAll(PDO::FETCH_ASSOC);

        return [
            'companies' => App::db()->query("SELECT id,name FROM companies WHERE status='active' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC),
            'transactions' => App::db()->query("SELECT t.id,t.reference_no,c.name company_name FROM transactions t JOIN companies c ON c.id=t.payer_company_id WHERE t.deleted_at IS NULL ORDER BY t.id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC),
            'reportRows' => $reportRows,
            'users' => App::db()->query("SELECT u.id,u.name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.is_active=1 AND r.code IN ('admin_complain','supervisor','super_admin') ORDER BY u.name")->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    /** @return array<int,array<string,mixed>> */
    private function histories(int $complaintId): array
    {
        try {
            $statement = App::db()->prepare(
                'SELECT h.*,u.name user_name FROM complaint_histories h LEFT JOIN users u ON u.id=h.user_id WHERE h.complaint_id=? ORDER BY h.id DESC'
            );
            $statement->execute([$complaintId]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    private function logHistory(int $complaintId, string $action, string $note, ?string $from, ?string $to): void
    {
        try {
            App::db()->prepare(
                'INSERT INTO complaint_histories (complaint_id,action,note,status_from,status_to,user_id,created_at) VALUES (?,?,?,?,?,?,NOW())'
            )->execute([$complaintId, $action, $note, $from ?: null, $to ?: null, Auth::id()]);
        } catch (\Throwable) {
            // Histori khusus tersedia setelah migrasi V5; audit log utama tetap berjalan.
        }
    }

    /** @return array<string,mixed> */
    private function find(int $id): array
    {
        $statement = App::db()->prepare('SELECT * FROM complaints WHERE id=?');
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new RuntimeException('Complain tidak ditemukan.');
        }
        return $row;
    }
}
