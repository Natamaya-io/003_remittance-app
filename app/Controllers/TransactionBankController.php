<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use PDO;
use RuntimeException;
use Throwable;

final class TransactionBankController extends BaseController
{
    public function index(): void
    {
        $this->permission('transaction.view');
        $this->ensureBniTables();

        $briJobs = (int) App::db()->query(
            "SELECT COUNT(*) FROM transaction_jobs"
        )->fetchColumn();

        $bniBatches = (int) App::db()->query(
            "SELECT COUNT(*) FROM bni_transaction_batches"
        )->fetchColumn();

        $this->view('transactions/index', [
            'title' => 'Transaksi',
            'briJobs' => $briJobs,
            'bniBatches' => $bniBatches,
        ]);
    }

    public function bniForm(): void
    {
        $this->permission('transaction.manage');
        $this->ensureBniTables();

        $statement = App::db()->prepare(
            "SELECT b.*, u.name AS creator_name,
                (SELECT COUNT(*) FROM bni_transaction_rows r WHERE r.batch_id = b.id) AS row_count
             FROM bni_transaction_batches b
             LEFT JOIN users u ON u.id = b.created_by
             ORDER BY b.id DESC
             LIMIT 20"
        );
        $statement->execute();

        $this->view('transactions/bni_import', [
            'title' => 'Import Template Bank BNI',
            'batches' => $statement->fetchAll(PDO::FETCH_ASSOC),
        ]);
    }

    public function bniStore(): void
    {
        $this->permission('transaction.manage');
        $this->ensureBniTables();

        $batchName = trim((string) ($_POST['batch_name'] ?? ''));
        if ($batchName === '') {
            $batchName = 'BNI ' . date('d-m-Y H.i');
        }

        $raw = trim((string) ($_POST['rows_json'] ?? ''));
        $rows = json_decode($raw, true);

        if (!is_array($rows) || $rows === []) {
            flash('danger', 'Data BNI masih kosong. Copy paste data Excel lalu klik Bentuk Tabel.');
            redirect('/transactions/bni');
            return;
        }

        if (count($rows) > 300) {
            flash('danger', 'Maksimal 300 baris per batch BNI.');
            redirect('/transactions/bni');
            return;
        }

        $db = App::db();

        try {
            $db->beginTransaction();

            $insertBatch = $db->prepare(
                "INSERT INTO bni_transaction_batches
                (name,total_rows,created_by,created_at,updated_at)
                VALUES (?,?,?,NOW(),NOW())"
            );
            $insertBatch->execute([
                $batchName,
                count($rows),
                Auth::id(),
            ]);

            $batchId = (int) $db->lastInsertId();

            $insertRow = $db->prepare(
                "INSERT INTO bni_transaction_rows
                (batch_id,row_no,transaction_date,beneficiary_name,remitter_name,currency,amount,payload_json,created_at,updated_at)
                VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())"
            );

            $rowNo = 0;
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }

                $normalized = $this->normalizeBniRow($row);
                if ($this->isBniBlank($normalized)) {
                    continue;
                }

                $rowNo++;
                $insertRow->execute([
                    $batchId,
                    $rowNo,
                    $this->nullable($normalized['transaction_date']),
                    $this->nullable($normalized['beneficiary_name']),
                    $this->nullable($normalized['remitter_name']),
                    $this->nullable(strtoupper((string) $normalized['currency'])),
                    (float) $normalized['amount'],
                    json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }

            if ($rowNo === 0) {
                throw new RuntimeException('Tidak ada baris BNI yang dapat disimpan.');
            }

            $db->prepare(
                "UPDATE bni_transaction_batches SET total_rows = ?, updated_at = NOW() WHERE id = ?"
            )->execute([$rowNo, $batchId]);

            AuditService::log('create', 'bni_transaction_batch', $batchId, null, [
                'name' => $batchName,
                'rows' => $rowNo,
            ]);

            $db->commit();
            flash('success', "Batch BNI berhasil disimpan. {$rowNo} formulir siap dicetak.");
            redirect('/transactions/bni/batches/' . $batchId);
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            flash('danger', 'Data BNI gagal disimpan: ' . $e->getMessage());
            redirect('/transactions/bni');
        }
    }

    public function bniBatch(string $id): void
    {
        $this->permission('transaction.view');
        $this->ensureBniTables();

        $batch = $this->findBniBatch((int) $id);
        $statement = App::db()->prepare(
            "SELECT * FROM bni_transaction_rows WHERE batch_id = ? ORDER BY row_no, id"
        );
        $statement->execute([(int) $id]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $payload = json_decode((string) ($row['payload_json'] ?? ''), true);
            $row['payload'] = is_array($payload) ? $payload : [];
        }
        unset($row);

        $this->view('transactions/bni_batch', [
            'title' => (string) ($batch['name'] ?? 'Batch BNI'),
            'batch' => $batch,
            'rows' => $rows,
        ]);
    }

    public function bniPrint(string $id): void
    {
        $this->permission('transaction.view');
        $this->ensureBniTables();

        $statement = App::db()->prepare(
            "SELECT r.*, b.name AS batch_name
             FROM bni_transaction_rows r
             JOIN bni_transaction_batches b ON b.id = r.batch_id
             WHERE r.id = ? LIMIT 1"
        );
        $statement->execute([(int) $id]);
        $record = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($record)) {
            http_response_code(404);
            echo 'Form BNI tidak ditemukan.';
            return;
        }

        $payload = json_decode((string) ($record['payload_json'] ?? ''), true);
        if (!is_array($payload)) {
            $payload = [];
        }

        $this->view('transactions/bni_print', [
            'title' => 'Cetak Form BNI',
            'record' => $record,
            'data' => $payload,
        ], 'print');
    }

    public function destroyBniBatch(string $id): void
    {
        $this->permission('transaction.manage');
        $this->ensureBniTables();

        $batch = $this->findBniBatch((int) $id);
        App::db()->prepare(
            "DELETE FROM bni_transaction_batches WHERE id = ?"
        )->execute([(int) $id]);

        AuditService::log('delete', 'bni_transaction_batch', (int) $id, $batch, null);
        flash('success', 'Batch BNI berhasil dihapus.');
        redirect('/transactions/bni');
    }

    private function findBniBatch(int $id): array
    {
        $statement = App::db()->prepare(
            "SELECT b.*, u.name AS creator_name
             FROM bni_transaction_batches b
             LEFT JOIN users u ON u.id = b.created_by
             WHERE b.id = ? LIMIT 1"
        );
        $statement->execute([$id]);
        $record = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($record)) {
            throw new RuntimeException('Batch BNI tidak ditemukan.');
        }

        return $record;
    }

    private function ensureBniTables(): void
    {
        App::db()->exec(
            "CREATE TABLE IF NOT EXISTS bni_transaction_batches (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(200) NOT NULL,
                total_rows INT UNSIGNED NOT NULL DEFAULT 0,
                created_by BIGINT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_bni_batch_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        App::db()->exec(
            "CREATE TABLE IF NOT EXISTS bni_transaction_rows (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                batch_id BIGINT UNSIGNED NOT NULL,
                row_no INT UNSIGNED NOT NULL DEFAULT 1,
                transaction_date DATE NULL,
                beneficiary_name VARCHAR(255) NULL,
                remitter_name VARCHAR(255) NULL,
                currency VARCHAR(10) NULL,
                amount DECIMAL(22,4) NOT NULL DEFAULT 0,
                payload_json LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_bni_rows_batch (batch_id,row_no),
                CONSTRAINT fk_bni_rows_batch FOREIGN KEY (batch_id)
                    REFERENCES bni_transaction_batches(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    private function normalizeBniRow(array $row): array
    {
        $keys = [
            'transaction_date','beneficiary_status','beneficiary_name','beneficiary_address',
            'beneficiary_phone','beneficiary_city','beneficiary_country','beneficiary_bank',
            'beneficiary_bank_city','beneficiary_bank_country','beneficiary_account','remitter_status',
            'remitter_name','remitter_alias','remitter_id','remitter_address','remitter_phone',
            'remitter_city','remitter_country','transaction_purpose','message','correspondent_charge_to',
            'transfer_type','source_of_fund','check_no','currency','amount','exchange_rate',
            'commission','handling','correspondent_bank','other_charge','total_charge',
            'total_payment','amount_words'
        ];

        $normalized = [];
        foreach ($keys as $key) {
            $normalized[$key] = trim((string) ($row[$key] ?? ''));
        }

        $normalized['transaction_date'] = $this->dateValue($normalized['transaction_date']);
        $normalized['currency'] = strtoupper($normalized['currency']);
        $normalized['amount'] = $this->amountValue($normalized['amount']);
        $normalized['exchange_rate'] = $this->amountValue($normalized['exchange_rate']);
        $normalized['commission'] = $this->amountValue($normalized['commission']);
        $normalized['handling'] = $this->amountValue($normalized['handling']);
        $normalized['correspondent_bank'] = $this->amountValue($normalized['correspondent_bank']);
        $normalized['other_charge'] = $this->amountValue($normalized['other_charge']);

        $computedCharge = $normalized['commission'] + $normalized['handling']
            + $normalized['correspondent_bank'] + $normalized['other_charge'];

        $normalized['total_charge'] = $this->amountValue($normalized['total_charge']);
        if ($normalized['total_charge'] <= 0 && $computedCharge > 0) {
            $normalized['total_charge'] = $computedCharge;
        }

        $normalized['total_payment'] = $this->amountValue($normalized['total_payment']);
        if ($normalized['total_payment'] <= 0 && $normalized['amount'] > 0) {
            $normalized['total_payment'] = $normalized['amount'] + $normalized['total_charge'];
        }

        return $normalized;
    }

    private function isBniBlank(array $row): bool
    {
        return trim((string) ($row['beneficiary_name'] ?? '')) === ''
            && trim((string) ($row['remitter_name'] ?? '')) === ''
            && (float) ($row['amount'] ?? 0) <= 0;
    }

    private function dateValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        foreach (['d/m/Y','d-m-Y','Y-m-d','m/d/Y'] as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date instanceof \DateTime && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        $timestamp = strtotime($value);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : '';
    }

    private function amountValue(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $text = preg_replace('/[^0-9,.-]/', '', (string) $value) ?? '';
        if ($text === '') {
            return 0.0;
        }

        if (str_contains($text, ',') && str_contains($text, '.')) {
            if (strrpos($text, ',') > strrpos($text, '.')) {
                $text = str_replace('.', '', $text);
                $text = str_replace(',', '.', $text);
            } else {
                $text = str_replace(',', '', $text);
            }
        } elseif (str_contains($text, ',')) {
            $text = str_replace(',', '.', $text);
        }

        return is_numeric($text) ? (float) $text : 0.0;
    }

    private function nullable(mixed $value): ?string
    {
        $text = trim((string) $value);
        return $text !== '' ? $text : null;
    }
}
