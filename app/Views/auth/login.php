<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| RemitGo! Login
|--------------------------------------------------------------------------
| Version 35
|--------------------------------------------------------------------------
*/

$fxData = isset($fx) && is_array($fx)
    ? $fx
    : [];

$fxBase = strtoupper(
    trim(
        (string) (
            $fxData['base']
            ?? 'IDR'
        )
    )
);

$rawRates = is_array(
    $fxData['rates']
    ?? null
)
    ? $fxData['rates']
    : [];

$currencyMeta = [

    'USD' => [
        'flag' => '🇺🇸',
        'flag_code' => 'us',
        'name' => 'Dolar Amerika',
    ],

    'EUR' => [
        'flag' => '🇪🇺',
        'flag_code' => 'eu',
        'name' => 'Euro',
    ],

    'CNY' => [
        'flag' => '🇨🇳',
        'flag_code' => 'cn',
        'name' => 'Yuan Tiongkok',
    ],

    'AED' => [
        'flag' => '🇦🇪',
        'flag_code' => 'ae',
        'name' => 'Dirham UEA',
    ],
];

$wantedCurrencies = [
    'USD',
    'EUR',
    'CNY',
    'AED',
];

$indexedRates = [];

foreach ($rawRates as $key => $row) {

    if (is_numeric($row)) {

        $row = [
            'quote_currency' => is_string($key)
                ? $key
                : '',

            'display_rate' => (float) $row,
        ];
    }

    if (!is_array($row)) {
        continue;
    }

    $code = strtoupper(
        trim(
            (string) (
                $row['quote_currency']
                ?? $row['currency']
                ?? $row['currency_code']
                ?? $row['code']
                ?? (
                    is_string($key)
                        ? $key
                        : ''
                )
            )
        )
    );

    $code = str_replace(
        ['/', '-', '_', ' '],
        '',
        $code
    );

    if (strlen($code) === 6) {

        if (str_starts_with($code, 'IDR')) {

            $code = substr(
                $code,
                3,
                3
            );

        } elseif (str_ends_with($code, 'IDR')) {

            $code = substr(
                $code,
                0,
                3
            );
        }
    }

    if (
        !in_array(
            $code,
            $wantedCurrencies,
            true
        )
    ) {
        continue;
    }

    $displayRate = (float) (
        $row['display_rate']
        ?? $row['mid']
        ?? $row['idr_rate']
        ?? $row['value']
        ?? $row['price']
        ?? 0
    );

    if ($displayRate <= 0) {

        $providerRate = (float) (
            $row['rate']
            ?? 0
        );

        if ($providerRate > 0) {

            $displayRate = $fxBase === 'IDR'
                ? 1 / $providerRate
                : $providerRate;
        }
    }

    $change = (float) (
        $row['change_percent']
        ?? 0
    );

    $direction = trim(
        (string) (
            $row['direction']
            ?? ''
        )
    );

    if (
        !in_array(
            $direction,
            [
                'up',
                'down',
                'flat',
            ],
            true
        )
    ) {

        $direction = $change > 0
            ? 'up'
            : (
                $change < 0
                    ? 'down'
                    : 'flat'
            );
    }

    $indexedRates[$code] = [

        'code' => $code,

        'rate' => $displayRate,

        'change' => $change,

        'direction' => $direction,

        'available' => $displayRate > 0,
    ];
}

$loginRates = [];

foreach ($wantedCurrencies as $code) {

    $rate = $indexedRates[$code]
        ?? [

            'code' => $code,

            'rate' => 0.0,

            'change' => 0.0,

            'direction' => 'flat',

            'available' => false,
        ];

    $rate['flag'] =
        $currencyMeta[$code]['flag'];

    $rate['flag_code'] =
        $currencyMeta[$code]['flag_code'];

    $rate['name'] =
        $currencyMeta[$code]['name'];

    $loginRates[] = $rate;
}

$flagSprite = asset('img/flags.svg');

$lastUpdated = '-';

$updatedAt = trim(
    (string) (
        $fxData['updated_at']
        ?? $fxData['rate_date']
        ?? ''
    )
);

if ($updatedAt !== '') {

    $timestamp = strtotime($updatedAt);

    if ($timestamp !== false) {

        $lastUpdated = date(
            'd/m/Y',
            $timestamp
        );
    }
}
?>

<div
    class="remit-login"
    data-login-page
>

    <!-- =========================================================
         LOGIN PANEL
    ========================================================== -->

    <section class="login-panel">

        <span
            class="panel-light panel-light-one"
            aria-hidden="true"
        ></span>

        <span
            class="panel-light panel-light-two"
            aria-hidden="true"
        ></span>

        <main class="login-panel-content">

            <!-- TITLE -->

            <h2>
                Masuk
            </h2>

            <!-- =================================================
                 FORM
            ================================================== -->

            <form
                method="post"
                action="<?= e(url('/login')) ?>"
                class="login-form"
                data-login-form
            >

                <?= csrf_field() ?>

                <!-- LOGIN -->

                <label class="form-field">

                    <span>
                        Username atau email
                    </span>

                    <div class="field-control">

                        <i class="bi bi-person"></i>

                        <input
                            type="text"
                            name="login"
                            value="<?= e(old('login', '')) ?>"
                            placeholder="Masukkan username atau email"
                            autocomplete="username"
                            required
                            autofocus
                        >

                        <span class="field-success">

                            <i class="bi bi-check-lg"></i>

                        </span>

                    </div>

                </label>

                <!-- PASSWORD -->

                <label class="form-field">

                    <span>
                        Password
                    </span>

                    <div class="field-control">

                        <i class="bi bi-lock"></i>

                        <input
                            id="loginPassword"
                            type="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle
                            data-target="loginPassword"
                            aria-label="Tampilkan password"
                        >

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </label>

                <button
                    type="submit"
                    class="submit-login"
                    data-login-submit
                >

                    <span>
                        Masuk ke Dashboard
                    </span>

                    <i class="bi bi-arrow-right"></i>

                </button>

            </form>

            <!-- =================================================
                 FX
            ================================================== -->

            <section class="fx-box">

                <header class="fx-box-heading">

                    <div>

                        <h3>
                            IDR Market Monitor
                        </h3>

                    </div>

                    <span class="bi-source">

                        <i></i>

                        Bank Indonesia

                    </span>

                </header>

                <div class="fx-cards">

                    <?php foreach ($loginRates as $rate): ?>

                        <?php

                        $direction = (string) (
                            $rate['direction']
                            ?? 'flat'
                        );

                        $change = (float) (
                            $rate['change']
                            ?? 0
                        );

                        $symbol = $direction === 'up'
                            ? '↗'
                            : (
                                $direction === 'down'
                                    ? '↘'
                                    : '→'
                            );

                        ?>

                        <article class="fx-item">

                            <div class="fx-top">

                                <div class="currency-name">

                                    <span class="currency-flag">
                                        <svg viewBox="0 0 64 48" aria-hidden="true" focusable="false">
                                            <use href="<?= e($flagSprite . '#flag-' . ($rate['flag_code'] ?? 'world')) ?>"></use>
                                        </svg>
                                    </span>

                                    <div>

                                        <strong>

                                            <?= e(
                                                (string)
                                                $rate['code']
                                            ) ?>

                                        </strong>

                                        <small>

                                            <?= e(
                                                (string)
                                                $rate['name']
                                            ) ?>

                                        </small>

                                    </div>

                                </div>

                                <span
                                    class="
                                        currency-change
                                        <?= e($direction) ?>
                                    "
                                >

                                    <?= e($symbol) ?>

                                    <?= number_format(
                                        abs($change),
                                        3,
                                        ',',
                                        '.'
                                    ) ?>%

                                </span>

                            </div>

                            <div class="currency-price">

                                <span>

                                    1
                                    <?= e(
                                        (string)
                                        $rate['code']
                                    ) ?>
                                    =

                                </span>

                                <strong>

                                    <?php if (
                                        !empty(
                                            $rate['available']
                                        )
                                    ): ?>

                                        <small>
                                            Rp
                                        </small>

                                        <?= number_format(
                                            (float)
                                            $rate['rate'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </strong>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

                <footer class="fx-bottom">

                    <span>

                        <i class="bi bi-broadcast-pin"></i>

                        Kurs Transaksi BI

                    </span>

                    <span>

                        Update

                        <strong>
                            <?= e($lastUpdated) ?>
                        </strong>

                    </span>

                </footer>

            </section>

        </main>

    </section>

</div>

<script>
(() => {

    'use strict';

    const page =
        document.querySelector(
            '[data-login-page]'
        );

    if (!page) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | PASSWORD TOGGLE
    |--------------------------------------------------------------------------
    */

    page.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-password-toggle]'
                );

            if (!button) {
                return;
            }

            const input =
                document.getElementById(
                    button.dataset.target || ''
                );

            if (!input) {
                return;
            }

            const show =
                input.type === 'password';

            input.type =
                show
                    ? 'text'
                    : 'password';

            button.setAttribute(
                'aria-label',
                show
                    ? 'Sembunyikan password'
                    : 'Tampilkan password'
            );

            const icon =
                button.querySelector('i');

            if (icon) {

                icon.className =
                    show
                        ? 'bi bi-eye-slash'
                        : 'bi bi-eye';
            }

        }
    );

    /*
    |--------------------------------------------------------------------------
    | LOGIN LOADING
    |--------------------------------------------------------------------------
    */

    const form =
        page.querySelector(
            '[data-login-form]'
        );

    const submit =
        page.querySelector(
            '[data-login-submit]'
        );

    form?.addEventListener(
        'submit',
        function () {

            if (!submit) {
                return;
            }

            submit.disabled = true;

            submit.classList.add(
                'is-loading'
            );

            submit.innerHTML = `
                <span>
                    Memproses akses...
                </span>

                <i class="bi bi-arrow-repeat"></i>
            `;

        }
    );

})();
</script>