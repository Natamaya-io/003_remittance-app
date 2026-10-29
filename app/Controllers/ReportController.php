<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use App\Services\ReportImportService;
use App\Services\UploadService;
use App\Services\XlsxWriter;
use PDO;

final class ReportController extends BaseController
{
    public function index(): void
    {
        $this->permission('report.view');

        $filters = $this->readFilters();
        [$where, $params] = $this->buildBatchFilter($filters);
        $effectiveDate = $this->effectiveDateSql('b');
        $teamExpression = $this->teamSql('b');

        $statement = App::db()->prepare(
            "SELECT
                b.month_key,
                COUNT(DISTINCT b.id) AS file_count,
                COUNT(DISTINCT {$effectiveDate}) AS date_count,
                COUNT(DISTINCT {$teamExpression}) AS team_count,
                COALESCE(SUM(rs.total_rows), 0) AS total_rows,
                COALESCE(SUM(rs.sent_rows), 0) AS sent_rows,
                COALESCE(SUM(rs.pending_rows), 0) AS pending_rows,
                COALESCE(SUM(b.skipped_rows), 0) AS skipped_rows,
                MAX(b.updated_at) AS last_upload
             FROM report_batches b
             LEFT JOIN (
                SELECT
                    batch_id,
                    COUNT(*) AS total_rows,
                    SUM(delivery_status='SENT') AS sent_rows,
                    SUM(delivery_status='PENDING') AS pending_rows
                FROM report_rows
                GROUP BY batch_id
             ) rs ON rs.batch_id=b.id
             WHERE 1=1 {$where}
             GROUP BY b.month_key
             ORDER BY b.month_key DESC"
        );
        $statement->execute($params);
        $months = $statement->fetchAll(PDO::FETCH_ASSOC);

        $briJobs = 0;
        $bniBatches = 0;
        try {
            $briJobs = (int) App::db()->query("SELECT COUNT(*) FROM transaction_jobs")->fetchColumn();
            $bniBatches = (int) App::db()->query("SELECT COUNT(*) FROM bni_transaction_batches")->fetchColumn();
        } catch (\Throwable $e) {
            // fallback jika tabel belum siap
        }

        $this->view('reports/index', [
            'title' => 'Laporan',
            'months' => $months,
            'filters' => $filters,
            'teams' => $this->teamOptions(),
            'availableYears' => $this->availableReportYears(),
            'briJobs' => $briJobs,
            'bniBatches' => $bniBatches,
        ]);
    }

    public function month(string $month): void
    {
        $this->permission('report.view');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            flash('danger', 'Format bulan tidak valid.');
            redirect('/reports');
        }

        $filters = $this->readFilters();
        $filters['month'] = $month;
        [$where, $params] = $this->buildBatchFilter($filters);
        $effectiveDate = $this->effectiveDateSql('b');
        $teamExpression = $this->teamSql('b');

        $statement = App::db()->prepare(
            "SELECT
                {$effectiveDate} AS report_day,
                COUNT(DISTINCT b.id) AS file_count,
                COUNT(DISTINCT {$teamExpression}) AS team_count,
                COALESCE(SUM(rs.total_rows), 0) AS total_rows,
                COALESCE(SUM(rs.sent_rows), 0) AS sent_rows,
                COALESCE(SUM(rs.pending_rows), 0) AS pending_rows,
                COALESCE(SUM(b.skipped_rows), 0) AS skipped_rows,
                MAX(b.updated_at) AS last_upload
             FROM report_batches b
             LEFT JOIN (
                SELECT
                    batch_id,
                    COUNT(*) AS total_rows,
                    SUM(delivery_status='SENT') AS sent_rows,
                    SUM(delivery_status='PENDING') AS pending_rows
                FROM report_rows
                GROUP BY batch_id
             ) rs ON rs.batch_id=b.id
             WHERE 1=1 {$where}
             GROUP BY report_day
             ORDER BY report_day DESC"
        );
        $statement->execute($params);
        $dates = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/month', [
            'title' => 'Report ' . $this->monthLabel($month),
            'month' => $month,
            'dates' => $dates,
            'filters' => $filters,
            'teams' => $this->teamOptions(),
        ]);
    }

    public function date(string $date): void
    {
        $this->permission('report.view');
        if (!$this->isValidDate($date)) {
            flash('danger', 'Format tanggal tidak valid.');
            redirect('/reports');
        }

        $filters = $this->readFilters();
        $filters['date_from'] = $date;
        $filters['date_to'] = $date;
        [$where, $params] = $this->buildBatchFilter($filters);
        $effectiveDate = $this->effectiveDateSql('b');
        $teamExpression = $this->teamSql('b');

        $statement = App::db()->prepare(
            "SELECT
                b.*,
                u.name AS creator_name,
                {$effectiveDate} AS effective_date,
                {$teamExpression} AS effective_team,
                COALESCE(rs.total_rows, 0) AS total_rows_live,
                COALESCE(rs.sent_rows, 0) AS sent_rows_live,
                COALESCE(rs.pending_rows, 0) AS pending_rows_live,
                COALESCE(rs.success_rows, 0) AS success_rows_live,
                COALESCE(rs.failed_rows, 0) AS failed_rows_live
             FROM report_batches b
             LEFT JOIN users u ON u.id=b.created_by
             LEFT JOIN (
                SELECT
                    batch_id,
                    COUNT(*) AS total_rows,
                    SUM(delivery_status='SENT') AS sent_rows,
                    SUM(delivery_status='PENDING') AS pending_rows,
                    SUM(result_status='SUCCESS') AS success_rows,
                    SUM(result_status='FAILED') AS failed_rows
                FROM report_rows
                GROUP BY batch_id
             ) rs ON rs.batch_id=b.id
             WHERE 1=1 {$where}
             ORDER BY effective_team ASC, b.id DESC"
        );
        $statement->execute($params);
        $batches = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/date', [
            'title' => 'Report ' . $this->dateLabel($date),
            'date' => $date,
            'batches' => $batches,
            'filters' => $filters,
            'teams' => $this->teamOptions(),
        ]);
    }

    public function search(): void
    {
        $this->permission('report.view');
        $filters = $this->readFilters();

        if ($filters['q'] === '') {
            flash('warning', 'Masukkan kata kunci pencarian.');
            redirect('/reports');
        }

        [$where, $params] = $this->buildBatchFilter($filters);
        $effectiveDate = $this->effectiveDateSql('b');
        $teamExpression = $this->teamSql('b');

        $batchStatement = App::db()->prepare(
            "SELECT
                b.*,
                {$effectiveDate} AS effective_date,
                {$teamExpression} AS effective_team,
                COALESCE(rs.total_rows, 0) AS total_rows_live,
                COALESCE(rs.sent_rows, 0) AS sent_rows_live,
                COALESCE(rs.pending_rows, 0) AS pending_rows_live
             FROM report_batches b
             LEFT JOIN (
                SELECT
                    batch_id,
                    COUNT(*) AS total_rows,
                    SUM(delivery_status='SENT') AS sent_rows,
                    SUM(delivery_status='PENDING') AS pending_rows
                FROM report_rows
                GROUP BY batch_id
             ) rs ON rs.batch_id=b.id
             WHERE 1=1 {$where}
             ORDER BY effective_date DESC, effective_team ASC, b.id DESC
             LIMIT 100"
        );
        $batchStatement->execute($params);
        $batches = $batchStatement->fetchAll(PDO::FETCH_ASSOC);

        [$rowWhere, $rowParams] = $this->buildRowSearchFilter($filters);
        $rowStatement = App::db()->prepare(
            "SELECT
                r.*,
                b.name AS batch_name,
                b.month_key,
                b.report_date,
                COALESCE(NULLIF(b.team_name,''), NULLIF(b.client_name,''), NULLIF(r.team_label,''), 'Tanpa Tim') AS effective_team
             FROM report_rows r
             INNER JOIN report_batches b ON b.id=r.batch_id
             WHERE 1=1 {$rowWhere}
             ORDER BY COALESCE(b.report_date, r.transaction_date, DATE(b.created_at)) DESC, b.id DESC, r.id DESC
             LIMIT 200"
        );
        $rowStatement->execute($rowParams);
        $rows = $rowStatement->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/search', [
            'title' => 'Pencarian Report',
            'filters' => $filters,
            'teams' => $this->teamOptions(),
            'batches' => $batches,
            'rows' => $rows,
        ]);
    }

    public function create(): void
    {
        $this->permission('report.manage');
        $defaultMonth = trim((string) ($_GET['month'] ?? date('Y-m')));
        $defaultDate = trim((string) ($_GET['date'] ?? ''));
        $defaultTeam = trim((string) ($_GET['team'] ?? ''));

        if (!preg_match('/^\d{4}-\d{2}$/', $defaultMonth)) {
            $defaultMonth = date('Y-m');
        }
        if ($defaultDate !== '' && !$this->isValidDate($defaultDate)) {
            $defaultDate = '';
        }

        $this->view('reports/create', [
            'title' => 'Upload Laporan',
            'defaultMonth' => $defaultMonth,
            'defaultDate' => $defaultDate,
            'defaultTeam' => $defaultTeam,
        ]);
    }

    public function store(): void
    {
        $this->permission('report.manage');

        $name = trim((string) ($_POST['name'] ?? ''));
        $month = trim((string) ($_POST['month_key'] ?? ''));
        $reportDate = trim((string) ($_POST['report_date'] ?? '')) ?: null;
        $clientName = trim((string) ($_POST['client_name'] ?? '')) ?: null;
        $teamName = trim((string) ($_POST['team_name'] ?? '')) ?: $clientName;

        if ($name === '' || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            remember_old($_POST);
            flash('danger', 'Nama report dan bulan wajib diisi.');
            redirect('/reports/create');
        }
        if ($reportDate !== null && (!$this->isValidDate($reportDate) || substr($reportDate, 0, 7) !== $month)) {
            remember_old($_POST);
            flash('danger', 'Tanggal report harus valid dan berada pada bulan yang dipilih.');
            redirect('/reports/create');
        }
        if (empty($_FILES['report_file']['tmp_name'])) {
            remember_old($_POST);
            flash('danger', 'Pilih file XLSX atau CSV yang akan diunggah.');
            redirect('/reports/create');
        }

        $storedPath = null;
        $db = App::db();
        try {
            $extension = strtolower(pathinfo((string) ($_FILES['report_file']['name'] ?? ''), PATHINFO_EXTENSION));
            if (!in_array($extension, ['xlsx', 'csv'], true)) {
                throw new \RuntimeException('Format report harus XLSX atau CSV. File XLS lama perlu disimpan ulang menjadi XLSX.');
            }

            $storedPath = UploadService::store($_FILES['report_file'], 'reports/' . str_replace('-', '/', $month));
            if ($storedPath === null) {
                throw new \RuntimeException('File report gagal disimpan.');
            }

            $absolutePath = BASE_PATH . '/public/' . $storedPath;
            $parsed = (new ReportImportService())->parse($absolutePath, $month, $reportDate, $teamName);
            $rows = $parsed['rows'];
            $uploadUuid = $this->uuidV4();
            $fileHash = hash_file('sha256', $absolutePath) ?: null;

            $db->beginTransaction();
            $batchInsert = $db->prepare(
                'INSERT INTO report_batches
                (name,month_key,report_date,client_name,team_name,source_filename,source_path,upload_uuid,file_hash,total_rows,sent_rows,pending_rows,skipped_rows,import_summary_json,created_by,created_at,updated_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())'
            );
            $batchInsert->execute([
                $name,
                $month,
                $reportDate,
                $clientName,
                $teamName,
                basename((string) $_FILES['report_file']['name']),
                $storedPath,
                $uploadUuid,
                $fileHash,
                count($rows),
                0,
                count($rows),
                (int) $parsed['skipped'],
                json_encode([
                    'report_date' => $reportDate,
                    'team_name' => $teamName,
                    'source_rows' => $parsed['total_source_rows'],
                    'accepted_rows' => count($rows),
                    'skipped_rows' => $parsed['skipped'],
                    'issues' => $parsed['issues'],
                    'reasons' => $parsed['reasons'],
                    'sheets' => $parsed['sheets'],
                    'upload_uuid' => $uploadUuid,
                    'file_hash' => $fileHash,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                Auth::id(),
            ]);
            $batchId = (int) $db->lastInsertId();

            $rowInsert = $db->prepare(
                'INSERT INTO report_rows
                (batch_id,source_sheet,source_row,source_hash,team_label,item_no,transaction_date,bill_to,beneficiary_name,beneficiary_account,beneficiary_bank,beneficiary_bank_address,amount,beneficiary_country,beneficiary_city,beneficiary_address,swift,payment_details,currency,invoice_no,invoice_date,contract_no,contract_date,correspondent_bank,correspondent_swift,correspondent_country,bank_of_payer,invoice_link,processing_bank,delivery_status,result_status,refund_status,notes,source_json,created_at,updated_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())'
            );

            foreach ($rows as $row) {
                $rowInsert->execute([
                    $batchId,
                    $row['source_sheet'],
                    $row['source_row'],
                    $row['source_hash'],
                    $row['team_label'] ?: $teamName,
                    $row['item_no'] ?: null,
                    $row['transaction_date'] ?: $reportDate,
                    $row['bill_to'] ?: null,
                    $row['beneficiary_name'] ?: null,
                    $row['beneficiary_account'] ?: null,
                    $row['beneficiary_bank'] ?: null,
                    $row['beneficiary_bank_address'] ?: null,
                    $row['amount'],
                    $row['beneficiary_country'] ?: null,
                    $row['beneficiary_city'] ?: null,
                    $row['beneficiary_address'] ?: null,
                    $row['swift'] ?: null,
                    $row['payment_details'] ?: null,
                    $row['currency'] ?: null,
                    $row['invoice_no'] ?: null,
                    $row['invoice_date'],
                    $row['contract_no'] ?: null,
                    $row['contract_date'],
                    $row['correspondent_bank'] ?: null,
                    $row['correspondent_swift'] ?: null,
                    $row['correspondent_country'] ?: null,
                    $row['bank_of_payer'] ?: null,
                    $row['invoice_link'] ?: null,
                    $row['processing_bank'] ?: null,
                    $row['delivery_status'],
                    $row['result_status'],
                    $row['refund_status'] ?: null,
                    $row['notes'] ?: null,
                    $row['source_json'],
                ]);
            }

            $db->commit();
            clear_old();
            AuditService::log('import', 'report_batch', $batchId, null, [
                'name' => $name,
                'date' => $reportDate,
                'team' => $teamName,
                'rows' => count($rows),
                'sheets' => $parsed['sheets'],
                'skipped' => $parsed['skipped'],
                'reasons' => $parsed['reasons'],
            ]);

            $message = count($rows) . ' baris valid berhasil diimpor.';
            if ((int) $parsed['skipped'] > 0) {
                $message .= ' ' . (int) $parsed['skipped'] . ' baris tidak sesuai ketentuan dilewati.';
            }
            flash('success', $message);

            if ($reportDate !== null) {
                redirect('/reports/date/' . $reportDate);
            }
            redirect('/reports/' . $batchId);
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if ($storedPath !== null && is_file(BASE_PATH . '/public/' . $storedPath)) {
                @unlink(BASE_PATH . '/public/' . $storedPath);
            }
            remember_old($_POST);
            flash('danger', 'Import report gagal: ' . $exception->getMessage());
            redirect('/reports/create');
        }
    }

    public function show(string $id): void
    {
        $this->permission('report.view');
        if (!ctype_digit($id) && !is_numeric($id)) {
            flash('danger', 'Report tidak ditemukan atau telah dihapus.');
            redirect('/reports');
        }
        $batch = $this->findBatch((int) $id);
        $filters = [
            'date' => trim((string) ($_GET['date'] ?? '')),
            'delivery_status' => strtoupper(trim((string) ($_GET['delivery_status'] ?? ''))),
            'result_status' => strtoupper(trim((string) ($_GET['result_status'] ?? ''))),
            'q' => trim((string) ($_GET['q'] ?? '')),
        ];

        $where = ' AND r.batch_id=?';
        $params = [(int) $id];
        if ($filters['date'] !== '' && $this->isValidDate($filters['date'])) {
            $where .= ' AND r.transaction_date=?';
            $params[] = $filters['date'];
        }
        if (in_array($filters['delivery_status'], ['PENDING', 'SENT'], true)) {
            $where .= ' AND r.delivery_status=?';
            $params[] = $filters['delivery_status'];
        }
        if (in_array($filters['result_status'], ['PENDING', 'SUCCESS', 'FAILED'], true)) {
            $where .= ' AND r.result_status=?';
            $params[] = $filters['result_status'];
        }
        if ($filters['q'] !== '') {
            $where .= ' AND (r.team_label LIKE ? OR r.bill_to LIKE ? OR r.beneficiary_name LIKE ? OR r.beneficiary_account LIKE ? OR r.beneficiary_bank LIKE ? OR r.swift LIKE ? OR r.invoice_no LIKE ? OR r.contract_no LIKE ? OR r.payment_details LIKE ? OR r.processing_bank LIKE ?)';
            $search = '%' . $filters['q'] . '%';
            for ($i = 0; $i < 10; $i++) {
                $params[] = $search;
            }
        }

        $statement = App::db()->prepare("SELECT r.* FROM report_rows r WHERE 1=1 {$where} ORDER BY r.transaction_date,r.source_sheet,r.source_row,r.id");
        $statement->execute($params);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $datesStatement = App::db()->prepare('SELECT DISTINCT transaction_date FROM report_rows WHERE batch_id=? AND transaction_date IS NOT NULL ORDER BY transaction_date');
        $datesStatement->execute([(int) $id]);
        $dates = $datesStatement->fetchAll(PDO::FETCH_COLUMN);

        $summaryStatement = App::db()->prepare(
            "SELECT COUNT(*) total,
                    SUM(delivery_status='SENT') sent,
                    SUM(delivery_status='PENDING') pending,
                    SUM(result_status='SUCCESS') success,
                    SUM(result_status='FAILED') failed
             FROM report_rows WHERE batch_id=?"
        );
        $summaryStatement->execute([(int) $id]);
        $summary = $summaryStatement->fetch(PDO::FETCH_ASSOC) ?: [];

        $this->view('reports/show', [
            'title' => $batch['name'],
            'batch' => $batch,
            'rows' => $rows,
            'filters' => $filters,
            'dates' => $dates,
            'summary' => $summary,
            'effectiveDate' => $this->resolveBatchDate($batch),
        ]);
    }

    public function edit(string $id): void
    {
        $this->permission('report.manage');
        if (!ctype_digit($id) && !is_numeric($id)) {
            flash('danger', 'Report tidak ditemukan atau telah dihapus.');
            redirect('/reports');
        }
        $batch = $this->findBatch((int) $id);

        $this->view('reports/edit', [
            'title' => 'Edit Report',
            'batch' => $batch,
        ]);
    }

    public function update(string $id): void
    {
        $this->permission('report.manage');
        if (!ctype_digit($id) && !is_numeric($id)) {
            flash('danger', 'Report tidak ditemukan atau telah dihapus.');
            redirect('/reports');
        }
        $batchId = (int) $id;
        $before = $this->findBatch($batchId);

        $name = trim((string) ($_POST['name'] ?? ''));
        $month = trim((string) ($_POST['month_key'] ?? ''));
        $reportDate = trim((string) ($_POST['report_date'] ?? '')) ?: null;
        $clientName = trim((string) ($_POST['client_name'] ?? '')) ?: null;
        $teamName = trim((string) ($_POST['team_name'] ?? '')) ?: $clientName;

        if ($name === '' || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            flash('danger', 'Nama report dan bulan wajib diisi.');
            redirect('/reports/' . $batchId . '/edit');
        }
        if ($reportDate !== null && (!$this->isValidDate($reportDate) || substr($reportDate, 0, 7) !== $month)) {
            flash('danger', 'Tanggal report harus valid dan berada pada bulan yang dipilih.');
            redirect('/reports/' . $batchId . '/edit');
        }

        App::db()->prepare(
            'UPDATE report_batches
             SET name=?, month_key=?, report_date=?, client_name=?, team_name=?, updated_at=NOW()
             WHERE id=?'
        )->execute([$name, $month, $reportDate, $clientName, $teamName, $batchId]);

        if ($teamName !== null) {
            App::db()->prepare(
                "UPDATE report_rows
                 SET team_label=?, updated_at=NOW()
                 WHERE batch_id=? AND (team_label IS NULL OR TRIM(team_label)='')"
            )->execute([$teamName, $batchId]);
        }

        $after = $this->findBatch($batchId);
        AuditService::log('update', 'report_batch', $batchId, $before, $after);
        flash('success', 'Metadata report berhasil diperbarui.');

        if ($reportDate !== null) {
            redirect('/reports/date/' . $reportDate);
        }
        redirect('/reports/' . $batchId);
    }

    public function updateRow(string $id, string $rowId): void
    {
        $this->permission('report.manage');
        $batchId = (int) $id;
        $rowId = (int) $rowId;
        $delivery = strtoupper(trim((string) ($_POST['delivery_status'] ?? 'PENDING')));
        $result = strtoupper(trim((string) ($_POST['result_status'] ?? 'PENDING')));
        $notes = trim((string) ($_POST['notes'] ?? '')) ?: null;

        if (!in_array($delivery, ['PENDING', 'SENT'], true)) {
            $delivery = 'PENDING';
        }
        if (!in_array($result, ['PENDING', 'SUCCESS', 'FAILED'], true)) {
            $result = 'PENDING';
        }

        $statement = App::db()->prepare('UPDATE report_rows SET delivery_status=?,result_status=?,notes=?,updated_at=NOW() WHERE id=? AND batch_id=?');
        $statement->execute([$delivery, $result, $notes, $rowId, $batchId]);
        $this->refreshBatchCounts($batchId);
        flash('success', 'Status baris report diperbarui.');
        redirect('/reports/' . $batchId . $this->returnQuery());
    }

    public function bulkStatus(string $id): void
    {
        $this->permission('report.manage');
        $batchId = (int) $id;
        $rowIds = array_values(array_filter(array_map('intval', (array) ($_POST['row_ids'] ?? []))));
        if ($rowIds === []) {
            flash('warning', 'Pilih minimal satu baris report.');
            redirect('/reports/' . $batchId . $this->returnQuery());
        }

        $delivery = strtoupper(trim((string) ($_POST['delivery_status'] ?? '')));
        $result = strtoupper(trim((string) ($_POST['result_status'] ?? '')));
        $sets = [];
        $params = [];
        if (in_array($delivery, ['PENDING', 'SENT'], true)) {
            $sets[] = 'delivery_status=?';
            $params[] = $delivery;
        }
        if (in_array($result, ['PENDING', 'SUCCESS', 'FAILED'], true)) {
            $sets[] = 'result_status=?';
            $params[] = $result;
        }
        if ($sets === []) {
            flash('warning', 'Pilih status yang akan diterapkan.');
            redirect('/reports/' . $batchId . $this->returnQuery());
        }

        $placeholders = implode(',', array_fill(0, count($rowIds), '?'));
        $params = array_merge($params, [$batchId], $rowIds);
        $statement = App::db()->prepare(
            'UPDATE report_rows SET ' . implode(',', $sets) . ',updated_at=NOW() WHERE batch_id=? AND id IN (' . $placeholders . ')'
        );
        $statement->execute($params);
        $this->refreshBatchCounts($batchId);
        flash('success', count($rowIds) . ' baris report berhasil diperbarui.');
        redirect('/reports/' . $batchId . $this->returnQuery());
    }

    public function export(string $id): void
    {
        $this->permission('report.view');
        if (!ctype_digit($id) && !is_numeric($id)) {
            flash('danger', 'Report tidak ditemukan atau telah dihapus.');
            redirect('/reports');
        }
        $batch = $this->findBatch((int) $id);
        $statement = App::db()->prepare('SELECT * FROM report_rows WHERE batch_id=? ORDER BY transaction_date,source_sheet,source_row,id');
        $statement->execute([(int) $id]);
        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->downloadRows([$batch['name'] => $records], $batch['name'] . '.xlsx');
    }

    public function exportDate(string $date): void
    {
        $this->permission('report.view');
        if (!$this->isValidDate($date)) {
            flash('danger', 'Format tanggal tidak valid.');
            redirect('/reports');
        }

        $effectiveDate = $this->effectiveDateSql('b');
        $statement = App::db()->prepare(
            "SELECT b.id, b.name, COALESCE(NULLIF(b.team_name,''), NULLIF(b.client_name,''), CONCAT('Report ', b.id)) AS team_name
             FROM report_batches b
             WHERE {$effectiveDate}=?
             ORDER BY team_name, b.id"
        );
        $statement->execute([$date]);
        $batches = $statement->fetchAll(PDO::FETCH_ASSOC);

        $sheets = [];
        foreach ($batches as $batch) {
            $rowStatement = App::db()->prepare('SELECT * FROM report_rows WHERE batch_id=? ORDER BY transaction_date,source_sheet,source_row,id');
            $rowStatement->execute([(int) $batch['id']]);
            $records = $rowStatement->fetchAll(PDO::FETCH_ASSOC);
            $sheetName = (string) ($batch['team_name'] ?: $batch['name']);
            $sheetName = $this->uniqueSheetName($sheetName, array_keys($sheets));
            $sheets[$sheetName] = $records;
        }

        if ($sheets === []) {
            flash('warning', 'Tidak ada report pada tanggal tersebut.');
            redirect('/reports/date/' . $date);
        }

        $this->downloadRows($sheets, 'Report ' . $date . '.xlsx');
    }

    public function destroy(string $id): void
    {
        $this->permission('report.manage');
        if (!ctype_digit($id) && !is_numeric($id)) {
            flash('danger', 'Report tidak ditemukan atau telah dihapus.');
            redirect('/reports');
        }
        $batch = $this->findBatch((int) $id);
        App::db()->prepare('DELETE FROM report_batches WHERE id=?')->execute([(int) $id]);
        if (!empty($batch['source_path']) && is_file(BASE_PATH . '/public/' . $batch['source_path'])) {
            @unlink(BASE_PATH . '/public/' . $batch['source_path']);
        }
        AuditService::log('delete', 'report_batch', (int) $id, $batch, null);
        flash('success', 'Report dan seluruh barisnya berhasil dihapus.');
        redirect($this->safeReturnPath('/reports'));
    }

    public function legacyDaily(): void
    {
        redirect('/reports');
    }

    private function readFilters(): array
    {
        return [
            'month' => trim((string) ($_GET['month'] ?? '')),
            'year' => trim((string) ($_GET['year'] ?? '')),
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to' => trim((string) ($_GET['date_to'] ?? '')),
            'team' => trim((string) ($_GET['team'] ?? '')),
            'delivery_status' => strtoupper(trim((string) ($_GET['delivery_status'] ?? ''))),
            'result_status' => strtoupper(trim((string) ($_GET['result_status'] ?? ''))),
            'q' => trim((string) ($_GET['q'] ?? '')),
        ];
    }

    private function buildBatchFilter(array $filters): array
    {
        $where = '';
        $params = [];
        $effectiveDate = $this->effectiveDateSql('b');

        $month = trim((string) ($filters['month'] ?? ''));
        $year = trim((string) ($filters['year'] ?? ''));

        if ($month !== '' && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $where .= ' AND b.month_key=?';
            $params[] = $month;
        } elseif ($month !== '' && preg_match('/^(?:0?[1-9]|1[0-2])$/', $month) && $year !== '' && preg_match('/^\d{4}$/', $year)) {
            $monthKey = sprintf('%04d-%02d', (int) $year, (int) $month);
            $where .= ' AND b.month_key=?';
            $params[] = $monthKey;
        } elseif ($month !== '' && preg_match('/^(?:0?[1-9]|1[0-2])$/', $month)) {
            $where .= ' AND b.month_key LIKE ?';
            $params[] = sprintf('%%-%02d', (int) $month);
        } elseif ($year !== '' && preg_match('/^\d{4}$/', $year)) {
            $where .= ' AND b.month_key LIKE ?';
            $params[] = sprintf('%04d-%%', (int) $year);
        }
        if (($filters['date_from'] ?? '') !== '' && $this->isValidDate((string) $filters['date_from'])) {
            $where .= " AND {$effectiveDate}>=?";
            $params[] = $filters['date_from'];
        }
        if (($filters['date_to'] ?? '') !== '' && $this->isValidDate((string) $filters['date_to'])) {
            $where .= " AND {$effectiveDate}<=?";
            $params[] = $filters['date_to'];
        }
        if (($filters['team'] ?? '') !== '') {
            $where .= " AND (
                COALESCE(NULLIF(b.team_name,''), NULLIF(b.client_name,''), '')=?
                OR EXISTS (
                    SELECT 1 FROM report_rows rt
                    WHERE rt.batch_id=b.id AND rt.team_label=?
                )
            )";
            $params[] = $filters['team'];
            $params[] = $filters['team'];
        }
        if (in_array(($filters['delivery_status'] ?? ''), ['PENDING', 'SENT'], true)) {
            $where .= ' AND EXISTS (SELECT 1 FROM report_rows rd WHERE rd.batch_id=b.id AND rd.delivery_status=?)';
            $params[] = $filters['delivery_status'];
        }
        if (in_array(($filters['result_status'] ?? ''), ['PENDING', 'SUCCESS', 'FAILED'], true)) {
            $where .= ' AND EXISTS (SELECT 1 FROM report_rows rr_result WHERE rr_result.batch_id=b.id AND rr_result.result_status=?)';
            $params[] = $filters['result_status'];
        }
        if (($filters['q'] ?? '') !== '') {
            $rawQ = trim((string) $filters['q']);
            if (preg_match('/^payer:\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (
                    b.client_name LIKE ?
                    OR EXISTS (SELECT 1 FROM report_rows rq WHERE rq.batch_id=b.id AND rq.bill_to LIKE ?)
                )";
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^(?:benef|beneficiary):\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND EXISTS (SELECT 1 FROM report_rows rq WHERE rq.batch_id=b.id AND (rq.beneficiary_name LIKE ? OR rq.beneficiary_account LIKE ?))";
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^bank:\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND EXISTS (SELECT 1 FROM report_rows rq WHERE rq.batch_id=b.id AND (rq.beneficiary_bank LIKE ? OR rq.processing_bank LIKE ? OR rq.swift LIKE ?))";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^(?:tim|team):\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (
                    b.team_name LIKE ?
                    OR b.client_name LIKE ?
                    OR EXISTS (SELECT 1 FROM report_rows rq WHERE rq.batch_id=b.id AND rq.team_label LIKE ?)
                )";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^date:\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (
                    b.month_key LIKE ?
                    OR DATE_FORMAT({$effectiveDate}, '%Y-%m-%d') LIKE ?
                    OR DATE_FORMAT({$effectiveDate}, '%d/%m/%Y') LIKE ?
                    OR EXISTS (SELECT 1 FROM report_rows rq WHERE rq.batch_id=b.id AND rq.transaction_date LIKE ?)
                )";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^(?:inv|invoice):\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND EXISTS (SELECT 1 FROM report_rows rq WHERE rq.batch_id=b.id AND (rq.invoice_no LIKE ? OR rq.contract_no LIKE ?))";
                $params[] = $search;
                $params[] = $search;
            } else {
                $search = '%' . $rawQ . '%';
                $where .= " AND (
                    b.name LIKE ?
                    OR b.client_name LIKE ?
                    OR b.team_name LIKE ?
                    OR b.source_filename LIKE ?
                    OR b.month_key LIKE ?
                    OR DATE_FORMAT({$effectiveDate}, '%d/%m/%Y') LIKE ?
                    OR DATE_FORMAT({$effectiveDate}, '%Y-%m-%d') LIKE ?
                    OR EXISTS (
                        SELECT 1
                        FROM report_rows rq
                        WHERE rq.batch_id=b.id
                          AND (
                            rq.team_label LIKE ?
                            OR rq.bill_to LIKE ?
                            OR rq.beneficiary_name LIKE ?
                            OR rq.beneficiary_account LIKE ?
                            OR rq.beneficiary_bank LIKE ?
                            OR rq.swift LIKE ?
                            OR rq.invoice_no LIKE ?
                            OR rq.contract_no LIKE ?
                            OR rq.payment_details LIKE ?
                            OR rq.processing_bank LIKE ?
                            OR rq.currency LIKE ?
                          )
                    )
                )";
                for ($i = 0; $i < 18; $i++) {
                    $params[] = $search;
                }
            }
        }

        return [$where, $params];
    }

    private function buildRowSearchFilter(array $filters): array
    {
        $where = '';
        $params = [];

        if (($filters['month'] ?? '') !== '' && preg_match('/^\d{4}-\d{2}$/', (string) $filters['month'])) {
            $where .= ' AND b.month_key=?';
            $params[] = $filters['month'];
        }
        if (($filters['date_from'] ?? '') !== '' && $this->isValidDate((string) $filters['date_from'])) {
            $where .= ' AND COALESCE(b.report_date, r.transaction_date, DATE(b.created_at))>=?';
            $params[] = $filters['date_from'];
        }
        if (($filters['date_to'] ?? '') !== '' && $this->isValidDate((string) $filters['date_to'])) {
            $where .= ' AND COALESCE(b.report_date, r.transaction_date, DATE(b.created_at))<=?';
            $params[] = $filters['date_to'];
        }
        if (($filters['team'] ?? '') !== '') {
            $where .= " AND COALESCE(NULLIF(b.team_name,''), NULLIF(b.client_name,''), NULLIF(r.team_label,''), '')=?";
            $params[] = $filters['team'];
        }
        if (in_array(($filters['delivery_status'] ?? ''), ['PENDING', 'SENT'], true)) {
            $where .= ' AND r.delivery_status=?';
            $params[] = $filters['delivery_status'];
        }
        if (in_array(($filters['result_status'] ?? ''), ['PENDING', 'SUCCESS', 'FAILED'], true)) {
            $where .= ' AND r.result_status=?';
            $params[] = $filters['result_status'];
        }
        if (($filters['q'] ?? '') !== '') {
            $rawQ = trim((string) $filters['q']);
            if (preg_match('/^payer:\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (r.bill_to LIKE ? OR b.client_name LIKE ?)";
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^(?:benef|beneficiary):\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (r.beneficiary_name LIKE ? OR r.beneficiary_account LIKE ?)";
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^bank:\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (r.beneficiary_bank LIKE ? OR r.processing_bank LIKE ? OR r.swift LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^(?:tim|team):\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (b.team_name LIKE ? OR r.team_label LIKE ? OR b.client_name LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^date:\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (b.report_date LIKE ? OR r.transaction_date LIKE ? OR b.month_key LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            } elseif (preg_match('/^(?:inv|invoice):\s*(.*)$/i', $rawQ, $m)) {
                $search = '%' . trim($m[1]) . '%';
                $where .= " AND (r.invoice_no LIKE ? OR r.contract_no LIKE ?)";
                $params[] = $search;
                $params[] = $search;
            } else {
                $search = '%' . $rawQ . '%';
                $where .= " AND (
                    b.name LIKE ?
                    OR b.client_name LIKE ?
                    OR b.team_name LIKE ?
                    OR b.source_filename LIKE ?
                    OR b.month_key LIKE ?
                    OR r.team_label LIKE ?
                    OR r.bill_to LIKE ?
                    OR r.beneficiary_name LIKE ?
                    OR r.beneficiary_account LIKE ?
                    OR r.beneficiary_bank LIKE ?
                    OR r.swift LIKE ?
                    OR r.invoice_no LIKE ?
                    OR r.contract_no LIKE ?
                    OR r.payment_details LIKE ?
                    OR r.processing_bank LIKE ?
                    OR r.currency LIKE ?
                )";
                for ($i = 0; $i < 16; $i++) {
                    $params[] = $search;
                }
            }
        }

        return [$where, $params];
    }

    private function effectiveDateSql(string $alias): string
    {
        return "COALESCE(
            {$alias}.report_date,
            (SELECT MIN(rd.transaction_date) FROM report_rows rd WHERE rd.batch_id={$alias}.id AND rd.transaction_date IS NOT NULL),
            DATE({$alias}.created_at)
        )";
    }

    private function teamSql(string $alias): string
    {
        return "COALESCE(
            NULLIF({$alias}.team_name,''),
            NULLIF({$alias}.client_name,''),
            (SELECT NULLIF(rt.team_label,'') FROM report_rows rt WHERE rt.batch_id={$alias}.id AND NULLIF(rt.team_label,'') IS NOT NULL LIMIT 1),
            'Tanpa Tim'
        )";
    }

    private function teamOptions(): array
    {
        $statement = App::db()->query(
            "SELECT DISTINCT team
             FROM (
                SELECT NULLIF(TRIM(team_name),'') AS team FROM report_batches
                UNION
                SELECT NULLIF(TRIM(client_name),'') AS team FROM report_batches
                UNION
                SELECT NULLIF(TRIM(team_label),'') AS team FROM report_rows
             ) teams
             WHERE team IS NOT NULL
             ORDER BY team"
        );
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    private function findBatch(int $id): array
    {
        if ($id <= 0) {
            flash('danger', 'Report tidak ditemukan atau telah dihapus.');
            redirect('/reports');
        }

        $effectiveDate = $this->effectiveDateSql('b');
        $teamExpression = $this->teamSql('b');
        $statement = App::db()->prepare(
            "SELECT b.*, u.name creator_name, {$effectiveDate} AS effective_date, {$teamExpression} AS effective_team
             FROM report_batches b
             LEFT JOIN users u ON u.id=b.created_by
             WHERE b.id=? LIMIT 1"
        );
        $statement->execute([$id]);
        $batch = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$batch) {
            flash('danger', 'Report tidak ditemukan atau telah dihapus.');
            redirect('/reports');
        }
        return $batch;
    }

    private function resolveBatchDate(array $batch): string
    {
        if (!empty($batch['report_date'])) {
            return (string) $batch['report_date'];
        }

        $statement = App::db()->prepare('SELECT MIN(transaction_date) FROM report_rows WHERE batch_id=? AND transaction_date IS NOT NULL');
        $statement->execute([(int) $batch['id']]);
        $date = $statement->fetchColumn();

        return $date ? (string) $date : substr((string) $batch['created_at'], 0, 10);
    }

    private function refreshBatchCounts(int $batchId): void
    {
        App::db()->prepare(
            "UPDATE report_batches b SET
                total_rows=(SELECT COUNT(*) FROM report_rows r WHERE r.batch_id=b.id),
                sent_rows=(SELECT COUNT(*) FROM report_rows r WHERE r.batch_id=b.id AND r.delivery_status='SENT'),
                pending_rows=(SELECT COUNT(*) FROM report_rows r WHERE r.batch_id=b.id AND r.delivery_status='PENDING'),
                updated_at=NOW()
             WHERE b.id=?"
        )->execute([$batchId]);
    }

    private function downloadRows(array $sheets, string $filename): void
    {
        $headers = [
            'TEAM', 'No.', 'Date of Transaction', 'Bill To (Payer)', 'Beneficiary Name',
            'Account number of Beneficiary', 'Beneficiary Bank', 'Bank address', 'Amounts',
            'Country of Beneficiary', 'City of Beneficiary', 'Address of Beneficiary', 'Swift',
            'Payment details', 'Currency', 'Invoice #', 'Invoice Date', 'Contract #', 'Contract Date',
            'Correspondent Bank', 'Swift Bank Corr', 'Country Bank Corr', 'Bank of Payer', 'Link to Invoice',
            'Bank Proses', 'Delivery Status', 'Result Status', 'Refund Status', 'Notes', 'Source Sheet'
        ];

        $workbook = [];
        foreach ($sheets as $sheetName => $records) {
            $rows = [];
            foreach ($records as $record) {
                $rows[] = [
                    $record['team_label'], $record['item_no'], $record['transaction_date'], $record['bill_to'],
                    $record['beneficiary_name'], $record['beneficiary_account'], $record['beneficiary_bank'],
                    $record['beneficiary_bank_address'], (float) $record['amount'], $record['beneficiary_country'],
                    $record['beneficiary_city'], $record['beneficiary_address'], $record['swift'], $record['payment_details'],
                    $record['currency'], $record['invoice_no'], $record['invoice_date'], $record['contract_no'],
                    $record['contract_date'], $record['correspondent_bank'], $record['correspondent_swift'],
                    $record['correspondent_country'], $record['bank_of_payer'], $record['invoice_link'],
                    $record['processing_bank'], $record['delivery_status'], $record['result_status'], $record['refund_status'],
                    $record['notes'], $record['source_sheet'],
                ];
            }

            $workbook[$sheetName] = [
                'headers' => $headers,
                'rows' => $rows,
                'widths' => [10, 8, 15, 28, 28, 24, 24, 30, 16, 18, 18, 36, 16, 34, 10, 22, 15, 22, 15, 24, 18, 18, 16, 34, 16, 16, 16, 16, 30, 18],
            ];
        }

        XlsxWriter::download($workbook, $filename);
    }

    private function uniqueSheetName(string $name, array $existing): string
    {
        $base = preg_replace('/[\\\/\?\*\[\]:]/', ' ', trim($name)) ?: 'Report';
        $base = substr($base, 0, 27);
        $candidate = $base;
        $counter = 2;
        while (in_array($candidate, $existing, true)) {
            $suffix = ' ' . $counter;
            $candidate = substr($base, 0, 31 - strlen($suffix)) . $suffix;
            $counter++;
        }
        return $candidate;
    }

    private function returnQuery(): string
    {
        $query = trim((string) ($_POST['return_query'] ?? ''));
        return $query !== '' ? '?' . ltrim($query, '?') : '';
    }

    private function safeReturnPath(string $fallback): string
    {
        $returnTo = trim((string) ($_POST['return_to'] ?? ''));
        if ($returnTo !== '' && str_starts_with($returnTo, '/reports') && !str_contains($returnTo, '://')) {
            return $returnTo;
        }
        return $fallback;
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTime::createFromFormat('Y-m-d', $date);
        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function uuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function monthLabel(string $month): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthNumber = (int) substr($month, 5, 2);
        return ($months[$monthNumber] ?? $month) . ' ' . substr($month, 0, 4);
    }

    private function dateLabel(string $date): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $parsed = new \DateTime($date);
        return $parsed->format('j') . ' ' . ($months[(int) $parsed->format('n')] ?? '') . ' ' . $parsed->format('Y');
    }

    private function availableReportYears(): array
    {
        try {
            $stmt = App::db()->query(
                "SELECT DISTINCT SUBSTRING(month_key, 1, 4) AS yr
                 FROM report_batches
                 WHERE month_key IS NOT NULL AND month_key != ''
                 ORDER BY yr DESC"
            );
            $dbYears = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $dbYears = array_filter($dbYears, static fn($y) => is_string($y) && preg_match('/^\d{4}$/', $y));
        } catch (\Throwable) {
            $dbYears = [];
        }

        $currentYear = (int) date('Y');
        $defaultYears = [(string) ($currentYear + 1), (string) $currentYear, (string) ($currentYear - 1), (string) ($currentYear - 2)];
        $allYears = array_unique(array_merge($dbYears, $defaultYears));
        rsort($allYears);
        return array_values($allYears);
    }
}
