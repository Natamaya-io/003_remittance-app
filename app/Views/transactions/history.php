<?php

declare(strict_types=1);


$viewData =
    get_defined_vars();


$transactions =
    isset(
        $viewData['transactions']
    )
    &&
    is_array(
        $viewData['transactions']
    )
        ? $viewData['transactions']
        : [];


$companies =
    isset(
        $viewData['companies']
    )
    &&
    is_array(
        $viewData['companies']
    )
        ? $viewData['companies']
        : [];


$jobs =
    isset(
        $viewData['jobs']
    )
    &&
    is_array(
        $viewData['jobs']
    )
        ? $viewData['jobs']
        : [];


$filters =
    isset(
        $viewData['filters']
    )
    &&
    is_array(
        $viewData['filters']
    )
        ? $viewData['filters']
        : [];


$total =
    (int) (
        $viewData['total']
        ?? 0
    );


$page =
    max(
        1,
        (int) (
            $viewData['page']
            ?? 1
        )
    );


$pages =
    max(
        1,
        (int) (
            $viewData['pages']
            ?? 1
        )
    );


unset(
    $viewData
);


$filters = array_replace(
    [
        'q' => '',
        'company_id' => 0,
        'job_id' => 0,
        'status' => '',
        'date_from' => '',
        'date_to' => '',
    ],
    $filters
);

?>


<div class="trx-list-page">

    <section class="trx-list-heading d-flex justify-content-end mb-4">

        <?php if (
            can(
                'transaction.manage'
            )
        ): ?>

            <div class="trx-list-actions">

                <a
                    class="btn-secondary"
                    href="<?= url(
                        '/transactions/bri'
                    ) ?>"
                >
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    <span>Import Template</span>
                </a>


                <a
                    class="btn-primary"
                    href="<?= url(
                        '/transactions/create'
                    ) ?>"
                >
                    <i class="bi bi-plus-lg"></i>
                    <span>Input Manual</span>
                </a>

            </div>

        <?php endif; ?>

    </section>


    <!-- FILTER -->

    <section class="trx-filter-card">

        <form
            method="get"
            class="trx-filter-grid"
        >

            <div class="trx-search-field">
                <span class="block text-xs font-semibold text-gray-700 mb-1">Pencarian</span>
                <?= render_search_input('q', (string) ($filters['q'] ?? ''), [
                    'placeholder' => 'Referensi, invoice, beneficiary, SWIFT, rekening...',
                ]) ?>
            </div>


            <label>

                <span>
                    Perusahaan
                </span>

                <select
                    class="custom-form-select"
                    name="company_id"
                >

                    <option value="0">
                        Semua perusahaan
                    </option>

                    <?php foreach (
                        $companies
                        as $company
                    ): ?>

                        <?php

                        $companyId =
                            (int) (
                                $company['id']
                                ?? 0
                            );

                        ?>

                        <option
                            value="<?= $companyId ?>"
                            <?= selected(
                                (int) $filters[
                                    'company_id'
                                ],
                                $companyId
                            ) ?>
                        >
                            <?= e(
                                (string) (
                                    $company['name']
                                    ?? ''
                                )
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </label>


            <label>

                <span>
                    Batch
                </span>

                <select
                    class="custom-form-select"
                    name="job_id"
                >

                    <option value="0">
                        Semua batch
                    </option>

                    <?php foreach (
                        $jobs
                        as $job
                    ): ?>

                        <?php

                        $jobId =
                            (int) (
                                $job['id']
                                ?? 0
                            );

                        ?>

                        <option
                            value="<?= $jobId ?>"
                            <?= selected(
                                (int) $filters[
                                    'job_id'
                                ],
                                $jobId
                            ) ?>
                        >
                            <?= e(
                                (string) (
                                    $job['name']
                                    ?? ''
                                )
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </label>


            <label>

                <span>
                    Status
                </span>

                <select
                    class="custom-form-select"
                    name="status"
                >

                    <option value="">
                        Semua status
                    </option>

                    <?php foreach (
                        [
                            'PENDING',
                            'SUCCESS',
                            'FAILED',
                            'CANCELLED',
                        ]
                        as $status
                    ): ?>

                        <option
                            value="<?= $status ?>"
                            <?= selected(
                                $filters['status'],
                                $status
                            ) ?>
                        >
                            <?= $status ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </label>


            <label>

                <span>
                    Dari
                </span>

                <?= render_datepicker(
                    'date_from',
                    (string) ($filters['date_from'] ?? ''),
                    [
                        'placeholder' => 'Pilih tanggal mulai',
                    ]
                ) ?>

            </label>


            <label>

                <span>
                    Sampai
                </span>

                <?= render_datepicker(
                    'date_to',
                    (string) ($filters['date_to'] ?? ''),
                    [
                        'placeholder' => 'Pilih tanggal akhir',
                    ]
                ) ?>

            </label>


            <div class="trx-filter-actions">

                <button
                    class="btn-primary"
                    type="submit"
                >
                    <span>Terapkan</span>
                </button>

                <a
                    class="btn-secondary"
                    href="<?= url(
                        '/transactions'
                    ) ?>"
                >
                    <span>Reset</span>
                </a>

            </div>

        </form>

    </section>


    <!-- TABLE -->

    <section class="trx-table-card">

        <header class="trx-table-head">

            <div>

                <h3>
                    <?= number_format(
                        $total,
                        0,
                        ',',
                        '.'
                    ) ?>
                    transaksi
                </h3>

                <p>
                    Klik detail untuk melihat seluruh informasi transaksi.
                </p>

            </div>

        </header>


        <div class="trx-list-table-wrap">

            <table class="trx-list-table">

                <thead>

                    <tr>

                        <th>Tanggal</th>

                        <th>Tim</th>

                        <th>Batch</th>

                        <th>Perusahaan</th>

                        <th>Referensi</th>

                        <th>Beneficiary</th>

                        <th>Nominal</th>

                        <th>SWIFT</th>

                        <th>Status</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $transactions
                    as $transaction
                ): ?>

                    <?php

                    $transactionId =
                        (int) (
                            $transaction['id']
                            ?? 0
                        );


                    $jobId =
                        (int) (
                            $transaction[
                                'transaction_job_id'
                            ]
                            ?? 0
                        );


                    $reference =
                        trim(
                            (string) (
                                $transaction[
                                    'reference_no'
                                ]
                                ?? ''
                            )
                        );


                    if (
                        $reference === ''
                    ) {
                        $reference =
                            'TRX-' .
                            $transactionId;
                    }

                    ?>


                    <tr>

                        <td class="text-nowrap">

                            <?= e(
                                format_date(
                                    (string) (
                                        $transaction[
                                            'transaction_date'
                                        ]
                                        ?? ''
                                    )
                                )
                            ) ?>

                        </td>


                        <td>

                            <span class="code-pill">
                                T<?= (int) (
                                    $transaction[
                                        'team_no'
                                    ]
                                    ?? 0
                                ) ?>
                            </span>

                        </td>


                        <td>

                            <?php if (
                                $jobId > 0
                            ): ?>

                                <a
                                    class="table-link"
                                    href="<?= url(
                                        '/transactions/jobs/' .
                                        $jobId
                                    ) ?>"
                                >
                                    <?= e(
                                        (string) (
                                            $transaction[
                                                'job_name'
                                            ]
                                            ?? 'Batch'
                                        )
                                    ) ?>
                                </a>

                            <?php else: ?>

                                <span class="text-muted">
                                    Manual
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <strong>
                                <?= e(
                                    (string) (
                                        $transaction[
                                            'company_name'
                                        ]
                                        ?? '-'
                                    )
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <a
                                class="table-link"
                                href="<?= url(
                                    '/transactions/' .
                                    $transactionId
                                ) ?>"
                            >
                                <?= e($reference) ?>
                            </a>

                            <?php if (
                                !empty(
                                    $transaction[
                                        'invoice_no'
                                    ]
                                )
                            ): ?>

                                <small>
                                    <?= e(
                                        (string) $transaction[
                                            'invoice_no'
                                        ]
                                    ) ?>
                                </small>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= e(
                                (string) (
                                    $transaction[
                                        'beneficiary_name'
                                    ]
                                    ?: '-'
                                )
                            ) ?>

                            <small>
                                <?= e(
                                    (string) (
                                        $transaction[
                                            'beneficiary_bank'
                                        ]
                                        ?? ''
                                    )
                                ) ?>
                            </small>

                        </td>


                        <td class="text-nowrap">

                            <strong>
                                <?= e(
                                    format_money(
                                        (float) (
                                            $transaction[
                                                'amount'
                                            ]
                                            ?? 0
                                        ),

                                        (string) (
                                            $transaction[
                                                'currency'
                                            ]
                                            ?? 'USD'
                                        )
                                    )
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <span class="code-pill">
                                <?= e(
                                    (string) (
                                        $transaction[
                                            'swift'
                                        ]
                                        ?: '-'
                                    )
                                ) ?>
                            </span>

                        </td>


                        <td>

                            <?= status_badge(
                                (string) (
                                    $transaction[
                                        'status'
                                    ]
                                    ?? 'PENDING'
                                )
                            ) ?>

                        </td>


                        <td>

                            <div class="trx-crud-actions">

                                <!-- READ -->

                                <a
                                    class="trx-action-button view"
                                    href="<?= url(
                                        '/transactions/' .
                                        $transactionId
                                    ) ?>"
                                    title="Lihat"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>


                                <?php if (
                                    can(
                                        'transaction.manage'
                                    )
                                ): ?>

                                    <!-- UPDATE -->

                                    <a
                                        class="trx-action-button edit"
                                        href="<?= url(
                                            '/transactions/' .
                                            $transactionId .
                                            '/edit'
                                        ) ?>"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>


                                    <!-- DELETE -->

                                    <form
                                        method="post"
                                        action="<?= url(
                                            '/transactions/' .
                                            $transactionId .
                                            '/delete'
                                        ) ?>"
                                        data-confirm="Hapus transaksi ini?"
                                    >

                                        <?= csrf_field() ?>

                                        <button
                                            class="trx-action-button delete"
                                            type="submit"
                                            title="Hapus"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if (
                    $transactions === []
                ): ?>

                    <tr>

                        <td
                            colspan="10"
                            class="empty-state"
                        >
                            Transaksi tidak ditemukan.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


        <?php if (
            $pages > 1
        ): ?>

            <footer class="trx-pagination">

                <span>
                    Halaman
                    <?= $page ?>
                    dari
                    <?= $pages ?>
                </span>


                <div>

                <?php for (
                    $i = max(
                        1,
                        $page - 2
                    );

                    $i <= min(
                        $pages,
                        $page + 2
                    );

                    $i++
                ): ?>

                    <a
                        class="<?= $i === $page
                            ? 'btn-primary btn-primary-sm'
                            : 'btn-secondary !h-8 !px-3 text-xs' ?>"
                        href="?<?= e(
                            http_build_query(
                                array_merge(
                                    $filters,
                                    [
                                        'page' =>
                                            $i,
                                    ]
                                )
                            )
                        ) ?>"
                    >
                        <?= $i ?>
                    </a>

                <?php endfor; ?>

                </div>

            </footer>

        <?php endif; ?>

    </section>

</div>


<style>

.trx-list-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}


.trx-list-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 26px;
}


.trx-list-heading h1 {
    margin: 7px 0 7px;
    color: #4b3056;
    font-size: 36px;
}


.trx-list-heading p {
    margin: 0;
    color: #88778b;
}


.trx-list-actions {
    display: flex;
    gap: 9px;
}


.trx-filter-card,
.trx-table-card {
    padding: 22px;
    border: 1px solid #eaddea;
    border-radius: 21px;
    background: #ffffff;
}


.trx-filter-grid {
    display: grid;
    grid-template-columns:
        minmax(250px, 1.5fr)
        repeat(3, minmax(160px, 1fr));
    gap: 12px;
    align-items: end;
}


.trx-filter-grid label {
    display: flex;
    flex-direction: column;
    gap: 6px;
}


.trx-filter-grid label > span {
    color: #553c5c;
    font-size: 12px;
    font-weight: 700;
}


.trx-filter-actions {
    display: flex;
    gap: 8px;
}


.trx-table-head {
    margin-bottom: 15px;
}


.trx-table-head h3 {
    margin: 0;
    color: #4c3456;
}


.trx-table-head p {
    margin: 3px 0 0;
    color: #88798b;
    font-size: 12px;
}


.trx-list-table-wrap {
    width: 100%;
    overflow: auto;
    border: 1px solid #e8dde9;
    border-radius: 15px;
}


.trx-list-table {
    width: 100%;
    min-width: 1200px;
    border-collapse: collapse;
}


.trx-list-table th,
.trx-list-table td {
    padding: 11px 12px;
    border-bottom: 1px solid #ede4ee;
    vertical-align: middle;
    text-align: left;
}


.trx-list-table th {
    color: #4d3457;
    background: linear-gradient(135deg,#fff1f8,#f2e8ff);
    font-size: 11px;
    font-weight: 750;
}


.trx-list-table td {
    color: #59485e;
    font-size: 12px;
}


.trx-list-table td small {
    display: block;
    margin-top: 3px;
    color: #948598;
}


.trx-crud-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}


.trx-crud-actions form {
    margin: 0;
}


.trx-action-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 34px;
    height: 34px;

    padding: 0;

    border: 1px solid #e3d8e5;
    border-radius: 9px;

    cursor: pointer;

    background: #ffffff;
}


.trx-action-button.view {
    color: #496edb;
}


.trx-action-button.edit {
    color: #8246d7;
}


.trx-action-button.delete {
    color: #dd4271;
}


.trx-action-button:hover {
    background: #fff7fb;
}


.trx-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 16px;
}


.trx-pagination > div {
    display: flex;
    gap: 6px;
}


@media (max-width: 1100px) {

    .trx-filter-grid {
        grid-template-columns:
            repeat(
                2,
                minmax(0,1fr)
            );
    }

}


@media (max-width: 720px) {

    .trx-list-heading {
        flex-direction: column;
    }

    .trx-filter-grid {
        grid-template-columns: 1fr;
    }

}

</style>