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
    </div>

    <!-- KOLOM KANAN: WORKSPACE FORMULIR MASUK (FIT VIEWPORT NON-SCROLLABLE) -->
    <div class="w-full lg:w-1/2 h-full lg:h-screen max-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8 bg-white overflow-hidden flex-1">
        <div class="max-w-[380px] sm:max-w-[400px] w-full mx-auto my-auto space-y-4 sm:space-y-5">

            <!-- BRANDING LOGO RESMI (REMITGO!) -->
            <div class="flex flex-col items-center justify-center text-center">
                <h1 class="m-0 leading-none inline-flex items-center justify-center">
                    <img
                        src="<?= e(url('/remitgo.png')) ?>"
                        alt="RemitGo!"
                        class="h-10 sm:h-12 w-auto max-w-[180px] sm:max-w-[200px] object-contain select-none"
                        loading="eager"
                        width="1720"
                        height="744"
                    >
                    <span class="sr-only">RemitGo!</span>
                </h1>
            </div>

            <!-- FORMULIR MASUK -->
            <form
                method="post"
                action="<?= e(url('/login')) ?>"
                class="space-y-3.5 sm:space-y-4"
                x-data="{ loading: false }"
                @submit="loading = true"
            >
                <?= csrf_field() ?>

                <!-- FIELD USERNAME / EMAIL -->
                <div>
                    <label for="loginUsername" class="block text-xs font-semibold text-gray-700 mb-1 font-sans">
                        Username atau Email
                    </label>
                    <div class="relative flex items-center">
                        <i class="bi bi-person absolute left-3 text-gray-400 pointer-events-none text-base"></i>
                        <input
                            id="loginUsername"
                            type="text"
                            name="login"
                            value="<?= e(old('login', '')) ?>"
                            placeholder="Masukkan username atau email"
                            autocomplete="username"
                            required
                            autofocus
                            class="custom-form-input has-leading-icon"
                        >
                    </div>
                </div>

                <!-- FIELD PASSWORD DENGAN VISIBILITY TOGGLE TELANJANG -->
                <div x-data="{ showPass: false }">
                    <label for="loginPassword" class="block text-xs font-semibold text-gray-700 mb-1 font-sans">
                        Password
                    </label>
                    <div class="relative flex items-center">
                        <i class="bi bi-lock absolute left-3 text-gray-400 pointer-events-none text-base"></i>
                        <input
                            :type="showPass ? 'text' : 'password'"
                            type="password"
                            id="loginPassword"
                            name="password"
                            placeholder="Masukkan password akun"
                            autocomplete="current-password"
                            required
                            class="custom-form-input has-leading-icon has-trailing-action"
                        >
                        <button
                            type="button"
                            @click="showPass = !showPass"
                            class="absolute right-2.5 p-1 text-gray-400 hover:text-gray-700 transition-colors focus:outline-none"
                            :aria-label="showPass ? 'Sembunyikan password' : 'Tampilkan password'"
                        >
                            <i :class="showPass ? 'bi bi-eye-slash' : 'bi bi-eye'" class="bi bi-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- TOMBOL AKSI UTAMA (.btn-primary RESMI GEMINI.MD SECTION 13 & GLOBAL AI SPEC) -->
                <div class="pt-1.5 sm:pt-2">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="btn-primary w-full justify-center h-10 px-4 font-medium text-[15px] leading-none text-white font-cta rounded-[9px] shadow-[inset_0_1px_0_rgba(255,255,255,0.32),inset_0_-1px_0_rgba(0,0,0,0.16),0_1px_2px_rgba(0,0,0,0.55)] [text-shadow:0_1px_1px_rgba(0,0,0,0.2)] hover:brightness-110 active:translate-y-[1px] active:brightness-95 active:shadow-[inset_0_1px_2px_rgba(0,0,0,0.35)] disabled:opacity-75 disabled:pointer-events-none"
                        id="submitLoginBtn"
                    >
                        <span x-show="!loading" class="inline-flex items-center gap-2">
                            <span>Masuk ke Dashboard</span>
                            <i class="bi bi-arrow-right"></i>
                        </span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                            <i class="bi bi-arrow-repeat animate-spin"></i>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
