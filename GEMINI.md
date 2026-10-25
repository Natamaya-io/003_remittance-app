# GEMINI.md - Aturan Terkunci Arsitektur UI & Sistem Desain Global

> **STATUS DOKUMEN: HUKUM MUTLAK & TERKUNCI (STRICT COMPLIANCE)**  
> Dokumen ini adalah konstitusi teknis dan visual untuk repositori **`remittance.raxbyte.com`**. Seluruh agen AI (Antigravity/Gemini) dan pengembang perangkat lunak **MUTLAK WAJIB** mematuhi aturan ini tanpa pengecualian.

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

### 2.3. Aturan Teknis Alert
1. **Transisi Bersih:** Transisi Alpine.js `x-transition:leave` HANYA memanipulasi `opacity`. Dilarang keras menyertakan `scale-95` atau translasi yang menyebabkan efek zoom/lompatan layout.
2. **Ikon Dinamis:** Setiap jenis alert wajib memiliki ikon yang mewakili statusnya secara spesifik (jangan hardcode `bi-info-circle-fill` untuk semua status).
3. **Elevasi Halus:** Border solid 1px dengan sudut membulat 14px (`rounded-xl` / `.alert`), bayangan mikro `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04)`.

---

## 3. Aturan Terkunci: Sistem Datepicker & Input Tanggal (Global Datepicker Specification)

Seluruh input pemilihan tanggal dan bulan wajib memiliki visual yang seragam, modern, dan nyaman di semua browser (Chrome, Edge, Firefox, Safari).

### 3.1. Standar Styling Global
- Selector otomatis: `input[type="date"]`, `input[type="month"]`, `.form-datepicker`, `.datepicker-input`.
- **Geometri:** Tinggi 40px, padding `8px 12px`, radius 10px (`rounded-xl` / `rounded-lg`).
- **Warna & Border:** Background solid `#ffffff`, border `1px solid #d1d5db`, teks `#111827`.
- **Focus Ring:** `outline: none; border-color: #1f6bf0; box-shadow: 0 0 0 3px rgba(31, 107, 240, 0.15);`.
- **Normalisasi WebKit Indicator:**
  - `::-webkit-calendar-picker-indicator`: Cursor `pointer`, opacity `0.6`, radius `6px`, padding `3px`.
  - Hover indicator: opacity `1`, background `#f3f4f6`.
  - `::-webkit-datetime-edit`: Padding dinormalkan ke `0`.

### 3.2. Pola Wrapper dengan Ikon Kalender

Untuk form dengan ikon kalender eksplisit di sisi kiri atau kanan:
```html
<div class="datepicker-wrapper">
    <span class="datepicker-icon-left">
        <i class="bi bi-calendar3"></i>
    </span>
    <input
        type="date"
        name="report_date"
        class="form-datepicker has-icon-left"
        value="<?= e($dateValue) ?>"
        required
    >
</div>
```

### 3.3. Tombol Shortcut Preset Tanggal

Untuk bilah filter yang membutuhkan preset cepat:
```html
<div class="flex items-center gap-1.5">
    <button type="button" class="datepicker-preset-btn active">Hari Ini</button>
    <button type="button" class="datepicker-preset-btn">7 Hari Terakhir</button>
    <button type="button" class="datepicker-preset-btn">Bulan Ini</button>
</div>
```

---

## 4. Aturan Terkunci: Sistem Dropdown Global (Global Dropdown Specification)

Sistem dropdown mendukung **Preline UI (`hs-dropdown`)** dan **Alpine.js (`x-data="{ open: false }"`)**. Keduanya berbagi standar geometri dan visual yang sama.

### 4.1. Anatomi 4 Lapisan Dropdown
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

### 4.2. Contoh Implementasi Standar Preline UI
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

### 4.3. Aturan Terkunci: Custom Select Dropdown (Alpine.js Filter & Selection)

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

### 4.4. Normalisasi Global Elemen `<select>` Native

Jika tag native `<select>` masih digunakan untuk kasus tertentu (seperti floating batch toolbar atau reactive modal with `x-model`), elemen wajib dinormalkan melalui CSS global:
- `appearance: none; -webkit-appearance: none; -moz-appearance: none;`
- Menginjeksi custom SVG chevron di sisi kanan via `background-image` (data URL SVG), `background-repeat: no-repeat`, `background-position: right 0.75rem center`.
- Padding kanan wajib minimal `2.25rem` (`36px`) agar teks opsi tidak menabrak ikon chevron.
- Border solid `#d1d5db`, radius `10px`, warna background solid `#ffffff`, dan focus ring `3px rgba(31, 107, 240, 0.15)`.

---

## 5. Aturan Terkunci: Peniadaan Judul Halaman Ganda (Global Zero-Redundant Page Title Directives / Naked Page Architecture)

Setiap halaman di dalam aplikasi remittance beroperasi di bawah prinsip **Naked Page Architecture** untuk menjamin visual berwibawa, efisiensi ruang vertikal, dan ketiadaan teks mubazir (*zero slop*).

### 5.1. Single Source of Truth untuk Judul Halaman
- Seluruh judul halaman utama (seperti *Laporan*, *Pencarian Report*, *Rekap Transaksi*, *Invoice*, *Complain*, *Master Rekening Payer*, dll.) **MUTLAK HANYA DITAMPILKAN SATU KALI** pada bilah navigasi atas (Sticky Topbar: `app/Views/partials/topbar.php` via tag `<h1><?= e($title ?? 'Dashboard') ?></h1>`).
- **DILARANG KERAS** menambahkan banner judul halaman duplikat di dalam area konten body, seperti:
  - `<h2>Laporan</h2>` atau `<h2>Report {Bulan}</h2>` atau `<h2>{Tanggal}</h2>`
  - `<div class="page-heading"><h2>...</h2></div>`
  - `<section class="bank-hub-heading"><h2>...</h2></section>`
  - `<div class="spk-hero"><h2>...</h2></div>`
  - `<section class="bank-hero"><h2>...</h2></section>`
  - `<div class="payer-page-heading"><h1>...</h1></div>`
  - Kicker/eyebrow seremonial seperti `"OPERATION CONTROL CENTER"`, `"CASE MANAGEMENT"`, `"MASTER DATA"`, dll.

### 5.2. Anatomi Body Halaman Murni (Naked Page Anatomy)
1. **Langsung ke Inti Fungsi:** Body halaman harus langsung menyajikan bilah filter/pencarian, formulir kerja, kartu bank, atau tabel data tanpa didahului banner seremonial.
2. **Penataan Tombol Aksi:**
   - Tombol navigasi/aksi halaman (seperti *Kembali*, *Daftar Bulan*, *Export Semua*, *Complain Baru*, *Tambah PT*) diletakkan pada bilah aksi independen yang rata kanan (`flex items-center justify-end gap-2 mb-4` / `.page-actions`) atau dilebur langsung ke bilah pencarian/filter.
   - Tombol utama tidak boleh membungkus diri dalam card heading besar berwarna yang membuang ruang layar.

---

## 6. Protokol Keamanan, Git & Kompilasi Aset

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
