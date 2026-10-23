<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use App\Services\LegacyXlsWriter;
use App\Services\TransactionTemplateService;
use App\Services\UploadService;
use App\Services\XlsxWriter;
use PDO;

final class TransactionController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $this->permission('transaction.view');

        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'company_id' => (int) ($_GET['company_id'] ?? 0),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to' => trim((string) ($_GET['date_to'] ?? '')),
            'job_id' => (int) ($_GET['job_id'] ?? 0),
        ];

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $perPage = 25;

        $offset = ($page - 1) * $perPage;

        [$where, $params] = $this->where(
            $filters
        );


        $countStatement = App::db()->prepare(
            "
            SELECT COUNT(*)
            FROM transactions t
            WHERE
                t.deleted_at IS NULL
                {$where}
            "
        );

        $countStatement->execute(
            $params
        );

        $total = (int) $countStatement->fetchColumn();


        $statement = App::db()->prepare(
            "
            SELECT
                t.*,
                c.name AS company_name,
                u.name AS creator_name,
                j.name AS job_name
            FROM transactions t

            JOIN companies c
                ON c.id = t.payer_company_id

            LEFT JOIN users u
                ON u.id = t.created_by

            LEFT JOIN transaction_jobs j
                ON j.id = t.transaction_job_id

            WHERE
                t.deleted_at IS NULL
                {$where}

            ORDER BY
                t.transaction_date DESC,
                t.id DESC

            LIMIT {$perPage}
            OFFSET {$offset}
            "
        );

        $statement->execute(
            $params
        );

        $transactions = $statement->fetchAll(
            PDO::FETCH_ASSOC
        );


        $companies = App::db()
            ->query(
                "
                SELECT
                    id,
                    name
                FROM companies
                WHERE status = 'active'
                ORDER BY name
                "
            )
            ->fetchAll(
                PDO::FETCH_ASSOC
            );


        $jobsWhere = '';

        $jobParams = [];

        if (Auth::isTransactionAdmin()) {
            $jobsWhere = ' WHERE team_no = ?';

            $jobParams[] = Auth::transactionTeam();
        }


        $jobsStatement = App::db()->prepare(
            "
            SELECT
                id,
                name
            FROM transaction_jobs
            {$jobsWhere}
            ORDER BY id DESC
            LIMIT 100
            "
        );

        $jobsStatement->execute(
            $jobParams
        );

        $jobs = $jobsStatement->fetchAll(
            PDO::FETCH_ASSOC
        );


        $pages = max(
            1,
            (int) ceil(
                $total / $perPage
            )
        );


        $this->view(
            'transactions/history',
            [
                'title' => 'Riwayat Transaksi BRI',

                'transactions' => $transactions,

                'companies' => $companies,

                'jobs' => $jobs,

                'filters' => $filters,

                'page' => $page,

                'pages' => $pages,

                'total' => $total,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        $this->permission(
            'transaction.manage'
        );

        $companies = App::db()
            ->query(
                "
                SELECT
                    id,
                    name
                FROM companies
                WHERE status = 'active'
                ORDER BY name
                "
            )
            ->fetchAll(
                PDO::FETCH_ASSOC
            );

        $payerAccounts = $this->payerAccounts();


        $this->view(
            'transactions/form',
            [
                'title' => 'Tambah Transaksi',

                'transaction' => null,

                'companies' => $companies,

                'payerAccounts' => $payerAccounts,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        $this->permission(
            'transaction.manage'
        );

        $data = $this->validated();


        $columns = array_keys(
            $data
        );


        $placeholders = implode(
            ',',
            array_fill(
                0,
                count($columns),
                '?'
            )
        );


        $sql =
            'INSERT INTO transactions (' .
            implode(',', $columns) .
            ',created_at,updated_at) VALUES (' .
            $placeholders .
            ',NOW(),NOW())';


        $statement = App::db()->prepare(
            $sql
        );


        $statement->execute(
            array_values($data)
        );


        $id = (int) App::db()->lastInsertId();


        AuditService::log(
            'create',
            'transaction',
            $id,
            null,
            $data
        );


        flash(
            'success',
            'Transaksi berhasil disimpan.'
        );


        redirect(
            '/transactions/' . $id
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        string $id
    ): void {
        $this->permission(
            'transaction.view'
        );


        $transaction = $this->find(
            (int) $id
        );


        $this->enforceTeam(
            $transaction
        );


        $complaints = App::db()->prepare(
            "
            SELECT *
            FROM complaints
            WHERE transaction_id = ?
            ORDER BY created_at DESC
            "
        );

        $complaints->execute(
            [
                (int) $id,
            ]
        );


        $invoices = App::db()->prepare(
            "
            SELECT *
            FROM invoices
            WHERE transaction_id = ?
            ORDER BY created_at DESC
            "
        );

        $invoices->execute(
            [
                (int) $id,
            ]
        );


        $this->view(
            'transactions/show',
            [
                'title' => 'Detail Transaksi',

                'transaction' =>
                    $transaction,

                'complaints' =>
                    $complaints->fetchAll(
                        PDO::FETCH_ASSOC
                    ),

                'invoices' =>
                    $invoices->fetchAll(
                        PDO::FETCH_ASSOC
                    ),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        string $id
    ): void {
        $this->permission(
            'transaction.manage'
        );


        $transaction = $this->find(
            (int) $id
        );


        $this->enforceTeam(
            $transaction
        );


        $companies = App::db()
            ->query(
                "
                SELECT
                    id,
                    name
                FROM companies
                WHERE status = 'active'
                ORDER BY name
                "
            )
            ->fetchAll(
                PDO::FETCH_ASSOC
            );


        $payerAccounts = $this->payerAccounts();


        $this->view(
            'transactions/form',
            [
                'title' => 'Ubah Transaksi',

                'transaction' =>
                    $transaction,

                'companies' =>
                    $companies,

                'payerAccounts' =>
                    $payerAccounts,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        string $id
    ): void {
        $this->permission(
            'transaction.manage'
        );


        $before = $this->find(
            (int) $id
        );


        $this->enforceTeam(
            $before
        );


        $data = $this->validated();


        unset(
            $data['created_by']
        );


        $sets = implode(
            ',',
            array_map(
                static fn (
                    string $column
                ): string =>
                    $column . ' = ?',
                array_keys($data)
            )
        );


        $statement = App::db()->prepare(
            "
            UPDATE transactions
            SET
                {$sets},
                updated_at = NOW()
            WHERE id = ?
            "
        );


        $statement->execute(
            array_merge(
                array_values($data),
                [
                    (int) $id,
                ]
            )
        );


        AuditService::log(
            'update',
            'transaction',
            (int) $id,
            $before,
            $data
        );


        flash(
            'success',
            'Transaksi berhasil diperbarui.'
        );


        redirect(
            '/transactions/' . $id
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        string $id
    ): void {
        $this->permission(
            'transaction.manage'
        );


        $before = $this->find(
            (int) $id
        );


        $this->enforceTeam(
            $before
        );


        $statement = App::db()->prepare(
            "
            UPDATE transactions
            SET
                deleted_at = NOW(),
                updated_at = NOW()
            WHERE id = ?
            "
        );


        $statement->execute(
            [
                (int) $id,
            ]
        );


        AuditService::log(
            'delete',
            'transaction',
            (int) $id,
            $before,
            null
        );


        flash(
            'success',
            'Transaksi berhasil dihapus.'
        );


        redirect(
            '/transactions'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | IMPORT FORM
    |--------------------------------------------------------------------------
    */

    public function importForm(): void
    {
        $this->permission(
            'transaction.manage'
        );


        $where = '';

        $params = [];


        if (
            Auth::isTransactionAdmin()
        ) {
            $where =
                ' WHERE j.team_no = ?';

            $params[] =
                Auth::transactionTeam();
        }


        $statement = App::db()->prepare(
            "
            SELECT
                j.*,
                u.name AS creator_name,

                (
                    SELECT COUNT(*)
                    FROM transactions t
                    WHERE
                        t.transaction_job_id = j.id
                        AND
                        t.deleted_at IS NULL
                ) AS imported_rows

            FROM transaction_jobs j

            LEFT JOIN users u
                ON u.id = j.created_by

            {$where}

            ORDER BY
                j.id DESC

            LIMIT 30
            "
        );


        $statement->execute(
            $params
        );


        $jobs = $statement->fetchAll(
            PDO::FETCH_ASSOC
        );


        /*
        |--------------------------------------------------------------------------
        | MASTER PAYER
        |--------------------------------------------------------------------------
        */

        $payerAccounts =
            $this->payerAccounts();


        $this->view(
            'transactions/import',
            [
                'title' =>
                    'Import Template Bank BRI',

                'jobs' =>
                    $jobs,

                'payerAccounts' =>
                    $payerAccounts,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | IMPORT
    |--------------------------------------------------------------------------
    */

    public function import(): void
    {
        $this->permission(
            'transaction.manage'
        );


        $sourceMode = strtolower(
            trim(
                (string) (
                    $_POST['source_mode']
                    ?? 'manual'
                )
            )
        );


        if (
            !in_array(
                $sourceMode,
                [
                    'file',
                    'manual',
                ],
                true
            )
        ) {
            $sourceMode = 'manual';
        }


        $jobName = trim(
            (string) (
                $_POST['job_name']
                ?? ''
            )
        );


        $outputEmail = trim(
            (string) (
                $_POST['output_email']
                ?? 'remit.service09@gmail.com'
            )
        );


        $autoCreateCompany =
            !empty(
                $_POST[
                    'auto_create_company'
                ]
            );


        if ($jobName === '') {
            $jobName =
                'TRANSAKSI ' .
                date('d-m-Y H.i');
        }


        if (
            !filter_var(
                $outputEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            flash(
                'danger',
                'Alamat email output tidak valid.'
            );


            redirect(
                '/transactions/import'
            );

            return;
        }


        $storedPath = null;

        $sourceFilename = '';

        $sourcePath = '';


        $service =
            new TransactionTemplateService();


        $db =
            App::db();


        try {

            /*
            |--------------------------------------------------------------------------
            | MANUAL
            |--------------------------------------------------------------------------
            */

            if (
                $sourceMode ===
                'manual'
            ) {
                $manualJson = trim(
                    (string) (
                        $_POST[
                            'manual_rows_json'
                        ]
                        ?? ''
                    )
                );


                $manualRows = json_decode(
                    $manualJson,
                    true
                );


                if (
                    !is_array(
                        $manualRows
                    )
                    ||
                    $manualRows === []
                ) {
                    throw new \RuntimeException(
                        'Spreadsheet transaksi masih kosong.'
                    );
                }


                $parsed =
                    $service->parseManualRows(
                        $manualRows
                    );


                $sourceFilename =
                    'Manual Copy Paste Excel';


                $sourcePath =
                    'manual://excel-grid';

            } else {

                /*
                |--------------------------------------------------------------------------
                | FILE
                |--------------------------------------------------------------------------
                */

                if (
                    empty(
                        $_FILES[
                            'transaction_file'
                        ]['tmp_name']
                    )
                ) {
                    throw new \RuntimeException(
                        'Pilih file transaksi terlebih dahulu.'
                    );
                }


                $extension = strtolower(
                    pathinfo(
                        (string) (
                            $_FILES[
                                'transaction_file'
                            ]['name']
                            ?? ''
                        ),
                        PATHINFO_EXTENSION
                    )
                );


                if (
                    !in_array(
                        $extension,
                        [
                            'xlsx',
                            'xls',
                            'csv',
                        ],
                        true
                    )
                ) {
                    throw new \RuntimeException(
                        'Format harus XLSX, XLS XML, atau CSV.'
                    );
                }


                $storedPath =
                    UploadService::store(
                        $_FILES[
                            'transaction_file'
                        ],
                        'transaction-jobs/' .
                        date('Y/m')
                    );


                if (
                    $storedPath === null
                ) {
                    throw new \RuntimeException(
                        'File gagal disimpan.'
                    );
                }


                $sourceFilename =
                    basename(
                        (string) (
                            $_FILES[
                                'transaction_file'
                            ]['name']
                            ?? 'transaction.xlsx'
                        )
                    );


                $sourcePath =
                    $storedPath;


                $parsed =
                    $service->parse(
                        BASE_PATH .
                        '/public/' .
                        $storedPath
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | PARSED DATA
            |--------------------------------------------------------------------------
            */

            $rows =
                is_array(
                    $parsed['rows']
                    ?? null
                )
                    ? $parsed['rows']
                    : [];


            $validationIssues =
                is_array(
                    $parsed['issues']
                    ?? null
                )
                    ? $parsed['issues']
                    : [];


            $sourceSkipped =
                (int) (
                    $parsed['skipped']
                    ?? 0
                );


            $sourceTotal =
                (int) (
                    $parsed[
                        'total_source_rows'
                    ]
                    ?? count($rows)
                );


            $teamNo =
                Auth::transactionTeam()
                ?:
                max(
                    1,
                    min(
                        4,
                        (int) (
                            $_POST[
                                'team_no'
                            ]
                            ?? 1
                        )
                    )
                );


            if ($rows === []) {
                throw new \RuntimeException(
                    'Tidak ada transaksi valid untuk diproses.'
                );
            }


            $db->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | CREATE JOB
            |--------------------------------------------------------------------------
            */

            $jobInsert =
                $db->prepare(
                    "
                    INSERT INTO transaction_jobs
                    (
                        name,
                        team_no,
                        source_filename,
                        source_path,
                        source_sheet,
                        output_email,
                        status,
                        total_rows,
                        imported_rows,
                        skipped_rows,
                        validation_json,
                        created_by,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        'READY',
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        NOW(),
                        NOW()
                    )
                    "
                );


            $jobInsert->execute(
                [
                    $jobName,

                    $teamNo,

                    $sourceFilename,

                    $sourcePath,

                    (string) (
                        $parsed['sheet']
                        ?? 'Data'
                    ),

                    $outputEmail,

                    $sourceTotal,

                    0,

                    $sourceSkipped,

                    json_encode(
                        [
                            'issues' =>
                                $validationIssues,
                        ],
                        JSON_UNESCAPED_UNICODE
                        |
                        JSON_UNESCAPED_SLASHES
                    ),

                    Auth::id(),
                ]
            );


            $jobId =
                (int) $db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | INSERT TRANSACTION
            |--------------------------------------------------------------------------
            */

            $insertColumns = [
                'transaction_job_id',
                'sequence_no',
                'team_no',
                'invoice_no',
                'reference_no',
                'transaction_date',
                'payer_company_id',
                'payer_account',
                'payer_address',
                'payer_city',
                'payer_country',
                'payer_email',
                'beneficiary_name',
                'beneficiary_account',
                'beneficiary_bank',
                'beneficiary_bank_location',
                'amount',
                'beneficiary_country',
                'beneficiary_city',
                'beneficiary_address',
                'swift',
                'payment_details',
                'currency',
                'correspondent_bank',
                'correspondent_swift',
                'correspondent_location',
                'payer_bank',
                'invoice_link',
                'status',
                'refund_status',
                'notes',
                'source_hash',
                'created_by',
            ];


            $insertPlaceholder =
                implode(
                    ',',
                    array_fill(
                        0,
                        count(
                            $insertColumns
                        ),
                        '?'
                    )
                );


            $insert = $db->prepare(
                '
                INSERT INTO transactions
                (
                    ' .
                    implode(
                        ',',
                        $insertColumns
                    ) .
                    ',
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ' .
                    $insertPlaceholder .
                    ',
                    NOW(),
                    NOW()
                )
                '
            );


            $duplicateCheck =
                $db->prepare(
                    "
                    SELECT id
                    FROM transactions
                    WHERE source_hash = ?
                    LIMIT 1
                    "
                );


            $imported = 0;

            $skipped =
                $sourceSkipped;


            foreach (
                $rows
                as $row
            ) {
                if (
                    !is_array(
                        $row
                    )
                ) {
                    $skipped++;

                    continue;
                }


                $payer = trim(
                    (string) (
                        $row['payer']
                        ?? ''
                    )
                );


                if ($payer === '') {
                    $skipped++;

                    continue;
                }


                $companyId =
                    $this->companyId(
                        $payer,
                        $autoCreateCompany,
                        $row
                    );


                if (
                    $companyId === null
                ) {
                    $skipped++;

                    continue;
                }


                $rowTeam =
                    Auth::isTransactionAdmin()
                        ? $teamNo
                        : max(
                            1,
                            min(
                                4,
                                (int) (
                                    (
                                        $row['team_no']
                                        ?? 0
                                    )
                                    ?: $teamNo
                                )
                            )
                        );


                $hash = hash(
                    'sha256',
                    implode(
                        '|',
                        [
                            (string) (
                                $row[
                                    'transaction_date'
                                ]
                                ?? ''
                            ),

                            (string) $companyId,

                            (string) (
                                $row['amount']
                                ?? 0
                            ),

                            (string) (
                                $row[
                                    'beneficiary_name'
                                ]
                                ?? ''
                            ),

                            (string) (
                                $row[
                                    'beneficiary_account'
                                ]
                                ?? ''
                            ),

                            (string) (
                                $row['swift']
                                ?? ''
                            ),

                            (string) (
                                $row[
                                    'payment_details'
                                ]
                                ?? ''
                            ),

                            (string) (
                                $row['currency']
                                ?? ''
                            ),
                        ]
                    )
                );


                $duplicateCheck->execute(
                    [
                        $hash,
                    ]
                );


                if (
                    $duplicateCheck->fetchColumn()
                ) {
                    $skipped++;

                    continue;
                }


                $insert->execute(
                    [
                        $jobId,

                        $this->nullable(
                            $row[
                                'sequence_no'
                            ]
                            ?? null
                        ),

                        $rowTeam,

                        $this->nullable(
                            $row[
                                'invoice_no'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'reference_no'
                            ]
                            ?? null
                        ),

                        (string) (
                            $row[
                                'transaction_date'
                            ]
                            ?? ''
                        ),

                        $companyId,

                        $this->nullable(
                            $row[
                                'payer_account'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'payer_address'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'payer_city'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'payer_country'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'payer_email'
                            ]
                            ?? null
                        )
                        ?? $outputEmail,

                        $this->nullable(
                            $row[
                                'beneficiary_name'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'beneficiary_account'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'beneficiary_bank'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'beneficiary_bank_location'
                            ]
                            ?? null
                        ),

                        (float) (
                            $row['amount']
                            ?? 0
                        ),

                        $this->nullable(
                            $row[
                                'beneficiary_country'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'beneficiary_city'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'beneficiary_address'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row['swift']
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'payment_details'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row['currency']
                            ?? null
                        )
                        ?? 'USD',

                        $this->nullable(
                            $row[
                                'correspondent_bank'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'correspondent_swift'
                            ]
                            ?? null
                        ),

                        $this->nullable(
                            $row[
                                'correspondent_location'
                            ]
                            ?? null
                        ),

                        null,

                        null,

                        'PENDING',

                        'none',

                        null,

                        $hash,

                        Auth::id(),
                    ]
                );


                $imported++;
            }


            $validationPayload = [
                'source_mode' =>
                    $sourceMode,

                'issues' =>
                    $validationIssues,

                'source_rows' =>
                    $sourceTotal,

                'valid_rows' =>
                    count($rows),

                'imported_rows' =>
                    $imported,

                'skipped_rows' =>
                    $skipped,
            ];


            $db->prepare(
                "
                UPDATE transaction_jobs
                SET
                    imported_rows = ?,
                    skipped_rows = ?,
                    validation_json = ?,
                    status = ?,
                    updated_at = NOW()
                WHERE id = ?
                "
            )->execute(
                [
                    $imported,

                    $skipped,

                    json_encode(
                        $validationPayload,
                        JSON_UNESCAPED_UNICODE
                        |
                        JSON_UNESCAPED_SLASHES
                    ),

                    $imported > 0
                        ? 'READY'
                        : 'EMPTY',

                    $jobId,
                ]
            );


            $db->commit();


            AuditService::log(
                'import',
                'transaction_job',
                $jobId,
                null,
                [
                    'source_mode' =>
                        $sourceMode,

                    'imported' =>
                        $imported,

                    'skipped' =>
                        $skipped,
                ]
            );


            flash(
                $imported > 0
                    ? 'success'
                    : 'warning',

                $imported > 0
                    ? "Tahap 1 selesai. {$imported} transaksi berhasil disimpan."
                    : 'Tidak ada transaksi baru yang dapat disimpan.'
            );


            redirect(
                '/transactions/jobs/' .
                $jobId
            );

            return;

        } catch (\Throwable $exception) {

            if (
                $db->inTransaction()
            ) {
                $db->rollBack();
            }


            if (
                $storedPath !== null
                &&
                is_file(
                    BASE_PATH .
                    '/public/' .
                    $storedPath
                )
            ) {
                @unlink(
                    BASE_PATH .
                    '/public/' .
                    $storedPath
                );
            }


            flash(
                'danger',
                'Proses transaksi gagal: ' .
                $exception->getMessage()
            );


            redirect(
                '/transactions/import'
            );

            return;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | JOB
    |--------------------------------------------------------------------------
    */

    public function job(
        string $id
    ): void {
        $this->permission(
            'transaction.view'
        );


        $job = $this->findJob(
            (int) $id
        );


        $this->enforceJobTeam(
            $job
        );


        $transactions =
            $this->jobTransactions(
                (int) $id
            );


        $service =
            new TransactionTemplateService();


        $stage2Rows =
            $service->outputRows(
                $transactions,
                (string) (
                    $job['output_email']
                    ?? ''
                )
            );


        $stage3Rows =
            $service->cleanOutputRows(
                $stage2Rows
            );


        $this->view(
            'transactions/job',
            [
                'title' =>
                    (string) (
                        $job['name']
                        ?? 'Batch Transaksi'
                    ),

                'job' =>
                    $job,

                'transactions' =>
                    $transactions,

                'stage2Rows' =>
                    $stage2Rows,

                'stage3Rows' =>
                    $stage3Rows,

                'outputHeaders' =>
                    $service->outputHeaders(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT SINGLE
    |--------------------------------------------------------------------------
    */

    public function exportSingleBri(
        string $id
    ): void {
        $this->permission(
            'transaction.view'
        );


        $transaction = $this->find(
            (int) $id
        );


        $this->enforceTeam(
            $transaction
        );


        $email = trim(
            (string) (
                $transaction[
                    'payer_email'
                ]
                ?? ''
            )
        );


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $email =
                'remit.service09@gmail.com';
        }


        $service =
            new TransactionTemplateService();


        $reference = trim(
            (string) (
                $transaction[
                    'reference_no'
                ]
                ?? ''
            )
        );


        if ($reference === '') {
            $reference =
                'TRX-' .
                (int) $id;
        }


        $stage2 =
            $service->outputRows(
                [
                    $transaction,
                ],
                $email
            );


        $finalRows =
            $service->cleanOutputRows(
                $stage2
            );


        XlsxWriter::download(
            [
                'FINAL' => [
                    'headers' =>
                        $service->outputHeaders(),

                    'rows' =>
                        $finalRows,

                    'widths' =>
                        $service->outputWidths(),
                ],
            ],

            $reference .
            '-FINAL-TANPA-TANDA-BACA.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT JOB XLSX
    |--------------------------------------------------------------------------
    */

    public function exportJobXlsx(
        string $id
    ): void {
        $this->permission(
            'transaction.view'
        );


        $job = $this->findJob(
            (int) $id
        );


        $this->enforceJobTeam(
            $job
        );


        $transactions =
            $this->jobTransactions(
                (int) $id
            );


        $service =
            new TransactionTemplateService();


        $stage2Rows =
            $service->outputRows(
                $transactions,
                (string) (
                    $job['output_email']
                    ?? ''
                )
            );


        $stage =
            trim(
                (string) (
                    $_GET['stage']
                    ?? '3'
                )
            );


        $isStage2 =
            $stage === '2';


        $rows =
            $isStage2
                ? $stage2Rows
                : $service->cleanOutputRows(
                    $stage2Rows
                );


        $sheet =
            $isStage2
                ? 'TAHAP 2'
                : 'FINAL';


        $suffix =
            $isStage2
                ? '-TAHAP-2.xlsx'
                : '-FINAL-TANPA-TANDA-BACA.xlsx';


        XlsxWriter::download(
            [
                $sheet => [
                    'headers' =>
                        $service->outputHeaders(),

                    'rows' =>
                        $rows,

                    'widths' =>
                        $service->outputWidths(),
                ],
            ],

            (string) (
                $job['name']
                ?? 'TRANSAKSI'
            )
            .
            $suffix
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT JOB XLS
    |--------------------------------------------------------------------------
    */

    public function exportJobXls(
        string $id
    ): void {
        $this->permission(
            'transaction.view'
        );


        $job = $this->findJob(
            (int) $id
        );


        $this->enforceJobTeam(
            $job
        );


        $transactions =
            $this->jobTransactions(
                (int) $id
            );


        $service =
            new TransactionTemplateService();


        $stage2Rows =
            $service->outputRows(
                $transactions,
                (string) (
                    $job['output_email']
                    ?? ''
                )
            );


        $stage =
            trim(
                (string) (
                    $_GET['stage']
                    ?? '3'
                )
            );


        $isStage2 =
            $stage === '2';


        $rows =
            $isStage2
                ? $stage2Rows
                : $service->cleanOutputRows(
                    $stage2Rows
                );


        LegacyXlsWriter::download(
            $service->outputHeaders(),

            $rows,

            (string) (
                $job['name']
                ?? 'TRANSAKSI'
            )
            .
            (
                $isStage2
                    ? '-TAHAP-2.xls'
                    : '-FINAL-TANPA-TANDA-BACA.xls'
            ),

            $isStage2
                ? 'TAHAP 2'
                : 'FINAL'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string,mixed>
     */
    private function validated(): array
    {
        foreach (
            [
                'transaction_date',
                'payer_company_id',
                'amount',
                'currency',
            ]
            as $field
        ) {
            if (
                trim(
                    (string) (
                        $_POST[$field]
                        ?? ''
                    )
                ) === ''
            ) {
                remember_old(
                    $_POST
                );


                flash(
                    'danger',
                    'Tanggal, perusahaan, nominal, dan mata uang wajib diisi.'
                );


                redirect(
                    $_SERVER[
                        'HTTP_REFERER'
                    ]
                    ?? '/transactions'
                );


                throw new \RuntimeException(
                    'Data wajib belum lengkap.'
                );
            }
        }


        $team =
            Auth::isTransactionAdmin()
                ? Auth::transactionTeam()
                : max(
                    1,
                    min(
                        4,
                        (int) (
                            $_POST['team_no']
                            ?? 1
                        )
                    )
                );


        $companyId =
            (int) (
                $_POST[
                    'payer_company_id'
                ]
                ?? 0
            );


        $payerAccountId =
            (int) (
                $_POST[
                    'payer_account_id'
                ]
                ?? 0
            );


        $selectedAccount =
            $payerAccountId > 0
                ? $this->payerAccount(
                    $payerAccountId,
                    $companyId
                )
                : null;


        $payerAccountNo =
            trim(
                (string) (
                    $_POST[
                        'payer_account'
                    ]
                    ?? ''
                )
            );


        $payerAddress =
            trim(
                (string) (
                    $_POST[
                        'payer_address'
                    ]
                    ?? ''
                )
            );


        $payerCity =
            trim(
                (string) (
                    $_POST[
                        'payer_city'
                    ]
                    ?? ''
                )
            );


        $payerCountry =
            trim(
                (string) (
                    $_POST[
                        'payer_country'
                    ]
                    ?? ''
                )
            );


        $payerEmail =
            trim(
                (string) (
                    $_POST[
                        'payer_email'
                    ]
                    ?? ''
                )
            );


        $payerBank =
            trim(
                (string) (
                    $_POST[
                        'payer_bank'
                    ]
                    ?? ''
                )
            );


        if (
            $selectedAccount !== null
        ) {
            $payerAccountNo =
                (string) (
                    $selectedAccount[
                        'account_no'
                    ]
                    ?? $payerAccountNo
                );


            $payerAddress =
                (string) (
                    $selectedAccount[
                        'address'
                    ]
                    ?? $payerAddress
                );


            $payerCity =
                (string) (
                    $selectedAccount[
                        'city'
                    ]
                    ?? $payerCity
                );


            $payerCountry =
                (string) (
                    $selectedAccount[
                        'country'
                    ]
                    ?? $payerCountry
                );


            $payerEmail =
                (string) (
                    $selectedAccount[
                        'email'
                    ]
                    ?? $payerEmail
                );


            $payerBank =
                (string) (
                    $selectedAccount[
                        'bank_name'
                    ]
                    ?? $payerBank
                );
        }


        clear_old();


        return [
            'sequence_no' =>
                $this->nullable(
                    $_POST[
                        'sequence_no'
                    ]
                    ?? null
                ),

            'team_no' =>
                $team,

            'invoice_no' =>
                $this->nullable(
                    $_POST[
                        'invoice_no'
                    ]
                    ?? null
                ),

            'reference_no' =>
                $this->nullable(
                    $_POST[
                        'reference_no'
                    ]
                    ?? null
                ),

            'transaction_date' =>
                (string) (
                    $_POST[
                        'transaction_date'
                    ]
                    ?? ''
                ),

            'payer_company_id' =>
                $companyId,

            'payer_account_id' =>
                $payerAccountId > 0
                    ? $payerAccountId
                    : null,

            'payer_account' =>
                $this->nullable(
                    $payerAccountNo
                ),

            'payer_address' =>
                $this->nullable(
                    $payerAddress
                ),

            'payer_city' =>
                $this->nullable(
                    $payerCity
                ),

            'payer_country' =>
                $this->nullable(
                    $payerCountry
                ),

            'payer_email' =>
                $this->nullable(
                    $payerEmail
                ),

            'beneficiary_name' =>
                $this->nullable(
                    $_POST[
                        'beneficiary_name'
                    ]
                    ?? null
                ),

            'beneficiary_account' =>
                $this->nullable(
                    $_POST[
                        'beneficiary_account'
                    ]
                    ?? null
                ),

            'beneficiary_bank' =>
                $this->nullable(
                    $_POST[
                        'beneficiary_bank'
                    ]
                    ?? null
                ),

            'beneficiary_bank_location' =>
                $this->nullable(
                    $_POST[
                        'beneficiary_bank_location'
                    ]
                    ?? null
                ),

            'amount' =>
                $this->parseAmount(
                    $_POST['amount']
                    ?? 0
                ),

            'beneficiary_country' =>
                $this->nullable(
                    $_POST[
                        'beneficiary_country'
                    ]
                    ?? null
                ),

            'beneficiary_city' =>
                $this->nullable(
                    $_POST[
                        'beneficiary_city'
                    ]
                    ?? null
                ),

            'beneficiary_address' =>
                $this->nullable(
                    $_POST[
                        'beneficiary_address'
                    ]
                    ?? null
                ),

            'swift' =>
                $this->nullable(
                    strtoupper(
                        trim(
                            (string) (
                                $_POST['swift']
                                ?? ''
                            )
                        )
                    )
                ),

            'payment_details' =>
                $this->nullable(
                    $_POST[
                        'payment_details'
                    ]
                    ?? null
                ),

            'currency' =>
                strtoupper(
                    trim(
                        (string) (
                            $_POST['currency']
                            ?? ''
                        )
                    )
                ),

            'correspondent_bank' =>
                $this->nullable(
                    $_POST[
                        'correspondent_bank'
                    ]
                    ?? null
                ),

            'correspondent_swift' =>
                $this->nullable(
                    strtoupper(
                        trim(
                            (string) (
                                $_POST[
                                    'correspondent_swift'
                                ]
                                ?? ''
                            )
                        )
                    )
                ),

            'correspondent_location' =>
                $this->nullable(
                    $_POST[
                        'correspondent_location'
                    ]
                    ?? null
                ),

            'payer_bank' =>
                $this->nullable(
                    $payerBank
                ),

            'invoice_link' =>
                $this->nullable(
                    $_POST[
                        'invoice_link'
                    ]
                    ?? null
                ),

            'status' =>
                $this->normalizeStatus(
                    (string) (
                        $_POST['status']
                        ?? 'PENDING'
                    )
                ),

            'refund_status' =>
                $this->normalizeRefund(
                    (string) (
                        $_POST[
                            'refund_status'
                        ]
                        ?? 'none'
                    )
                ),

            'notes' =>
                $this->nullable(
                    $_POST['notes']
                    ?? null
                ),

            'created_by' =>
                Auth::id(),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER WHERE
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string,mixed> $filters
     * @return array{0:string,1:array<int,mixed>}
     */
    private function where(
        array $filters
    ): array {
        $where = '';

        $params = [];


        if (
            Auth::isTransactionAdmin()
        ) {
            $where .=
                ' AND t.team_no = ?';

            $params[] =
                Auth::transactionTeam();
        }


        if (
            (
                $filters['q']
                ?? ''
            ) !== ''
        ) {
            $where .=
                '
                AND
                (
                    t.reference_no LIKE ?
                    OR t.invoice_no LIKE ?
                    OR t.beneficiary_name LIKE ?
                    OR t.swift LIKE ?
                    OR t.payer_account LIKE ?
                )
                ';


            $search =
                '%' .
                $filters['q'] .
                '%';


            array_push(
                $params,
                $search,
                $search,
                $search,
                $search,
                $search
            );
        }


        if (
            (int) (
                $filters[
                    'company_id'
                ]
                ?? 0
            ) > 0
        ) {
            $where .=
                ' AND t.payer_company_id = ?';

            $params[] =
                (int) $filters[
                    'company_id'
                ];
        }


        if (
            (
                $filters['status']
                ?? ''
            ) !== ''
        ) {
            $where .=
                ' AND t.status = ?';

            $params[] =
                $filters['status'];
        }


        if (
            (
                $filters['date_from']
                ?? ''
            ) !== ''
        ) {
            $where .=
                ' AND t.transaction_date >= ?';

            $params[] =
                $filters[
                    'date_from'
                ];
        }


        if (
            (
                $filters['date_to']
                ?? ''
            ) !== ''
        ) {
            $where .=
                ' AND t.transaction_date <= ?';

            $params[] =
                $filters[
                    'date_to'
                ];
        }


        if (
            (int) (
                $filters['job_id']
                ?? 0
            ) > 0
        ) {
            $where .=
                ' AND t.transaction_job_id = ?';

            $params[] =
                (int) $filters[
                    'job_id'
                ];
        }


        return [
            $where,
            $params,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | FIND TRANSACTION
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string,mixed>
     */
    private function find(
        int $id
    ): array {
        $statement = App::db()->prepare(
            "
            SELECT
                t.*,
                c.name AS company_name,
                u.name AS creator_name,
                j.name AS job_name
            FROM transactions t

            JOIN companies c
                ON c.id = t.payer_company_id

            LEFT JOIN users u
                ON u.id = t.created_by

            LEFT JOIN transaction_jobs j
                ON j.id = t.transaction_job_id

            WHERE
                t.id = ?
                AND
                t.deleted_at IS NULL

            LIMIT 1
            "
        );


        $statement->execute(
            [
                $id,
            ]
        );


        $record =
            $statement->fetch(
                PDO::FETCH_ASSOC
            );


        if (
            !is_array(
                $record
            )
        ) {
            throw new \RuntimeException(
                'Transaksi tidak ditemukan.'
            );
        }


        return $record;
    }


    /*
    |--------------------------------------------------------------------------
    | FIND JOB
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string,mixed>
     */
    private function findJob(
        int $id
    ): array {
        $statement =
            App::db()->prepare(
                "
                SELECT
                    j.*,
                    u.name AS creator_name
                FROM transaction_jobs j

                LEFT JOIN users u
                    ON u.id = j.created_by

                WHERE j.id = ?

                LIMIT 1
                "
            );


        $statement->execute(
            [
                $id,
            ]
        );


        $job =
            $statement->fetch(
                PDO::FETCH_ASSOC
            );


        if (
            !is_array(
                $job
            )
        ) {
            throw new \RuntimeException(
                'Batch transaksi tidak ditemukan.'
            );
        }


        return $job;
    }


    /*
    |--------------------------------------------------------------------------
    | JOB TRANSACTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<int,array<string,mixed>>
     */
    private function jobTransactions(
        int $jobId
    ): array {
        $statement = App::db()->prepare(
            "
            SELECT
                t.*,
                c.name AS company_name
            FROM transactions t

            JOIN companies c
                ON c.id = t.payer_company_id

            WHERE
                t.transaction_job_id = ?
                AND
                t.deleted_at IS NULL

            ORDER BY
                CAST(
                    t.sequence_no AS UNSIGNED
                ),
                t.sequence_no,
                t.id
            "
        );


        $statement->execute(
            [
                $jobId,
            ]
        );


        return $statement->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TEAM ACCESS
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string,mixed> $transaction
     */
    private function enforceTeam(
        array $transaction
    ): void {
        if (
            Auth::isTransactionAdmin()
            &&
            (int) (
                $transaction[
                    'team_no'
                ]
                ?? 0
            )
            !==
            Auth::transactionTeam()
        ) {
            http_response_code(
                403
            );


            throw new \RuntimeException(
                'Transaksi berada di tim lain.'
            );
        }
    }


    /**
     * @param array<string,mixed> $job
     */
    private function enforceJobTeam(
        array $job
    ): void {
        if (
            Auth::isTransactionAdmin()
            &&
            (int) (
                $job[
                    'team_no'
                ]
                ?? 0
            )
            !==
            Auth::transactionTeam()
        ) {
            http_response_code(
                403
            );


            throw new \RuntimeException(
                'Batch transaksi berada di tim lain.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MASTER PAYER
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<int,array<string,mixed>>
     */
    private function payerAccounts(): array
    {
        try {
            return App::db()
                ->query(
                    "
                    SELECT
                        pa.*,
                        c.name AS company_name
                    FROM payer_accounts pa

                    JOIN companies c
                        ON c.id = pa.company_id

                    WHERE
                        pa.status = 'active'

                    ORDER BY
                        pa.is_default DESC,
                        c.name,
                        pa.account_no
                    "
                )
                ->fetchAll(
                    PDO::FETCH_ASSOC
                );

        } catch (\Throwable) {
            return [];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ONE PAYER ACCOUNT
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string,mixed>|null
     */
    private function payerAccount(
        int $id,
        int $companyId
    ): ?array {
        try {
            $statement = App::db()->prepare(
                "
                SELECT *
                FROM payer_accounts
                WHERE
                    id = ?
                    AND
                    company_id = ?
                    AND
                    status = 'active'
                LIMIT 1
                "
            );


            $statement->execute(
                [
                    $id,
                    $companyId,
                ]
            );


            $record =
                $statement->fetch(
                    PDO::FETCH_ASSOC
                );

        } catch (\Throwable) {
            return null;
        }


        return is_array($record)
            ? $record
            : null;
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    private function normalizeStatus(
        string $status
    ): string {
        $status =
            strtoupper(
                trim($status)
            );


        return match (true) {

            in_array(
                $status,
                [
                    'SUCCESS',
                    'SUKSES',
                    'TERKIRIM',
                ],
                true
            )
                => 'SUCCESS',

            in_array(
                $status,
                [
                    'FAILED',
                    'FAIL',
                    'GAGAL',
                    'REJECT',
                    'REJECTED',
                ],
                true
            )
                => 'FAILED',

            in_array(
                $status,
                [
                    'CANCEL',
                    'CANCELLED',
                ],
                true
            )
                => 'CANCELLED',

            default =>
                'PENDING',
        };
    }


    private function normalizeRefund(
        string $status
    ): string {
        $status =
            strtolower(
                trim($status)
            );


        return match (true) {

            str_contains(
                $status,
                'success'
            ),

            str_contains(
                $status,
                'sukses'
            )
                => 'success',

            str_contains(
                $status,
                'process'
            )
                => 'processed',

            str_contains(
                $status,
                'request'
            )
                => 'requested',

            str_contains(
                $status,
                'fail'
            )
                => 'failed',

            default =>
                'none',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | AMOUNT
    |--------------------------------------------------------------------------
    */

    private function parseAmount(
        mixed $value
    ): float {
        if (
            is_numeric($value)
        ) {
            return (float) $value;
        }


        $text =
            preg_replace(
                '/[^0-9,.-]/',
                '',
                (string) $value
            )
            ?? '';


        if ($text === '') {
            return 0.0;
        }


        if (
            str_contains(
                $text,
                ','
            )
            &&
            str_contains(
                $text,
                '.'
            )
        ) {
            if (
                strrpos(
                    $text,
                    ','
                )
                >
                strrpos(
                    $text,
                    '.'
                )
            ) {
                $text =
                    str_replace(
                        '.',
                        '',
                        $text
                    );


                $text =
                    str_replace(
                        ',',
                        '.',
                        $text
                    );

            } else {
                $text =
                    str_replace(
                        ',',
                        '',
                        $text
                    );
            }

        } elseif (
            str_contains(
                $text,
                ','
            )
        ) {
            $text =
                str_replace(
                    ',',
                    '.',
                    $text
                );
        }


        return is_numeric($text)
            ? (float) $text
            : 0.0;
    }


    /*
    |--------------------------------------------------------------------------
    | COMPANY
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string,mixed> $source
     */
    private function companyId(
        string $name,
        bool $autoCreate,
        array $source
    ): ?int {
        $name =
            trim($name);


        if ($name === '') {
            return null;
        }


        $statement = App::db()->prepare(
            "
            SELECT id
            FROM companies
            WHERE
                UPPER(
                    REPLACE(
                        name,
                        '.',
                        ''
                    )
                )
                =
                UPPER(
                    REPLACE(
                        ?,
                        '.',
                        ''
                    )
                )
            LIMIT 1
            "
        );


        $statement->execute(
            [
                $name,
            ]
        );


        $id =
            $statement->fetchColumn();


        if ($id) {
            return (int) $id;
        }


        if (!$autoCreate) {
            return null;
        }


        $normalized =
            strtoupper(
                preg_replace(
                    '/[^A-Z0-9]/',
                    '',
                    $name
                )
                ?? ''
            );


        if ($normalized === '') {
            $normalized = 'PT';
        }


        $code =
            substr(
                $normalized,
                0,
                8
            )
            .
            random_int(
                100,
                999
            );


        App::db()
            ->prepare(
                "
                INSERT INTO companies
                (
                    code,
                    name,
                    address,
                    email,
                    status,
                    created_by,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    'active',
                    ?,
                    NOW(),
                    NOW()
                )
                "
            )
            ->execute(
                [
                    $code,

                    $name,

                    $this->nullable(
                        $source[
                            'payer_address'
                        ]
                        ?? null
                    ),

                    $this->nullable(
                        $source[
                            'payer_email'
                        ]
                        ?? null
                    ),

                    Auth::id(),
                ]
            );


        return (int)
            App::db()
                ->lastInsertId();
    }


    /*
    |--------------------------------------------------------------------------
    | NULLABLE STRING
    |--------------------------------------------------------------------------
    */

    private function nullable(
        mixed $value
    ): ?string {
        $value =
            trim(
                (string) $value
            );


        return $value !== ''
            ? $value
            : null;
    }
}