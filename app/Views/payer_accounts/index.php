<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| NORMALISASI DATA VIEW
|--------------------------------------------------------------------------
*/

$viewData = get_defined_vars();


$accounts = isset(
    $viewData['accounts']
)
    && is_array(
        $viewData['accounts']
    )
        ? $viewData['accounts']
        : [];


$companies = isset(
    $viewData['companies']
)
    && is_array(
        $viewData['companies']
    )
        ? $viewData['companies']
        : [];


$filters = isset(
    $viewData['filters']
)
    && is_array(
        $viewData['filters']
    )
        ? $viewData['filters']
        : [];


$total = isset(
    $viewData['total']
)
    && is_numeric(
        $viewData['total']
    )
        ? (int) $viewData['total']
        : count($accounts);


$page = isset(
    $viewData['page']
)
    && is_numeric(
        $viewData['page']
    )
        ? max(
            1,
            (int) $viewData['page']
        )
        : 1;


$pages = isset(
    $viewData['pages']
)
    && is_numeric(
        $viewData['pages']
    )
        ? max(
            1,
            (int) $viewData['pages']
        )
        : 1;


unset(
    $viewData
);


$filters = array_replace(
    [
        'q' => '',
        'company_id' => 0,
        'status' => '',
    ],
    $filters
);

?>


<div class="payer-master-page">


    <!-- =====================================================
         PAGE ACTIONS
    ====================================================== -->

    <?php if (
        can(
            'transaction.manage'
        )
    ): ?>

        <div class="d-flex justify-content-end mb-4">
            <a
                class="btn-primary"
                href="<?= url(
                    '/payer-accounts/create'
                ) ?>"
            >
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Rekening</span>
            </a>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         SUMMARY
    ====================================================== -->

    <section class="payer-summary-grid">

        <article class="payer-summary-card purple">

            <span class="payer-summary-icon">
                <i class="bi bi-bank"></i>
            </span>

            <div>

                <small>
                    Total rekening
                </small>

                <strong>
                    <?= number_format(
                        $total,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

                <p>
                    Rekening terdaftar
                </p>

            </div>

        </article>


        <?php

        $activeCount = 0;

        $defaultCount = 0;

        $companyIds = [];


        foreach (
            $accounts
            as $account
        ) {

            if (
                strtolower(
                    trim(
                        (string) (
                            $account['status']
                            ?? ''
                        )
                    )
                )
                ===
                'active'
            ) {
                $activeCount++;
            }


            if (
                (int) (
                    $account['is_default']
                    ?? 0
                )
                ===
                1
            ) {
                $defaultCount++;
            }


            $companyId = (int) (
                $account['company_id']
                ?? 0
            );


            if (
                $companyId > 0
            ) {
                $companyIds[
                    $companyId
                ] = true;
            }

        }

        ?>


        <article class="payer-summary-card green">

            <span class="payer-summary-icon">
                <i class="bi bi-check-circle"></i>
            </span>

            <div>

                <small>
                    Rekening aktif
                </small>

                <strong>
                    <?= $activeCount ?>
                </strong>

                <p>
                    Siap digunakan
                </p>

            </div>

        </article>


        <article class="payer-summary-card blue">

            <span class="payer-summary-icon">
                <i class="bi bi-buildings"></i>
            </span>

            <div>

                <small>
                    Perusahaan
                </small>

                <strong>
                    <?= count(
                        $companyIds
                    ) ?>
                </strong>

                <p>
                    Memiliki rekening
                </p>

            </div>

        </article>


        <article class="payer-summary-card pink">

            <span class="payer-summary-icon">
                <i class="bi bi-star"></i>
            </span>

            <div>

                <small>
                    Rekening utama
                </small>

                <strong>
                    <?= $defaultCount ?>
                </strong>

                <p>
                    Default payer
                </p>

            </div>

        </article>

    </section>


    <!-- =====================================================
         FILTER
    ====================================================== -->

    <section class="payer-filter-card">

        <form
            method="get"
            class="payer-filter-grid"
        >

            <label class="payer-filter-search">

                <span class="block text-xs font-semibold text-gray-700 mb-1">Pencarian</span>
                <?= render_search_input('q', (string) ($filters['q'] ?? ''), [
                    'placeholder' => 'Nomor rekening, bank, perusahaan, SWIFT...',
                ]) ?>

            </label>


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

                        $companyId = (int) (
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
                    Status
                </span>

                <select
                    class="custom-form-select"
                    name="status"
                >

                    <option value="">
                        Semua status
                    </option>

                    <option
                        value="active"
                        <?= selected(
                            (string) $filters[
                                'status'
                            ],
                            'active'
                        ) ?>
                    >
                        Aktif
                    </option>

                    <option
                        value="inactive"
                        <?= selected(
                            (string) $filters[
                                'status'
                            ],
                            'inactive'
                        ) ?>
                    >
                        Tidak aktif
                    </option>

                </select>

            </label>


            <div class="payer-filter-actions">

                <button
                    class="btn-primary"
                    type="submit"
                >
                    <i class="bi bi-funnel"></i>
                    <span>Terapkan</span>
                </button>


                <a
                    class="btn-secondary"
                    href="<?= url(
                        '/payer-accounts'
                    ) ?>"
                >
                    <span>Reset</span>
                </a>

            </div>

        </form>

    </section>


    <!-- =====================================================
         ACCOUNT LIST
    ====================================================== -->

    <section class="payer-list-section">

        <?php if (
            $accounts !== []
        ): ?>

            <div class="payer-account-grid">


                <?php foreach (
                    $accounts
                    as $account
                ): ?>


                    <?php

                    $accountId = (int) (
                        $account['id']
                        ?? 0
                    );


                    $companyName = trim(
                        (string) (
                            $account['company_name']
                            ?? ''
                        )
                    );


                    $bankName = trim(
                        (string) (
                            $account['bank_name']
                            ?? ''
                        )
                    );


                    $branchName = trim(
                        (string) (
                            $account['branch_name']
                            ?? ''
                        )
                    );


                    $accountNo = trim(
                        (string) (
                            $account['account_no']
                            ?? ''
                        )
                    );


                    $accountName = trim(
                        (string) (
                            $account['account_name']
                            ?? ''
                        )
                    );


                    $currency = strtoupper(
                        trim(
                            (string) (
                                $account['currency']
                                ?? 'IDR'
                            )
                        )
                    );


                    $swift = strtoupper(
                        trim(
                            (string) (
                                $account['swift']
                                ?? ''
                            )
                        )
                    );


                    $email = trim(
                        (string) (
                            $account['email']
                            ?? ''
                        )
                    );


                    $address = trim(
                        (string) (
                            $account['address']
                            ?? ''
                        )
                    );


                    $city = trim(
                        (string) (
                            $account['city']
                            ?? ''
                        )
                    );


                    $country = trim(
                        (string) (
                            $account['country']
                            ?? ''
                        )
                    );


                    $status = strtolower(
                        trim(
                            (string) (
                                $account['status']
                                ?? 'inactive'
                            )
                        )
                    );


                    $isActive =
                        $status ===
                        'active';


                    $isDefault =
                        (int) (
                            $account[
                                'is_default'
                            ]
                            ?? 0
                        )
                        ===
                        1;

                    ?>


                    <article class="payer-account-card">


                        <!-- =================================
                             CARD HEADER
                        ================================== -->

                        <header class="payer-account-header">

                            <div class="payer-company-info">

                                <span class="payer-company-icon">
                                    <i class="bi bi-bank2"></i>
                                </span>


                                <div>

                                    <small>
                                        PERUSAHAAN
                                    </small>

                                    <h3>
                                        <?= e(
                                            $companyName !== ''
                                                ? $companyName
                                                : 'Perusahaan'
                                        ) ?>
                                    </h3>

                                </div>

                            </div>


                            <div class="payer-card-badges">

                                <?php if (
                                    $isDefault
                                ): ?>

                                    <span class="payer-badge default">
                                        <i class="bi bi-star-fill"></i>
                                        Utama
                                    </span>

                                <?php endif; ?>


                                <span
                                    class="payer-badge <?= $isActive
                                        ? 'active'
                                        : 'inactive' ?>"
                                >
                                    <span></span>

                                    <?= $isActive
                                        ? 'Aktif'
                                        : 'Tidak aktif' ?>
                                </span>

                            </div>

                        </header>


                        <!-- =================================
                             BANK
                        ================================== -->

                        <div class="payer-bank-row">

                            <div>

                                <small>
                                    BANK
                                </small>

                                <strong>
                                    <?= e(
                                        $bankName !== ''
                                            ? $bankName
                                            : '-'
                                    ) ?>
                                </strong>

                            </div>


                            <?php if (
                                $branchName !== ''
                            ): ?>

                                <div class="payer-branch">

                                    <small>
                                        CABANG
                                    </small>

                                    <strong>
                                        <?= e(
                                            $branchName
                                        ) ?>
                                    </strong>

                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- =================================
                             ACCOUNT NUMBER
                        ================================== -->

                        <div class="payer-account-number-box">

                            <div>

                                <span>
                                    Nomor Rekening
                                </span>

                                <strong>
                                    <?= e(
                                        $accountNo !== ''
                                            ? $accountNo
                                            : '-'
                                    ) ?>
                                </strong>

                            </div>


                            <?php if (
                                $accountNo !== ''
                            ): ?>

                                <button
                                    class="payer-copy-button"
                                    type="button"
                                    data-copy-account="<?= e(
                                        $accountNo
                                    ) ?>"
                                    title="Salin nomor rekening"
                                >
                                    <i class="bi bi-copy"></i>
                                </button>

                            <?php endif; ?>

                        </div>


                        <!-- =================================
                             DETAILS
                        ================================== -->

                        <div class="payer-detail-grid">


                            <div class="payer-detail-item">

                                <span class="payer-detail-icon purple">
                                    <i class="bi bi-person"></i>
                                </span>

                                <div>

                                    <small>
                                        Nama Rekening
                                    </small>

                                    <strong>
                                        <?= e(
                                            $accountName !== ''
                                                ? $accountName
                                                : '-'
                                        ) ?>
                                    </strong>

                                </div>

                            </div>


                            <div class="payer-detail-item">

                                <span class="payer-detail-icon green">
                                    <i class="bi bi-cash-stack"></i>
                                </span>

                                <div>

                                    <small>
                                        Mata Uang
                                    </small>

                                    <strong>
                                        <?= e(
                                            $currency !== ''
                                                ? $currency
                                                : '-'
                                        ) ?>
                                    </strong>

                                </div>

                            </div>


                            <div class="payer-detail-item">

                                <span class="payer-detail-icon blue">
                                    <i class="bi bi-globe2"></i>
                                </span>

                                <div>

                                    <small>
                                        SWIFT
                                    </small>

                                    <strong class="payer-code">
                                        <?= e(
                                            $swift !== ''
                                                ? $swift
                                                : '-'
                                        ) ?>
                                    </strong>

                                </div>

                            </div>


                            <div class="payer-detail-item">

                                <span class="payer-detail-icon pink">
                                    <i class="bi bi-envelope"></i>
                                </span>

                                <div>

                                    <small>
                                        Email Output
                                    </small>

                                    <strong>
                                        <?= e(
                                            $email !== ''
                                                ? $email
                                                : '-'
                                        ) ?>
                                    </strong>

                                </div>

                            </div>

                        </div>


                        <!-- =================================
                             ADDRESS
                        ================================== -->

                        <?php if (
                            $address !== ''
                            ||
                            $city !== ''
                            ||
                            $country !== ''
                        ): ?>

                            <div class="payer-address-box">

                                <span class="payer-address-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </span>


                                <div>

                                    <small>
                                        ALAMAT PAYER
                                    </small>

                                    <?php if (
                                        $address !== ''
                                    ): ?>

                                        <strong>
                                            <?= e(
                                                $address
                                            ) ?>
                                        </strong>

                                    <?php endif; ?>


                                    <p>

                                        <?php

                                        $locationParts =
                                            array_filter(
                                                [
                                                    $city,
                                                    $country,
                                                ],
                                                static fn (
                                                    string $value
                                                ): bool =>
                                                    $value !== ''
                                            );

                                        ?>

                                        <?= e(
                                            implode(
                                                ', ',
                                                $locationParts
                                            )
                                        ) ?>

                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- =================================
                             CARD ACTION
                        ================================== -->

                        <footer class="payer-account-actions">

                            <?php if (
                                can(
                                    'transaction.manage'
                                )
                            ): ?>

                                <a
                                    class="payer-action-btn edit"
                                    href="<?= url(
                                        '/payer-accounts/' .
                                        $accountId .
                                        '/edit'
                                    ) ?>"
                                >
                                    <i class="bi bi-pencil"></i>

                                    Ubah
                                </a>


                                <form
                                    method="post"
                                    action="<?= url(
                                        '/payer-accounts/' .
                                        $accountId .
                                        '/delete'
                                    ) ?>"
                                    data-confirm="Hapus rekening payer ini?"
                                >

                                    <?= csrf_field() ?>


                                    <button
                                        class="payer-action-btn delete"
                                        type="submit"
                                    >
                                        <i class="bi bi-trash"></i>

                                        Hapus
                                    </button>

                                </form>

                            <?php endif; ?>

                        </footer>

                    </article>

                <?php endforeach; ?>

            </div>


        <?php else: ?>


            <div class="payer-empty-state">

                <span>
                    <i class="bi bi-bank"></i>
                </span>

                <h3>
                    Belum ada rekening payer
                </h3>

                <p>
                    Tambahkan rekening payer untuk digunakan
                    pada transaksi remittance.
                </p>


                <?php if (
                    can(
                        'transaction.manage'
                    )
                ): ?>

                    <a
                        class="btn-primary"
                        href="<?= url(
                            '/payer-accounts/create'
                        ) ?>"
                    >
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Rekening</span>
                    </a>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </section>


    <!-- =====================================================
         PAGINATION
    ====================================================== -->

    <?php if (
        $pages > 1
    ): ?>

        <div class="payer-pagination">

            <span>
                Halaman
                <?= $page ?>
                dari
                <?= $pages ?>
            </span>


            <div>

                <?php

                $paginationStart =
                    max(
                        1,
                        $page - 2
                    );


                $paginationEnd =
                    min(
                        $pages,
                        $page + 2
                    );

                ?>


                <?php for (
                    $i = $paginationStart;
                    $i <= $paginationEnd;
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
                                        'page' => $i,
                                    ]
                                )
                            )
                        ) ?>"
                    >
                        <?= $i ?>
                    </a>

                <?php endfor; ?>

            </div>

        </div>

    <?php endif; ?>


</div>


<style>

/* =========================================================
   PAGE
========================================================= */

.payer-master-page {
    display: flex;
    flex-direction: column;
    gap: 20px;

    width: 100%;
    min-width: 0;
}


/* =========================================================
   PAGE HEADER
========================================================= */

.payer-page-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 28px;

    padding: 4px 0 2px;
}


.payer-page-heading-copy {
    max-width: 850px;
}


.payer-page-heading h1 {
    margin: 7px 0 7px;

    color: #4d3158;

    font-size: clamp(
        30px,
        3vw,
        40px
    );

    line-height: 1.12;

    letter-spacing: -.035em;
}


.payer-page-heading p {
    margin: 0;

    color: #88758b;

    font-size: 14px;

    line-height: 1.55;
}


.payer-add-button {
    flex: 0 0 auto;
}


/* =========================================================
   SUMMARY
========================================================= */

.payer-summary-grid {
    display: grid;

    grid-template-columns:
        repeat(
            4,
            minmax(0, 1fr)
        );

    gap: 12px;
}


.payer-summary-card {
    display: flex;
    align-items: center;

    gap: 13px;

    min-height: 108px;

    padding: 18px;

    border-radius: 18px;

    color: #ffffff;

    box-shadow:
        0 10px 22px
        rgba(
            92,
            49,
            106,
            .08
        );
}


.payer-summary-card.purple {
    background:
        linear-gradient(
            135deg,
            #8250dd,
            #a04ad6
        );
}


.payer-summary-card.green {
    background:
        linear-gradient(
            135deg,
            #2bc59a,
            #34b8a2
        );
}


.payer-summary-card.blue {
    background:
        linear-gradient(
            135deg,
            #42addf,
            #568fe1
        );
}


.payer-summary-card.pink {
    background:
        linear-gradient(
            135deg,
            #e8418a,
            #d649a4
        );
}


.payer-summary-icon {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 48px;
    height: 48px;

    flex: 0 0 48px;

    border-radius: 14px;

    background:
        rgba(
            255,
            255,
            255,
            .17
        );

    font-size: 21px;
}


.payer-summary-card > div {
    display: flex;
    flex-direction: column;

    min-width: 0;
}


.payer-summary-card small {
    color:
        rgba(
            255,
            255,
            255,
            .9
        );

    font-size: 10px;

    font-weight: 700;
}


.payer-summary-card strong {
    margin: 3px 0;

    color: #ffffff;

    font-size: 27px;

    line-height: 1;

    font-weight: 850;
}


.payer-summary-card p {
    margin: 2px 0 0;

    color:
        rgba(
            255,
            255,
            255,
            .86
        );

    font-size: 10px;
}


/* =========================================================
   FILTER
========================================================= */

.payer-filter-card {
    padding: 18px;

    border: 1px solid #eaddeb;

    border-radius: 19px;

    background:
        rgba(
            255,
            255,
            255,
            .96
        );

    box-shadow:
        0 8px 26px
        rgba(
            93,
            54,
            106,
            .04
        );
}


.payer-filter-grid {
    display: grid;

    grid-template-columns:
        minmax(
            300px,
            1.6fr
        )
        minmax(
            220px,
            1fr
        )
        minmax(
            170px,
            .7fr
        )
        auto;

    align-items: end;

    gap: 12px;
}


.payer-filter-grid label {
    display: flex;
    flex-direction: column;

    gap: 6px;
}


.payer-filter-grid label > span {
    color: #5c4562;

    font-size: 11px;

    font-weight: 750;
}


.payer-input-icon {
    position: relative;
}


.payer-input-icon > i {
    position: absolute;

    top: 50%;
    left: 14px;

    z-index: 2;

    transform:
        translateY(-50%);

    color: #997ba4;
}


.payer-input-icon .form-control {
    padding-left: 39px;
}


.payer-filter-actions {
    display: flex;

    gap: 8px;
}


/* =========================================================
   LIST
========================================================= */

.payer-list-section {
    display: flex;
    flex-direction: column;

    gap: 15px;
}


.payer-list-heading h2 {
    margin: 5px 0 3px;

    color: #4d3457;

    font-size: 23px;
}


.payer-list-heading p {
    margin: 0;

    color: #8c7a8e;

    font-size: 12px;
}


/* =========================================================
   ACCOUNT GRID
========================================================= */

.payer-account-grid {
    display: grid;

    grid-template-columns:
        repeat(
            2,
            minmax(
                0,
                1fr
            )
        );

    gap: 16px;
}


/* =========================================================
   ACCOUNT CARD
========================================================= */

.payer-account-card {
    display: flex;
    flex-direction: column;

    min-width: 0;

    overflow: hidden;

    border: 1px solid #e7dbe9;

    border-radius: 20px;

    background: #ffffff;

    box-shadow:
        0 10px 28px
        rgba(
            91,
            52,
            104,
            .05
        );

    transition:
        transform .16s ease,
        border-color .16s ease,
        box-shadow .16s ease;
}


.payer-account-card:hover {
    transform:
        translateY(-2px);

    border-color:
        #d5b6df;

    box-shadow:
        0 14px 32px
        rgba(
            91,
            52,
            104,
            .08
        );
}


/* =========================================================
   CARD HEADER
========================================================= */

.payer-account-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 14px;

    padding: 18px 19px;

    border-bottom:
        1px solid
        #eee4ef;

    background:
        linear-gradient(
            115deg,
            #fff5fa,
            #f5ecff,
            #eef7ff
        );
}


.payer-company-info {
    display: flex;

    align-items: center;

    min-width: 0;

    gap: 12px;
}


.payer-company-icon {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 43px;
    height: 43px;

    flex: 0 0 43px;

    border-radius: 13px;

    color: #7640d6;

    background: #eee2ff;

    font-size: 20px;
}


.payer-company-info > div {
    min-width: 0;
}


.payer-company-info small {
    display: block;

    margin-bottom: 2px;

    color: #9b7ca1;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: .11em;
}


.payer-company-info h3 {
    overflow: hidden;

    margin: 0;

    color: #4d3457;

    font-size: 15px;

    font-weight: 780;

    text-overflow: ellipsis;

    white-space: nowrap;
}


/* =========================================================
   BADGES
========================================================= */

.payer-card-badges {
    display: flex;

    align-items: center;

    gap: 6px;

    flex: 0 0 auto;
}


.payer-badge {
    display: inline-flex;

    align-items: center;

    gap: 5px;

    min-height: 27px;

    padding: 0 9px;

    border-radius: 999px;

    font-size: 9px;

    font-weight: 800;
}


.payer-badge.active {
    color: #14795d;

    background: #ddf6ec;
}


.payer-badge.active > span {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #26bf91;
}


.payer-badge.inactive {
    color: #8b6471;

    background: #f5e9ed;
}


.payer-badge.inactive > span {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #c995a8;
}


.payer-badge.default {
    color: #8542c7;

    background: #f0e2ff;
}


/* =========================================================
   BANK
========================================================= */

.payer-bank-row {
    display: grid;

    grid-template-columns:
        repeat(
            2,
            minmax(
                0,
                1fr
            )
        );

    gap: 10px;

    padding:
        16px 19px 0;
}


.payer-bank-row > div {
    min-width: 0;
}


.payer-bank-row small {
    display: block;

    margin-bottom: 3px;

    color: #99889d;

    font-size: 9px;

    font-weight: 750;

    letter-spacing: .08em;
}


.payer-bank-row strong {
    display: block;

    overflow: hidden;

    color: #503858;

    font-size: 14px;

    text-overflow: ellipsis;

    white-space: nowrap;
}


/* =========================================================
   ACCOUNT NUMBER
========================================================= */

.payer-account-number-box {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 12px;

    margin: 14px 19px;

    padding: 13px 14px;

    border: 1px solid #e3d6e6;

    border-radius: 14px;

    background:
        linear-gradient(
            120deg,
            #fff9fc,
            #f7efff
        );
}


.payer-account-number-box > div {
    min-width: 0;

    display: flex;
    flex-direction: column;

    gap: 2px;
}


.payer-account-number-box span {
    color: #8b798f;

    font-size: 9px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: .06em;
}


.payer-account-number-box strong {
    overflow: hidden;

    color: #6f37c6;

    font-size: 19px;

    font-weight: 850;

    letter-spacing: .025em;

    text-overflow: ellipsis;

    white-space: nowrap;
}


.payer-copy-button {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    flex: 0 0 38px;

    padding: 0;

    border: 1px solid #dfd0e5;

    border-radius: 11px;

    color: #8043d5;

    background: #ffffff;

    cursor: pointer;

    transition: .15s ease;
}


.payer-copy-button:hover {
    color: #ffffff;

    border-color: transparent;

    background:
        linear-gradient(
            135deg,
            #7940dd,
            #d53da2
        );
}


/* =========================================================
   DETAIL GRID
========================================================= */

.payer-detail-grid {
    display: grid;

    grid-template-columns:
        repeat(
            2,
            minmax(
                0,
                1fr
            )
        );

    gap: 9px;

    padding:
        0 19px 15px;
}


.payer-detail-item {
    display: flex;

    align-items: center;

    min-width: 0;

    gap: 10px;

    padding: 10px;

    border: 1px solid #eee5ef;

    border-radius: 12px;

    background: #fffdfd;
}


.payer-detail-icon {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 34px;
    height: 34px;

    flex: 0 0 34px;

    border-radius: 10px;

    font-size: 14px;
}


.payer-detail-icon.purple {
    color: #7540d3;

    background: #eee3ff;
}


.payer-detail-icon.green {
    color: #168264;

    background: #e2f8ef;
}


.payer-detail-icon.blue {
    color: #167eac;

    background: #e5f4fa;
}


.payer-detail-icon.pink {
    color: #ca3d80;

    background: #ffe7f2;
}


.payer-detail-item > div {
    min-width: 0;
}


.payer-detail-item small {
    display: block;

    margin-bottom: 2px;

    color: #948397;

    font-size: 9px;
}


.payer-detail-item strong {
    display: block;

    overflow: hidden;

    color: #513b58;

    font-size: 11px;

    text-overflow: ellipsis;

    white-space: nowrap;
}


.payer-code {
    font-family:
        ui-monospace,
        SFMono-Regular,
        Menlo,
        Consolas,
        monospace;

    letter-spacing: .02em;
}


/* =========================================================
   ADDRESS
========================================================= */

.payer-address-box {
    display: flex;

    align-items: flex-start;

    gap: 11px;

    margin: 0 19px 15px;

    padding: 12px 13px;

    border-radius: 13px;

    background:
        linear-gradient(
            120deg,
            #f9f2ff,
            #eff8ff
        );
}


.payer-address-icon {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 35px;
    height: 35px;

    flex: 0 0 35px;

    border-radius: 10px;

    color: #7650c3;

    background: #ffffff;
}


.payer-address-box > div {
    min-width: 0;
}


.payer-address-box small {
    display: block;

    margin-bottom: 3px;

    color: #987f9c;

    font-size: 9px;

    font-weight: 750;
}


.payer-address-box strong {
    display: block;

    color: #513858;

    font-size: 11px;

    line-height: 1.45;
}


.payer-address-box p {
    margin: 3px 0 0;

    color: #88758d;

    font-size: 10px;
}


/* =========================================================
   ACTIONS
========================================================= */

.payer-account-actions {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    gap: 8px;

    margin-top: auto;

    padding: 13px 19px;

    border-top: 1px solid #eee4ef;

    background: #fffafd;
}


.payer-account-actions form {
    margin: 0;
}


.payer-action-btn {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 6px;

    min-height: 36px;

    padding: 0 13px;

    border: 1px solid #e1d5e4;

    border-radius: 10px;

    font-family: inherit;

    font-size: 11px;

    font-weight: 750;

    text-decoration: none;

    cursor: pointer;
}


.payer-action-btn.edit {
    color: #7440c6;

    background: #ffffff;
}


.payer-action-btn.edit:hover {
    color: #ffffff;

    border-color: transparent;

    background:
        linear-gradient(
            135deg,
            #7a43da,
            #aa46d2
        );
}


.payer-action-btn.delete {
    color: #d8396d;

    border-color: #f0cfd9;

    background: #fff8fa;
}


.payer-action-btn.delete:hover {
    color: #ffffff;

    border-color: transparent;

    background: #e74373;
}


/* =========================================================
   EMPTY
========================================================= */

.payer-empty-state {
    display: flex;

    align-items: center;
    justify-content: center;
    flex-direction: column;

    min-height: 320px;

    padding: 30px;

    border: 1px solid #eaddeb;

    border-radius: 20px;

    background: #ffffff;

    text-align: center;
}


.payer-empty-state > span {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 60px;
    height: 60px;

    margin-bottom: 12px;

    border-radius: 18px;

    color: #7b43d5;

    background: #eee3ff;

    font-size: 25px;
}


.payer-empty-state h3 {
    margin: 0 0 5px;

    color: #4d3457;
}


.payer-empty-state p {
    margin: 0 0 15px;

    color: #8b7a8e;
}


/* =========================================================
   PAGINATION
========================================================= */

.payer-pagination {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 12px;

    padding-top: 4px;
}


.payer-pagination > span {
    color: #857389;

    font-size: 11px;
}


.payer-pagination > div {
    display: flex;

    gap: 6px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1250px) {

    .payer-summary-grid {
        grid-template-columns:
            repeat(
                2,
                minmax(
                    0,
                    1fr
                )
            );
    }


    .payer-filter-grid {
        grid-template-columns:
            repeat(
                2,
                minmax(
                    0,
                    1fr
                )
            );
    }

}


@media (max-width: 950px) {

    .payer-account-grid {
        grid-template-columns:
            1fr;
    }

}


@media (max-width: 700px) {

    .payer-page-heading {
        flex-direction: column;
    }


    .payer-summary-grid,
    .payer-filter-grid,
    .payer-detail-grid {
        grid-template-columns:
            1fr;
    }


    .payer-add-button {
        width: 100%;
    }


    .payer-account-header {
        align-items: flex-start;

        flex-direction: column;
    }


    .payer-bank-row {
        grid-template-columns:
            1fr;
    }


    .payer-pagination {
        align-items: flex-start;

        flex-direction: column;
    }

}

</style>


<script>

(() => {

    'use strict';


    /*
    |--------------------------------------------------------------------------
    | COPY ACCOUNT NUMBER
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '[data-copy-account]'
        )
        .forEach(
            button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const accountNumber =
                            button.dataset
                                .copyAccount
                            ?? '';


                        if (
                            accountNumber === ''
                        ) {
                            return;
                        }


                        try {

                            await navigator
                                .clipboard
                                .writeText(
                                    accountNumber
                                );


                            const original =
                                button.innerHTML;


                            button.innerHTML =
                                '<i class="bi bi-check-lg"></i>';


                            button.title =
                                'Tersalin';


                            window.setTimeout(
                                () => {

                                    button.innerHTML =
                                        original;


                                    button.title =
                                        'Salin nomor rekening';

                                },
                                1300
                            );

                        } catch (
                            error
                        ) {

                            const temporary =
                                document
                                    .createElement(
                                        'textarea'
                                    );


                            temporary.value =
                                accountNumber;


                            temporary.style.position =
                                'fixed';


                            temporary.style.opacity =
                                '0';


                            document.body
                                .appendChild(
                                    temporary
                                );


                            temporary.select();


                            document.execCommand(
                                'copy'
                            );


                            temporary.remove();

                        }

                    }
                );

            }
        );

})();

</script>