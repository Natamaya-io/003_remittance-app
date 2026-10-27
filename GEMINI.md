# GEMINI.md - Aturan Terkunci Arsitektur UI & Sistem Desain Global

> **STATUS DOKUMEN: HUKUM MUTLAK & TERKUNCI (STRICT COMPLIANCE)**  
> Dokumen ini adalah konstitusi teknis dan visual untuk repositori **`remittance.raxbyte.com`**. Seluruh agen AI (Antigravity/Gemini) dan pengembang perangkat lunak **MUTLAK WAJIB** mematuhi aturan ini tanpa pengecualian.

> **[HUKUM MUTLAK BATAS LINGKUP BEDAH & ANTI-PERUBAHAN LIAR (STRICT SURGICAL SCOPE)]**  
> Seluruh agen AI dan pengembang **MUTLAK DILARANG** melakukan modifikasi, refaktor, penambahan, penghapusan, atau penyesuaian kode terhadap hal-hal yang **TIDAK SECARA EKSPLISIT DIMINTA** dan **TIDAK DIBUTUHKAN**. Setiap perubahan wajib bersifat intervensi bedah murni (*minimal surgical intervention*) yang terisolasi secara presisi pada target yang diinstruksikan oleh pengguna, dengan nol efek samping sampingan (*zero collateral damage*) terhadap berkas, tata letak, logika bisnis, atau komponen lain yang sudah stabil. Aturan ini **TERKUNCI MUTLAK DAN TIDAK BOLEH DILANGGAR DALAM KONDISI APA PUN**.

> **[HUKUM MUTLAK ANTI-KODE UTILITAS AD-HOC SEMENTARA & WAJIB KUSTOM UI KOMPREHENSIF (THE ZERO AD-HOC QUICK PLACEHOLDER & MANDATORY COMPREHENSIVE CUSTOM UI LAW)]**  
> Setiap kali agen AI atau pengembang merombak, merefaktor, menyusun ulang, atau memperbarui elemen antarmuka (view, formulir, input, tombol, kartu, tabel, dialog, dsb.), AI **MUTLAK DILARANG KERAS** menggunakan kode utilitas ad-hoc sementara (*quick placeholder*) atau rangkaian kelas utility instan yang belum matang dengan dalih "nanti diperbaiki".  
> Setiap perombakan elemen **MUTLAK WAJIB LANGSUNG MENGGUNAKAN KUSTOM UI KOMPREHENSIF** yang telah terstandarisasi, memiliki kelas komponen terpusat di `@layer components` atau helper resmi, mematuhi skala kenyamanan visual 125% W3C (`html { font-size: 20px; }`), memiliki geometri proporsional yang selaras 1:1 dengan elemen pendamping (seperti tinggi 40px tombol `.btn-primary` dan search bar), beroperasi dengan 4 warna solid kunci (#F5F2F2, #F2765E, #315B8C, #413333), serta memiliki elevasi taktil dan interaktivitas yang tuntas sejak detik pertama implementasi.

---

## 1. Identitas Proyek & Hukum Amnesti Proyek Warisan (Legacy Amnesty)

1. **Lingkungan & Stack Proyek:**
   - Aplikasi: Perbankan & Remitansi Operasional (`003_remittance-app`).
   - Branding Resmi: **RemitGo!**
   - Stack Dasar: **PHP 8.2+**, **MariaDB**, **TailwindCSS (v3.4+)**, **Preline UI (v2.7+)**, **Alpine.js**, dan **Bootstrap Icons**.
   - **Hukum Amnesti Proyek Lama:** Proyek ini beroperasi di atas stack warisan yang telah teruji stabil. DILARANG MEROMBAK struktur ini ke stack proyek baru (seperti Rust, Axum, SolidJS, atau PostgreSQL) kecuali ada instruksi eksplisit.
2. **Hukum Mutlak Antislop (Gamified Asphalt UI):**
   - **Tipografi:** Wajib menggunakan **DM Sans** (Headings, Tombol/CTA) dan **Roboto** (Body text, data tabel, metadata).
   - **Skala Visual 125% Terstandarisasi W3C (Native 125% Root REM Scaling Law):** Seluruh antarmuka aplikasi beroperasi dengan skala kenyamanan visual 125% murni berbasis standar W3C (`html { font-size: 20px; }` pada layar desktop $\ge 1024\text{px}$, `18px` pada tablet, dan `16px` pada mobile). Properti CSS non-standar `zoom: 1.25;` **DIHARAMKAN SECARA MUTLAK** demi menjamin akurasi 100% sistem koordinat JavaScript (Popper.js, Chart.js, modal backdrop, scrollbar) serta keseragaman ukuran seluruh halaman antarmuka.
   - **Diet Bold:** Dilarang menggunakan font-weight ultra-tebal (800/900). Maksimum penekanan adalah `font-weight: 600` (`font-semibold` / `.font-bold`).
   - **Bebas Gradien & Skema Warna Solid Murni (Solid Color Discipline & Absolute Zero-Alien Color Law):** Seluruh elemen UI utama (body canvas, hero, card, panel, alert, dropdown, datepicker, tombol, badge, hingga grafik canvas) **MUTLAK WAJIB** menggunakan warna solid murni berlandaskan **4 Warna Kunci Proyek: 1. #F5F2F2 (Asphalt Off-White Base), 2. #F2765E (Vibrant Coral Accent), 3. #315B8C (Deep Brand Blue), 4. #413333 (Deep Asphalt Brown)**. **DILARANG KERAS MENGGUNAKAN WARNA LAIN DI LUAR SKEMA WARNA YANG SUDAH DITENTUKAN INI SECARA MUTLAK**. Dilarang keras menggunakan `linear-gradient`, `radial-gradient`, `conic-gradient`, atau efek kaca `backdrop-filter: blur(...)` / opasitas tembus pandang semu (lihat Bagian 11 untuk aturan terkunci lengkap).
   - **Bebas Transform (Zero-Transform State):** State `:hover`, `:active`, dan `:focus` DILARANG menggunakan efek `transform: translateY(...)`, `scale(...)`, atau pergeseran posisi liar, KECUALI tactile micro-press `translateY(1px)` khusus pada tombol utama (`.btn-primary`) dan tombol aksi resmi sesuai spesifikasi fisika AI global (lihat Bagian 13 untuk aturan terkunci lengkap). Animasi transisi hanya boleh memanipulasi `opacity`, `background-color`, `border-color`, dan `box-shadow`.
   - **Peniadaan Deskripsi Statis & Tooltip Mutlak (Universal Description-to-Tooltip Discipline):** Seluruh deskripsi, keterangan panduan, catatan bantuan, dan subjudul fungsional pada panel, header, card, tabel, maupun formulir **DILARANG KERAS** dirender sebagai teks atau paragraf statis (`<p>`, `<small>`, `.description`). Seluruh deskripsi **MUTLAK WAJIB** dikonversi menjadi tooltip on-demand berbasis hover pada ikon info `(i)` (lihat Bagian 12 untuk aturan terkunci lengkap).
   - **Peniadaan Wrapper Ikon & Monokrom Mutlak (Universal Naked Monochrome Icon Discipline):** Seluruh ikon antarmuka di seluruh aplikasi **MUTLAK DILARANG** menggunakan wrapper/wadah pembungkus dekoratif berlatar warna dan **MUTLAK HANYA DIPERBOLEHKAN BERWARNA HITAM ATAU PUTIH**. Ikon wajib berdiri telanjang berdampingan langsung dengan konten tanpa pembungkus buatan (lihat Bagian 14 untuk aturan terkunci lengkap).
   - **Scrollbar Kustom Global Mutlak (Universal Custom Scrollbar Law):** Seluruh scrollbar browser bawaan sistem operasi (native Windows/macOS/Linux) yang tebal, kaku, dan abu-abu semen kotak **DIHARAMKAN SECARA MUTLAK**. Seluruh antarmuka (window viewport `html`, `body`, modal, popover, dropdown, hingga tabel scroll horizontal) **MUTLAK WAJIB** menggunakan sistem scrollbar kustom global ramping (7px), sudut membulat penuh (*capsule thumb*), track transparan, dengan warna thumb netral `#cbd5e1` dan interaksi hover solid *Deep Brand Blue* (`#315B8C`) (lihat Bagian 15 untuk aturan terkunci lengkap).
   - **Sistem Popup Notifikasi Toast Kustom Mutlak (Universal Custom Toast Notification Law):** Seluruh notifikasi popup, pesan umpan balik tindakan, konfirmasi aksi, maupun peringatan sistem **MUTLAK HARUS MENGGUNAKAN TOAST DENGAN UI KUSTOM TANPA STYLE BAWAAN BROWSER**. Penggunaan notifikasi browser native (seperti alert dialog abu-abu OS, Web Notification bubble bawaan sistem operasi, atau `window.alert()` / `window.confirm()`) **DIHARAMKAN SECARA MUTLAK**. Toast beroperasi secara mengambang (*floating viewport stack*), sudut membulat elegan (`rounded-xl` / `rounded-2xl`), border solid 1px, soft elevation, 4 warna solid varian status resmi, tombol tutup dismissible kustom, auto-dismiss dengan timer pause saat hover, dan transisi Alpine.js halus tanpa efek bounce atau layout shift (lihat Bagian 16 untuk aturan terkunci lengkap).
   - **Sistem Form Input Teks Kustom Mutlak (Universal Custom Form Input Law):** Seluruh elemen input form teks (`<input type="text">`, `type="password"`, `type="email"`, dsb.) **MUTLAK WAJIB** menggunakan kelas terpusat `.custom-form-input` dengan ketinggian presisi 40px (selaras 1:1 dengan `.btn-primary` dan `.custom-search-input`), sudut membulat modular `rounded-[9px]`, latar permukaan well `#F5F2F2` dengan bayangan inset mikro, batas kontur `#d1d5db`, transisi fokus bersih ke putih dengan border solid `#315B8C` (*Deep Brand Blue*), serta jarak ikon kiri rapat alami (38px padding kiri, celah 8-10px) (lihat Bagian 17 untuk aturan terkunci lengkap).
   - **Anti-Kode Utilitas Ad-Hoc Sementara & Wajib Kustom UI Komprehensif (Zero Quick Placeholder Law):** DILARANG KERAS menggunakan rangkaian kelas utilitas tambal sulam instan (*ad-hoc quick placeholder*) saat merombak antarmuka. Setiap perombakan elemen UI MUTLAK WAJIB langsung mengimplementasikan Kustom UI Komprehensif dengan geometri modular terstandarisasi (misal tinggi 40px, radius 9px, surface depth #F5F2F2, spasi rapat), terintegrasi ke sistem komponen global, dan tahan terhadap penskalaan visual root 20px (lihat Bagian 18 untuk aturan terkunci lengkap).

---

## 2. Aturan Terkunci: Sistem Alert Global (Global Alert Specification)

Semua notifikasi flash, pesan peringatan, konfirmasi aksi, dan status sistem **WAJIB** menggunakan sistem alert global yang terstandarisasi.

### 2.1. Standar 4 Varian Warna Solid

| Varian | CSS Class | Background | Border | Teks | Ikon Resmi |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Success** | `.alert-success` | `#ecfdf5` (`emerald-50`) | `#a7f3d0` (`emerald-200`) | `#065f46` (`emerald-900`) | `<i class="bi bi-check-circle-fill"></i>` (`#059669`) |
| **Danger / Error** | `.alert-danger`, `.alert-error` | `#fef2f2` (`red-50`) | `#fecaca` (`red-200`) | `#991b1b` (`red-900`) | `<i class="bi bi-x-circle-fill"></i>` (`#dc2626`) |
| **Warning** | `.alert-warning` | `#fffbeb` (`amber-50`) | `#fde68a` (`amber-200`) | `#92400e` (`amber-900`) | `<i class="bi bi-exclamation-triangle-fill"></i>` (`#d97706`) |
| **Info** | `.alert-info` | `#eff6ff` (`blue-50`) | `#bfdbfe` (`blue-200`) | `#1e40af` (`blue-900`) | `<i class="bi bi-info-circle-fill"></i>` (`#2563eb`) |

### 2.2. Anatomi DOM Alert Terkunci

Setiap blok alert wajib mengikuti struktur hierarki berikut:
```html
<div
    class="alert alert-success"
    role="alert"
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="alert-content">
        <span class="alert-icon">
            <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="alert-body">
            <!-- Judul opsional jika ada konteks khusus -->
            <strong class="alert-title">Berhasil!</strong>
            <div class="alert-message">Data laporan batch harian berhasil disimpan.</div>
        </div>
    </div>

    <!-- Tombol Tutup (Dismissible) -->
    <button
        class="alert-close"
        type="button"
        @click="show = false"
        aria-label="Tutup notifikasi"
    >
        <i class="bi bi-x text-lg leading-none"></i>
    </button>
</div>
```

### 2.3. Aturan Teknis Alert & Sistem Dialog Kustom (Anti-Native Alert & Confirm)
1. **Transisi Bersih:** Transisi Alpine.js `x-transition:leave` HANYA memanipulasi `opacity`. Dilarang keras menyertakan `scale-95` atau translasi yang menyebabkan efek zoom/lompatan layout.
2. **Ikon Dinamis:** Setiap jenis alert wajib memiliki ikon yang mewakili statusnya secara spesifik (jangan hardcode `bi-info-circle-fill` untuk semua status).
3. **Elevasi Halus:** Border solid 1px dengan sudut membulat 14px (`rounded-xl` / `.alert`), bayangan mikro `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04)`.
4. **Pemusnahan Dialog & Popup Notifikasi Native Browser (Anti-window.confirm / Anti-window.alert / Anti-Native Popup):**
   - Dilarang keras memicu `window.confirm()` atau `window.alert()` browser abu-abu.
   - Semua form yang membutuhkan konfirmasi wajib menggunakan atribut `data-confirm="Pesan konfirmasi..."` yang dicegat otomatis oleh handler global `custom-dialogs.js`.
   - Untuk aksi JavaScript terprogram, wajib menggunakan `await window.remitConfirm(pesan, opsi)` atau `window.remitAlert(pesan, opsi)`.
   - Seluruh notifikasi popup sistem dan umpan balik aksi wajib menggunakan sistem toast UI kustom mengambang (lihat Bagian 16).
   - Modal konfirmasi dan peringatan dirender terpusat di `app/Views/layouts/app.php` dengan komponen Alpine `remitGlobalDialogs` berdesain Asphalt Gamified (sudut membulat, tombol aksi solid, dan backdrop blur bersih).

---

## 3. Aturan Terkunci: Sistem Datepicker Kustom Global (Anti-Native Datepicker Law)

Penggunaan `<input type="date">` dan `<input type="month">` native browser memicu kalender Shadow DOM sistem operasi yang kaku dan tidak konsisten lintas perangkat. Oleh karena itu, seluruh aplikasi RemitGo! **MUTLAK WAJIB** menggunakan custom datepicker kalender Alpine.js.

### 3.1. Arsitektur Komponen Datepicker Kustom
- Nilai tersimpan secara murni di dalam `<input type="hidden" :name="name" :value="value">` sehingga pengiriman form (GET dan POST) 100% kompatibel dan identik dengan input native.
- Trigger berupa tombol kustom `.form-datepicker` yang menampilkan label tanggal terformat (contoh: `26 Okt 2026` atau `Agustus 2026`) beserta ikon Bootstrap Icons (`bi-calendar3` atau `bi-calendar-month`).
- Popover kalender kustom (`.dropdown-menu`) menyediakan navigasi bulan/tahun, kisi hari 7 kolom, highlight tanggal aktif, status hari ini, serta shortcut tombol "Hari ini" / "Bulan ini" dan "Hapus".

### 3.2. Penggunaan via Helper PHP Resmi
Setiap view yang membutuhkan input tanggal atau bulan **WAJIB** menggunakan helper `render_datepicker()` di `app/helpers.php`:
```php
<!-- Mode Tanggal Tunggal -->
<?= render_datepicker('date_from', (string) ($filters['date_from'] ?? ''), [
    'placeholder' => 'Pilih tanggal mulai',
    'required'    => false,
]) ?>

<!-- Mode Bulan & Tahun -->
<?= render_datepicker('month', (string) ($filters['month'] ?? ''), [
    'mode'        => 'month',
    'placeholder' => 'Pilih bulan report',
    'required'    => true,
]) ?>
```

---

## 4. Aturan Terkunci: Sistem Dropdown Global (Global Dropdown Specification)

Sistem dropdown mendukung **Preline UI (`hs-dropdown`)** dan **Alpine.js (`x-data="{ open: false }"`)**. Keduanya berbagi standar geometri dan visual yang sama.

### 4.1. Hukum Visibilitas Dropdown & Anti-Display Conflict
- **Dilarang Keras:** Menetapkan `.dropdown-menu { display: none; }` secara global di stylesheet CSS tanpa selector kelas pembuka (seperti `.open`), karena akan menolak `x-show` milik Alpine.js saat inline style dihapus.
- **Standar Bulletproof:** Seluruh menu dropdown Alpine.js wajib menyertakan kelas Tailwind `:class="{ 'block': open, 'hidden': !open }"` dan `x-cloak` selain `x-show="open"`, agar dropdown terjamin muncul seketika saat tombol ditekan.

### 4.2. Anatomi 4 Lapisan Dropdown (Geometri & Fisika UI)
1. **Trigger Toggle (`.dropdown-toggle`, `.hs-dropdown-toggle`):**
   - Tinggi 38px, padding horizontal 14px, radius 10px, border solid `#e5e7eb`, background `#ffffff`.
   - Focus ring: `border-color: #1f6bf0`, ring `rgba(31, 107, 240, 0.15)`.
   - Indikator Chevron: Ikon `<i class="bi bi-chevron-down"></i>` dengan rotasi 180° otomatis saat menu terbuka (`transform: rotate(180deg) !important; transition: transform 0.2s;`).
2. **Menu Panel (`.dropdown-menu`, `.hs-dropdown-menu`):**
   - Background solid `#ffffff`, border `1px solid #e5e7eb`, radius 14px (`rounded-xl` / `rounded-2xl`).
   - Padding container: `p-1.5` (`padding: 6px;`). Min-width: `200px`.
   - Bayangan: `box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);`.
   - Z-Index: `z-50` (atau `z-[60]` di area topbar). Bebas translasi atau scale pop.
3. **Menu Items (`.dropdown-item`, `.hs-dropdown-item`):**
   - Padding: `py-2 px-3` (`8px 12px`), radius 8px (`rounded-lg`).
   - Font: Roboto / DM Sans 13px, font-weight 500.
   - Hover / Focus: Background `#f3f4f6`, teks `#111827`.
   - **Active State (`.active`, `.is-active`):** Background solid `#eff6ff`, teks `#1d4ed8`, font-weight 600.
   - **Danger State (`.danger`, `.text-red-600`):** Teks `#dc2626`, hover background `#fef2f2`, hover teks `#b91c1c`.
   - **Disabled State (`.disabled`, `:disabled`):** Opacity `0.45`, `pointer-events: none`.
4. **Header & Separator:**
   - `.dropdown-header`: Teks huruf kapital 10.5px, tracking-wider, warna `#9ca3af`.
   - `.dropdown-divider`: Garis solid 1px warna `#f3f4f6`, margin `4px -6px`.

### 4.3. Contoh Implementasi Standar Preline UI
```html
<div class="hs-dropdown relative inline-flex">
    <button
        id="hs-dropdown-options"
        type="button"
        class="hs-dropdown-toggle"
        aria-haspopup="menu"
        aria-expanded="false"
    >
        <i class="bi bi-sliders"></i>
        <span>Opsi Tampilan</span>
        <i class="bi bi-chevron-down text-xs text-gray-400"></i>
    </button>

    <div
        class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden"
        role="menu"
        aria-labelledby="hs-dropdown-options"
    >
        <span class="dropdown-header">Navigasi Cepat</span>
        <a class="dropdown-item" href="<?= url('/reports/create') ?>">
            <i class="bi bi-cloud-arrow-up text-blue-600"></i>
            Upload File Baru
        </a>
        <a class="dropdown-item active" href="<?= url('/reports') ?>">
            <i class="bi bi-table text-indigo-600"></i>
            Tampilan Tabel
        </a>
        <div class="dropdown-divider"></div>
        <button type="button" class="dropdown-item danger">
            <i class="bi bi-trash"></i>
            Hapus Pilihan
        </button>
    </div>
</div>
```

### 4.4. Aturan Terkunci: Custom Select Dropdown (Alpine.js Filter & Selection)

Untuk kontrol pemilihan nilai (filter form, combobox, pemilihan entitas) yang membutuhkan tampilan custom UI bebas dari keterbatasan styling browser native:

1. **Struktur Anatomi `.custom-select-root`:**
   - Komponen dibungkus dalam container dengan `x-data="{ open: false, selected: '...', label: '...' }"` dan `@click.outside="open = false"`.
   - Menggunakan `<input type="hidden" name="..." :value="selected">` agar pengiriman form (GET/POST) tetap 100% kompatibel dan identik dengan `<select>` standar.
   - Tombol pemicu `.dropdown-toggle` menampilkan teks label terpilih (`x-text="label"`), ikon kategori di sisi kiri, dan chevron down di sisi kanan (`:class="{ 'rotate-180': open }"`).
   - Menu panel `.dropdown-menu` menampilkan daftar opsi (`role="listbox"`), item yang terpilih mendapatkan kelas `.active` dan centang `<i class="bi bi-check2 text-blue-600"></i>`.
2. **Contoh Implementasi Standar Custom Select:**
```html
<div x-data="{
    open: false,
    selected: '<?= e($filters['team'] ?? '') ?>',
    label: '<?= ($filters['team'] ?? '') !== '' ? addslashes(e($filters['team'])) : 'Semua tim' ?>'
}">
    <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
    <div class="custom-select-root relative" @click.outside="open = false">
        <input type="hidden" name="team" :value="selected">
        <button
            type="button"
            class="dropdown-toggle"
            @click="open = !open"
            :aria-expanded="open"
            aria-label="Filter Tim"
        >
            <i class="bi bi-people text-gray-400"></i>
            <span class="truncate text-gray-900" x-text="label"></span>
            <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
        </button>

        <div
            class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
            role="listbox"
        >
            <button
                type="button"
                class="dropdown-item"
                :class="{ 'active': selected === '' }"
                @click="selected = ''; label = 'Semua tim'; open = false"
            >
                <span>Semua tim</span>
                <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
            </button>
            <?php foreach ($teams as $team): ?>
                <button
                    type="button"
                    class="dropdown-item"
                    :class="{ 'active': selected === '<?= addslashes(e($team)) ?>' }"
                    @click="selected = '<?= addslashes(e($team)) ?>'; label = '<?= addslashes(e($team)) ?>'; open = false"
                >
                    <span><?= e($team) ?></span>
                    <i x-show="selected === '<?= addslashes(e($team)) ?>'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>
```

### 4.5. Normalisasi Global Elemen `<select>` Native

Jika tag native `<select>` masih digunakan untuk kasus tertentu (seperti floating batch toolbar atau reactive modal with `x-model`), elemen wajib dinormalkan melalui CSS global:
- `appearance: none; -webkit-appearance: none; -moz-appearance: none;`
- Menginjeksi custom SVG chevron di sisi kanan via `background-image` (data URL SVG), `background-repeat: no-repeat`, `background-position: right 0.75rem center`.
- Padding kanan wajib minimal `2.25rem` (`36px`) agar teks opsi tidak menabrak ikon chevron.
- Border solid `#d1d5db`, radius `10px`, warna background solid `#ffffff`, dan focus ring `3px rgba(31, 107, 240, 0.15)`.

---

## 5. Aturan Terkunci: Navigasi SPA Bawah & Peniadaan Navbar/Sidebar Samping (Global Zero-Navbar, Zero-Sidebar & Pure Naked SPA Bottom Navigation Law)

Setiap halaman di dalam aplikasi remittance beroperasi di bawah prinsip **Pure Naked Architecture** dan **SPA Bottom Navigation Architecture** untuk menjamin visual berwibawa, efisiensi ruang horizontal dan vertikal maksimal, serta kanvas kerja 100% penuh (*zero side-rail / zero chrome / zero slop*).

### 5.1. Peniadaan Total Navbar & Sidebar Vertikal (Zero-Navbar & Zero-Sidebar Law)
1. **Pemusnahan Bilah Navigasi Atas & Sidebar Samping:**
   - Bilah navigasi atas (`<header>` / topbar) dan sidebar vertikal kiri (`<aside class="w-64">`) **MUTLAK DITIADAKAN** di seluruh halaman aplikasi.
   - Area kanvas kerja desktop kini 100% penuh tanpa offset horizontal `lg:ps-64`, memaksimalkan densitas data dan kenyamanan analisis pada tabel transaksi dan laporan harian.
2. **Arsitektur SPA Bottom Navigation:**
   - **Tablet & Desktop (`sm:` dan ke atas):** Navigasi dipusatkan pada **Floating Bottom Dock** mengambang di tengah bawah (`fixed bottom-5 left-1/2 -translate-x-1/2 z-40 bg-white/95 backdrop-blur-md border border-gray-200/90 rounded-2xl shadow-[0_12px_36px_-6px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.06)] p-1.5 flex items-center gap-1.5`):
     - **Peniadaan Branding Dock / Sidebar:** Bebas dari badge identitas brand atau logo seremonial di dalam bilah navigasi, langsung fokus menyajikan tab fungsional inti.
     - **Tab Utama (`.spa-dock-tab`):** Dashboard, Transaksi, Laporan. State aktif menggunakan background solid `#eff6ff`, border `#bfdbfe`, dan teks `#1d4ed8` dengan zero-transform.
     - **Menu "Lainnya" (Upward Popover):** Popover mengambang ke atas (`bottom-full mb-3`) menampilkan modul sekunder terkelompok: Operasional (Rekening Payer, Data PT, Complain, Invoice), Dokumen & Analitik (Dokumen & Template, Entropy–TOPSIS), dan Administrasi (User & Role, Audit Log). Tombol pemicu otomatis menampilkan status aktif jika pengguna berada di salah satu rute tersebut.
     - **Pill Profil & Sesi:** Menampilkan avatar dan nama pengguna, dengan upward popover untuk tautan Akun (`/profile`) dan tombol Keluar (`POST /logout` dengan CSRF token valid).
   - **Mobile (`< sm`):** Navigasi menggunakan **Fixed Bottom Tab Bar** (`fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200/90 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-2 py-1 pb-[calc(0.25rem+env(safe-area-inset-bottom,0px))]`):
     - Memuat tab ringkas `.spa-mobile-tab` (Dashboard, Transaksi, Laporan, Menu, Akun).
     - Tombol "Menu" membuka **Mobile Bottom Sheet / Drawer** yang meluncur halus dari bawah (`translate-y` transition dengan backdrop blur) memuat kartu profil, tombol logout, serta daftar modul lengkap yang terorganisir rapi.
3. **Pemusnahan Tombol Hamburger Mobile:**
   - Tombol toggle drawer kiri atas (`fixed top-3 start-3`) **DITIADAKAN SECARA TOTAL**.
   - Container utama konten menggunakan bantalan atas bersih (`p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6`), khusus halaman Dashboard beroperasi secara Fullscreen Edge-to-Edge (`px-2 sm:px-3 lg:px-4 py-3 sm:py-4` dengan `.dashboard-modern` melebar 100% penuh mentok kiri dan kanan tanpa batasan max-width). Ruang aman bawah terkunci pada `pb-24 sm:pb-28` agar konten tidak pernah tertutup oleh dock mengambang maupun tab bar mobile.

### 5.2. Single Source of Truth untuk Konteks Halaman
- Konteks halaman ditampilkan secara elegan melalui judul tab browser (`<title><?= e($pageTitle) ?> · <?= e($appName) ?></title>`) serta penanda aktif pada bottom navigation dock.
- **DILARANG KERAS** menambahkan banner judul halaman duplikat atau hero seremonial di dalam area konten body, seperti:
  - `<h2>Laporan</h2>` atau `<h2>Report {Bulan}</h2>` atau `<h2>{Tanggal}</h2>`
  - `<div class="page-heading"><h2>...</h2></div>`
  - `<section class="bank-hub-heading"><h2>...</h2></section>`
  - `<div class="spk-hero"><h2>...</h2></div>`
  - `<section class="bank-hero"><h2>...</h2></section>`
  - `<div class="payer-page-heading"><h1>...</h1></div>`
  - Kapsul/badge status seremonial seperti `"Sistem aktif"`, `"Terbuka"`, dsb.
  - Kicker/eyebrow seremonial seperti `"OPERATION CONTROL CENTER"`, `"CASE MANAGEMENT"`, `"MASTER DATA"`, dll.

### 5.3. Anatomi Body Halaman Murni (Naked Page Anatomy)
1. **Langsung ke Inti Fungsi:** Body halaman harus langsung menyajikan bilah filter/pencarian, formulir kerja, kartu bank, atau tabel data tanpa didahului banner seremonial.
2. **Penataan Tombol Aksi:**
   - Tombol navigasi/aksi halaman (seperti *Kembali*, *Daftar Bulan*, *Export Semua*, *Complain Baru*, *Tambah PT*) diletakkan pada bilah aksi independen yang rata kanan (`flex items-center justify-end gap-2 mb-4` / `.page-actions`) atau dilebur langsung ke bilah pencarian/filter.
   - Tombol utama tidak boleh membungkus diri dalam card heading besar berwarna yang membuang ruang layar.
3. **Filter Modal On-Demand (Bilah Aksi Terintegrasi):**
   - Filter lanjutan pada halaman laporan (`/reports`) diintegrasikan ke dalam tombol aksi ramping `[Filter]` di samping `[Upload Excel]`, memicu dialog modal filter (`openFilterModal`) saat ditekan.
   - Peniadaan total card accordion tebal dari aliran kanvas utama halaman, menjamin densitas informasi optimal dan tata letak berwibawa (*Pure Naked Architecture*).

---

## 6. Aturan Terkunci: Sistem Search Bar Kustom Global (Anti-Native Search Bar Law)

Penggunaan input pencarian dengan gaya bawaan browser memicu tombol batal (cancel 'x') abu-abu native WebKit/Blink, outline kaku, dan sudut yang tidak seragam lintas perangkat. Oleh karena itu, seluruh komponen pencarian di RemitGo! **MUTLAK WAJIB** menggunakan sistem Search Bar Kustom non-native berbasis Alpine.js dan TailwindCSS.

### 6.1. Pemusnahan Gaya Bawaan Browser (Anti-Native Search Reset)
Seluruh pseudo-element native browser pada input pencarian diharamkan secara mutlak:
```css
/* Pemusnahan Cancel Button Abu-Abu & Dekorasi Native WebKit/Blink */
input[type="search"]::-webkit-search-cancel-button,
input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-results-button,
input[type="search"]::-webkit-search-results-decoration {
  -webkit-appearance: none !important;
  appearance: none !important;
  display: none !important;
}

/* Reset Tampilan Native */
input[type="search"],
.form-search,
.search-input {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  outline: none;
}
```

### 6.2. Anatomi 4 Lapisan Search Bar Kustom
1. **Container Wrapper (`.custom-search-bar`):**
   - Wadah `relative flex items-center w-full`. Bebas translasi dan scale.
   - Menggunakan state Alpine `x-data="{ query: '...' }"`.
2. **Leading Search Icon (`.search-leading-icon`):**
   - Ikon pencarian `<i class="bi bi-search"></i>` terletak presisi di sisi kiri (`left: 12px` / `ps-3.5`).
   - Warna default `#9ca3af` (`gray-400`), otomatis berubah menjadi warna brand `#1f6bf0` saat input aktif (`.custom-search-bar:focus-within .search-leading-icon`).
3. **Input Pencarian Kustom (`.custom-search-input`):**
   - Geometri: Tinggi 40px (atau 36px untuk `.custom-search-input-sm`), padding horizontal `8px 40px 8px 38px`.
   - Radius: Membulat halus 12px (`rounded-xl` / `.rounded-2xl`) atau opsi kapsul (`.custom-search-input-pill`).
   - Warna & Border: Background solid `#ffffff`, border solid 1px `#d1d5db`, teks `#111827`, placeholder `#9ca3af`.
   - Focus Ring: `border-color: #1f6bf0; box-shadow: 0 0 0 3px rgba(31, 107, 240, 0.15);`.
   - Zero-Transform: Bebas dari efek bounce, translateY, atau zoom.
4. **Interactive Clear Button (`.search-clear-btn`):**
   - Tombol hapus kustom berbentuk lingkaran halus 22px dengan ikon `<i class="bi bi-x-circle-fill"></i>`.
   - Muncul HANYA ketika input memiliki nilai (`x-show="query && query.length > 0" x-cloak`).
   - Saat ditekan: mengosongkan input seketika (`query = ''; $refs.searchInput.value = ''`), memicu event `input`, dan mengembalikan fokus kursor tanpa me-reload halaman.
5. **Keyboard Shortcut Hint (`.search-kbd-hint`):**
   - Hint tombol `<kbd>/</kbd>` yang halus muncul di sisi kanan saat input kosong.
   - Menekan tombol `/` pada keyboard (di luar input/textarea aktif) otomatis memindahkan fokus kursor ke search bar aktif.

### 6.3. Arsitektur Search Bar Asli & Dropdown Panduan Operasional (Real Input & Inline Tips Law)
Untuk menjaga kealamian antarmuka web, kecepatan interaksi, dan ergonomi keyboard tanpa distraksi modal gelap, bar pencarian utama beroperasi mengikuti prinsip **Real Input, Anti-Dummy-Button & Anti-Esc-Button**:
1. **Search Bar Asli Langsung di Halaman (`<input type="search">`):**
   - Bar pencarian adalah elemen `<input>` nyata yang langsung bisa diklik dan diketik oleh pengguna seketika tanpa perantara tombol (`<button>`) palsu maupun popup modal terpisah.
   - Menggunakan helper resmi `render_reports_search_bar()` yang membungkus input dengan form GET standar, parameter tersembunyi (`extraParams`), dan Alpine component `remitInlineSearch`.
   - **Pemusnahan Tombol "Esc" & Modal Gelap:** Tidak ada tombol "Esc" maupun backdrop modal gelap yang mengaburkan halaman.
2. **Dropdown Panduan Operasional Tersemat (Inline Tips Popover):**
   - Muncul secara on-demand langsung di bawah search bar saat input difokuskan atau diketik (`@focus="isOpen = true"`).
   - **Peniadaan Total "Recents":** Daftar riwayat modul/halaman situs tetap ditiadakan dari laporan agar terfokus murni pada data transaksi.
   - **Pemeliharaan Panduan Operasional ("Search tips"):** Menyajikan panduan sintaks filter operasional (`payer:`, `benef:`, `bank:`, `tim:`, `date:`, `inv:`). Memilih salah satu tip otomatis mengisi prefix ke dalam input dan memfokuskan kursor.
   - **Bilah Panduan Keyboard Footer:** Bagian bawah dropdown popover menyajikan instruksi keyboard secara permanen (`↑ ↓ navigasi`, `↵ pilih / cari`, `esc tutup`).
   - **Navigasi Keyboard Interaktif:** Pengguna dapat menavigasi tips maupun live action menggunakan tombol panah atas/bawah, menekan `Enter` untuk memilih tip atau langsung mengeksekusi pencarian, dan menekan tombol `Esc` pada keyboard untuk menutup dropdown popover.
   - Menekan shortcut keyboard `/` atau `Ctrl+K` di mana saja otomatis mengarahkan fokus kursor ke input pencarian aktif.

### 6.4. Penggunaan via Helper PHP Resmi
Setiap view yang membutuhkan kontrol pencarian **MUTLAK WAJIB** menggunakan helper berikut di `app/helpers.php`:

```php
<!-- Mode 1: Search Bar Asli Laporan dengan Dropdown Panduan Operasional (Direkomendasikan) -->
<?= render_reports_search_bar([
    'action'      => url('/reports/search'),
    'placeholder' => 'Cari transaksi...',
    'query'       => (string) ($filters['q'] ?? ''),
]) ?>

<!-- Mode 2: Input Form Tersemat Sederhana (Khusus Filter Parameter Ringkas) -->
<?= render_search_input('q', (string) ($filters['q'] ?? ''), [
    'placeholder'  => 'Ketik kata kunci pencarian...',
    'required'     => true,
    'wrapperClass' => 'flex-1 min-w-[200px]',
]) ?>
```
3. **Normalisasi Parameter Objek (`extraParamsList`):** Alpine.js v3 mengevaluasi iterasi objek pada `<template x-for="(val, key) in object">` sebagai `[key, value]` tuple dengan `key` berupa indeks numerik. Seluruh parameter tambahan wajib dinormalisasi melalui computed getter `extraParamsList` yang menghasilkan array `[{ name, value }]` murni.
4. **Pembersihan State Terpadu (State Sanitization):** Setiap eksekusi `openModal(config)` wajib mereset seluruh atribut state (`title`, `subtitle`, `placeholder`, `action`, `query`, dan `extraParams`) kembali ke nilai konfigurasi default untuk mencegah pencemaran state (*state pollution*) saat berpindah halaman atau menggunakan shortcut global.
5. **Zero-Transform Transition:** Transisi modal hanya boleh menggunakan manipulasi `opacity` murni (contoh: `x-transition:enter-start="opacity-0"` ke `x-transition:enter-end="opacity-100"`). Dilarang keras menyertakan `-translate-y-2` atau scale transform.

---

## 7. Protokol Keamanan, Git & Kompilasi Aset

1. **Kompilasi Aset Tailwind:**
   - Setelah mengedit kelas styling di `public/assets/css/tailwind-input.css`, WAJIB menjalankan:
     ```bash
     npm run build:css
     ```
   - Pastikan output `public/assets/css/tailwind.css` terupdate bersih tanpa error.
2. **Hukum Identitas Git & Anti-Ghost Commit:**
   - DILARANG melakukan komit dengan email sembarangan.
   - Wajib mengambil token PAT dari Doppler:
     ```bash
     GH_PAT=$(doppler secrets get XM4YESTIK_GH_PAT --plain)
     ```
   - Wajib mengatur identitas lokal sebelum commit:
     ```bash
     git config user.name "xm4yestiK"
     git config user.email "xm4yestiK@users.noreply.github.com"
     ```
   - Push via PAT resmi:
     ```bash
     git push https://${GH_PAT}@github.com/Natamaya-io/003_remittance-app.git main
     ```
3. **Anti-Leak & Anti-Slogan:**
   - DILARANG membocorkan IP internal Tailscale (`100.x.x.x`), kredensial server, atau nama node internal ke dalam file kode/markup/dokumentasi publik.
   - DILARANG menggunakan slogan internal "SOTA 2026" di mana pun dalam kode maupun teks antarmuka.

---

## 8. Aturan Terkunci: Sistem Informasi On-Demand via Hover Ikon (i) (Hover-Only Info Helper Law)

> **STATUS ATURAN: TERKUNCI MUTLAK (LOCKED & NON-NEGOTIABLE)**  
> Seluruh teks penjelasan, catatan bantuan, atau keterangan deskriptif panjang **MUTLAK HANYA BOLEH MUNCUL KETIKA DI-HOVER DI IKON `i`**. Aturan ini dikunci untuk menjamin estetika minimalis berwibawa (*Naked Page Architecture*), efisiensi ruang vertikal, dan ketiadaan pergeseran tata letak (*Zero Layout Shift*).

### 8.1. Hukum Pemicu Tunggal Hover pada Ikon (i) (Strict Hover-Only Info Trigger)
1. **Keberadaan Default yang Bersih:** Area judul, kartu, accordion, atau langkah panduan hanya menampilkan judul murni yang berwibawa dengan ikon info melingkar halus (`<i class="bi bi-info-circle"></i>` / `.info-toggle-btn`) di sampingnya.
2. **Pemicu Tunggal Hover (Hover-Only):**
   - Teks informasi bantuan **MUTLAK HANYA MUNCUL KETIKA KURSOR MOUSE MELAYANG (HOVER) DI ATAS IKON `i`**.
   - Saat kursor mouse keluar dari ikon `(i)`, informasi **MUTLAK WAJIB LANGSUNG HILANG / TERTUTUP SEKETIKA**.
3. **Pemusnahan Pemicu Klik (Anti-Click Info Law):**
   - **DILARANG KERAS** memicu informasi melalui event klik (`click`), tap, atau menahan status terbuka (*sticky/expanded state*).
   - Ikon `(i)` tidak boleh bertindak sebagai tombol ekspansi accordion atau panel inline.
4. **Pemusnahan Pergeseran Layout (Zero Layout Shift):**
   - Dilarang merender box/panel ekspansi statis di bawah judul yang mendorong konten lain ke bawah saat dibuka.
   - Informasi wajib melayang di atas konten (*floating tooltip*) menggunakan `z-[70]` dan `pointer-events: none` sehingga tidak mengganggu alur baca maupun tata letak halaman.

### 8.2. Penggunaan via Helper PHP Resmi
Setiap view yang membutuhkan judul kartu dengan informasi on-demand via hover ikon `(i)` wajib menggunakan helper `render_heading_with_info()` di `app/helpers.php`:
```php
<?= render_heading_with_info(
    'Pencarian Transaksi',
    'Filter data transaksi berdasarkan parameter yang dipilih.'
) ?>
```
Atau menggunakan `render_tooltip()` langsung untuk kasus kustom:
```html
<div class="inline-flex items-center gap-1.5">
    <strong class="text-sm font-bold text-gray-900">Judul Card</strong>
    <?= render_tooltip(
        'Teks deskriptif yang hanya muncul ketika ikon i di-hover.',
        '<span class="info-toggle-btn cursor-help" aria-label="Informasi bantuan"><i class="bi bi-info-circle text-[11px] leading-none"></i></span>',
        ['placement' => 'top']
    ) ?>
</div>
```

---

## 9. Aturan Terkunci: Hukum Mutlak Batas Lingkup Bedah & Anti-Perubahan Liar (Strict Minimal Scope & Anti-Unsolicited Modification Law)

> **STATUS ATURAN: TERKUNCI MUTLAK (LOCKED & NON-NEGOTIABLE)**  
> Aturan ini dibuat untuk menghentikan segala bentuk *scope creep*, inisiatif liar AI yang tidak diharapkan (*unsolicited modifications*), refactoring spekulatif tanpa instruksi, dan perusakan stabilitas kode yang sudah berjalan baik.

### 9.1. Prinsip Intervensi Bedah Presisi (Minimal Surgical Intervention)
1. **Kepatuhan Target Murni (Strict Target Compliance):** Agen AI hanya diizinkan memodifikasi berkas, fungsi, atau elemen UI yang secara langsung, spesifik, dan eksplisit disebutkan dalam instruksi pengguna.
2. **Sentuhan Minimal (Minimal Touch Law):** Setiap perubahan kode wajib dibatasi hanya pada baris-baris yang mutlak esensial untuk memenuhi permintaan pengguna. Dilarang merombak baris atau fungsi di sekitarnya yang tidak relevan dengan tugas.
3. **Disiplin Diff Bersih (Clean Diff Discipline):** Dilarang melakukan format ulang massal (*mass reformatting*), mengubah indentasi seluruh file ribuan baris, atau mengubah urutan import/method tanpa instruksi. Setiap baris dalam git diff harus memiliki alasan langsung yang dapat dipertanggungjawabkan terhadap prompt pengguna.

### 9.2. Larangan Absolut Perubahan Spekulatif (Anti-Speculative & Anti-Unsolicited Edits)
1. **Dilarang "Memperbaiki" Hal yang Sedang Berjalan Baik:** Dilarang keras melakukan refactoring spekulatif ("modernisasi kode", penamaan ulang variabel, pemindahan struktur folder, penataan ulang arsitektur) pada modul, endpoint, view, atau controller yang sudah stabil dengan dalih "best practice", "kebersihan kode", atau "optimasi" kecuali pengguna secara eksplisit memerintahkannya.
2. **Dilarang Menambah Fitur Tanpa Permintaan (Anti-Feature Creep):** Dilarang menambahkan tombol, modal, link navigasi, filter, tab, badge, shortcut, atau alur baru yang tidak diminta secara tertulis oleh pengguna.
3. **Dilarang Menghapus Fitur atau Aset Tanpa Instruksi:** Dilarang menghapus logika bisnis, kolom database, helper, rute, atau asset yang sudah ada jika pengguna tidak secara spesifik memerintahkan penghapusannya.

### 9.3. Preservasi Total Logika Bisnis & Sistem Eksisting (Zero Collateral Damage)
1. **Perlindungan Logika Kritis:** Modul inti perbankan dan operasional—seperti perhitungan multi-kriteria Entropy–TOPSIS, parser file transaksi BNI, standing instruction, generator nomor invoice, otentikasi role/permission, dan audit log—wajib dipreservasi secara absolut dari perubahan sampingan yang tidak diinginkan.
2. **Integritas Kontrak & Dependensi:** Modifikasi pada komponen bersama (*shared partials/helpers/layouts*) wajib menjamin bahwa seluruh halaman atau fungsi lain yang mengonsumsi komponen tersebut tidak mengalami regresi, eror, atau perubahan perilaku yang tidak diharapkan.
3. **Verifikasi Bebas Efek Samping:** Setiap kali selesai mengimplementasikan perubahan, pengembang/agen AI wajib mengaudit git status dan git diff untuk memastikan tidak ada file atau baris yang termodifikasi secara tidak sengaja (*unintended changes*). Jika ditemukan berkas yang terubah di luar cakupan tugas, berkas tersebut **MUTLAK WAJIB DIKEMBALIKAN (REVERT) SEKETIKA**.

### 9.4. Kunci Hukum Mutlak (Strict Enforcement Protocol)
- Aturan ini adalah **HUKUM TERTINGGI REPOSITORI** yang setara dengan konstitusi utama.
- Segala bentuk penyimpangan, asumsi berlebih, atau modifikasi liar yang tidak diminta oleh pengguna dianggap sebagai **PELANGGARAN SISTEMATIS TINGKAT TINGGI** dan pekerjaan tersebut dinyatakan cacat (*rejected*).

---

## 10. Aturan Terkunci: Sistem Tooltip Global (Hover-Only Tooltip Law)

> **STATUS ATURAN: TERKUNCI MUTLAK (LOCKED & NON-NEGOTIABLE)**  
> Seluruh elemen petunjuk mikro (*tooltip*) di dalam repositori **`remittance.raxbyte.com`** tunduk di bawah hukum pemicu tunggal *hover*. Aturan ini dikunci secara mutlak untuk mencegah konflik interaksi dengan event klik, tombol aksi, atau form kontrol.

### 10.1. Hukum Pemicu Tunggal Hover (Strict Hover-Only Trigger Law)
1. **Pemicu Tunggal Hover (Hover-Only Behavior):**
   - Tooltip **MUTLAK HANYA BOLEH MUNCUL KETIKA DI-HOVER** (saat kursor mouse berada di atas elemen pemicu: state `:hover`, event `mouseenter`, atau `mouseenter`/`mouseover`).
   - Saat kursor mouse meninggalkan elemen pemicu (event `mouseleave` / `mouseout`), tooltip **MUTLAK WAJIB LANGSUNG HILANG / TERTUTUP SEKETIKA**.
2. **Pemusnahan Pemicu Klik (Anti-Click Tooltip Law):**
   - **DILARANG KERAS** memicu tooltip menggunakan event klik (`click`), tap, atau menahan status terbuka (*sticky/persistent state*) setelah elemen diklik.
   - Mengklik elemen atau ikon target hanya boleh mengeksekusi aksi fungsional murni (navigasi, submit form, membuka modal dialog, dsb), **BUKAN** untuk menampilkan atau menyembunyikan teks tooltip.
   - Tooltip dilarang bertindak sebagai popover atau dialog.

### 10.2. Integrasi Tooltip dengan Ikon Info (i) (Hover-Only Law)
1. **Pemicu Ikon Info (`.info-toggle-btn`):**
   - Ikon info `(i)` bertindak sebagai pemicu tooltip hover murni (`render_tooltip`).
   - Teks informasi bantuan atau catatan deskriptif **MUTLAK HANYA MUNCUL KETIKA DI-HOVER DI IKON `i`**.
   - Dilarang memicu informasi via klik atau merender ekspansi box yang menggeser tata letak halaman (*zero layout shift*).
2. **Tooltip Aksi & Tabel:**
   - Menampilkan petunjuk singkat untuk tombol aksi ikonik (Export Excel, Edit Metadata, Hapus Batch, Tautan Transaksi, Cetak, dsb).
   - Selalu beroperasi dengan `pointer-events: none` dan transisi opasitas murni.

### 10.3. Geometri & Fisika UI Tooltip (Gamified Asphalt Compliance)
1. **Bebas Transform (Zero-Transform State):**
   - State transisi kemunculan tooltip DILARANG menggunakan efek gerak `transform: translateY(...)`, `scale(...)`, atau translasi pop.
   - Transisi hanya boleh memanipulasi opasitas murni (`opacity-0` ke `opacity-100`, durasi cepat 100ms–150ms).
2. **Warna & Tipografi Nyaman:**
   - Background: Solid `#111827` (`gray-900`) atau `#1f2937` (`gray-800`), bebas gradien.
   - Teks: Solid `#ffffff`, ukuran proporsional yang jelas terbaca `12.5px` (`text-xs`), font-family **Roboto** / **DM Sans**, font-weight `500` (`font-medium`).
   - Sudut membulat: 10px (`rounded-xl`).
   - Padding proporsional: Lega dan nyaman `py-1.5 px-3` (`6px 12px`).
   - Elevasi: Bayangan lembut `shadow-lg` diperkuat border mikro solid `border border-gray-800/80`.
   - Z-Index: Wajib berada di lapisan atas aman (`z-[70]`).
   - Penataan Teks: Teks ringkas menggunakan `whitespace-nowrap`; untuk kalimat penjelasan yang lebih panjang (> 35 karakter), otomatis mengalir rapi dengan `max-w-xs text-center whitespace-normal leading-relaxed`.
3. **Non-Blokir / Non-Interferensi:**
   - Tooltip wajib memiliki kelas `pointer-events-none` agar tidak menghalangi kursor mouse mengklik tombol atau elemen di balik atau di sekitar tooltip.

### 10.4. Standar Implementasi Preline UI & TailwindCSS
1. **Standar Preline UI (`hs-tooltip`):**
   Wajib menggunakan konfigurasi default `hover` dan strategi fixed. Dilarang mengonfigurasi `data-hs-tooltip-options='{"eventMode": "click"}'`.
   ```html
   <div class="hs-tooltip inline-flex" data-hs-tooltip-options='{"placement":"top","strategy":"fixed"}'>
       <button type="button" class="hs-tooltip-toggle ...">
           <i class="bi bi-question-circle"></i>
           <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-[70] py-1.5 px-3 bg-gray-900 text-white text-xs font-medium rounded-xl shadow-lg border border-gray-800/80 pointer-events-none whitespace-nowrap" role="tooltip">
               Petunjuk singkat hanya muncul saat di-hover
           </span>
       </button>
   </div>
   ```
2. **Standar TailwindCSS Group Hover (CSS Murni):**
   ```html
   <div class="relative inline-flex items-center group">
       <button type="button" class="...">
           <i class="bi bi-download"></i>
       </button>
       <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover:block pointer-events-none z-50 whitespace-nowrap bg-gray-900 text-white text-xs font-medium py-1.5 px-3 rounded-xl shadow-lg border border-gray-800/80 transition-opacity duration-150">
           Download data CSV
       </div>
   </div>
   ```
3. **Standar Alpine.js (Jika Komponen Reaktif Menggunakan State):**
   Wajib menggunakan event mouseenter dan mouseleave saja:
   ```html
   <div class="relative inline-flex" x-data="{ tooltip: false }">
       <button
           type="button"
           @mouseenter="tooltip = true"
           @mouseleave="tooltip = false"
           @focus="tooltip = true"
           @blur="tooltip = false"
           class="..."
       >
           <i class="bi bi-printer"></i>
       </button>
       <div
           x-show="tooltip"
           x-cloak
           x-transition:enter="transition ease-out duration-100"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-75"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 pointer-events-none z-50 whitespace-nowrap bg-gray-900 text-white text-[11px] font-medium py-1 px-2.5 rounded-lg shadow-md"
           style="display: none;"
       >
           Cetak dokumen
       </div>
   </div>
   ```

---

## 11. Aturan Terkunci: Hukum Mutlak Skema Warna Solid Global (Global Solid Color Scheme Law)

> **STATUS ATURAN: TERKUNCI MUTLAK (LOCKED & NON-NEGOTIABLE)**  
> Seluruh elemen antarmuka, kanvas tata letak, latar belakang halaman (`body`), header, hero, panel, kartu data, tombol aksi, badge, tabel, dan grafik di dalam repositori **`remittance.raxbyte.com`** **MUTLAK WAJIB MENGGUNAKAN SKEMA WARNA SOLID (100% OPAQUE & SOLID COLOR DISCIPLINE)**. Aturan ini dikunci untuk memastikan kewibawaan visual Gamified Asphalt, kejelasan kontras data finansial, dan ketiadaan efek lembek/slop. **DILARANG KERAS MENGGUNAKAN WARNA LAIN SELAIN SKEMA RESMI INI SECARA MUTLAK**.

### 11.1. Pemusnahan Gradien Total (Absolute Zero-Gradient Law)
1. **Larangan Gradien CSS & Tailwind:**
   - **MUTLAK DILARANG** menggunakan `linear-gradient`, `radial-gradient`, `conic-gradient`, atau multi-stop gradient pada berkas stylesheet CSS (`.css`), file komponen view (`.php`), maupun inline style.
   - Dilarang menggunakan utilitas kelas gradien Tailwind CSS (seperti `bg-gradient-to-...`, `from-...`, `to-...`, `via-...`).
2. **Larangan Gradien pada Canvas 2D:**
   - Dilarang memanggil `context.createLinearGradient(...)` atau `context.createRadialGradient(...)` pada grafik canvas (seperti tren transaksi, sparkline FX, donat status, atau mini chart).
   - Seluruh kurva dan garis grafik wajib dirender dalam garis solid stroke presisi (`lineWidth: 2px`) dan titik koordinat solid tanpa isian gradien linear pudar di bawah kurva.
3. **Pemusnahan Garis Pelangi & Efek Glow:**
   - Dilarang menambahkan garis dekorasi gradien multi-warna (seperti garis pelangi `linear-gradient(90deg, #3b82f6, #ec4899, #8b5cf6)`) pada batas atas kartu atau panel.
   - Dilarang menempatkan pseudo-element atau div glow berbasis `radial-gradient` di belakang kartu data.

### 11.2. Pemusnahan Efek Kaca & Opasitas Semu (Anti-Glassmorphism & Anti-Opacity Slop Law)
1. **Pemusnahan Frosted Glass (Zero Blur):**
   - Dilarang keras menggunakan properti `backdrop-filter: blur(...)` pada elemen kartu, panel, atau header.
2. **Pemusnahan Latar Belakang Transparan:**
   - Dilarang menggunakan background semi-transparan `rgba(..., alpha)` atau utility kelas opasitas slash Tailwind (seperti `bg-gray-50/50`, `bg-gray-50/80`, `bg-white/10`) pada kontainer kartu, panel data, atau body halaman.
   - Semua warna latar belakang wajib berupa warna solid murni yang pekat dan terdefinisi dengan jelas.

### 11.3. Standar 4 Warna Kunci Proyek (Official Locked Quad-Color Scheme: #F5F2F2, #F2765E, #315B8C, #413333)
Seluruh desain antarmuka, tata letak, komponen interaktif, panel, kartu data, dan kanvas kerja di repositori **`remittance.raxbyte.com`** **MUTLAK DIKUNCI BERDASARKAN 4 WARNA RESMI INI**:

| No | Kode Hex | Nama Resmi | Peran & Hirarki Desain | Implementasi UI & Fungsional |
| :--- | :--- | :--- | :--- | :--- |
| **1** | **`#F5F2F2`** | **Asphalt Off-White / Clean Base** | **Latar Belakang Kanvas & Dasar Terang Optimis** | Kanvas utama halaman (`body`), latar kartu kerja lembut, striping baris genap tabel zebra (`even:bg-[#F5F2F2]`), dan kartu preview sekunder. Menggantikan warna krem lumpur dengan dasar asphalt terang yang bersih, cerah, dan memancarkan optimisme modern ala Gamified Asphalt (Gojek Style). |
| **2** | **`#F2765E`** | **Vibrant Coral / Warm Energy Accent** | **Aksen Kinetik & Penanda Fokus** | Eyebrow/kicker, highlight aktif, badge sorotan, border interaktif fokus, micro-interaction hover, dan aksen energi visual ala Gamified Asphalt (Gojek Style). |
| **3** | **`#315B8C`** | **Deep Classic Blue / Primary Brand** | **Identitas Brand & Jangkar Struktural** | Warna dasar tombol aksi utama (`--btn-primary-base: #315B8C`), header/hero solid, judul hierarki tinggi, border struktural, status aktif tab dock bawah, dan status transaksi prioritas. |
| **4** | **`#413333`** | **Deep Asphalt Brown / Dark Espresso** | **Permukaan Gelap & Kontras Tipografis Hangat** | Wadah/kontainer gelap, kartu permukaan kontras tinggi, panel gelap berwibawa, border kontras hangat, dan tipografi teks utama berbobot tinggi. Menggantikan warna hitam mati (`#000000`) atau abu-abu sintetis generik (`#2B2A2A` / `#1F1E1E`) agar selaras murni dengan kehangatan kanvas `#F5F2F2`. |

### 11.4. Spesifikasi Pemetaan Warna Solid Komponen Utama
1. **Latar Belakang Kanvas Global (`body`):**
   - Kanvas utama dikunci menggunakan warna solid asphalt off-white: `background: #F5F2F2 !important;` (atau alternatif terang bersih `#FFFFFF` berpadu kanvas `#F5F2F2`).
   - Menghadirkan atmosfer visual yang bersih, segar, dan optimis ala Gamified Asphalt (Gojek Style), bebas dari kesan kusam/krem lumpur.
2. **Tombol Aksi Utama (.btn-primary & CTA):**
   - Nilai token dasar brand utama MUTLAK menggunakan `#315B8C`:
     ```css
     :root {
       --btn-primary-base: #315B8C;
     }
     ```
   - Tombol utama menghasilkan translasi fisika solid dari `#315B8C` ke tint alaminya dengan border solid gelap, ring fokus `#315B8C`, dan teks `#FFFFFF`.
3. **Eyebrow, Kicker & Aksen Sorotan:**
   - Seluruh teks kicker/eyebrow (penanda section kecil di atas heading) menggunakan warna solid `#F2765E`.
   - Indikator peringatan penting, counter notifikasi aktif, dan dot penanda status kinetik menggunakan `#F2765E`.
4. **The Zebra Tables:**
   - Tabel murni tanpa garis horizontal/vertikal (zero-border).
   - Seluruh baris genap wajib menggunakan warna solid asphalt off-white: `even:bg-[#F5F2F2]`.
5. **Panel Kontainer & Kartu Data:**
   - Background: Solid `#FFFFFF`, kontainer kanvas `#F5F2F2`, atau kontainer gelap solid `#413333` beraksen `#315B8C` / `#F2765E`.
   - Border: Solid 1px `#E5E7EB` (pada kartu terang) atau solid 1px `#413333` / `#315B8C` (pada kartu aktif/terpilih/gelap).
   - Header Section / Hero: Solid `#315B8C` atau solid `#413333` dengan teks `#F5F2F2`, kicker `#F2765E`, dan tombol aksi `#315B8C` / `#F2765E`.
6. **Harmoni dengan Hukum Ikon Monokrom (Bagian 14):**
   - Keberadaan palet 4 warna (`#F5F2F2`, `#F2765E`, `#315B8C`, `#413333`) **TIDAK MENGUBAH HUKUM IKON MONOKROM**.
   - Ikon antarmuka tetap **MUTLAK HANYA BERWARNA HITAM (`#111827`) ATAU PUTIH (`#FFFFFF`)**.
   - Di atas kanvas `#F5F2F2` atau kartu putih: Ikon wajib hitam (`text-gray-900` / `#111827`).
   - Di atas tombol `#315B8C` atau kontainer gelap `#413333`: Ikon wajib putih (`text-white` / `#FFFFFF`).

### 11.5. Hukum Mutlak Larangan Warna Asing & Sterilisasi Palet (Absolute Zero-Alien Color Law)

> **STATUS HUKUM: TERKUNCI MUTLAK & STRICT COMPLIANCE (NON-NEGOTIABLE)**  
> **DILARANG KERAS MENGGUNAKAN WARNA LAIN SELAIN SKEMA WARNA YANG SUDAH DITENTUKAN SECARA MUTLAK DI DALAM DOKUMEN INI.**  
> Seluruh agen AI (Antigravity/Gemini) dan pengembang perangkat lunak **MUTLAK DILARANG** mengarang, menyusupkan, atau menerapkan kode warna heksadesimal, kelas Tailwind warna acak, atau inline style warna apa pun di luar katalog resmi di bawah ini. Penyimpangan sekecil apa pun dianggap sebagai kegagalan sistematis yang fatal (*fatal compliance failure*).

1. **Katalog Warna Sah & Tertutup (The Absolute Whitelist):**
   Satu-satunya warna yang diizinkan eksis dan dirender di seluruh antarmuka aplikasi `remittance.raxbyte.com` adalah:
   - **4 Warna Master Proyek:**
     - **`#F5F2F2`** (Asphalt Off-White / Clean Base)
     - **`#F2765E`** (Vibrant Coral / Warm Energy Accent)
     - **`#315B8C`** (Deep Classic Blue / Primary Brand)
     - **`#413333`** (Deep Asphalt Brown / Dark Espresso Neutral)
     - **`#F2765E`** (Vibrant Coral / Warm Energy Accent)
     - **`#315B8C`** (Deep Classic Blue / Primary Brand)
     - **`#413333`** (Deep Asphalt Brown / Dark Espresso Neutral)
   - **Netral Fungsional Kanvas & Teks:**
     - **`#FFFFFF`** (Putih solid untuk latar card/panel terang, teks tombol aksi, dan ikon putih).
     - **`#111827` / `#000000`** (Dark Ink / Hitam solid untuk teks body typography, judul gelap, dan ikon hitam monokrom).
     - **`#E5E7EB` / `#E0D3C1` / `#D1D5DB`** (Border solid netral untuk pemisah tabel dan pembatas panel terang).
   - **Indikator Status & Alert Semantik Terisolasi (Hanya untuk Alert & Badge Status):**
     - Sukses: `#10b981` / `#059669` / `#ecfdf5`
     - Bahaya / Error: `#dc2626` / `#991b1b` / `#fef2f2`
     - Peringatan: `#f59e0b` / `#92400e` / `#fffbeb`
     - Info: `#315B8C` / `#1e40af` / `#eff6ff`

2. **Daftar Hitam Warna Asing Terlarang (The Absolute Blacklist):**
   - **DILARANG KERAS** menggunakan warna ungu / violet / indigo asing (seperti `#8b5cf6`, `#7c3aed`, `#6366f1`, `#7440da`, `#a247dd`, `#1e152a`, dll.) pada komponen baru atau refaktor.
   - **DILARANG KERAS** menggunakan warna pink / fuchsia / magenta (seperti `#ec4899`, `#d946ef`, `#eb4c79`, `#d64069`, `#ffe3ed`).
   - **DILARANG KERAS** menggunakan warna cyan / teal neon (seperti `#06b6d4`, `#14b8a6`, `#31bad5`, `#2fca8b`).
   - **DILARANG KERAS** menggunakan abu-abu sintetis generik yang bertentangan dengan kehangatan palet asphalt (seperti `#2B2A2A`, `#1F1E1E`, `#f8fafc`). Seluruh permukaan gelap wajib menggunakan solid `#413333`.

3. **Protokol Penolakan & Pembersihan Otomatis:**
   - Setiap berkas CSS baru, penambahan utility class Tailwind, style canvas JavaScript, maupun template PHP view wajib divalidasi silang terhadap 4 warna master.
   - Jika ditemukan warna asing di luar palet resmi, kode tersebut **MUTLAK DITOLAK** dan wajib segera disterilisasi kembali ke 4 warna master proyek.

---

## 12. Aturan Terkunci: Hukum Mutlak Seluruh Deskripsi Menjadi Tooltips (Universal Description-to-Tooltip Law)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Di seluruh aplikasi **`remittance.raxbyte.com`**, **DILARANG KERAS** menampilkan teks deskripsi, subjudul penjelas, panduan fungsional, keterangan teknis, atau catatan bantuan sebagai teks/paragraf statis (`<p>`, `<small>`, `<span>`, subtitle card, atau subheader).  
> **SELURUH DESKRIPSI MUTLAK WAJIB DIKONVERSI MENJADI TOOLTIPS ON-DEMAND BERBASIS HOVER IKON INFO `(i)`**. Aturan ini dikunci secara absolut untuk menjaga kebersihan visual *Naked Page Architecture*, mencegah *layout bloat*, memelihara ruang kerja vertikal yang maksimal, dan mengeliminasi *AI-slop* deskriptif yang tidak perlu.

### 12.1. Pemusnahan Deskripsi Statis (Absolute Zero-Static Description Law)
1. **Pemusnahan Paragraf Subtitle Panel & Card:**
   - Dilarang merender teks deskripsi statis di bawah judul panel atau di dalam header section, seperti:
     - `<header class="dashboard-section-head"><h3>Judul</h3><p>Deskripsi fungsi di sini...</p></header>` (**DIHARAMKAN SECARA MUTLAK**).
     - `<div class="panel-head"><h3>Judul</h3><p>Keterangan alur kerja...</p></div>` (**DIHARAMKAN SECARA MUTLAK**).
     - `<div class="card-body"><span class="text-muted">Penjelasan input ini...</span></div>` (**DIHARAMKAN SECARA MUTLAK**).
   - Seluruh baris deskripsi statis tersebut wajib dibersihkan dari markup DOM statis.
2. **Konversi Mutlak Menjadi Tooltip Hover-Only:**
   - Setiap teks deskripsi, petunjuk konteks, atau panduan kerja **MUTLAK WAJIB** dimasukkan ke dalam tooltip yang hanya muncul ketika pengguna mengarahkan kursor mouse (*hover*) di atas ikon info `(i)` (`.info-toggle-btn` / `<i class="bi bi-info-circle"></i>`).
   - Judul kartu/panel wajib dibiarkan mandiri, tegas, dan berwibawa berdampingan secara *inline* dengan pemicu tooltip info.

### 12.2. Implementasi Wajib via Helper PHP Resmi
1. **Untuk Header Panel, Card, dan Section (`render_heading_with_info`):**
   Setiap judul yang memiliki teks deskripsi **MUTLAK WAJIB** menggunakan helper `render_heading_with_info()` dari `app/helpers.php`:
   ```php
   <!-- Standar Wajib Header Panel dengan Deskripsi Menjadi Tooltip -->
   <?= render_heading_with_info(
       'Pekerjaan operasional',
       'Pintasan ringkas sesuai hak akses akun.',
       [
           'titleTag'     => 'h3',
           'titleClass'   => 'm-0',
           'wrapperClass' => 'inline-flex items-center gap-1.5',
       ]
   ) ?>
   ```
2. **Untuk Label Form, Kolom Tabel, dan Komponen Kustom (`render_tooltip`):**
   Untuk label form input, header kolom data, atau elemen inline lainnya yang membutuhkan deskripsi bantuan:
   ```php
   <!-- Standar Wajib Label / Elemen Inline dengan Deskripsi Menjadi Tooltip -->
   <label class="form-label inline-flex items-center gap-1.5">
       <span>Nominal Transaksi</span>
       <?= render_tooltip(
           'Nominal dalam mata uang asal sebelum konversi kurs.',
           '<span class="info-toggle-btn cursor-help" aria-label="Bantuan nominal"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
           ['placement' => 'top']
       ) ?>
   </label>
   ```

### 12.3. Fisika & Geometri Tooltip Deskripsi (Zero-Shift & Zero-Transform)
1. **Pemicu Tunggal Hover (Strict Hover Trigger):**
   - Tooltip deskripsi **HANYA BOLEH MUNCUL KETIKA IKON `(i)` DI-HOVER** (`mouseenter` / `:hover`).
   - Begitu kursor mouse meninggalkan ikon `(i)`, tooltip **MUTLAK WAJIB LANGSUNG LENYAP SEKETIKA**.
   - Dilarang keras mengaitkan deskripsi ke event klik (`click`) yang membuka accordion atau toggle panel statis.
2. **Pemusnahan Pergeseran Tata Letak (Zero Layout Shift):**
   - Tooltip deskripsi wajib beroperasi sebagai lapisan mengambang (*floating overlay*) dengan posisi absolut/fixed dan layer `z-[70]`.
   - Tooltip dilarang mendorong elemen tetangga, merusak posisi tabel, atau memicu scrollbar tak terduga saat muncul.
3. **Standar Visual Tooltip:**
   - Background: Warna solid pekat `#111827` (`gray-900`) atau `#1f2937` (`gray-800`), bebas gradien.
   - Teks: Solid `#ffffff`, ukuran `12px` / `text-xs`, font **Roboto** / **DM Sans**, bobot medium (500).
   - Padding & Radius: `py-1.5 px-3`, membulat halus `rounded-xl` (10px–12px), border solid mikro `border border-gray-800/80`, bayangan lembut `shadow-lg`.
   - Kelas Non-Interferensi: Wajib menyertakan `pointer-events-none` agar tidak menghalangi interaksi tombol atau tautan di sekitarnya.
   - Text Wrapping: Menggunakan `max-w-xs text-center whitespace-normal leading-relaxed` untuk deskripsi multibaris, atau `whitespace-nowrap` untuk deskripsi ringkas.

### 12.4. Protokol Kepatuhan AI & Pengembang (Enforcement Mandate)
- Setiap kali agen AI atau pengembang membuat atau memperbarui antarmuka (view, card, modal, form, report, dashboard, login, settings):
  1. **Audit Eksistensi Paragraf Deskripsi:** Periksa apakah ada tag `<p>`, `<small>`, atau subtitle yang berisi kalimat panduan atau catatan fungsi.
  2. **Eksekusi Konversi Mutlak:** Segera pindahkan isi teks deskripsi tersebut ke dalam tooltip via `render_heading_with_info()` atau `render_tooltip()`.
  3. **Musnahkan Tag Statisnya:** Hapus elemen `<p>` atau subtitle statis tersebut tanpa sisa.
- Kegagalan mematuhi aturan ini (membiarkan teks deskripsi statis tampil di layar) diklasifikasikan sebagai pelanggaran desain fatal (*severe visual slop violation*).

---

## 13. Aturan Terkunci: Sistem Tombol Utama Global (The Initiation Button Law - .btn-primary)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Seluruh tombol aksi utama (Call to Action / Tombol Inisiasi) di dalam repositori **`remittance.raxbyte.com`** **MUTLAK DIKUNCI** menggunakan spesifikasi fisika dan geometri **The Initiation Button (`.btn-primary`)** dari pedoman arsitektur AI global (Asphalt Gojek Style). Dilarang merombak geometri, dimensi, maupun fisika tombol ini.

### 13.1. Spesifikasi Anatomi & Geometri Terkunci
1. **Dimensi & Sudut:**
   - Tinggi: Tepat `40px` (`height: 40px;`).
   - Padding: Horizontal `16px` (`padding: 0 16px;`).
   - Radius: Sudut membulat presisi `9px` (`border-radius: 9px;`).
   - Varian Ringkas (`.btn-primary-sm`, `.btn-sm.btn-primary`): Tinggi `32px`, padding `0 12px`, radius `7px`, font `13px`.
2. **Tipografi & Teks:**
   - Font Family: `'Inter', 'DM Sans', system-ui, -apple-system, sans-serif`.
   - Font Weight: `500` (Medium).
   - Font Size / Line Height: `15px/1`.
   - Text Shadow: `0 1px 1px rgba(0, 0, 0, 0.2)` (`var(--_text-shadow)`).
3. **Elevasi & Bayangan Inset:**
   - Highlight Atas: `inset 0 1px 0 rgba(255, 255, 255, 0.32)`.
   - Tepi Bawah: `inset 0 -1px 0 rgba(0, 0, 0, 0.16)`.
   - Bayangan Luar: `0 1px 2px rgba(0, 0, 0, 0.55)`.
4. **Fisika Interaksi (Tactile Feedback):**
   - **Hover State:** `filter: brightness(1.1);` (transisi halus `0.15s ease`).
   - **Active State (Tactile Micro-press):** `transform: translateY(1px) !important; filter: brightness(0.95); box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.35);`.
   - **Focus State:** `outline: 2px solid var(--_ring); outline-offset: 2px;`.

### 13.2. Spesifikasi Kode Resmi CSS
```css
.btn-primary,
.dashboard-action-primary {
  /* Skema Warna Kunci Proyek: #315B8C (Deep Classic Blue) */
  --_from: var(--btn-primary-from, color-mix(in srgb, var(--btn-primary-base, #315B8C) 75%, #ffffff));
  --_to: var(--btn-primary-to, var(--btn-primary-base, #315B8C));
  --_border: var(--btn-primary-border, color-mix(in srgb, var(--btn-primary-base, #315B8C) 80%, #000000));
  --_ring: var(--btn-primary-ring, color-mix(in srgb, var(--btn-primary-base, #315B8C) 40%, #ffffff));
  --_text: var(--btn-primary-text, #ffffff);
  --_text-shadow: var(--btn-primary-text-shadow, 0 1px 1px rgba(0, 0, 0, 0.2));

  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 40px;
  padding: 0 16px;
  font: 500 15px/1 'Inter', 'DM Sans', system-ui, -apple-system, sans-serif;
  color: var(--_text);
  background: linear-gradient(180deg, var(--_from) 0%, var(--_to) 100%);
  border: 1px solid var(--_border);
  border-radius: 9px;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.32),
    inset 0 -1px 0 rgba(0, 0, 0, 0.16),
    0 1px 2px rgba(0, 0, 0, 0.55);
  text-shadow: var(--_text-shadow);
  cursor: pointer;
  user-select: none;
  text-decoration: none;
  transition: filter 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
}

.btn-primary:hover,
.dashboard-action-primary:hover {
  filter: brightness(1.1);
}

.btn-primary:active,
.dashboard-action-primary:active {
  transform: translateY(1px) !important;
  filter: brightness(0.95);
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.35);
}

.btn-primary:focus-visible,
.dashboard-action-primary:focus-visible {
  outline: 2px solid var(--_ring);
  outline-offset: 2px;
}
```

### 13.3. Spesifikasi Tombol Sekunder & Light (.btn-secondary / .btn-light)
Tombol sekunder dan pendukung berbagi geometri yang selaras:
- Tinggi `40px`, padding horizontal `16px`, radius `9px`.
- Background solid `#ffffff`, border solid `1px solid #d1d5db`, teks `#1f2937`.
- Hover: Background `#f9fafb`, border `#9ca3af`.
- Active: `transform: translateY(1px) !important; background: #f3f4f6; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);`.

---

## 14. Aturan Terkunci: Sistem Ikon Global & Universal (Universal Naked Monochrome Icon Law)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Di seluruh aplikasi **`remittance.raxbyte.com`**, seluruh ikon antarmuka (Bootstrap Icons, SVG, maupun ikon indikator fungsional) **TUNDUK PADA HUKUM IKON TELANJANG (ZERO-WRAPPER) DAN HUKUM MONOKROM MUTLAK (PURE BLACK & WHITE ONLY)**.  
> Aturan ini bersifat universal dan berlaku di seluruh halaman tanpa kecuali (Dashboard, Laporan, Transaksi, Dokumen, SPK, Rekening Payer, Complain, Invoice, Master Data, hingga dialog modal).

### 14.1. Hukum Pemusnahan Wrapper Ikon (Absolute Zero-Wrapper Law)
1. **Larangan Wadah / Kotak / Kapsul Pembungkus Ikon:**
   - **MUTLAK DILARANG** membungkus elemen ikon di dalam kontainer dekoratif berbentuk kotak, lingkaran, kapsul, kartu mini, atau badge dengan latar belakang warna/pastel tersendiri.
   - Contoh elemen yang **DIHARAMKAN SECARA MUTLAK**:
     - Wadah berlatar warna: `<span class="dashboard-kpi-icon blue"><i class="..."></i></span>` (**HARAM**).
     - Kotak ikon aksi: `<span class="dashboard-quick-icon"><i class="..."></i></span>` (**HARAM**).
     - Wadah lingkaran: `<div class="icon-circle bg-blue-50"><i class="..."></i></div>` (**HARAM**).
     - Badge/wrapper seremonial: `<div class="icon-wrap">`, `<span class="avatar-icon">`, `<div class="card-icon-box">` (**HARAM**).
2. **Ikon Berdiri Telanjang (The Naked Icon Principle):**
   - Ikon wajib berdiri bebas (*naked icon*) secara langsung di dalam elemen hierarki utamanya (berdampingan langsung dengan teks judul, label, angka metrik, atau di dalam tombol aksi).
   - Ikon dilarang memiliki background sendiri (`background: transparent !important;`), dilarang memiliki border sendiri, dan dilarang memiliki padding wadah buatan.
3. **Standar Ikon Aksi Interaktif (Pure Naked Action Icon - Ikon Berdiri Sendiri):**
   - Seluruh ikon antarmuka (termasuk ikon interaktif seperti toggle visibilitas password atau pemicu aksi inline) **MUTLAK WAJIB BERDIRI SENDIRI MURNI SEBAGAI TAG IKON (`<i>` ATAU `<svg>`) TANPA WRAPPER ELEMEN APA PUN**.
   - **Pemusnahan Tag Pembungkus `<button>`:** DILARANG membungkus ikon di dalam tag `<button>`, dan DILARANG mengubah elemen tombol menjadi pengganti tag ikon jika yang dimaksudkan adalah ikon input. Aksi klik dan aksesibilitas keyboard W3C (WCAG 2.1 AA) **MUTLAK DIINJEKSIKAN LANGSUNG PADA TAG `<i>` ITU SENDIRI**:
     ```html
     <i
         :class="showPass ? 'bi bi-eye-slash' : 'bi bi-eye'"
         @click="showPass = !showPass"
         @keydown.enter.prevent="showPass = !showPass"
         @keydown.space.prevent="showPass = !showPass"
         role="button"
         tabindex="0"
         class="bi bi-eye absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none focus:outline-none transition-colors"
         :aria-label="showPass ? 'Sembunyikan password' : 'Tampilkan password'"
     ></i>
     ```
   - Dengan standar ini, ikon berdiri sendiri 100% (*pure naked icon*), simetris dengan ikon pasif pendampingnya, dan bebas dari wrapper elemen buatan apa pun.

### 14.2. Hukum Warna Monokrom Mutlak (Strict Black or White Law)
1. **Hanya Dua Warna yang Diizinkan:**
   Warna ikon di seluruh sistem **HANYA DIPERBOLEHKAN DUA WARNA MUTLAK**:
   - **HITAM (`#111827`, `#000000`, `text-gray-900`, atau `currentColor` pada kontainer terang):**
     - Digunakan secara universal pada seluruh kanvas terang: kartu putih, tabel, form input, modal, dropdown, dan teks standar.
   - **PUTIH (`#ffffff` / `text-white` pada kontainer gelap):**
     - Digunakan secara eksklusif pada kontainer berlatar gelap pekat: header/hero solid `#2B2A2A`, tombol aksi utama `.btn-primary` berlatar gelap, atau tooltip gelap `#111827`.
2. **Pemusnahan Ikon Berwarna-Warni (Absolute Ban on Colored Icons):**
   - **DILARANG KERAS** menggunakan warna-warni pada ikon, termasuk kelas warna Tailwind maupun inline style:
     - `text-blue-600`, `text-blue-500`, `text-indigo-600`, `text-cyan-600` (**DIHARAMKAN**).
     - `text-emerald-600`, `text-green-500`, `text-teal-600` (**DIHARAMKAN**).
     - `text-rose-600`, `text-red-500`, `text-amber-500`, `text-orange-500`, `text-purple-600` (**DIHARAMKAN**).
     - `color: #2563eb !important;`, `color: #059669 !important;`, dsb (**DIHARAMKAN**).
   - Ikon status (seperti tanda centang sukses atau silang gagal) tidak boleh mewarnai dirinya sendiri dengan warna pelangi; status fungsional diwakili oleh badge teks/label status resmi atau badge monokrom tegas.

### 14.3. Implementasi Kode & Transisi Bersih
1. **Contoh Benar vs Salah pada Kartu Data (KPI / Summary Card):**
   ```html
   <!-- SALAH (Menggunakan Wrapper & Warna Biru Pastel): -->
   <article class="dashboard-kpi-card">
       <span class="dashboard-kpi-icon blue"><i class="bi bi-arrow-left-right"></i></span>
       <div>...</div>
   </article>

   <!-- BENAR (Ikon Telanjang & Monokrom Murni): -->
   <article class="dashboard-kpi-card">
       <i class="bi bi-arrow-left-right text-gray-900 text-xl leading-none"></i>
       <div>...</div>
   </article>
   ```

2. **Contoh Benar vs Salah pada Tombol / Item Aksi:**
   ```html
   <!-- SALAH (Menggunakan Wrapper Ikon): -->
   <a class="dashboard-quick-item blue" href="...">
       <span class="dashboard-quick-icon"><i class="bi bi-plus-lg"></i></span>
       ...
   </a>

   <!-- BENAR (Ikon Telanjang Monokrom Menyatu dengan Tombol): -->
   <a class="dashboard-quick-item" href="...">
       <i class="bi bi-plus-lg text-gray-900 text-lg leading-none"></i>
       ...
   </a>
   ```

3. **Ikon Bantuan / Tooltip Info `(i)`:**
   - Ikon info `(i)` (`.info-toggle-btn`) wajib tampil telanjang dengan warna abu-abu netral/hitam (`#9ca3af` default, `#111827` saat hover) tanpa wrapper latar belakang berwarna.

### 14.4. Mandat Kepatuhan Audit (Enforcement Mandate)
- Setiap agen AI dan pengembang yang menyentuh view atau CSS antarmuka wajib:
  1. Mengaudit seluruh tag `<i>` atau `<svg>` di berkas terkait.
  2. Memusnahkan tag wrapper pembungkusnya (seperti `.dashboard-kpi-icon`, `.dashboard-quick-icon`, `.icon-wrap`, dsb).
  3. Memastikan warna ikon murni hitam pada elemen terang atau putih pada elemen gelap.

---

## 15. Aturan Terkunci: Sistem Scrollbar Kustom Global (Universal Custom Scrollbar Law)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Penggunaan scrollbar native bawaan browser atau sistem operasi yang tebal, kaku, abu-abu semen, dan bersudut tajam merusak kesatuan estetika arsitektur Gamified Asphalt RemitGo!.  
> Oleh karena itu, seluruh aplikasi **`remittance.raxbyte.com`** **MUTLAK WAJIB** beroperasi di bawah konstitusi **Sistem Scrollbar Kustom Global** yang terintegrasi secara universal lintas browser (WebKit, Chromium, Gecko/Firefox) dan seluruh kontainer scrollable (`html`, `body`, modal dialog, popover pencarian, dropdown, drawer, serta kontainer tabel `overflow-x-auto`).

### 15.1. Geometri & Fisika UI Scrollbar Terkunci

| Parameter | Spesifikasi Resmi | Keterangan & Nilai CSS |
| :--- | :--- | :--- |
| **Lebar & Tinggi Scrollbar** | Ramping & Proporsional | `width: 7px; height: 7px;` (WebKit) / `scrollbar-width: thin;` (Firefox) |
| **Bentuk Thumb (Pegangan)** | Capsule / Membulat Penuh | `border-radius: 9999px;` (Full Pill Radius) |
| **Background Track (Lintasan)** | Transparan Murni | `background: transparent;` (Menyatu tanpa garis batas kaku) |
| **Warna Normal Thumb** | Muted Asphalt Slate | `#cbd5e1` (`slate-300`), kontras lembut di atas base `#F5F2F2` |
| **Warna Hover Thumb** | Deep Brand Blue (Master #3) | `#315B8C` (`primary-brand-blue`), umpan balik interaktif berwibawa |
| **Warna Active (Dragging)** | Deep Asphalt Navy | `#24466c`, status interaktif saat di-drag pengguna |
| **Breathing Padding** | Floating Pill Thumb | `border: 1px solid transparent; background-clip: padding-box;` |
| **Sudut (Corner)** | Transparan Murni | `::-webkit-scrollbar-corner { background: transparent; }` |
| **Stabilisasi Gutter** | Anti-Layout Shift | `scrollbar-gutter: stable;` pada container viewport dan modal |

### 15.2. Spesifikasi Implementasi CSS Terkunci

Konfigurasi berikut terkunci secara global di `@layer base` dalam `public/assets/css/tailwind-input.css` dan dikompilasi ke `tailwind.css`:

```css
/* =========================================================
   UNIVERSAL CUSTOM SCROLLBAR SPECIFICATION (GEMINI.MD - HUKUM MUTLAK)
   ========================================================= */

/* 1. Standar W3C / Firefox Gecko */
* {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}

/* 2. WebKit & Chromium (Chrome, Edge, Safari, Opera, Brave, WebView) */
::-webkit-scrollbar {
  width: 7px;
  height: 7px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 9999px;
  border: 1px solid transparent;
  background-clip: padding-box;
  transition: background-color 0.2s ease;
}

::-webkit-scrollbar-thumb:hover {
  background-color: #315B8C;
}

::-webkit-scrollbar-thumb:active {
  background-color: #24466c;
}

::-webkit-scrollbar-corner {
  background: transparent;
}

/* 3. Utility Kelas Opsional: Scrollbar Tersembunyi (No Scrollbar) */
.no-scrollbar::-webkit-scrollbar {
  display: none !important;
}
.no-scrollbar {
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}

/* 4. Utility Kelas: Stabilisasi Gutter Layout */
.scrollbar-gutter-stable {
  scrollbar-gutter: stable;
}
```

### 15.3. Larangan Mutlak & Kepatuhan Anti-Slop
1. **Dilarang Keras Menggunakan Scrollbar Native:** Dilarang menimpa selektor global dengan scrollbar bawaan sistem operasi yang tebal (> 10px) atau kaku kotak (persegi).
2. **Dilarang Keras Menggunakan Warna Alien:** Warna track dilarang berwarna abu-abu pekat atau gradien. Warna thumb saat hover WAJIB menggunakan warna solid master `#315B8C`.
3. **Pemberlakuan Universal:** Aturan ini berlaku otomatis pada seluruh kontainer tanpa perlu menambahkan kelas utilitas khusus di HTML, menjamin konsistensi 100% pada tabel lebar, modal, drawer, dan dropdown.

---

## 16. Aturan Terkunci: Sistem Popup Notifikasi Toast Kustom Global (Universal Custom Toast Notification Law - Anti-Native Browser Popup Law)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Seluruh bentuk notifikasi popup, umpan balik tindakan pengguna, konfirmasi aksi berhasil/gagal, peringatan validasi, dan notifikasi sistem **MUTLAK HARUS MENGGUNAKAN TOAST DENGAN UI KUSTOM TANPA STYLE BAWAAN BROWSER**.  
> Penggunaan dialog notifikasi bawaan browser native (seperti dialog abu-abu `window.alert()`, `window.confirm()`, popup dialog sistem operasi, atau Web Notifications bubble native yang kaku) **DIHARAMKAN SECARA MUTLAK**. Seluruh notifikasi wajib beroperasi di bawah prinsip arsitektur Gamified Asphalt RemitGo!.

### 16.1. Prinsip Mutlak & Pemusnahan Notifikasi Native Browser (Anti-Native Browser Popup Discipline)
1. **Pemusnahan Total Style Bawaan Browser:**
   - Dilarang keras memicu `window.alert()`, `window.confirm()`, atau dialog modal native browser lainnya yang menghentikan thread eksekusi JavaScript (blocking UI) dan merusak estetika antarmuka.
   - Panggilan JavaScript `window.alert(msg)` secara global dicegat dan dialihkan secara otomatis ke sistem toast/modal kustom melalui `public/assets/js/custom-dialogs.js`.
2. **Non-Intrusive Floating Experience:**
   - Toast mengambang di atas kanvas kerja tanpa merusak alur tata letak dokumen (*zero document layout shift*).
   - Pengguna tetap dapat melanjutkan pekerjaan operasional, membaca data tabel, atau mengetik pada formulir saat toast sedang tampil.

### 16.2. Geometri, Elevasi & Tata Letak Floating Viewport Stack
1. **Posisi & Penataan Tumpukan (Floating Viewport Stack):**
   - **Desktop & Tablet (`sm:` dan ke atas):** Menempati sudut kanan atas layar (`fixed top-5 right-5 z-[9999]`), terisolasi dari bilah navigasi bawah (*SPA Bottom Navigation Dock*).
   - **Mobile (`< sm`):** Menempati area atas horizontal penuh dengan padding aman (`fixed top-4 inset-x-4 z-[9999]`).
   - Tumpukan toast tersusun secara vertikal dengan celah teratur `space-y-3` (`gap-3`).
2. **Geometri & Fisika Kartu Toast:**
   - **Radius Membulat Halus:** Sudut wajib membulat elegan `rounded-xl` (12px / 15px pada root 20px) hingga `rounded-2xl`. Sudut tajam kotak kaku **DIHARAMKAN SECARA MUTLAK**.
   - **Lebar Kontainer:** Lebar maksimal `max-w-md w-full` (atau `min-w-[320px]`).
   - **Border Solid 1px:** Border presisi mengelilingi seluruh kontur toast dengan warna serasi varian status.
   - **Elevasi Halus (Soft Elevation):** Menggunakan bayangan halus `box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04)`. Dilarang keras menggunakan glow neon semu, drop-shadow hitam pekat, atau efek kaca blur yang mengaburkan teks (*zero-glassmorphism*).

### 16.3. Standar 4 Varian Warna Solid & Tipografi Toast

Seluruh varian toast wajib mematuhi **Hukum Skema Warna Solid Murni (Bagian 11)** tanpa gradien:

| Varian | CSS Class | Background | Border | Teks Konten | Ikon Resmi |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Success Toast** | `.toast-success` | `#ecfdf5` (`emerald-50`) | `#a7f3d0` (`emerald-200`) | `#065f46` (`emerald-900`) | `<i class="bi bi-check-circle-fill"></i>` (`#059669`) |
| **Danger / Error Toast** | `.toast-danger`, `.toast-error` | `#fef2f2` (`red-50`) | `#fecaca` (`red-200`) | `#991b1b` (`red-900`) | `<i class="bi bi-x-circle-fill"></i>` (`#dc2626`) |
| **Warning Toast** | `.toast-warning` | `#fffbeb` (`amber-50`) | `#fde68a` (`amber-200`) | `#92400e` (`amber-900`) | `<i class="bi bi-exclamation-triangle-fill"></i>` (`#d97706`) |
| **Info Toast** | `.toast-info` | `#eff6ff` (`blue-50`) | `#bfdbfe` (`blue-200`) | `#1e40af` (`blue-900`) | `<i class="bi bi-info-circle-fill"></i>` (`#315B8C`) |

- **Tipografi Terkunci:**
  - Judul toast (opsional): Font **DM Sans** (`font-heading font-semibold text-sm`).
  - Pesan toast: Font **Roboto** (`font-sans text-xs sm:text-sm`).
  - Diet Bold: Maksimum penekanan adalah `font-weight: 600`. Dilarang menggunakan font ekstra tebal (800/900).
- **Universal Naked Monochrome Icon:**
  - Ikon diletakkan di sisi kiri berdampingan langsung dengan teks tanpa dibungkus kotak/lingkaran warna tambahan (mematuhi Bagian 14).

### 16.4. Anatomi DOM Toast Kustom Terkunci (Alpine.js)

Setiap elemen toast wajib mengimplementasikan struktur hierarki berikut:

```html
<!-- KONTINER UTAMA TUMPUKAN TOAST (DI LAYOUT APP & GUEST) -->
<div
    class="fixed top-5 right-5 z-[9999] w-full max-w-sm sm:max-w-md pointer-events-none space-y-3 px-4 sm:px-0"
    id="remitToastContainer"
    x-data="remitToastStack"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            class="pointer-events-auto rounded-xl border p-4 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.08),0_8px_10px_-6px_rgba(0,0,0,0.04)] transition-all duration-200 ease-out"
            :class="toastClasses(toast.type)"
            role="alert"
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-[-8px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-8px]"
            @mouseenter="pauseTimer(toast)"
            @mouseleave="resumeTimer(toast)"
        >
            <div class="flex items-start gap-3">
                <!-- Ikon Kustom Telanjang -->
                <span class="flex-shrink-0 text-base leading-none pt-0.5">
                    <i :class="toastIcon(toast.type)"></i>
                </span>

                <!-- Badan Pesan -->
                <div class="flex-1 min-w-0">
                    <strong
                        x-show="toast.title"
                        x-text="toast.title"
                        class="block font-heading font-semibold text-sm leading-tight mb-1"
                    ></strong>
                    <div
                        x-text="toast.message"
                        class="font-sans text-xs sm:text-sm leading-relaxed break-words"
                    ></div>
                </div>

                <!-- Tombol Tutup Kustom (Bebas Style Native Browser) -->
                <button
                    type="button"
                    class="flex-shrink-0 p-1 -mr-1 -mt-1 rounded-lg opacity-70 hover:opacity-100 hover:bg-black/5 transition-colors focus:outline-none"
                    @click="dismiss(toast.id)"
                    aria-label="Tutup notifikasi"
                >
                    <i class="bi bi-x text-lg leading-none"></i>
                </button>
            </div>
        </div>
    </template>
</div>
```

### 16.5. Kontrak API JavaScript Terprogram (`window.remitToast`) & Integrasi Flash Backend

1. **Shorthand JavaScript Global Terprogram:**
   Seluruh modul script aplikasi dapat memicu notifikasi toast kustom seketika tanpa dialog browser:
   ```javascript
   // Format umum
   window.remitToast(message, type = 'info', options = {});

   // Shorthand varian status
   window.remitToast.success('Laporan batch harian berhasil disimpan.');
   window.remitToast.error('Gagal memperbarui status transaksi: koneksi terputus.');
   window.remitToast.warning('Saldo rekening payer mendekati batas minimum.');
   window.remitToast.info('Proses rekonsiliasi sedang berlangsung di latar belakang.');

   // Opsi lengkap
   window.remitToast({
       title: 'Sinkronisasi Selesai',
       message: 'Sebanyak 1.420 data transaksi telah disinkronkan.',
       type: 'success',
       duration: 5000 // milidetik (0 untuk persisten sampai ditutup)
   });
   ```

2. **Perilaku Pengatur Waktu (Timer & Auto-Dismiss):**
   - Durasi default adalah **5000ms (5 detik)**.
   - **Pause on Hover:** Timer otomatis dijeda (*paused*) saat pengguna mengarahkan kursor ke atas toast agar teks panjang sempat terbaca tanpa terburu-buru hilang.
   - **Resume on Leave:** Timer melanjutkan hitungan mundur saat kursor meninggalkan area toast.

3. **Integrasi Flash Session Backend (PHP Framework):**
   - Pesan flash dari session backend (`Flash::all()`) otomatis dikonversi menjadi entri toast kustom saat view selesai dimuat (`DOMContentLoaded`).
   - Tidak ada lagi kotak flash statis yang membuang ruang vertikal kanvas kerja utama.

### 16.6. Larangan Mutlak & Kepatuhan Anti-Slop Toast
1. **Dilarang Keras Menggunakan Dialog Native:** Pemanggilan `window.alert()`, `window.confirm()`, atau notification bubble OS native dilarang secara absolut.
2. **Dilarang Keras Efek Bounce/Scale Liar:** Transisi toast dilarang menggunakan `scale-95`, `scale-105`, atau translasi jarak jauh (> 10px). Transisi masuk/keluar wajib murni opasitas dan micro-shift `translate-y-[-8px]`.
3. **Dilarang Keras Menggunakan Warna Gradien atau Efek Kaca:** Background toast wajib solid murni sesuai 4 varian resmi tabel 16.3.
4. **Dilarang Keras Tombol Tutup Native Browser:** Tombol dismiss wajib menggunakan Bootstrap Icons `<i class="bi bi-x"></i>` dengan styling kustom, bukan kontrol silang native OS.

---

## 17. Aturan Terkunci: Sistem Form Input Teks Kustom Global (Universal Custom Form Input Law)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Penggunaan input form teks generik yang tidak proporsional, tinggi kembung (*bloated*), celah spasi ikon yang renggang, atau berpenampilan putih polos tanpa kedalaman karakter **DIHARAMKAN SECARA MUTLAK**.  
> Seluruh elemen input form teks (`<input type="text">`, `type="password"`, `type="email"`, `type="tel"`, dsb.) di seluruh aplikasi RemitGo! **MUTLAK WAJIB** menggunakan kelas terpusat `.custom-form-input` dengan geometri, elevasi, dan fisika UI yang selaras dengan sistem desain **Gamified Asphalt**.

### 17.1. Geometri & Fisika UI Form Input Terkunci

| Parameter | Spesifikasi Resmi | Nilai & Kode CSS | Alasan Arsitektural |
| :--- | :--- | :--- | :--- |
| **Ketinggian (Height)** | 40px Presisi | `height: 40px; line-height: 40px;` | Selaras modular 1:1 dengan tombol aksi `.btn-primary` (40px) dan `.custom-search-input` (40px) |
| **Bentuk & Radius** | Rounded 9px Modular | `border-radius: 9px;` | Simetri geometri dengan radius 9px pada tombol utama `.btn-primary` |
| **Latar Normal (Surface Well)**| Asphalt Off-White Base | `background-color: #F5F2F2;` | Memberi efek "wadah/well" fisik yang berbobot di atas card putih |
| **Bayangan Normal** | Inset Shadow Mikro | `box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);` | Efek kedalaman cekungan taktil alami |
| **Batas Kontur (Border)** | Border Solid Netral | `border: 1px solid #d1d5db;` | Kontur batas tegas dan rapi |
| **Tipografi & Teks** | Roboto Normal 14px | `font-family: var(--font-sans); font-size: 14px;` | Kontras tinggi, mudah dibaca |
| **Warna Placeholder** | Muted Slate Gray | `color: #9ca3af; font-weight: 400;` | Teks petunjuk netral kontras sedang |
| **Interaksi Hover** | Border Kontras Sedang | `border-color: #9ca3af;` | Umpan balik halus saat kursor melintas |
| **Interaksi Fokus (Focus)**| Clean White + Deep Blue Halo | `background-color: #ffffff; border-color: #315B8C; box-shadow: 0 0 0 3px rgba(49, 91, 140, 0.15);` | Kontras aktif seketika saat diketik, menggunakan warna brand master #315B8C |
| **Spasi Ikon Kiri** | Celah Rapat Alami | `left: 12px; padding-left: 38px;` | Celah antara ikon dan teks hanya 8px-10px (anti-jurang kosong) |
| **Spasi Tombol Kanan** | Aksi Kanan Aman | `right: 10px; padding-right: 38px;` | Teks tidak menabrak tombol toggle visibility password |

### 17.2. Spesifikasi Implementasi CSS Terkunci

Didefinisikan secara resmi di `@layer components` dalam `public/assets/css/tailwind-input.css` dan dikompilasi ke `tailwind.css`:

```css
/* =========================================================
   GAMIFIED ASPHALT LOCKED FORM INPUT (.custom-form-input)
   GEMINI.MD SECTION 17 - HUKUM MUTLAK
   ========================================================= */
.custom-form-input {
  display: block;
  width: 100%;
  height: 40px;
  padding: 0 14px;
  font-family: var(--font-sans);
  font-size: 14px;
  font-weight: 400;
  line-height: 40px;
  color: #111827;
  background-color: #F5F2F2;
  border: 1px solid #d1d5db;
  border-radius: 9px;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
  outline: none;
  transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
  transform: none !important;
  -webkit-appearance: none;
  appearance: none;
}

.custom-form-input::placeholder {
  color: #9ca3af;
  font-weight: 400;
}

.custom-form-input:hover {
  border-color: #9ca3af;
}

.custom-form-input:focus {
  background-color: #ffffff;
  border-color: #315B8C;
  box-shadow: 0 0 0 3px rgba(49, 91, 140, 0.15), inset 0 1px 1px rgba(0, 0, 0, 0.02);
}

/* Modifier: Field dengan Ikon Kiri */
.custom-form-input.has-leading-icon {
  padding-left: 38px;
}

/* Modifier: Field dengan Aksi/Toggle Kanan */
.custom-form-input.has-trailing-action {
  padding-right: 38px;
}
```

### 17.3. Contoh Penggunaan Baku di File Template View

```html
<!-- 1. Input Standar Tanpa Ikon -->
<input
    type="text"
    name="subject"
    placeholder="Masukkan subjek laporan"
    class="custom-form-input"
    required
>

<!-- 2. Input dengan Ikon Kiri (Leading Icon) -->
<div class="relative flex items-center">
    <i class="bi bi-person absolute left-3 text-gray-400 pointer-events-none text-base"></i>
    <input
        type="text"
        name="login"
        placeholder="Masukkan username atau email"
        class="custom-form-input has-leading-icon"
        required
    >
</div>

<!-- 3. Input Password dengan Visibility Toggle Ikon Murni Berdiri Sendiri -->
<div class="relative flex items-center" x-data="{ showPass: false }">
    <i class="bi bi-lock absolute left-3 text-gray-400 pointer-events-none text-base"></i>
    <input
        :type="showPass ? 'text' : 'password'"
        type="password"
        name="password"
        placeholder="Masukkan password akun"
        class="custom-form-input has-leading-icon has-trailing-action"
        required
    >
    <i
        :class="showPass ? 'bi bi-eye-slash' : 'bi bi-eye'"
        @click="showPass = !showPass"
        @keydown.enter.prevent="showPass = !showPass"
        @keydown.space.prevent="showPass = !showPass"
        role="button"
        tabindex="0"
        class="bi bi-eye absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none focus:outline-none transition-colors"
        :aria-label="showPass ? 'Sembunyikan password' : 'Tampilkan password'"
    ></i>
</div>
```

### 17.4. Larangan Mutlak & Kepatuhan Anti-Slop Form Input
1. **Dilarang Keras Ketinggian Non-Standar:** Dilarang menggunakan utilitas ketinggian liar seperti `h-11`, `h-12`, atau `h-14` yang membengkak di bawah root scaling 20px desktop. Tinggi wajib 40px (`.custom-form-input`).
2. **Dilarang Keras Celah Ikon Renggang:** Padding teks kiri jika ada ikon dilarang melebihi 40px (`pl-10` = 50px diharamkan). Wajib 38px (`has-leading-icon`) agar teks dan ikon berjarak rapat dan harmonis.
3. **Dilarang Keras Tampilan Datar Putih di Atas Putih:** Input di atas kartu putih wajib memiliki latar cekungan `#F5F2F2` dengan bayangan inset mikro.
4. **Dilarang Keras Warna Alien:** Border fokus dilarang menggunakan warna ungu, pink, atau hijau. Wajib menggunakan warna master `#315B8C` (*Deep Brand Blue*).

---

## 18. Aturan Terkunci: Hukum Mutlak Anti-Kode Utilitas Ad-Hoc Sementara & Wajib Kustom UI Komprehensif (Zero Quick Placeholder & Comprehensive Custom UI Law)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Kebiasaan menyusun kelas utilitas Tailwind instan secara tambal sulam (*ad-hoc quick placeholder*) saat merombak atau membersihkan elemen lama dengan asumsi "nanti bisa dipercantik" **DIHARAMKAN SECARA ABSOLUT SAMPAI KE AKARNYA**.  
> Seluruh agen AI dan pengembang perangkat lunak **MUTLAK WAJIB LANGSUNG MERANCANG & MENERAPKAN KUSTOM UI KOMPREHENSIF** sejak intervensi bedah pertama dilakukan.

### 18.1. Definisi & Pemusnahan "Quick Placeholder" (The Total Purge of Temporary Ad-Hoc Styling)
1. **Apa Itu "Quick Placeholder":**
   Rangkaian kelas utilitas atomik panjang (contoh: `w-full h-11 pl-10 pr-4 text-sm bg-white border border-gray-300 rounded-xl`) yang ditulis secara ad-hoc langsung pada elemen tanpa:
   - Memperhitungkan efek penskalaan root W3C 125% (`html { font-size: 20px; }`).
   - Memastikan keselarasan modular dengan komponen pendamping (seperti tinggi 40px tombol `.btn-primary` dan search bar).
   - Memperhitungkan celah mikro antara ikon indikator dan teks input.
   - Memberikan kedalaman tekstur fisik (*tactile surface well* / inset shadow) khas arsitektur Gamified Asphalt.
2. **Kiamat Kode Sementara:**
   - Konsep "kode sementara", "draft kasar", atau "styling asal jalan dulu" dinyatakan sebagai **KEGAGALAN SISTEMATIS TINGKAT TINGGI**.
   - Setiap baris antarmuka yang ditulis oleh AI wajib berstatus *production-grade*, berwibawa, dan selesai secara tuntas (*finished state*).

### 18.2. Lima Pilar Kustom UI Komprehensif (The 5 Pillars of Comprehensive Custom UI)
Setiap kali elemen antarmuka dirombak, dimodernisasi, atau dibersihkan, hasilnya **MUTLAK WAJIB** memenuhi 5 pilar arsitektur berikut:

1. **Terpusat di Sistem Komponen (Centralized Component Definition):**
   - Elemen yang dirombak WAJIB didefinisikan sebagai kelas komponen terstandarisasi di `@layer components` dalam `public/assets/css/tailwind-input.css` (seperti `.custom-form-input`, `.btn-primary`, `.custom-search-input`) atau helper resmi PHP di `app/helpers.php`.
   - DILARANG menyalin string utilitas panjang yang rentan inkonsistensi dari satu view ke view lainnya.
2. **Imunitas Terhadap Penskalaan W3C 125% (Immunity to Root REM Scaling):**
   - Ketinggian, padding horizontal, dan radius kelengkungan wajib dihitung secara presisi di bawah kondisi `html { font-size: 20px; }`.
   - DILARANG menggunakan utilitas kelas relatif sembrono seperti `h-11` (yang membengkak menjadi 55px) jika target proporsinya adalah 40px. Ketinggian elemen wajib dikunci secara presisi (`height: 40px;`).
3. **Simetri Modular 1:1 dengan Elemen Pendamping (Modular Geometric Harmony):**
   - Seluruh elemen kontrol utama (input teks, tombol aksi, search bar, trigger dropdown, datepicker) wajib berbagi proporsi ketinggian terpadu **40px** dan radius modular **9px hingga 12px**.
   - Dilarang membuat satu elemen berpenampilan raksasa (*bloated*) sementara elemen sebelahnya berpenampilan kerdil.
4. **Kedalaman Permukaan Taktil (Tactile Surface Depth vs Flat White Slop):**
   - DILARANG membuat elemen putih polos datar di atas kartu putih polos (*flat white-on-white slop*).
   - Wadah kontrol wajib memanfaatkan palet master: Off-White Base `#F5F2F2` dengan bayangan cekungan mikro (`box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04)`), kontur batas netral `#d1d5db`, dan transisi fokus aktif ke putih bersih dengan halo warna brand `#315B8C` (*Deep Brand Blue*).
5. **Spasi Mikro Ergonomis (Micro-Spacing & Anti-Void Gap):**
   - Jarak antara ikon indikator dan teks dilarang menyisakan celah kosong melompong (> 12px).
   - Ikon di `left-3` (12px) wajib diimbangi dengan padding teks `38px` (`has-leading-icon`), sehingga celah nyata antara ujung ikon dan huruf pertama placeholder terkunci rapat di kisaran 8px - 10px.

### 18.3. Protokol Eksekusi Perombakan Elemen UI (Element Overhaul Workflow)
Setiap agen AI yang diperintahkan merombak atau memperbaiki antarmuka wajib menempuh 3 langkah protokol ini secara berurutan:
1. **Langkah 1 (Audit Spesifikasi Terkunci):** Periksa apakah elemen yang akan dirombak sudah memiliki kelas baku di `GEMINI.md` (Tombol di Bagian 13, Input Form di Bagian 17, Search Bar di Bagian 6, Datepicker di Bagian 3, Dropdown di Bagian 4, Toast di Bagian 16, Alert di Bagian 2).
2. **Langkah 2 (Implementasi Kustom Komprehensif):**
   - Jika kelas baku sudah ada: Terapkan kelas resmi tersebut secara murni (misal: `<input class="custom-form-input has-leading-icon">`).
   - Jika kelas baku belum ada: Rancang kelas komponen kustom komprehensif baru di `public/assets/css/tailwind-input.css` di bawah `@layer components`, lakukan kompilasi aset dengan `npm run build:css`, dan bukukan aturannya ke dalam `GEMINI.md`. Dilarang meninggalkan string utilitas ad-hoc di markup.
3. **Langkah 3 (Verifikasi DOM & Rendering Nyata):**
   - Lakukan verifikasi curl ke server lokal (`http://127.0.0.1:8000`) untuk memastikan struktur HTML ter-render sempurna tanpa eror sintaks.
   - Periksa git diff untuk memastikan tidak ada efek samping liar terhadap file atau bagian lain yang tidak diminta.

### 18.4. Checklist Verifikasi Anti-Placeholder Sebelum Penyerahan Kode
Sebelum menyatakan tugas perombakan UI selesai, seluruh poin audit berikut wajib terjawab **YES**:
- [ ] Apakah elemen menggunakan kelas komponen kustom terpusat atau helper resmi (bukan string utilitas ad-hoc sementara)?
- [ ] Apakah ketinggian elemen terbukti proporsional dan selaras modular 1:1 dengan elemen pendamping (seperti tinggi 40px tombol `.btn-primary`)?
- [ ] Apakah jarak antara ikon dan teks rapat alami (celah 8px - 10px, tanpa jurang kosong)?
- [ ] Apakah permukaan kontrol memiliki kedalaman taktil (surface well #F5F2F2, inset shadow, kontur border rapi)?
- [ ] Apakah status interaktif (hover, focus border #315B8C, ring halo) aktif dan tuntas?
- [ ] Apakah aset CSS telah dikompilasi ulang menggunakan `npm run build:css`?

### 18.5. Sanksi Arsitektural & Pelanggaran Mutlak
Penyerahan kode antarmuka yang terbukti menggunakan kode utilitas ad-hoc sementara (*quick placeholder*) akan **DITOLAK SECARA MUTLAK (REJECTED)**. Agen AI wajib melakukan refaktor seketika menjadi Kustom UI Komprehensif sebelum tugas dapat dinyatakan tuntas.
