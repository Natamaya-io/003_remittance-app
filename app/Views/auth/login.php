<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| RemitGo! Login View
|--------------------------------------------------------------------------
| Desain Gamified Asphalt murni berlandaskan GEMINI.md:
| - 4 Warna Kunci Proyek: #F5F2F2 (Canvas Base), #315B8C (Deep Brand Blue),
|   #F2765E (Coral Accent), #413333 (Asphalt Brown)
| - Zero-Gradient: 100% Solid Colors, tanpa glow radial atau efek semu
| - Zero-Transform: Bebas pergeseran liar, active micro-press translateY(1px)
| - Zero-Slop: Peniadaan lencana palsu, status kapsul, dan deskripsi statis
| - Universal Naked Monochrome Icon: Ikon telanjang monokrom murni
| - The Initiation Button (.btn-primary): Tombol solid Deep Brand Blue
|--------------------------------------------------------------------------
*/
?>

<div class="h-screen max-h-screen w-full flex flex-col lg:flex-row bg-[#F5F2F2] overflow-hidden">

    <!-- KOLOM KIRI: VISUAL ART PENUH (KROP OTOMATIS FIT VIEWPORT) -->
    <div class="w-full lg:w-1/2 h-44 sm:h-56 lg:h-full lg:min-h-screen relative overflow-hidden bg-slate-900 flex-shrink-0">
        <picture class="w-full h-full block">
            <source srcset="<?= e(url('/login_art.avif')) ?>" type="image/avif">
            <source srcset="<?= e(url('/login_art.webp')) ?>" type="image/webp">
            <img
                src="<?= e(url('/login_art.jpeg')) ?>"
                alt="RemitGo! Visual Art"
                class="w-full h-full object-cover object-center select-none"
                loading="eager"
                width="1696"
                height="2528"
            >
        </picture>

        <!-- PEMISAH BERGELOMBANG DESKTOP (VERTIKAL DI TEPI KANAN ART) -->
        <svg class="hidden lg:block absolute inset-y-0 -right-[1px] h-full w-14 lg:w-16 xl:w-20 2xl:w-24 pointer-events-none z-10 text-white" viewBox="0 0 100 1000" preserveAspectRatio="none" aria-hidden="true">
            <path fill="currentColor" d="M 100 0 L 100 1000 L 40 1000 C 15 900, 80 850, 45 750 C 10 650, 75 550, 35 450 C 5 350, 70 250, 40 150 C 15 80, 60 40, 30 0 Z"></path>
        </svg>

        <!-- PEMISAH BERGELOMBANG MOBILE (HORIZONTAL DI TEPI BAWAH ART) -->
        <svg class="block lg:hidden absolute -bottom-[1px] inset-x-0 w-full h-6 sm:h-8 pointer-events-none z-10 text-white" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
            <path fill="currentColor" d="M 0 120 L 1200 120 L 1200 45 C 1050 85, 900 15, 750 50 C 600 85, 450 15, 300 50 C 150 85, 50 20, 0 45 Z"></path>
        </svg>
    </div>

    <!-- KOLOM KANAN: WORKSPACE FORMULIR MASUK (FIT VIEWPORT NON-SCROLLABLE & HIGH ACCESSIBILITY) -->
    <div class="w-full lg:w-1/2 h-full lg:h-screen max-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8 bg-white overflow-hidden flex-1">
        <div class="max-w-[420px] sm:max-w-[460px] w-full mx-auto my-auto space-y-5 sm:space-y-6">

            <!-- BRANDING LOGO RESMI (REMITGO!) -->
            <div class="flex flex-col items-center justify-center text-center">
                <h1 class="m-0 leading-none inline-flex items-center justify-center">
                    <img
                        src="<?= e(url('/remitgo.png')) ?>"
                        alt="RemitGo!"
                        class="h-12 sm:h-14 lg:h-16 w-auto max-w-[200px] sm:max-w-[240px] object-contain select-none"
                        loading="eager"
                        width="1720"
                        height="744"
                    >
                    <span class="sr-only">RemitGo!</span>
                </h1>
            </div>

            <!-- FORMULIR MASUK (RAMAH LANSIA / HIGH ACCESSIBILITY) -->
            <form
                method="post"
                action="<?= e(url('/login')) ?>"
                class="space-y-4 sm:space-y-5"
                x-data="{ loading: false }"
                @submit="loading = true"
            >
                <?= csrf_field() ?>

                <!-- FIELD USERNAME / EMAIL -->
                <div>
                    <label for="loginUsername" class="block text-sm sm:text-base font-semibold text-gray-800 mb-1.5 font-sans">
                        Username atau Email
                    </label>
                    <div class="relative flex items-center">
                        <i class="bi bi-person absolute left-4 text-gray-400 pointer-events-none text-xl"></i>
                        <input
                            id="loginUsername"
                            type="text"
                            name="login"
                            value="<?= e(old('login', '')) ?>"
                            placeholder="Masukkan username atau email"
                            autocomplete="username"
                            required
                            autofocus
                            class="custom-form-input custom-form-input-lg has-leading-icon"
                        >
                    </div>
                </div>

                <!-- FIELD PASSWORD DENGAN VISIBILITY TOGGLE TELANJANG -->
                <div x-data="{ showPass: false }">
                    <label for="loginPassword" class="block text-sm sm:text-base font-semibold text-gray-800 mb-1.5 font-sans">
                        Password
                    </label>
                    <div class="relative flex items-center">
                        <i class="bi bi-lock absolute left-4 text-gray-400 pointer-events-none text-xl"></i>
                        <input
                            :type="showPass ? 'text' : 'password'"
                            type="password"
                            id="loginPassword"
                            name="password"
                            placeholder="Masukkan password akun"
                            autocomplete="current-password"
                            required
                            class="custom-form-input custom-form-input-lg has-leading-icon has-trailing-action"
                        >
                        <!-- IKON MATA: TAMPILKAN PASSWORD (SAAT PASSWORD TERSEMBUNYI) -->
                        <i
                            x-show="!showPass"
                            @click="showPass = true"
                            @keydown.enter.prevent="showPass = true"
                            @keydown.space.prevent="showPass = true"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye absolute right-4 text-gray-400 hover:text-gray-700 cursor-pointer text-xl select-none leading-none focus:outline-none transition-colors p-1"
                            aria-label="Tampilkan password"
                            title="Tampilkan password"
                        ></i>

                        <!-- IKON MATA TERCOROT: SEMBUNYIKAN PASSWORD (SAAT PASSWORD TERLIHAT) -->
                        <i
                            x-show="showPass"
                            x-cloak
                            @click="showPass = false"
                            @keydown.enter.prevent="showPass = false"
                            @keydown.space.prevent="showPass = false"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye-slash absolute right-4 text-gray-400 hover:text-gray-700 cursor-pointer text-xl select-none leading-none focus:outline-none transition-colors p-1"
                            aria-label="Sembunyikan password"
                            title="Sembunyikan password"
                        ></i>
                    </div>
                </div>

                <!-- TOMBOL AKSI UTAMA (.btn-primary-lg RESMI GEMINI.MD SECTION 13 & 17.5) -->
                <div class="pt-2 sm:pt-2.5">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="btn-primary btn-primary-lg w-full justify-center h-[50px] px-6 font-semibold text-base sm:text-lg leading-none text-white font-cta rounded-[11px] shadow-[inset_0_1px_0_rgba(255,255,255,0.32),inset_0_-1px_0_rgba(0,0,0,0.16),0_1px_2px_rgba(0,0,0,0.55)] [text-shadow:0_1px_1px_rgba(0,0,0,0.2)] hover:brightness-110 active:translate-y-[1px] active:brightness-95 active:shadow-[inset_0_1px_2px_rgba(0,0,0,0.35)] disabled:opacity-75 disabled:pointer-events-none"
                        id="submitLoginBtn"
                    >
                        <span x-show="!loading" class="inline-flex items-center gap-2.5">
                            <span>Masuk ke Dashboard</span>
                            <i class="bi bi-arrow-right text-lg sm:text-xl"></i>
                        </span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2.5">
                            <i class="bi bi-arrow-repeat animate-spin text-lg sm:text-xl"></i>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
