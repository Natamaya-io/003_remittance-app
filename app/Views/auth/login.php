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

<div class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6 bg-[#F5F2F2]">
    <div class="w-full max-w-[420px] bg-white border border-gray-200 rounded-2xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.06),0_8px_10px_-6px_rgba(0,0,0,0.04)] p-6 sm:p-8 space-y-6">

        <!-- BRANDING & JUDUL TELANJANG (ZERO-SLOP) -->
        <div class="flex flex-col items-center text-center space-y-2">
            <div class="inline-flex items-center justify-center text-3xl text-gray-900">
                <i class="bi bi-bank2"></i>
            </div>
            <h1 class="font-heading font-semibold text-2xl text-gray-900 tracking-tight">
                RemitGo!
            </h1>
        </div>

        <!-- FORMULIR MASUK -->
        <form
            method="post"
            action="<?= e(url('/login')) ?>"
            class="space-y-4"
            x-data="{ loading: false }"
            @submit="loading = true"
        >
            <?= csrf_field() ?>

            <!-- FIELD USERNAME / EMAIL -->
            <div>
                <label for="loginUsername" class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">
                    Username atau Email
                </label>
                <div class="relative flex items-center">
                    <i class="bi bi-person absolute left-3.5 text-gray-400 pointer-events-none text-base"></i>
                    <input
                        id="loginUsername"
                        type="text"
                        name="login"
                        value="<?= e(old('login', '')) ?>"
                        placeholder="Masukkan username atau email"
                        autocomplete="username"
                        required
                        autofocus
                        class="w-full h-11 pl-10 pr-4 text-sm text-gray-900 bg-white border border-gray-300 rounded-xl focus:outline-none focus:border-[#315B8C] focus:ring-2 focus:ring-[#315B8C]/15 transition-colors placeholder:text-gray-400 font-sans"
                    >
                </div>
            </div>

            <!-- FIELD PASSWORD DENGAN VISIBILITY TOGGLE TELANJANG -->
            <div x-data="{ showPass: false }">
                <label for="loginPassword" class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">
                    Password
                </label>
                <div class="relative flex items-center">
                    <i class="bi bi-lock absolute left-3.5 text-gray-400 pointer-events-none text-base"></i>
                    <input
                        :type="showPass ? 'text' : 'password'"
                        type="password"
                        id="loginPassword"
                        name="password"
                        placeholder="Masukkan password akun"
                        autocomplete="current-password"
                        required
                        class="w-full h-11 pl-10 pr-11 text-sm text-gray-900 bg-white border border-gray-300 rounded-xl focus:outline-none focus:border-[#315B8C] focus:ring-2 focus:ring-[#315B8C]/15 transition-colors placeholder:text-gray-400 font-sans"
                    >
                    <button
                        type="button"
                        @click="showPass = !showPass"
                        class="absolute right-3 p-1.5 text-gray-400 hover:text-gray-700 transition-colors focus:outline-none"
                        :aria-label="showPass ? 'Sembunyikan password' : 'Tampilkan password'"
                    >
                        <i :class="showPass ? 'bi bi-eye-slash' : 'bi bi-eye'" class="bi bi-eye text-base"></i>
                    </button>
                </div>
            </div>

            <!-- TOMBOL AKSI UTAMA (.btn-primary RESMI GEMINI.MD SECTION 13 & GLOBAL AI SPEC) -->
            <div class="pt-2">
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
