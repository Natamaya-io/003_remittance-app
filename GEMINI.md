# GEMINI.md - Aturan Terkunci Arsitektur UI & Sistem Desain Global

> **STATUS DOKUMEN: HUKUM MUTLAK & TERKUNCI (STRICT COMPLIANCE)**  
> Dokumen ini adalah konstitusi teknis dan visual untuk repositori **`remittance.raxbyte.com`**. Seluruh agen AI (Antigravity/Gemini) dan pengembang perangkat lunak **MUTLAK WAJIB** mematuhi aturan ini tanpa pengecualian.

> **[HUKUM MUTLAK BATAS LINGKUP BEDAH & ANTI-PERUBAHAN LIAR (STRICT SURGICAL SCOPE)]**  
> Seluruh agen AI dan pengembang **MUTLAK DILARANG** melakukan modifikasi, refaktor, penambahan, penghapusan, atau penyesuaian kode terhadap hal-hal yang **TIDAK SECARA EKSPLISIT DIMINTA** dan **TIDAK DIBUTUHKAN**. Setiap perubahan wajib bersifat intervensi bedah murni (*minimal surgical intervention*) yang terisolasi secara presisi pada target yang diinstruksikan oleh pengguna, dengan nol efek samping sampingan (*zero collateral damage*) terhadap berkas, tata letak, logika bisnis, atau komponen lain yang sudah stabil. Aturan ini **TERKUNCI MUTLAK DAN TIDAK BOLEH DILANGGAR DALAM KONDISI APA PUN**.

---

## 1. Identitas Proyek & Hukum Amnesti Proyek Warisan (Legacy Amnesty)

1. **Lingkungan & Stack Proyek:**
   - Aplikasi: Perbankan & Remitansi Operasional (`003_remittance-app`).
   - Branding Resmi: **RemitGo!**
   - Stack Dasar: **PHP 8.2+**, **MariaDB**, **TailwindCSS (v3.4+)**, **Preline UI (v2.7+)**, **Alpine.js**, dan **Bootstrap Icons**.
   - **Hukum Amnesti Proyek Lama:** Proyek ini beroperasi di atas stack warisan yang telah teruji stabil. DILARANG MEROMBAK struktur ini ke stack proyek baru (seperti Rust, Axum, SolidJS, atau PostgreSQL) kecuali ada instruksi eksplisit.
2. **Hukum Mutlak Antislop (Gamified Asphalt UI):**
   - **Tipografi:** Wajib menggunakan **DM Sans** (Headings, Tombol/CTA) dan **Roboto** (Body text, data tabel, metadata).
   - **Diet Bold:** Dilarang menggunakan font-weight ultra-tebal (800/900). Maksimum penekanan adalah `font-weight: 600` (`font-semibold` / `.font-bold`).
   - **Bebas Gradien (Solid Color Discipline):** Seluruh elemen UI utama (tombol, card, alert, dropdown, datepicker) wajib menggunakan warna solid. Dilarang menggunakan `linear-gradient` atau `radial-gradient` pelangi/slop.
   - **Bebas Transform (Zero-Transform State):** State `:hover`, `:active`, dan `:focus` DILARANG menggunakan efek `transform: translateY(...)`, `scale(...)`, atau pergeseran posisi. Animasi transisi hanya boleh memanipulasi `opacity`, `background-color`, `border-color`, dan `box-shadow`.

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
4. **Pemusnahan Dialog Native Browser (Anti-window.confirm / Anti-window.alert):**
   - Dilarang keras memicu `window.confirm()` atau `window.alert()` browser abu-abu.
   - Semua form yang membutuhkan konfirmasi wajib menggunakan atribut `data-confirm="Pesan konfirmasi..."` yang dicegat otomatis oleh handler global `custom-dialogs.js`.
   - Untuk aksi JavaScript terprogram, wajib menggunakan `await window.remitConfirm(pesan, opsi)` atau `window.remitAlert(pesan, opsi)`.
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

## 5. Aturan Terkunci: Peniadaan Navbar & Judul Halaman Ganda (Global Zero-Navbar & Zero-Redundant Page Directives / Pure Naked Architecture)

Setiap halaman di dalam aplikasi remittance beroperasi di bawah prinsip **Pure Naked Architecture** untuk menjamin visual berwibawa, efisiensi ruang vertikal maksimal, dan ketiadaan teks maupun bilah mubazir (*zero chrome / zero slop*).

### 5.1. Peniadaan Total Navbar (Zero-Navbar Law)
1. **Pemusnahan Bilah Navigasi Atas (Top Navbar Elimination):**
   - Bilah navigasi atas (`<header>` / topbar) **MUTLAK DITIADAKAN** di seluruh halaman aplikasi. Dilarang merender topbar, navbar header, atau bilah status horizontal statis di atas area konten.
   - Ruang vertikal layar dimaksimalkan murni untuk visual konten kerja, filter fungsional, dan tabel operasional.
2. **Sentralisasi Kontrol Global ke Sidebar:**
   - Seluruh kontrol navigasi dan sesi dipusatkan secara terpadu di dalam sidebar aplikasi (`app/Views/partials/sidebar.php`):
     - **Navigasi Modul:** Menu vertikal dengan penanda status aktif (`aria-current="page"`, `.bg-blue-50 .text-blue-700`).
     - **Pencarian Global:** Tombol pemicu Search Modal kustom di bawah brand header (`Cari transaksi... /`).
     - **Profil & Sesi:** Footer sidebar memuat kartu profil pengguna (tautan ke `/profile`) dan tombol logout (`POST /logout` dengan CSRF token valid).
3. **Navigasi Mobile Responsif (Floating Mobile Drawer Toggle):**
   - Pada layar mobile (`lg:hidden`), navigasi diakses melalui tombol mengambang ramping (`fixed top-3 start-3 z-40 lg:hidden`) yang membuka overlay drawer sidebar tanpa membebani layar dengan navbar statis.
   - Container utama konten menyediakan bantalan atas halus (`pt-14 sm:pt-6 lg:pt-8`) untuk menjamin kenyamanan interaksi lintas ukuran layar.

### 5.2. Single Source of Truth untuk Konteks Halaman
- Konteks halaman ditampilkan secara elegan melalui judul tab browser (`<title><?= e($pageTitle) ?> · <?= e($appName) ?></title>`) serta penanda aktif pada sidebar navigasi.
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

### 6.3. Arsitektur Search Bar Sebagai Modal Terpisah (Search Modal Architecture)
Untuk menjaga kebersihan tata letak halaman (*Naked Page Architecture*) dan efisiensi ruang layar, bar pencarian utama pada area kartu dan header **DITAMPILKAN SEBAGAI MODAL TERPISAH**:
1. **Pemicu Modal (Search Trigger Button):**
   - Halaman merender tombol aksi ramping `.btn-primary` (atau tombol ikon topbar) berlabel "Cari" dengan ikon `<i class="bi bi-search"></i>` dan hint shortcut `<kbd>/</kbd>`.
   - Menggunakan helper resmi `render_search_modal_trigger()` yang otomatis memicu event `$dispatch('open-search-modal', { ... })`.
2. **Modal Pencarian Global (`remitSearchModal`):**
   - Dirender terpusat di `app/Views/layouts/app.php` dengan backdrop blur halus (`bg-gray-900/60 backdrop-blur-xs`).
   - Dialog mengambang `max-w-xl` dengan animasi transisi opasitas murni.
   - Input pencarian kustom langsung mendapatkan fokus otomatis (`$refs.modalSearchInput.focus()`).
   - Tombol shortcut global: Menekan tombol `/` atau kombinasi `Ctrl+K` / `Cmd+K` di mana saja (di luar input/textarea aktif) otomatis membuka Search Modal.
   - Menekan tombol `ESC` atau klik di luar area dialog otomatis menutup modal.

### 6.4. Penggunaan via Helper PHP Resmi
Setiap view yang membutuhkan kontrol pencarian **MUTLAK WAJIB** menggunakan helper berikut di `app/helpers.php`:

```php
<!-- Mode 1: Pemicu Modal Terpisah (Direkomendasikan untuk Header/Card) -->
<?= render_search_modal_trigger([
    'action'      => url('/reports/search'),
    'title'       => 'Pencarian Transaksi Global',
    'subtitle'    => 'Cari data di seluruh bulan, tanggal, tim, berkas, payer, dan beneficiary.',
    'placeholder' => 'Ketik kata kunci transaksi...',
    'query'       => (string) ($filters['q'] ?? ''),
]) ?>

<!-- Mode 2: Input Form Tersemat (Khusus Halaman Hasil / Filter Parameter) -->
<?= render_search_input('q', (string) ($filters['q'] ?? ''), [
    'placeholder'  => 'Ketik kata kunci pencarian...',
    'required'     => true,
    'wrapperClass' => 'flex-1 min-w-[200px]',
]) ?>
```

### 6.5. Protokol Anti-Regresi & Keamanan Event Propagation Search Modal
1. **Scope Isolation & Dual-Dispatch Safety:** Setiap elemen tombol pemicu modal wajib memiliki atribut `x-data`, penahan bubbling `@click.stop`, dan fallback JavaScript native `onclick="event.stopPropagation(); window.openSearchModal(JSON.parse(this.dataset.searchConfig || '{}')); return false;"` yang didukung `data-search-config`. Hal ini menjamin pemicu tetap berfungsi 100% meskipun tombol diletakkan di luar hierarki komponen Alpine induk.
2. **Backdrop Event Handling (Anti-Race Condition):** Penutupan modal saat mengklik area luar wajib menggunakan `@click.self="closeModal()"` langsung pada fixed container backdrop (`.fixed.inset-0`), **DILARANG** menggunakan `@click.outside="closeModal()"` pada kartu dialog modal untuk mencegah penutupan seketika akibat bubbling klik pemicu.
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

## 8. Aturan Terkunci: Sistem Informasi On-Demand & Toggleable Helper (Anti-Description Slop)

Untuk menjaga estetika minimalis berwibawa (*Naked Page Architecture*) dan efisiensi ruang vertikal, seluruh teks penjelasan, catatan bantuan, atau subtitle deskriptif panjang (contoh: *"Cari data di seluruh bulan, tanggal, tim, berkas, payer, dan beneficiary."*) **DILARANG DITAMPILKAN SECARA PERMANEN** di bawah judul card atau heading section.

### 8.1. Transformasi Menjadi Informasi On-Demand (Toggleable Info Law)
1. **Keberadaan Default:** Secara default, area heading card hanya menyajikan judul utama yang bersih dan berwibawa tanpa teks penjelasan yang memakan tempat.
2. **Tombol Pemicu Info (`.info-toggle-btn`):** Di samping kanan teks judul, disematkan tombol ikon info melingkar halus (`<i class="bi bi-info-circle"></i>`) berukuran 20px dengan state interaktif lembut (hover background `#eff6ff`, border `#dbeafe`, focus outline ring).
3. **Panel Informasi (`.info-toggle-box`):** Saat tombol ditekan, teks penjelasan bertransisi mulus (*Zero-Transform*, opasitas murni) langsung di bawah judul dengan ikon penjelas dan padding ringkas.
4. **Isolasi Event (`@click.stop`):** Tombol info wajib menggunakan penahan perambatan `@click.stop` agar jika disematkan di dalam accordion (seperti Filter Lanjutan) atau tombol aksi lain, klik tidak memicu pembukaan/penutupan elemen induk secara tidak sengaja.

### 8.2. Penggunaan via Helper PHP Resmi
Setiap view yang membutuhkan judul kartu dengan informasi on-demand wajib menggunakan helper `render_heading_with_info()` di `app/helpers.php`:
```php
<?= render_heading_with_info(
    'Pencarian Transaksi',
    'Cari data di seluruh bulan, tanggal, tim, berkas, payer, dan beneficiary.'
) ?>
```
Atau menggunakan komponen Alpine langsung untuk kasus inline custom:
```html
<div class="inline-flex items-center gap-1.5" x-data="{ showInfo: false }">
    <strong class="text-sm font-bold text-gray-900">Judul Card</strong>
    <button
        type="button"
        @click.stop="showInfo = !showInfo"
        class="info-toggle-btn"
        :class="{ 'is-active': showInfo }"
        :aria-expanded="showInfo.toString()"
        aria-label="Tampilkan informasi bantuan"
        title="Informasi bantuan"
    >
        <i class="bi bi-info-circle text-[11px] leading-none"></i>
    </button>
    <div x-show="showInfo" x-cloak x-transition class="info-toggle-box" style="display: none;">
        <i class="bi bi-info-circle text-blue-500 mt-0.5 shrink-0"></i>
        <span>Teks deskriptif yang hanya muncul ketika diminta.</span>
    </div>
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
