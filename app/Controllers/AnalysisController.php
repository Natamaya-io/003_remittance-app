<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use App\Services\SpkAnalysisService;
use App\Services\SpkImportService;
use App\Services\UploadService;
use PDO;

final class AnalysisController extends BaseController
{
    public function index(): void
    {
        $this->permission('analysis.view');
        $service = new SpkAnalysisService();
        $sources = $service->sources();
        $activeSources = $service->activeSources();
        $sourceReadiness = $service->sourceReadiness();

        $stats = [
            'criteria' => (int) App::db()->query('SELECT COUNT(*) FROM spk_criteria')->fetchColumn(),
            'subcriteria' => (int) App::db()->query('SELECT COUNT(*) FROM spk_subcriteria')->fetchColumn(),
            'alternatives' => (int) App::db()->query('SELECT COUNT(*) FROM spk_alternatives WHERE is_active=1')->fetchColumn(),
            'assessments' => (int) App::db()->query('SELECT COUNT(*) FROM spk_assessments')->fetchColumn(),
            'runs' => (int) App::db()->query('SELECT COUNT(*) FROM spk_runs')->fetchColumn(),
        ];

        $latestRun = $service->latestRun();
        $latestResults = $latestRun ? $service->runResults((int) $latestRun['id']) : [];

        $this->view('analysis/index', [
            'title' => 'Entropy-TOPSIS',
            'sources' => $sources,
            'activeSources' => $activeSources,
            'sourceReadiness' => $sourceReadiness,
            'sourceTypes' => SpkImportService::SOURCE_TYPES,
            'stats' => $stats,
            'latestRun' => $latestRun,
            'latestResults' => $latestResults,
        ]);
    }

    public function uploadSource(): void
    {
        $this->permission('analysis.run');
        $sourceType = trim((string) ($_POST['source_type'] ?? ''));
        if (!isset(SpkImportService::SOURCE_TYPES[$sourceType])) {
            flash('danger', 'Pilih jenis sumber data analisis.');
            redirect('/analysis');
        }
        if (empty($_FILES['analysis_file']['tmp_name'])) {
            flash('danger', 'Pilih file Excel terlebih dahulu.');
            redirect('/analysis');
        }

        try {
            $extension = strtolower(pathinfo((string) ($_FILES['analysis_file']['name'] ?? ''), PATHINFO_EXTENSION));
            if (!in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
                throw new \RuntimeException('Format file harus XLSX, XLS XML, atau CSV.');
            }

            $storedPath = UploadService::store($_FILES['analysis_file'], 'analysis-spk/' . date('Y/m'));
            if (!$storedPath) {
                throw new \RuntimeException('File gagal disimpan.');
            }

            $importer = new SpkImportService();
            $parsed = $importer->parse(BASE_PATH . '/public/' . $storedPath, $sourceType);
            $name = trim((string) ($_POST['source_name'] ?? ''));
            if ($name === '') {
                $name = SpkImportService::SOURCE_TYPES[$sourceType];
            }

            $sourceId = $importer->persist(
                $parsed,
                $sourceType,
                $name,
                basename((string) $_FILES['analysis_file']['name']),
                $storedPath,
                Auth::id(),
                'upload'
            );

            (new SpkAnalysisService())->rebuildAssessments();
            unset($_SESSION['spk_calculation_preview']);
            AuditService::log('import', 'spk_source', $sourceId, null, [
                'source_type' => $sourceType,
                'imported_rows' => $parsed['imported_rows'] ?? 0,
            ]);

            flash('success', 'Sumber data berhasil diimpor dan penilaian otomatis telah diperbarui.');
        } catch (\Throwable $exception) {
            flash('danger', 'Sumber data tidak dapat diimpor: ' . $exception->getMessage());
        }
        redirect('/analysis');
    }

    public function criteria(): void
    {
        $this->permission('analysis.view');
        $criteria = App::db()->query('SELECT * FROM spk_criteria ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
        $this->view('analysis/criteria', [
            'title' => 'Data Kriteria - Entropy TOPSIS',
            'criteria' => $criteria,
            'rules' => $this->rules(),
        ]);
    }

    public function saveCriterion(): void
    {
        $this->permission('analysis.run');
        $id = (int) ($_POST['id'] ?? 0);
        $code = strtoupper(trim((string) ($_POST['code'] ?? '')));
        $name = trim((string) ($_POST['name'] ?? ''));
        $type = strtolower(trim((string) ($_POST['criterion_type'] ?? 'benefit')));
        $rule = trim((string) ($_POST['data_rule'] ?? 'manual'));
        $description = trim((string) ($_POST['description'] ?? ''));
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;

        if (!preg_match('/^[A-Z][A-Z0-9_]{0,9}$/', $code) || $name === '' || !in_array($type, ['benefit', 'cost'], true)) {
            flash('danger', 'Kode, nama, atau jenis kriteria tidak valid.');
            redirect('/analysis/criteria');
        }
        if (!array_key_exists($rule, $this->rules())) {
            $rule = 'manual';
        }

        if ($id > 0) {
            $statement = App::db()->prepare(
                'UPDATE spk_criteria SET code=?,name=?,criterion_type=?,data_rule=?,description=?,sort_order=?,is_active=?,updated_at=NOW() WHERE id=?'
            );
            $statement->execute([$code, $name, $type, $rule, $description, $sortOrder, $active, $id]);
        } else {
            $statement = App::db()->prepare(
                'INSERT INTO spk_criteria(code,name,criterion_type,data_rule,description,sort_order,is_active,created_by,created_at,updated_at)
                 VALUES (?,?,?,?,?,?,?, ?,NOW(),NOW())'
            );
            $statement->execute([$code, $name, $type, $rule, $description, $sortOrder, $active, Auth::id()]);
        }
        (new SpkAnalysisService())->rebuildAssessments();
        flash('success', 'Kriteria berhasil disimpan.');
        redirect('/analysis/criteria');
    }

    public function deleteCriterion(string $id): void
    {
        $this->permission('analysis.run');
        $statement = App::db()->prepare('DELETE FROM spk_criteria WHERE id=?');
        $statement->execute([(int) $id]);
        flash('success', 'Kriteria dihapus.');
        redirect('/analysis/criteria');
    }

    public function subcriteria(): void
    {
        $this->permission('analysis.view');
        $criteria = App::db()->query('SELECT * FROM spk_criteria ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
        $rows = App::db()->query(
            'SELECT s.*,c.code,c.name criterion_name FROM spk_subcriteria s JOIN spk_criteria c ON c.id=s.criterion_id ORDER BY c.sort_order,s.sort_order,s.id'
        )->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int) $row['criterion_id']][] = $row;
        }
        $this->view('analysis/subcriteria', [
            'title' => 'Data Sub Kriteria - Entropy TOPSIS',
            'criteria' => $criteria,
            'grouped' => $grouped,
        ]);
    }

    public function saveSubcriterion(): void
    {
        $this->permission('analysis.run');
        $id = (int) ($_POST['id'] ?? 0);
        $criterionId = (int) ($_POST['criterion_id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $value = (float) ($_POST['value'] ?? 0);
        $sort = (int) ($_POST['sort_order'] ?? 0);
        if ($criterionId <= 0 || $name === '' || $value < 0) {
            flash('danger', 'Data sub kriteria tidak valid.');
            redirect('/analysis/subcriteria');
        }
        if ($id > 0) {
            $stmt = App::db()->prepare('UPDATE spk_subcriteria SET criterion_id=?,name=?,value=?,sort_order=? WHERE id=?');
            $stmt->execute([$criterionId, $name, $value, $sort, $id]);
        } else {
            $stmt = App::db()->prepare('INSERT INTO spk_subcriteria(criterion_id,name,value,sort_order) VALUES (?,?,?,?)');
            $stmt->execute([$criterionId, $name, $value, $sort]);
        }
        flash('success', 'Sub kriteria berhasil disimpan.');
        redirect('/analysis/subcriteria');
    }

    public function deleteSubcriterion(string $id): void
    {
        $this->permission('analysis.run');
        $stmt = App::db()->prepare('DELETE FROM spk_subcriteria WHERE id=?');
        $stmt->execute([(int) $id]);
        flash('success', 'Sub kriteria dihapus.');
        redirect('/analysis/subcriteria');
    }

    public function alternatives(): void
    {
        $this->permission('analysis.view');
        $alternatives = App::db()->query("SELECT * FROM spk_alternatives WHERE alternative_key IN ('FABLE','JURITA','GSB','KAIROS') ORDER BY FIELD(alternative_key,'FABLE','JURITA','GSB','KAIROS')")->fetchAll(PDO::FETCH_ASSOC);
        $this->view('analysis/alternatives', [
            'title' => 'Data Alternatif - Entropy TOPSIS',
            'alternatives' => $alternatives,
        ]);
    }

    public function saveAlternative(): void
    {
        $this->permission('analysis.run');
        $id = (int) ($_POST['id'] ?? 0);
        $key = strtoupper(trim((string) ($_POST['alternative_key'] ?? '')));
        $name = trim((string) ($_POST['name'] ?? ''));
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($name === '') {
            flash('danger', 'Nama alternatif wajib diisi.');
            redirect('/analysis/alternatives');
        }
        if ($key === '') {
            $key = 'MANUAL_' . strtoupper(substr(hash('sha256', $name . microtime(true)), 0, 12));
        }
        if ($id > 0) {
            $stmt = App::db()->prepare('UPDATE spk_alternatives SET name=?,is_active=?,updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $active, $id]);
        } else {
            $stmt = App::db()->prepare(
                'INSERT INTO spk_alternatives(alternative_key,name,is_active,source_note,created_at,updated_at) VALUES (?,?,?,\'Alternatif manual\',NOW(),NOW())'
            );
            $stmt->execute([$key, $name, $active]);
            (new SpkAnalysisService())->rebuildAssessments();
        }
        flash('success', 'Alternatif berhasil disimpan.');
        redirect('/analysis/alternatives');
    }

    public function deleteAlternative(string $id): void
    {
        $this->permission('analysis.run');
        $stmt = App::db()->prepare('DELETE FROM spk_alternatives WHERE id=?');
        $stmt->execute([(int) $id]);
        flash('success', 'Alternatif dihapus.');
        redirect('/analysis/alternatives');
    }

    public function assessment(): void
    {
        $this->permission('analysis.view');
        $criteria = App::db()->query('SELECT * FROM spk_criteria WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
        $alternatives = App::db()->query('SELECT * FROM spk_alternatives WHERE is_active=1 ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $rows = App::db()->query('SELECT * FROM spk_assessments')->fetchAll(PDO::FETCH_ASSOC);
        $matrix = [];
        foreach ($rows as $row) {
            $matrix[(int) $row['alternative_id']][(int) $row['criterion_id']] = $row;
        }
        $this->view('analysis/assessment', [
            'title' => 'Data Penilaian - Entropy TOPSIS',
            'criteria' => $criteria,
            'alternatives' => $alternatives,
            'assessmentMatrix' => $matrix,
        ]);
    }

    public function saveAssessment(): void
    {
        $this->permission('analysis.run');
        // Pada mode penelitian resmi C1-C6 tidak boleh diubah secara manual.
        // Tetap sediakan route kompatibel untuk formulir lawas.
        App::db()->exec('UPDATE spk_assessments SET override_value=NULL WHERE override_value IS NOT NULL');
        flash('warning', 'Override manual dinonaktifkan agar perhitungan sama dengan data Excel olahan.');
        redirect('/analysis/assessment');
    }

    public function resetAssessment(): void
    {
        $this->permission('analysis.run');
        App::db()->exec('UPDATE spk_assessments SET override_value=NULL,updated_at=NOW()');
        (new SpkAnalysisService())->rebuildAssessments();
        flash('success', 'Seluruh penilaian dikembalikan ke nilai otomatis dari sumber Excel.');
        redirect('/analysis/assessment');
    }

    public function calculation(): void
    {
        $this->permission('analysis.view');
        try {
            $calculation = (new SpkAnalysisService())->calculation();
            $_SESSION['spk_calculation_preview'] = $calculation;
            $this->view('analysis/calculation', [
                'title' => 'Data Perhitungan - Entropy TOPSIS',
                'calculation' => $calculation,
            ]);
        } catch (\Throwable $exception) {
            flash('danger', $exception->getMessage());
            redirect('/analysis');
        }
    }

    public function saveCalculation(): void
    {
        $this->permission('analysis.run');
        // HITUNG ULANG saat disimpan: jangan mempercayai preview dari sesi lama,
        // karena dataset dapat berubah setelah preview dibuat.
        $calculation = (new SpkAnalysisService())->calculation();
        $name = trim((string) ($_POST['name'] ?? 'Hasil Entropy-TOPSIS ' . date('d-m-Y H:i')));
        $runId = (new SpkAnalysisService())->saveRun($calculation, $name, Auth::id());
        unset($_SESSION['spk_calculation_preview']);
        flash('success', 'Hasil perhitungan berhasil disimpan.');
        redirect('/analysis/results/' . $runId);
    }

    public function results(?string $id = null): void
    {
        $this->permission('analysis.view');
        $service = new SpkAnalysisService();
        if ($id !== null && (int) $id > 0) {
            $stmt = App::db()->prepare('SELECT r.*,u.name creator_name FROM spk_runs r LEFT JOIN users u ON u.id=r.created_by WHERE r.id=?');
            $stmt->execute([(int) $id]);
            $run = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } else {
            $run = $service->latestRun();
        }
        $results = $run ? $service->runResults((int) $run['id']) : [];
        $weights = $run ? $service->runWeights((int) $run['id']) : [];
        $runs = App::db()->query('SELECT id,name,created_at FROM spk_runs ORDER BY id DESC LIMIT 25')->fetchAll(PDO::FETCH_ASSOC);
        $this->view('analysis/results', [
            'title' => 'Data Hasil Akhir - Entropy TOPSIS',
            'run' => $run,
            'results' => $results,
            'weights' => $weights,
            'runs' => $runs,
        ]);
    }

    /** @return array<string,string> */
    private function rules(): array
    {
        return [
            'growth_transaction_count' => 'C1 - Pertumbuhan jumlah transaksi (Report 2024)',
            'growth_tx_per_active_day' => 'C2 - Pertumbuhan transaksi per hari aktif (Report 2024)',
            'growth_country_count' => 'C3 - Pertumbuhan cakupan negara (Report 2024)',
            'growth_bank_swift_count' => 'C4 - Pertumbuhan cakupan bank/SWIFT (Report 2024)',
            'growth_currency_count' => 'C5 - Pertumbuhan keragaman mata uang (Report 2024)',
            'no_refund_rate_p2' => 'C6 - Tingkat transaksi tanpa refund P2 (Report + Actual)',
            'manual' => 'Manual (tidak digunakan pada mode penelitian)',
        ];
    }
}
