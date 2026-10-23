<?php

declare(strict_types=1);


$viewData =
    get_defined_vars();


$transaction =
    isset(
        $viewData[
            'transaction'
        ]
    )
    &&
    is_array(
        $viewData[
            'transaction'
        ]
    )
        ? $viewData[
            'transaction'
        ]
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


$payerAccounts =
    isset(
        $viewData[
            'payerAccounts'
        ]
    )
    &&
    is_array(
        $viewData[
            'payerAccounts'
        ]
    )
        ? $viewData[
            'payerAccounts'
        ]
        : [];


unset(
    $viewData
);


$isEdit =
    (int) (
        $transaction['id']
        ?? 0
    )
    > 0;


$transactionId =
    (int) (
        $transaction['id']
        ?? 0
    );


$action =
    $isEdit
        ? url(
            '/transactions/' .
            $transactionId
        )
        : url(
            '/transactions'
        );

?>


<div class="trx-form-page">

    <div class="page-heading">

        <div>

            <span class="eyebrow dark">
                OPERASIONAL TRANSAKSI
            </span>

            <h2>
                <?= $isEdit
                    ? 'Edit Transaksi'
                    : 'Tambah Transaksi' ?>
            </h2>

            <p>
                Isi data transaksi dan pilih Rekening Payer dari master
                apabila tersedia.
            </p>

        </div>


        <a
            class="btn btn-light"
            href="<?= url('/transactions') ?>"
        >
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>

    </div>


    <form
        method="post"
        action="<?= $action ?>"
        class="trx-edit-card"
        id="transactionCrudForm"
    >

        <?= csrf_field() ?>


        <section class="trx-edit-section">

            <header>

                <span>01</span>

                <div>

                    <h3>
                        Informasi transaksi
                    </h3>

                    <p>
                        Identitas dan informasi dasar transaksi.
                    </p>

                </div>

            </header>


            <div class="trx-edit-grid">

                <label>

                    <span>
                        Tanggal transaksi *
                    </span>

                    <input
                        class="form-control"
                        type="date"
                        name="transaction_date"
                        required
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'transaction_date'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Nomor urut
                    </span>

                    <input
                        class="form-control"
                        name="sequence_no"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'sequence_no'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Reference
                    </span>

                    <input
                        class="form-control"
                        name="reference_no"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'reference_no'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        No invoice
                    </span>

                    <input
                        class="form-control"
                        name="invoice_no"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'invoice_no'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Tim
                    </span>

                    <select
                        class="form-select"
                        name="team_no"
                    >

                        <?php for (
                            $team = 1;
                            $team <= 4;
                            $team++
                        ): ?>

                            <option
                                value="<?= $team ?>"
                                <?= selected(
                                    (int) (
                                        $transaction[
                                            'team_no'
                                        ]
                                        ?? 1
                                    ),
                                    $team
                                ) ?>
                            >
                                Tim <?= $team ?>
                            </option>

                        <?php endfor; ?>

                    </select>

                </label>


                <label>

                    <span>
                        Status
                    </span>

                    <select
                        class="form-select"
                        name="status"
                    >

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
                                    (string) (
                                        $transaction[
                                            'status'
                                        ]
                                        ?? 'PENDING'
                                    ),
                                    $status
                                ) ?>
                            >
                                <?= $status ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>

            </div>

        </section>


        <!-- PAYER -->

        <section class="trx-edit-section">

            <header>

                <span>02</span>

                <div>

                    <h3>
                        Payer
                    </h3>

                    <p>
                        Perusahaan dan rekening sumber dana.
                    </p>

                </div>

            </header>


            <div class="trx-edit-grid">

                <label>

                    <span>
                        Perusahaan *
                    </span>

                    <select
                        class="form-select"
                        name="payer_company_id"
                        id="payerCompany"
                        required
                    >

                        <option value="">
                            Pilih perusahaan
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
                                    (int) (
                                        $transaction[
                                            'payer_company_id'
                                        ]
                                        ?? 0
                                    ),
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
                        Master Rekening
                    </span>

                    <select
                        class="form-select"
                        name="payer_account_id"
                        id="payerAccountMaster"
                    >

                        <option value="">
                            Pilih rekening
                        </option>

                        <?php foreach (
                            $payerAccounts
                            as $account
                        ): ?>

                            <?php

                            $accountId =
                                (int) (
                                    $account['id']
                                    ?? 0
                                );

                            ?>

                            <option
                                value="<?= $accountId ?>"
                                data-company="<?= (int) (
                                    $account[
                                        'company_id'
                                    ]
                                    ?? 0
                                ) ?>"
                                data-account="<?= e(
                                    (string) (
                                        $account[
                                            'account_no'
                                        ]
                                        ?? ''
                                    )
                                ) ?>"
                                data-address="<?= e(
                                    (string) (
                                        $account[
                                            'address'
                                        ]
                                        ?? ''
                                    )
                                ) ?>"
                                data-city="<?= e(
                                    (string) (
                                        $account[
                                            'city'
                                        ]
                                        ?? ''
                                    )
                                ) ?>"
                                data-country="<?= e(
                                    (string) (
                                        $account[
                                            'country'
                                        ]
                                        ?? ''
                                    )
                                ) ?>"
                                data-email="<?= e(
                                    (string) (
                                        $account[
                                            'email'
                                        ]
                                        ?? ''
                                    )
                                ) ?>"
                                data-bank="<?= e(
                                    (string) (
                                        $account[
                                            'bank_name'
                                        ]
                                        ?? ''
                                    )
                                ) ?>"
                                <?= selected(
                                    (int) (
                                        $transaction[
                                            'payer_account_id'
                                        ]
                                        ?? 0
                                    ),
                                    $accountId
                                ) ?>
                            >
                                <?= e(
                                    (string) (
                                        $account[
                                            'account_no'
                                        ]
                                        ?? ''
                                    )
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>


                <label>

                    <span>
                        Nomor Rekening
                    </span>

                    <input
                        class="form-control"
                        name="payer_account"
                        id="payerAccount"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'payer_account'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Bank Payer
                    </span>

                    <input
                        class="form-control"
                        name="payer_bank"
                        id="payerBank"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'payer_bank'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label class="trx-span-two">

                    <span>
                        Alamat Payer
                    </span>

                    <input
                        class="form-control"
                        name="payer_address"
                        id="payerAddress"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'payer_address'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Kota
                    </span>

                    <input
                        class="form-control"
                        name="payer_city"
                        id="payerCity"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'payer_city'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Negara
                    </span>

                    <input
                        class="form-control"
                        name="payer_country"
                        id="payerCountry"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'payer_country'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Email
                    </span>

                    <input
                        class="form-control"
                        type="email"
                        name="payer_email"
                        id="payerEmail"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'payer_email'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>

            </div>

        </section>


        <!-- BENEFICIARY -->

        <section class="trx-edit-section">

            <header>

                <span>03</span>

                <div>

                    <h3>
                        Beneficiary
                    </h3>

                    <p>
                        Informasi rekening penerima.
                    </p>

                </div>

            </header>


            <div class="trx-edit-grid">

                <label>

                    <span>
                        Nama Beneficiary
                    </span>

                    <input
                        class="form-control"
                        name="beneficiary_name"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'beneficiary_name'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Account Beneficiary
                    </span>

                    <input
                        class="form-control"
                        name="beneficiary_account"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'beneficiary_account'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Beneficiary Bank
                    </span>

                    <input
                        class="form-control"
                        name="beneficiary_bank"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'beneficiary_bank'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Lokasi Bank
                    </span>

                    <input
                        class="form-control"
                        name="beneficiary_bank_location"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'beneficiary_bank_location'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label class="trx-span-two">

                    <span>
                        Alamat Beneficiary
                    </span>

                    <input
                        class="form-control"
                        name="beneficiary_address"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'beneficiary_address'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Kota Beneficiary
                    </span>

                    <input
                        class="form-control"
                        name="beneficiary_city"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'beneficiary_city'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Negara Beneficiary
                    </span>

                    <input
                        class="form-control"
                        name="beneficiary_country"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'beneficiary_country'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        SWIFT
                    </span>

                    <input
                        class="form-control"
                        name="swift"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'swift'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>

            </div>

        </section>


        <!-- PAYMENT -->

        <section class="trx-edit-section">

            <header>

                <span>04</span>

                <div>

                    <h3>
                        Pembayaran
                    </h3>

                    <p>
                        Nominal dan detail pembayaran.
                    </p>

                </div>

            </header>


            <div class="trx-edit-grid">

                <label>

                    <span>
                        Amount *
                    </span>

                    <input
                        class="form-control"
                        name="amount"
                        required
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'amount'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label>

                    <span>
                        Currency *
                    </span>

                    <input
                        class="form-control"
                        name="currency"
                        required
                        maxlength="3"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'currency'
                                ]
                                ?? 'USD'
                            )
                        ) ?>"
                    >

                </label>


                <label class="trx-span-two">

                    <span>
                        Payment Details
                    </span>

                    <input
                        class="form-control"
                        name="payment_details"
                        value="<?= e(
                            (string) (
                                $transaction[
                                    'payment_details'
                                ]
                                ?? ''
                            )
                        ) ?>"
                    >

                </label>


                <label class="trx-span-two">

                    <span>
                        Catatan
                    </span>

                    <textarea
                        class="form-control"
                        name="notes"
                        rows="3"
                    ><?= e(
                        (string) (
                            $transaction[
                                'notes'
                            ]
                            ?? ''
                        )
                    ) ?></textarea>

                </label>

            </div>

        </section>


        <footer class="trx-edit-actions">

            <a
                class="btn btn-light"
                href="<?= url('/transactions') ?>"
            >
                Batal
            </a>


            <button
                class="btn btn-primary"
                type="submit"
            >
                <i class="bi bi-check-lg"></i>

                <?= $isEdit
                    ? 'Simpan Perubahan'
                    : 'Simpan Transaksi' ?>
            </button>

        </footer>

    </form>

</div>


<style>

.trx-form-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}


.trx-edit-card {
    display: flex;
    flex-direction: column;
    gap: 16px;
}


.trx-edit-section {
    padding: 22px;
    border: 1px solid #eaddea;
    border-radius: 20px;
    background: #ffffff;
}


.trx-edit-section > header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}


.trx-edit-section > header > span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 13px;
    color: #7640d6;
    background: #eee2ff;
    font-size: 11px;
    font-weight: 800;
}


.trx-edit-section h3 {
    margin: 0;
    color: #4d3457;
}


.trx-edit-section p {
    margin: 3px 0 0;
    color: #8b7a8e;
    font-size: 12px;
}


.trx-edit-grid {
    display: grid;
    grid-template-columns:
        repeat(
            2,
            minmax(0,1fr)
        );
    gap: 14px;
}


.trx-edit-grid label {
    display: flex;
    flex-direction: column;
    gap: 6px;
}


.trx-edit-grid label > span {
    color: #57405f;
    font-size: 12px;
    font-weight: 700;
}


.trx-span-two {
    grid-column: span 2;
}


.trx-edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
}


@media (max-width: 760px) {

    .trx-edit-grid {
        grid-template-columns: 1fr;
    }

    .trx-span-two {
        grid-column: auto;
    }

}

</style>


<script>

(() => {

    const company =
        document.getElementById(
            'payerCompany'
        );


    const master =
        document.getElementById(
            'payerAccountMaster'
        );


    if (
        !company
        ||
        !master
    ) {
        return;
    }


    const account =
        document.getElementById(
            'payerAccount'
        );


    const address =
        document.getElementById(
            'payerAddress'
        );


    const city =
        document.getElementById(
            'payerCity'
        );


    const country =
        document.getElementById(
            'payerCountry'
        );


    const email =
        document.getElementById(
            'payerEmail'
        );


    const bank =
        document.getElementById(
            'payerBank'
        );


    const filterMaster =
        () => {

            const companyId =
                company.value;


            [
                ...master.options
            ].forEach(
                option => {

                    if (
                        option.value === ''
                    ) {
                        option.hidden =
                            false;

                        return;
                    }


                    option.hidden =
                        option.dataset.company
                        !==
                        companyId;

                }
            );

        };


    company.addEventListener(
        'change',
        () => {

            master.value =
                '';

            filterMaster();

        }
    );


    master.addEventListener(
        'change',
        () => {

            const option =
                master.options[
                    master.selectedIndex
                ];


            if (
                !option
                ||
                option.value === ''
            ) {
                return;
            }


            company.value =
                option.dataset.company
                ?? '';


            if (account) {
                account.value =
                    option.dataset.account
                    ?? '';
            }


            if (address) {
                address.value =
                    option.dataset.address
                    ?? '';
            }


            if (city) {
                city.value =
                    option.dataset.city
                    ?? '';
            }


            if (country) {
                country.value =
                    option.dataset.country
                    ?? '';
            }


            if (email) {
                email.value =
                    option.dataset.email
                    ?? '';
            }


            if (bank) {
                bank.value =
                    option.dataset.bank
                    ?? '';
            }

        }
    );


    filterMaster();

})();

</script>