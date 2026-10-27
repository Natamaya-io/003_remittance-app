<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| RemitGo! Login
|--------------------------------------------------------------------------
*/
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