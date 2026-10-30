<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use PDO;
use RuntimeException;

/**
 * Business logic modul SPK standalone untuk penelitian.
 *
 * Sumber hitung HANYA:
 * 1) Report Sukses 2024  -> indikator transaksi C1-C5
 * 2) Actual LIST MT199   -> refund C6 (sheet REFUND BNI + REFUND BRI)
 *
 * Modul ini tidak membaca transactions, report_rows, companies, invoices,
 * complaints, atau tabel operasional lain.
 */
final class SpkAnalysisService
{
    public const P1_START = '2024-09-01';
    public const P1_END   = '2024-10-31';
    public const P2_START = '2024-11-01';
    public const P2_END   = '2024-12-31';

    /** @var array<string,string> */
    private const RESEARCH_ALTERNATIVES = [
        'FABLE' => 'PT FABLE TEKNOLOGI NUSANTARA',
        'JURITA' => 'PT JURITA AGROTAMA INDONESIA',
        'GSB' => 'PT GEMA SAPTA BINTANG',
        'KAIROS' => 'PT KAIROS SINERGI INDONESIA',
    ];

    /** @return array<int,array<string,mixed>> */
    public function sources(): array
    {
        return App::db()->query(
            "SELECT s.*,u.name creator_name
             FROM spk_sources s
             LEFT JOIN users u ON u.id=s.created_by
             WHERE s.source_type IN ('report_2024','actual')
             ORDER BY s.is_active DESC,FIELD(s.source_type,'report_2024','actual'),s.created_at DESC,s.id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<int,array<string,mixed>> */
    public function activeSources(): array
    {
        return App::db()->query(
            "SELECT * FROM spk_sources
             WHERE is_active=1 AND source_type IN ('report_2024','actual')
             ORDER BY FIELD(source_type,'report_2024','actual')"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<string,bool> */
    public function sourceReadiness(): array
    {
        $ready = ['report_2024' => false, 'actual' => false];
        foreach ($this->activeSources() as $row) {
            $type = (string) $row['source_type'];
            if (array_key_exists($type, $ready)) {
                $ready[$type] = true;
            }
        }
        return $ready;
    }

    /**
     * Menjaga definisi C1-C6 agar selalu sama dengan rancangan penelitian.
     */
    private function ensureResearchCriteria(): void
    {
        $db = App::db();
        $criteria = [
            ['C1', 'Indeks pertumbuhan jumlah transaksi', 'growth_transaction_count',
                'Jumlah transaksi sukses P2 dibagi jumlah transaksi sukses P1.'],
            ['C2', 'Indeks pertumbuhan transaksi per hari aktif', 'growth_tx_per_active_day',
                '(Transaksi P2 / hari aktif P2) dibagi (Transaksi P1 / hari aktif P1).'],
            ['C3', 'Indeks pertumbuhan cakupan negara', 'growth_country_count',
                'Jumlah negara beneficiary unik P2 dibagi jumlah negara beneficiary unik P1.'],
            ['C4', 'Indeks pertumbuhan cakupan bank/SWIFT', 'growth_bank_swift_count',
                'Jumlah bank/SWIFT beneficiary unik P2 dibagi jumlah bank/SWIFT beneficiary unik P1.'],
            ['C5', 'Indeks pertumbuhan keragaman mata uang', 'growth_currency_count',
                'Jumlah mata uang unik P2 dibagi jumlah mata uang unik P1.'],
            ['C6', 'Tingkat transaksi tanpa refund P2', 'no_refund_rate_p2',
                '1 - (jumlah refund P2 dari Actual LIST MT199 / jumlah transaksi sukses P2).'],
        ];

        $statement = $db->prepare(
            "INSERT INTO spk_criteria
             (code,name,criterion_type,data_rule,description,sort_order,is_active,created_by,created_at,updated_at)
             VALUES (?,?, 'benefit', ?, ?, ?, 1, NULL, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
                name=VALUES(name),criterion_type='benefit',data_rule=VALUES(data_rule),
                description=VALUES(description),sort_order=VALUES(sort_order),is_active=1,updated_at=NOW()"
        );
        foreach ($criteria as $index => $criterion) {
            $statement->execute([$criterion[0], $criterion[1], $criterion[2], $criterion[3], $index + 1]);
        }
        $db->exec("UPDATE spk_criteria SET is_active=0 WHERE code NOT IN ('C1','C2','C3','C4','C5','C6')");
    }

    /**
     * Bangun ulang empat alternatif penelitian dan matriks C1-C6.
     * Override manual SELALU dihapus agar hasil penelitian hanya berasal
     * dari Report 2024 + Actual LIST MT199 yang aktif.
     */
    public function rebuildAssessments(): void
    {
        $db = App::db();
        $db->beginTransaction();

        try {
            $this->ensureResearchCriteria();

            $report = $db->query(
                "SELECT
                    r.payer_key,
                    MAX(r.payer_name) payer_name,
                    SUM(CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P1_START . "' AND '" . self::P1_END . "' THEN 1 ELSE 0 END) p1_tx,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P1_START . "' AND '" . self::P1_END . "' THEN r.transaction_date END) p1_days,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P1_START . "' AND '" . self::P1_END . "' AND NULLIF(TRIM(r.beneficiary_country),'') IS NOT NULL THEN UPPER(TRIM(r.beneficiary_country)) END) p1_countries,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P1_START . "' AND '" . self::P1_END . "' THEN COALESCE(NULLIF(TRIM(r.swift),''),NULLIF(UPPER(TRIM(r.beneficiary_bank)),'')) END) p1_banks,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P1_START . "' AND '" . self::P1_END . "' AND NULLIF(TRIM(r.currency),'') IS NOT NULL THEN UPPER(TRIM(r.currency)) END) p1_currencies,
                    SUM(CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P2_START . "' AND '" . self::P2_END . "' THEN 1 ELSE 0 END) p2_tx,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P2_START . "' AND '" . self::P2_END . "' THEN r.transaction_date END) p2_days,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P2_START . "' AND '" . self::P2_END . "' AND NULLIF(TRIM(r.beneficiary_country),'') IS NOT NULL THEN UPPER(TRIM(r.beneficiary_country)) END) p2_countries,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P2_START . "' AND '" . self::P2_END . "' THEN COALESCE(NULLIF(TRIM(r.swift),''),NULLIF(UPPER(TRIM(r.beneficiary_bank)),'')) END) p2_banks,
                    COUNT(DISTINCT CASE WHEN r.event_type='SUCCESS' AND r.transaction_date BETWEEN '" . self::P2_START . "' AND '" . self::P2_END . "' AND NULLIF(TRIM(r.currency),'') IS NOT NULL THEN UPPER(TRIM(r.currency)) END) p2_currencies
                 FROM spk_records r
                 JOIN spk_sources s ON s.id=r.source_id
                    AND s.is_active=1 AND s.source_type='report_2024'
                 WHERE r.payer_key IN ('FABLE','JURITA','GSB','KAIROS')
                 GROUP BY r.payer_key"
            )->fetchAll(PDO::FETCH_ASSOC);

            $refundRows = $db->query(
                "SELECT r.payer_key,
                    COUNT(DISTINCT CASE
                        WHEN r.event_type='REFUND' AND r.transaction_date BETWEEN '" . self::P1_START . "' AND '" . self::P1_END . "'
                        THEN CONCAT(r.payer_key,'|',COALESCE(NULLIF(TRIM(r.reference_no),''),r.source_hash),'|',COALESCE(r.transaction_date,'')) END) refund_p1,
                    COUNT(DISTINCT CASE
                        WHEN r.event_type='REFUND' AND r.transaction_date BETWEEN '" . self::P2_START . "' AND '" . self::P2_END . "'
                        THEN CONCAT(r.payer_key,'|',COALESCE(NULLIF(TRIM(r.reference_no),''),r.source_hash),'|',COALESCE(r.transaction_date,'')) END) refund_p2
                 FROM spk_records r
                 JOIN spk_sources s ON s.id=r.source_id
                    AND s.is_active=1 AND s.source_type='actual'
                 WHERE r.payer_key IN ('FABLE','JURITA','GSB','KAIROS')
                 GROUP BY r.payer_key"
            )->fetchAll(PDO::FETCH_ASSOC);

            $summary = [];
            foreach (self::RESEARCH_ALTERNATIVES as $key => $name) {
                $summary[$key] = [
                    'payer_key' => $key,
                    'payer_name' => $name,
                    'p1_tx' => 0, 'p1_days' => 0, 'p1_countries' => 0, 'p1_banks' => 0, 'p1_currencies' => 0,
                    'p2_tx' => 0, 'p2_days' => 0, 'p2_countries' => 0, 'p2_banks' => 0, 'p2_currencies' => 0,
                    'refund_p1' => 0, 'refund_p2' => 0,
                ];
            }
            foreach ($report as $row) {
                $key = (string) $row['payer_key'];
                if (isset($summary[$key])) {
                    $summary[$key] = array_merge($summary[$key], $row);
                }
            }
            foreach ($refundRows as $row) {
                $key = (string) $row['payer_key'];
                if (isset($summary[$key])) {
                    $summary[$key]['refund_p1'] = (int) $row['refund_p1'];
                    $summary[$key]['refund_p2'] = (int) $row['refund_p2'];
                }
            }

            $upsertAlt = $db->prepare(
                "INSERT INTO spk_alternatives
                 (alternative_key,name,is_active,source_note,created_at,updated_at)
                 VALUES (?,?,?,?,NOW(),NOW())
                 ON DUPLICATE KEY UPDATE name=VALUES(name),is_active=VALUES(is_active),source_note=VALUES(source_note),updated_at=NOW()"
            );
            foreach ($summary as $key => $row) {
                $valid = (int) $row['p1_tx'] > 0 && (int) $row['p2_tx'] > 0;
                $note = $valid
                    ? 'Alternatif penelitian aktif; memiliki transaksi pada P1 dan P2.'
                    : 'Dinonaktifkan karena transaksi P1 atau P2 tidak tersedia.';
                $upsertAlt->execute([$key, self::RESEARCH_ALTERNATIVES[$key], $valid ? 1 : 0, $note]);
            }
            $db->exec("UPDATE spk_alternatives SET is_active=0 WHERE alternative_key NOT IN ('FABLE','JURITA','GSB','KAIROS')");

            $criteria = $db->query(
                "SELECT * FROM spk_criteria WHERE code IN ('C1','C2','C3','C4','C5','C6') ORDER BY sort_order,id"
            )->fetchAll(PDO::FETCH_ASSOC);
            $criterionByRule = [];
            foreach ($criteria as $criterion) {
                $criterionByRule[(string) $criterion['data_rule']] = $criterion;
            }

            $alternativeIds = [];
            foreach ($db->query(
                "SELECT id,alternative_key FROM spk_alternatives WHERE alternative_key IN ('FABLE','JURITA','GSB','KAIROS')"
            )->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $alternativeIds[(string) $row['alternative_key']] = (int) $row['id'];
            }

            $upsertAssessment = $db->prepare(
                "INSERT INTO spk_assessments
                 (alternative_id,criterion_id,auto_value,override_value,source_detail_json,updated_at)
                 VALUES (?,?,?,NULL,?,NOW())
                 ON DUPLICATE KEY UPDATE auto_value=VALUES(auto_value),override_value=NULL,source_detail_json=VALUES(source_detail_json),updated_at=NOW()"
            );

            foreach ($summary as $key => $row) {
                if (!isset($alternativeIds[$key])) {
                    continue;
                }

                $p1Tx = (int) $row['p1_tx'];
                $p1Days = (int) $row['p1_days'];
                $p1Countries = (int) $row['p1_countries'];
                $p1Banks = (int) $row['p1_banks'];
                $p1Currencies = (int) $row['p1_currencies'];
                $p2Tx = (int) $row['p2_tx'];
                $p2Days = (int) $row['p2_days'];
                $p2Countries = (int) $row['p2_countries'];
                $p2Banks = (int) $row['p2_banks'];
                $p2Currencies = (int) $row['p2_currencies'];
                $refundP1 = (int) $row['refund_p1'];
                $refundP2 = (int) $row['refund_p2'];

                $txPerDayP1 = ($p1Tx > 0 && $p1Days > 0) ? $p1Tx / $p1Days : 0.0;
                $txPerDayP2 = ($p2Tx > 0 && $p2Days > 0) ? $p2Tx / $p2Days : 0.0;

                $values = [
                    'growth_transaction_count' => $p1Tx > 0 ? $p2Tx / $p1Tx : 0.0,
                    'growth_tx_per_active_day' => $txPerDayP1 > 0 ? $txPerDayP2 / $txPerDayP1 : 0.0,
                    'growth_country_count' => $p1Countries > 0 ? $p2Countries / $p1Countries : 0.0,
                    'growth_bank_swift_count' => $p1Banks > 0 ? $p2Banks / $p1Banks : 0.0,
                    'growth_currency_count' => $p1Currencies > 0 ? $p2Currencies / $p1Currencies : 0.0,
                    'no_refund_rate_p2' => $p2Tx > 0 ? max(0.0, min(1.0, 1.0 - ($refundP2 / $p2Tx))) : 0.0,
                ];

                $detail = [
                    'period_1' => ['start' => self::P1_START, 'end' => self::P1_END],
                    'period_2' => ['start' => self::P2_START, 'end' => self::P2_END],
                    'p1' => [
                        'transactions' => $p1Tx,
                        'active_days' => $p1Days,
                        'transactions_per_active_day' => $txPerDayP1,
                        'countries' => $p1Countries,
                        'bank_swift' => $p1Banks,
                        'currencies' => $p1Currencies,
                        'refunds' => $refundP1,
                    ],
                    'p2' => [
                        'transactions' => $p2Tx,
                        'active_days' => $p2Days,
                        'transactions_per_active_day' => $txPerDayP2,
                        'countries' => $p2Countries,
                        'bank_swift' => $p2Banks,
                        'currencies' => $p2Currencies,
                        'refunds' => $refundP2,
                    ],
                    'sources' => [
                        'C1-C5' => 'Laporan Sukses 2024',
                        'C6' => 'Laporan Sukses 2024 + Actual LIST MT199 (REFUND BNI/BRI)',
                    ],
                ];
                $detailJson = json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                foreach ($criterionByRule as $rule => $criterion) {
                    // Satu aturan presisi untuk Excel manual dan aplikasi:
                    // nilai Xij pada matriks keputusan dibulatkan menjadi 8 angka
                    // desimal, sesuai kolom spk_assessments.auto_value DECIMAL(24,8).
                    // Entropy dan TOPSIS kemudian dihitung dengan presisi penuh
                    // dari matriks ini; hasil akhir hanya diformat menjadi 12 desimal.
                    $matrixValue = round(max(0.0, (float) ($values[$rule] ?? 0.0)), 8);
                    $upsertAssessment->execute([
                        $alternativeIds[$key],
                        (int) $criterion['id'],
                        $matrixValue,
                        $detailJson,
                    ]);
                }
            }

            // Berlaku untuk seluruh data C1-C6 penelitian, termasuk assessment lama.
            $db->exec('UPDATE spk_assessments SET override_value=NULL WHERE override_value IS NOT NULL');
            $db->commit();
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    /** @return array<string,mixed> */
    public function calculation(): array
    {
        $this->ensureResearchCriteria();
        $ready = $this->sourceReadiness();
        if (!$ready['report_2024'] || !$ready['actual']) {
            $missing = [];
            if (!$ready['report_2024']) $missing[] = 'Laporan Sukses 2024';
            if (!$ready['actual']) $missing[] = 'Actual LIST MT199';
            throw new RuntimeException(
                'Sumber analisis belum lengkap. Import terlebih dahulu: ' . implode(' dan ', $missing) . '.'
            );
        }

        $this->rebuildAssessments();
        $db = App::db();
        $criteria = $db->query(
            "SELECT * FROM spk_criteria WHERE is_active=1 AND code IN ('C1','C2','C3','C4','C5','C6') ORDER BY sort_order,id"
        )->fetchAll(PDO::FETCH_ASSOC);
        $alternatives = $db->query(
            "SELECT * FROM spk_alternatives
             WHERE is_active=1 AND alternative_key IN ('FABLE','JURITA','GSB','KAIROS')
             ORDER BY FIELD(alternative_key,'FABLE','JURITA','GSB','KAIROS')"
        )->fetchAll(PDO::FETCH_ASSOC);

        if (count($criteria) !== 6) {
            throw new RuntimeException('Enam kriteria penelitian C1-C6 harus aktif.');
        }
        if (count($alternatives) !== 4) {
            throw new RuntimeException('Empat alternatif penelitian harus memiliki data transaksi pada P1 dan P2.');
        }

        $assessmentStatement = $db->prepare(
            'SELECT criterion_id,auto_value final_value,auto_value,override_value,source_detail_json
             FROM spk_assessments WHERE alternative_id=?'
        );

        $matrix = [];
        $assessmentDetails = [];
        foreach ($alternatives as $alternative) {
            $assessmentStatement->execute([(int) $alternative['id']]);
            $byCriterion = [];
            foreach ($assessmentStatement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $byCriterion[(int) $row['criterion_id']] = $row;
            }
            $values = [];
            $details = [];
            foreach ($criteria as $criterion) {
                $entry = $byCriterion[(int) $criterion['id']] ?? null;
                $values[] = max(0.0, (float) ($entry['final_value'] ?? 0));
                $details[] = $entry;
            }
            $matrix[] = $values;
            $assessmentDetails[] = $details;
        }

        $types = array_fill(0, 6, 'benefit');
        $calculation = (new EntropyTopsisEngine())->calculate($matrix, $types);

        return [
            'criteria' => $criteria,
            'alternatives' => $alternatives,
            'matrix' => $matrix,
            'assessment_details' => $assessmentDetails,
            'periods' => [
                'P1' => ['start' => self::P1_START, 'end' => self::P1_END],
                'P2' => ['start' => self::P2_START, 'end' => self::P2_END],
            ],
            'entropy' => $calculation['entropy'],
            'topsis' => $calculation['topsis'],
        ];
    }

    /** @param array<string,mixed> $calculation */
    public function saveRun(array $calculation, string $name, int $userId): int
    {
        $db = App::db();
        $db->beginTransaction();
        try {
            // Selalu hanya satu hasil penelitian tersimpan, mengganti hasil sebelumnya.
            // Penghapusan dalam transaksi yang sama dengan INSERT baru.
            $db->exec('DELETE FROM spk_results');
            $db->exec('DELETE FROM spk_weights');
            $db->exec('DELETE FROM spk_runs');
            $criteria = $calculation['criteria'];
            $alternatives = $calculation['alternatives'];
            $criteriaSnapshot = [
                'periods' => $calculation['periods'] ?? null,
                'sources' => ['Laporan Sukses 2024', 'Actual LIST MT199 - REFUND BNI/BRI'],
                'criteria' => $criteria,
                'entropy_method' => 'Benefit Kij = Xij / max(Xj); aij = Kij / Sum(Kij); Ej; Dj=1-Ej; Wj=Dj/Sum(Dj)',
                'topsis_method' => 'Rij vector normalization; Yij=Wj*Rij; A+/A-; D+/D-; Vi=D-/(D++D-)',
            ];
            $statement = $db->prepare(
                'INSERT INTO spk_runs
                 (name,criteria_snapshot_json,matrix_json,entropy_json,topsis_json,created_by,created_at)
                 VALUES (?,?,?,?,?,?,NOW())'
            );
            $statement->execute([
                $name,
                json_encode($criteriaSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($calculation['matrix'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($calculation['entropy'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($calculation['topsis'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $userId,
            ]);
            $runId = (int) $db->lastInsertId();

            $weightInsert = $db->prepare(
                'INSERT INTO spk_weights(run_id,criterion_code,entropy_value,dispersion_value,weight_value) VALUES (?,?,?,?,?)'
            );
            foreach ($criteria as $j => $criterion) {
                $weightInsert->execute([
                    $runId,
                    $criterion['code'],
                    (float) $calculation['entropy']['entropy'][$j],
                    (float) $calculation['entropy']['dispersion'][$j],
                    (float) $calculation['entropy']['weights'][$j],
                ]);
            }

            $resultInsert = $db->prepare(
                'INSERT INTO spk_results
                 (run_id,alternative_id,alternative_name,values_json,distance_positive,distance_negative,preference_value,rank_position)
                 VALUES (?,?,?,?,?,?,?,?)'
            );
            foreach ($alternatives as $i => $alternative) {
                $resultInsert->execute([
                    $runId,
                    (int) $alternative['id'],
                    $alternative['name'],
                    json_encode($calculation['matrix'][$i], JSON_UNESCAPED_UNICODE),
                    (float) $calculation['topsis']['distance_positive'][$i],
                    (float) $calculation['topsis']['distance_negative'][$i],
                    (float) $calculation['topsis']['preferences'][$i],
                    (int) $calculation['topsis']['ranks'][$i],
                ]);
            }

            $db->commit();
            return $runId;
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    /** @return array<string,mixed>|null */
    public function latestRun(): ?array
    {
        $run = App::db()->query(
            'SELECT r.*,u.name creator_name FROM spk_runs r LEFT JOIN users u ON u.id=r.created_by ORDER BY r.id DESC LIMIT 1'
        )->fetch(PDO::FETCH_ASSOC);
        return $run ?: null;
    }

    /** @return array<int,array<string,mixed>> */
    public function runResults(int $runId): array
    {
        $statement = App::db()->prepare('SELECT * FROM spk_results WHERE run_id=? ORDER BY rank_position');
        $statement->execute([$runId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<int,array<string,mixed>> */
    public function runWeights(int $runId): array
    {
        $statement = App::db()->prepare('SELECT * FROM spk_weights WHERE run_id=? ORDER BY id');
        $statement->execute([$runId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
