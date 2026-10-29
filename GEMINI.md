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
   - **Spesifikasi Aset Logo Resmi RemitGo! (`public/remitgo.png` & `public/assets/img/remitgo.png`):**
     - Format: PNG transparan (TrueColorAlpha, 1720x744).
     - Tipografi Teks "Remit": **MUTLAK WAJIB BERWARNA ORANYE SOLID (`#F2765E` - Vibrant Coral Accent)**. DILARANG KERAS menggunakan warna putih atau biru pada segmen teks "Remit" karena menyebabkan teks hilang di atas kanvas putih formulir login.
     - Tipografi Teks "Go!": Solid Brand Blue (`#2482FF` / `#315B8C`).
     - Karakter Maskot: Mempertahankan kombinasi Deep Blue (`#0065F8`), Asphalt Dark (`#383838`), dan sorot mata putih (`#FFFFFF`).
     - **Larangan Checkout Aset Sembarangan:** Agen AI MUTLAK DILARANG menjalankan `git checkout` atau `git restore` pada file logo tanpa analisis komprehensif, untuk mencegah pemulihan ke versi lama yang salah.
2. **Hukum Mutlak Antislop (Gamified Asphalt UI):**
   - **Tipografi:** Wajib menggunakan **DM Sans** (Headings, Tombol/CTA) dan **Roboto** (Body text, data tabel, metadata).
   - **Skala Visual 125% Terstandarisasi W3C (Native 125% Root REM Scaling Law):** Seluruh antarmuka aplikasi beroperasi dengan skala kenyamanan visual 125% murni berbasis standar W3C (`html { font-size: 20px; }` pada layar desktop $\ge 1024\text{px}$, `18px` pada tablet, dan `16px` pada mobile). Properti CSS non-standar `zoom: 1.25;` **DIHARAMKAN SECARA MUTLAK** demi menjamin akurasi 100% sistem koordinat JavaScript (Popper.js, Chart.js, modal backdrop, scrollbar) serta keseragaman ukuran seluruh halaman antarmuka.
   - **Diet Bold:** Dilarang menggunakan font-weight ultra-tebal (800/900). Maksimum penekanan adalah `font-weight: 600` (`font-semibold` / `.font-bold`).
   - **Bebas Gradien & Skema Warna Solid Murni (Solid Color Discipline & Absolute Zero-Alien Color Law):** Seluruh elemen UI utama (body canvas, hero, card, panel, alert, dropdown, datepicker, tombol, badge, hingga grafik canvas) **MUTLAK WAJIB** menggunakan warna solid murni berlandaskan **4 Warna Kunci Proyek: 1. #F5F2F2 (Asphalt Off-White Base), 2. #F2765E (Vibrant Coral Accent), 3. #315B8C (Deep Brand Blue), 4. #413333 (Deep Asphalt Brown)**. **DILARANG KERAS MENGGUNAKAN WARNA LAIN DI LUAR SKEMA WARNA YANG SUDAH DITENTUKAN INI SECARA MUTLAK**. Dilarang keras menggunakan `linear-gradient`, `radial-gradient`, `conic-gradient`, atau efek kaca `backdrop-filter: blur(...)` / opasitas tembus pandang semu (lihat Bagian 11 untuk aturan terkunci lengkap).
   - **Bebas Transform (Zero-Transform State):** State `:hover`, `:active`, dan `:focus` DILARANG menggunakan efek `transform: translateY(...)`, `scale(...)`, atau pergeseran posisi liar, KECUALI tactile micro-press `translateY(1px)` khusus pada tombol utama (`.btn-primary`) dan tombol aksi resmi sesuai spesifikasi fisika AI global (lihat Bagian 13 untuk aturan terkunci lengkap). Animasi transisi hanya boleh memanipulasi `opacity`, `background-color`, `border-color`, dan `box-shadow`.
   - **Peniadaan Deskripsi Statis & Tooltip Mutlak (Universal Description-to-Tooltip Discipline):** Seluruh deskripsi, keterangan panduan, catatan bantuan, dan subjudul fungsional pada panel, header, card, tabel, maupun formulir **DILARANG KERAS** dirender sebagai teks atau paragraf statis (`<p>`, `<small>`, `.description`). Seluruh deskripsi **MUTLAK WAJIB** dikonversi menjadi tooltip on-demand berbasis hover pada ikon info `(i)` (lihat Bagian 12 untuk aturan terkunci lengkap).
   - **Peniadaan Wrapper Ikon & Monokrom Mutlak (Universal Naked Monochrome Icon Discipline):** Seluruh ikon antarmuka di seluruh aplikasi **MUTLAK DILARANG** menggunakan wrapper/wadah pembungkus dekoratif berlatar warna dan **MUTLAK HANYA DIPERBOLEHKAN BERWARNA HITAM ATAU PUTIH**. Ikon wajib berdiri telanjang berdampingan langsung dengan konten tanpa pembungkus buatan (lihat Bagian 14 untuk aturan terkunci lengkap).
   - **Scrollbar Kustom Global Mutlak (Universal Custom Scrollbar Law):** Seluruh scrollbar browser bawaan sistem operasi (native Windows/macOS/Linux) yang tebal, kaku, dan abu-abu semen kotak **DIHARAMKAN SECARA MUTLAK**. Seluruh antarmuka (window viewport `html`, `body`, modal, popover, dropdown, hingga tabel scroll horizontal) **MUTLAK WAJIB** menggunakan sistem scrollbar kustom global ramping (7px), sudut membulat penuh (*capsule thumb*), track transparan, dengan warna thumb netral `#cbd5e1` dan interaksi hover solid *Deep Brand Blue* (`#315B8C`) (lihat Bagian 15 untuk aturan terkunci lengkap).
   - **Sistem Popup Notifikasi Toast Kustom Mutlak (Universal Custom Toast Notification Law):** Seluruh notifikasi popup, pesan umpan balik tindakan, konfirmasi aksi, maupun peringatan sistem **MUTLAK HARUS MENGGUNAKAN TOAST DENGAN UI KUSTOM TANPA STYLE BAWAAN BROWSER**. Penggunaan notifikasi browser native (seperti alert dialog abu-abu OS, Web Notification bubble bawaan sistem operasi, atau `window.alert()` / `window.confirm()`) **DIHARAMKAN SECARA MUTLAK**. Toast beroperasi secara mengambang di area bawah layar (*bottom floating viewport stack* - sudut kanan bawah pada desktop `sm:bottom-6 sm:right-6` dan di atas bilah tab pada mobile `bottom-20 inset-x-4`), sudut membulat elegan (`rounded-xl` / `rounded-2xl`), border solid 1px, soft elevation, 4 warna solid varian status resmi, tombol tutup dismissible kustom, auto-dismiss dengan timer pause saat hover, dan transisi Alpine.js halus tanpa efek bounce atau layout shift (lihat Bagian 16 untuk aturan terkunci lengkap).
   - **Sistem Form Input Teks Kustom Mutlak (Universal Custom Form Input Law):** Seluruh elemen input form teks (`<input type="text">`, `type="password"`, `type="email"`, dsb.) **MUTLAK WAJIB** menggunakan kelas terpusat `.custom-form-input` dengan ketinggian presisi 40px (selaras 1:1 dengan `.btn-primary` dan `.custom-search-input`), sudut membulat modular `rounded-[9px]`, latar permukaan well `#F5F2F2` dengan bayangan inset mikro, batas kontur `#d1d5db`, transisi fokus bersih ke putih dengan border solid `#315B8C` (*Deep Brand Blue*), serta jarak ikon kiri rapat alami (38px padding kiri, celah 8-10px) (lihat Bagian 17 untuk aturan terkunci lengkap).
   - **Sistem Datepicker Kustom Flat Global Mutlak (Universal Custom Flat Datepicker Law):** Seluruh elemen pemilihan tanggal dan bulan (input trigger maupun interior kalender popover) **MUTLAK DILARANG** menggunakan native browser Shadow DOM, gaya skeuomorphic cekung (*inset shadow*), atau palet abu-abu semen placeholder draf (`#9ca3af`/`#d1d5db` pada teks aktif). Seluruh antarmuka datepicker **MUTLAK WAJIB** menggunakan sistem flat kustom komprehensif: tombol pemicu flat white presisi 40px (`.custom-datepicker-trigger`), popover flat 280px (`.datepicker-popover`), teks tanggal hitam tajam `#111827`, seleksi solid master blue `#315B8C` murni flat tanpa box-shadow/gradient, dan 12 kelas komponen terpusat di `@layer components` (lihat Bagian 3 untuk aturan terkunci lengkap).
   - **Sistem Elemen Art Background Global (Global Ambient Art Background Law):** Seluruh halaman aplikasi beroperasi dengan latar belakang kanvas `#F5F2F2` yang diperkaya secara elegan oleh elemen visual art RemitGo! samar dan menyatu (`.global-ambient-art`), opasitas halus 3.5%, `mix-blend-mode: multiply`, `pointer-events-none`, GPU-accelerated, dan zero-overhead tanpa mengganggu keterbacaan data finansial (lihat Bagian 21 untuk aturan terkunci lengkap).
   - **Anti-Kode Utilitas Ad-Hoc Sementara & Wajib Kustom UI Komprehensif (Zero Quick Placeholder Law):** DILARANG KERAS menggunakan rangkaian kelas utilitas tambal sulam instan (*ad-hoc quick placeholder*) saat merombak antarmuka. Setiap perombakan elemen UI MUTLAK WAJIB langsung mengimplementasikan Kustom UI Komprehensif dengan geometri modular terstandarisasi (misal tinggi 40px, radius 9px, surface depth #F5F2F2, spasi rapat), terintegrasi ke sistem komponen global, dan tahan terhadap penskalaan visual root 20px (lihat Bagian 18 untuk aturan terkunci lengkap).
   - **Arsitektur Halaman Login Split-Screen & Ramah Lansia (Universal Split Login & Senior Accessibility Law):** Halaman login beroperasi dengan tata letak split-screen 50/50 full-screen non-scrollable, pemisah bergelombang organik dinamis, logo resmi RemitGo! (teks "Remit" oranye solid `#F2765E`), skala elemen lansia 50px presisi (`.custom-form-input-lg` & `.btn-primary-lg`), bebas font-bold, dan ikon mata password telanjang murni tanpa wrapper (lihat Bagian 19 untuk aturan terkunci lengkap).

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

    <!-- Tombol Tutup (.alert-close) -->
    <button
        class="alert-close"
        type="button"
        @click="show = false"
        aria-label="Tutup notifikasi"
        title="Tutup notifikasi"
    >
        <i class="bi bi-x-lg text-xs leading-none"></i>
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

## 3. Aturan Terkunci: Sistem Datepicker Kustom Global (Universal Custom Flat Datepicker Law)

> **STATUS HUKUM: TERKUNCI MUTLAK & STRICT COMPLIANCE**  
> Penggunaan input tanggal native browser (`<input type="date">` dan `<input type="month">`) memicu kalender Shadow DOM sistem operasi yang kaku, tidak konsisten lintas platform, dan merusak tata letak responsif. Oleh karena itu, seluruh aplikasi RemitGo! **MUTLAK WAJIB** menggunakan custom datepicker kalender Alpine.js berbasis **Arsitektur Flat Kustom Komprehensif** baik pada tombol pemicu luar (`.custom-datepicker-trigger`) maupun seluruh interior popover kalender (`.datepicker-popover`).
> 
> **PANTANGAN MUTLAK (THE UNBREAKABLE TABOOS):**
> 1. **Anti-Native Shadow DOM:** DILARANG KERAS merender `<input type="date">` atau `<input type="month">` di seluruh view aplikasi.
> 2. **Anti-Inset Skeuomorphism:** DILARANG KERAS menerapkan bayangan cekung (`inset shadow`) atau latar semen `#F5F2F2` pada tombol pemicu datepicker yang membuatnya menyerupai input non-aktif/disabled.
> 3. **Anti-Wireframe Placeholder Gray:** DILARANG KERAS menggunakan warna teks abu-abu pudar (`text-gray-400`, `text-gray-700`, `#9ca3af`) pada angka tanggal normal, header hari, atau tombol aksi di dalam popover.
> 4. **Anti-Ad-Hoc Utility String:** DILARANG KERAS merangkai utilitas Tailwind inline tanpa kelas komponen terpusat di `@layer components`.

---

### 3.1. Spesifikasi Tombol Pemicu Luar (.custom-datepicker-trigger)

Tombol pemicu datepicker bertindak sebagai representasi visual field tanggal pada formulir:
- **Desain Flat Bersih (Pure Flat White):** Latar putih murni (`#ffffff`), border kontur solid netral `1px solid #d1d5db`, dan bayangan luar mikro `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04)`.
- **Geometri Proporsional 40px:** Ketinggian presisi **40px (`h-10`)**, radius modular **`rounded-[9px]`**, padding horizontal 12px, font-family **Roboto**, font-size 13.5px, dan line-height 40px (selaras 1:1 dengan `.btn-primary`, `.custom-form-input`, dan `.custom-search-input`).
- **State Interaksi:**
  - **State Normal:** Background `#ffffff`, border `#d1d5db`, teks `#111827`, bayangan `0 1px 2px rgba(0, 0, 0, 0.04)`.
  - **State Hover:** Transisi halus ke `background-color: #fafafa; border-color: #9ca3af;`.
  - **State Focus & Terbuka (`.is-open`):** Background `#ffffff`, border solid **`#315B8C`** (*Deep Brand Blue*), halo aktif `box-shadow: 0 0 0 3px rgba(49, 91, 140, 0.15)`. DILARANG menggunakan biru alien Tailwind (`blue-500` / `#3b82f6`).
  - **Bebas Transform:** Dilarang menggunakan translasi pergeseran atau scale zoom.
- **Tipografi Placeholder & Nilai Terstandarisasi:**
  - Nilai kosong (Placeholder): `.datepicker-placeholder` warna `#9ca3af` (font-weight 400).
  - Nilai terisi: `.datepicker-value` warna `#111827` (font-weight 500).
- **Indikator Ikon:**
  - Ikon leading (kiri): `<i class="bi bi-calendar3">` (atau `bi-calendar-month`) warna `#6b7280` / `#9ca3af` font-size 14px.
  - Ikon trailing (kanan): `<i class="bi bi-chevron-down">` warna `#9ca3af` dengan rotasi 180° otomatis saat popover terbuka (`transform: rotate(180deg) !important; transition: transform 0.2s;`).

---

### 3.2. Spesifikasi Lengkap Interior Popover Kalender (.datepicker-popover)

Seluruh elemen di dalam popover kalender beroperasi secara **Flat Bersih Kontras Tinggi** tanpa elemen skeuomorphic atau warna kusam:

#### 1. Kontainer Popover (`.datepicker-popover`)
- **Posisi:** Absolute di bawah pemicu (`top: 100%; inset-inline-start: 0; margin-top: 6px;`).
- **Dimensi:** Lebar tepat **280px** (`width: 280px; max-width: min(280px, calc(100vw - 2rem));`), padding terpadu 12px.
- **Visual:** Background solid `#ffffff`, border solid `1px solid #e5e7eb`, radius 12px (`rounded-xl`), bayangan halus `box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04)`, `z-index: 50`.
- **Hukum Isolasi Dropdown:** Terisolasi mandiri dari `.dropdown-menu` untuk menjamin tidak ada benturan min-width atau padding kaku bawaan dropdown menu Preline.
- **Visibilitas Alpine:** Didukung aturan mutlak `.datepicker-popover[style*="display: none"] { display: none !important; }`.

#### 2. Header Navigasi Popover (`.datepicker-popover-header`)
- **Layout:** Flexbox horizontal `justify-between items-center`, margin-bottom 8px, padding-bottom 8px, batas hairline bawah `1px solid #f3f4f6`.
- **Judul Bulan & Tahun (`.datepicker-popover-title`):** Font **DM Sans**, font-size 13px, font-weight 600, warna `#111827`, tracking `-0.01em`.
- **Tombol Navigasi Panah (`.datepicker-nav-btn`):** Ukuran 28px x 28px, radius 6px, flat transparan tanpa border, teks `#4b5563`, hover flat abu-abu halus `#f3f4f6` dengan teks `#111827`, zero-transform.

#### 3. Header Nama Hari (`.datepicker-days-header`)
- **Grid Layout:** Grid 7 kolom (`repeat(7, 1fr)`), gap 2px, margin-bottom 4px.
- **Label Nama Hari (`.datepicker-day-name`):** Tinggi 26px, font Roboto 11px, font-weight 500, warna **`#6b7280`** (`gray-500` tegas). **DILARANG MENGGUNAKAN WARNA PLACEHOLDER `#9ca3af` ATAU ABU-ABU PUDAR**.

#### 4. Kisi Tombol Tanggal (`.datepicker-days-grid` & `.datepicker-date-btn`)
- **Grid Layout:** Grid 7 kolom (`repeat(7, 1fr)`), gap 2px.
- **Geometri Sel Tanggal:** Dimensi 32px x 32px (`size-8`), radius 8px (`rounded-lg`), font Roboto 12.5px, font-weight 500.
- **Status & Warna Sel:**
  - **Tanggal Normal (Aktif):** Latar transparan, teks hitam tajam **`#111827`**, hover flat halus `#f3f4f6` dengan teks `#111827`. Bebas border dan bebas bayangan.
  - **Tanggal Terpilih (`.is-selected`):** Blok solid **`#315B8C`** (*Deep Brand Blue*), teks putih `#ffffff`, font-weight 600, **murni flat tanpa box-shadow / tanpa gradient (`box-shadow: none !important;`)**.
  - **Tanggal Hari Ini (`.is-today`):** Flat tint halus **`#eff6ff`**, teks **`#315B8C`** font-weight 700.
  - **Tanggal Luar Bulan (`.is-outside` / `:disabled`):** Muted flat **`#d1d5db`**, non-interaktif (`pointer-events-none; cursor: default;`).

#### 5. Kisi Mode Pemilihan Bulan (`.datepicker-months-grid` & `.datepicker-month-btn`)
- **Grid Layout:** Grid 3 kolom (`repeat(3, 1fr)`), gap 6px.
- **Tombol Bulan:** Padding vertikal 10px, radius 8px, font Roboto 12.5px, font-weight 500.
  - **Bulan Normal:** Latar transparan, teks `#111827`, hover flat `#f3f4f6`.
  - **Bulan Terpilih (`.is-selected`):** Solid **`#315B8C`**, teks putih `#ffffff`, font-weight 600, flat.
  - **Bulan Berjalan (`.is-current`):** Flat tint halus `#eff6ff`, teks **`#315B8C`** font-weight 700.

#### 6. Footer Aksi Cepat Popover (`.datepicker-popover-footer` & `.datepicker-footer-btn`)
- **Layout:** Flexbox horizontal `justify-between items-center`, margin-top 8px, padding-top 8px, batas hairline atas `1px solid #f3f4f6`.
- **Tombol "Hari ini" / "Bulan ini" (`.btn-today`):** Flat action button, font-size 12px, font-weight 600, teks **`#315B8C`**, hover flat tint `#eff6ff` teks `#1e3c5d`.
- **Tombol "Hapus" (`.btn-clear`):** Flat action button, font-size 12px, font-weight 500, teks **`#6b7280`** (tegas, bukan placeholder gray), hover flat tint `#fef2f2` teks merah `#dc2626`.

---

### 3.3. Inventarisasi 12 Kelas Komponen Terpusat (@layer components)

Seluruh styling datepicker **MUTLAK TERPUSAT** pada file [`public/assets/css/tailwind-input.css`](file:///home/m4yestik/Projects/client/remittance.raxbyte.com/public/assets/css/tailwind-input.css#L1023-L1377):

| Kelas CSS Komponen | Target Elemen DOM | Deskripsi Desain Flat & Geometri Terkunci |
| :--- | :--- | :--- |
| `.custom-datepicker-trigger` | `<button>` pemicu formulir | Tinggi 40px, radius 9px, flat white `#ffffff`, border `#d1d5db`, focus halo `#315B8C`. |
| `.datepicker-placeholder` | `<span>` teks kosong | Warna `#9ca3af`, font-weight 400, font-size 13.5px. |
| `.datepicker-value` | `<span>` teks tanggal terisi | Warna `#111827`, font-weight 500, font-size 13.5px. |
| `.datepicker-popover` | `<div>` kontainer popover | Lebar 280px, background `#ffffff`, border `#e5e7eb`, radius 12px, shadow halus `0 4px 16px -2px`. |
| `.datepicker-popover-header` | `<div>` bilah judul & panah | Flexbox between, border-bottom `#f3f4f6`, padding-bottom 8px. |
| `.datepicker-popover-title` | `<span>` judul bulan/tahun | DM Sans 13px font-semibold `#111827`, tracking `-0.01em`. |
| `.datepicker-nav-btn` | `<button>` panah `<` dan `>` | Ukuran 28x28px, flat transparan, hover `#f3f4f6 text-[#111827]`, zero-transform. |
| `.datepicker-days-header` | `<div>` bilah header hari | Grid 7 kolom, gap 2px, margin-bottom 4px. |
| `.datepicker-day-name` | `<span>` nama hari (Sen..Min)| Tinggi 26px, Roboto 11px, font-medium, warna tegas `#6b7280` (anti-placeholder). |
| `.datepicker-days-grid` | `<div>` kisi tanggal | Grid 7 kolom, gap 2px. |
| `.datepicker-date-btn` | `<button>` sel tanggal (1..31) | Ukuran 32x32px, radius 8px, teks `#111827`, hover `#f3f4f6`. `.is-selected` solid `#315B8C`. |
| `.datepicker-months-grid` | `<div>` kisi bulan mode month | Grid 3 kolom, gap 6px. |
| `.datepicker-month-btn` | `<button>` sel bulan (Jan..Des)| Padding 10x4px, radius 8px, teks `#111827`, hover `#f3f4f6`. `.is-selected` solid `#315B8C`. |
| `.datepicker-popover-footer` | `<div>` bilah aksi cepat | Flexbox between, border-top `#f3f4f6`, padding-top 8px. |
| `.datepicker-footer-btn` | `<button>` aksi cepat | Teks flat responsif. `.btn-today` (#315B8C) & `.btn-clear` (#6b7280 hover red). |

---

### 3.4. Cetak Biru DOM & Helper PHP Resmi (The Golden Blueprint)

Seluruh pemanggilan input tanggal di dalam view **MUTLAK WAJIB** menggunakan helper `render_datepicker()` di [`app/helpers.php`](file:///home/m4yestik/Projects/client/remittance.raxbyte.com/app/helpers.php):

```php
<!-- Mode Tanggal Harian Tunggal (Default) -->
<?= render_datepicker('report_date', (string) $selectedDate, [
    'placeholder' => 'Pilih tanggal report',
    'required'    => true,
]) ?>

<!-- Mode Filter Periode Tanggal -->
<?= render_datepicker('date_from', (string) ($filters['date_from'] ?? ''), [
    'placeholder' => 'Pilih tanggal mulai',
]) ?>
<?= render_datepicker('date_to', (string) ($filters['date_to'] ?? ''), [
    'placeholder' => 'Pilih tanggal akhir',
]) ?>

<!-- Mode Bulan & Tahun -->
<?= render_datepicker('month_key', (string) ($filters['month'] ?? ''), [
    'mode'        => 'month',
    'placeholder' => 'Pilih bulan report',
    'required'    => false,
]) ?>
```

#### Anatomi DOM yang Dihasilkan Helper:
```html
<div class="custom-datepicker-root relative" x-data="customDatePicker({ name: 'date', value: '2026-10-29', mode: 'date' })" @click.outside="open = false">
    <!-- Persistence Form Field (GET/POST 100% Kompatibel) -->
    <input type="hidden" :name="name" :value="value">

    <!-- Tombol Pemicu Flat 40px -->
    <button type="button" @click="open = !open" class="custom-datepicker-trigger" :class="{ 'is-open': open }" :aria-expanded="open.toString()">
        <div class="flex items-center gap-2 truncate">
            <i class="bi bi-calendar3 text-gray-500 text-sm"></i>
            <span x-text="displayLabel || placeholder" :class="Boolean(value) ? 'datepicker-value' : 'datepicker-placeholder'"></span>
        </div>
        <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200 shrink-0 ms-1.5" :class="{ 'rotate-180': open }"></i>
    </button>

    <!-- Interior Popover Kalender Flat 280px -->
    <div x-show="open" x-cloak class="datepicker-popover" style="display: none;">
        <template x-if="mode === 'date'">
            <div>
                <!-- Header Navigasi -->
                <div class="datepicker-popover-header">
                    <button type="button" @click.stop="prevMonth()" class="datepicker-nav-btn" aria-label="Bulan sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <span class="datepicker-popover-title" x-text="currentMonthName + ' ' + viewYear"></span>
                    <button type="button" @click.stop="nextMonth()" class="datepicker-nav-btn" aria-label="Bulan berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <!-- Header Nama Hari -->
                <div class="datepicker-days-header">
                    <template x-for="dn in dayNames" :key="dn">
                        <span class="datepicker-day-name" x-text="dn"></span>
                    </template>
                </div>

                <!-- Kisi Tanggal 7-Kolom -->
                <div class="datepicker-days-grid">
                    <template x-for="(d, idx) in daysInMonth" :key="idx">
                        <button
                            type="button"
                            @click.stop="d.isCurrentMonth && selectDate(d.dateStr)"
                            :disabled="!d.isCurrentMonth"
                            class="datepicker-date-btn"
                            :class="{
                                'is-outside': !d.isCurrentMonth,
                                'is-selected': d.isSelected,
                                'is-today': d.isToday && !d.isSelected
                            }"
                            x-text="d.day"
                        ></button>
                    </template>
                </div>

                <!-- Footer Aksi Cepat -->
                <div class="datepicker-popover-footer">
                    <button type="button" @click.stop="setToday()" class="datepicker-footer-btn btn-today">Hari ini</button>
                    <button type="button" x-show="value" @click.stop="clear()" class="datepicker-footer-btn btn-clear">Hapus</button>
                </div>
            </div>
        </template>
    </div>
</div>
```

---

### 3.5. Daftar 7 Larangan Mutlak Datepicker (The Anti-Patterns Checklist)

1. **Dilarang Native Datepicker Shadow DOM:** Penggunaan tag `<input type="date">` atau `<input type="month">` diharamkan secara mutlak.
2. **Dilarang Skeuomorphic Inset Shadow:** Dilarang memberi `box-shadow: inset ...` atau latar semen `#F5F2F2` pada tombol pemicu `.custom-datepicker-trigger`.
3. **Dilarang Warna Abu-Abu Placeholder di Tanggal:** Teks angka tanggal aktif DILARANG menggunakan `text-gray-400`, `text-gray-600`, atau `text-gray-700`. Teks wajib menggunakan warna hitam kontras tinggi **`#111827`**.
4. **Dilarang Warna Placeholder pada Header Hari & Aksi:** Header hari wajib menggunakan `#6b7280` (`gray-500`) dan tombol aksi Hapus wajib menggunakan `#6b7280` dengan hover merah `#dc2626`. Dilarang hardcode warna `#9ca3af`.
5. **Dilarang Box-Shadow / Gradient pada Tanggal Terpilih:** State tanggal terpilih (`.is-selected`) wajib murni flat solid `#315B8C` dengan teks putih. Dilarang menambahkan efek glow, bevel, gradient, atau drop shadow.
6. **Dilarang Rantai Utilitas Ad-Hoc:** Seluruh penataan visual popover kalender wajib merujuk pada kelas komponen resmi di `@layer components` (`.datepicker-popover`, `.datepicker-date-btn`, dll).
7. **Dilarang Merusak Form Persistence:** Setiap datepicker wajib menyertakan `<input type="hidden" :name="name" :value="value">` agar pengiriman formulir GET/POST tetap 100% kompatibel dengan backend controller.

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
     - **Pill Profil & Sesi (.spa-dock-user & .spa-avatar):** Menampilkan avatar dan nama pengguna dengan kelas kustom komprehensif terpusat `.spa-dock-user` (tinggi terkunci 38px, radius 12px, surface well #F5F2F2, inset shadow mikro) dan avatar `.spa-avatar` ($26\times 26\text{px}$, radius 8px, latar Deep Brand Blue `#315B8C` murni, teks inisial putih), dengan upward popover `.spa-dock-popover` untuk tautan Akun (`/profile` via `.dropdown-item`) dan tombol Keluar (`POST /logout` via `.dropdown-item.danger` dengan CSRF token valid). Dilarang keras menggunakan string utilitas atomik ad-hoc sementara seperti `size-6`, `bg-blue-600`, atau `border-gray-200/80` (Bagian 18).
   - **Mobile (`< sm`):** Navigasi menggunakan **Fixed Bottom Tab Bar** (`fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-2 py-1 pb-[calc(0.25rem+env(safe-area-inset-bottom,0px))]`):
     - Memuat tab ringkas `.spa-mobile-tab` (Dashboard, Transaksi, Laporan, Menu, Akun) dengan avatar ringkas `.spa-avatar.spa-avatar-sm` ($22\times 22\text{px}$, radius 6px).
     - Tombol "Menu" membuka **Mobile Bottom Sheet / Drawer** yang meluncur halus dari bawah (`translate-y` transition dengan backdrop blur) memuat kartu profil akun terstandarisasi `.spa-user-card` dengan avatar besar `.spa-avatar.spa-avatar-lg` ($40\times 40\text{px}$, radius 10px), tombol logout terstandarisasi `.btn-action-icon.btn-action-icon-danger`, serta daftar modul lengkap yang terorganisir rapi.
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
   - Filter lanjutan pada seluruh tingkatan hierarki laporan (`/reports`, `/reports/month/{month}`, dan `/reports/date/{date}`) diintegrasikan ke dalam tombol aksi ramping `[Filter]` di samping `[Upload Excel]`, memicu dialog modal filter (`openFilterModal`) saat ditekan dengan badge notifikasi aktif `.btn-filter-badge`.
   - Peniadaan total card filter tebal terbuka atau accordion dari aliran kanvas utama halaman, menjamin densitas informasi optimal dan tata letak berwibawa (*Pure Naked Architecture*). Setiap halaman mempertahankan fungsi filternya masing-masing secara spesifik (periode tahun/bulan di `/reports`, tim/tanggal/status kirim di `/reports/month/{month}`, serta tim/status kirim/hasil transaksi di `/reports/date/{date}`).

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

### 13.4. Spesifikasi Badge Notifikasi & Penghitung Aktif pada Tombol (.btn-filter-badge / .btn-counter-badge)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Seluruh badge notifikasi aktif, penghitung filter, atau indikator status di dalam tombol aksi (khususnya tombol `.btn-primary` seperti tombol Filter di `/reports`) **MUTLAK WAJIB** menggunakan kelas terpusat `.btn-filter-badge` di `@layer components`.  
> Penggunaan kode utilitas ad-hoc sementara seperti string atomik instan (misal `min-w-4.5 h-4.5`, `px-1.5`, `text-[11px] font-bold`) **DIHARAMKAN SECARA MUTLAK (BAGIAN 18)**.

1. **Proporsi Geometri & Imunitas Skala 125% W3C:**
   - **Tinggi Terkunci:** Presisi `20px` (`height: 20px;`) — tepat 50% dari ketinggian tombol 40px (`.btn-primary`).
   - **Lebar Minimum:** Presisi `20px` (`min-width: 20px;`) — menjamin angka 1 digit membentuk lingkaran sempurna $20\times 20\text{px}$.
   - **Padding Horizontal:** Presisi `0 5px` — otomatis bertransformasi menjadi kapsul proporsional saat memuat 2 digit atau lebih tanpa distorsi.
   - **Radius Membulat Penuh:** `border-radius: 9999px;` (lingkaran / kapsul penuh murni).
   - **Pusat Sumbu Simetris:** `display: inline-flex; align-items: center; justify-content: center; box-sizing: border-box; line-height: 1;`.
2. **Tipografi & Diet Bold:**
   - Font family: `var(--font-cta, 'Inter', 'DM Sans', system-ui, sans-serif)`.
   - Ukuran & Ketebalan: `11px` dengan `font-weight: 600` (diet bold, dilarang font 800/900).
   - Tabular Nums: `font-feature-settings: "tnum"; font-variant-numeric: tabular-nums;` untuk memastikan angka berjarak simetris.
3. **Skema Warna Solid Kunci (Bagian 11 - Tabel 11.1):**
   - **Varian Primer (Vibrant Coral `#F2765E`):** Warna default resmi badge notifikasi aktif RemitGo!.
     - Latar: `#F2765E` (Solid Vibrant Coral Accent - Aksen Kinetik & Penanda Fokus).
     - Teks: `#ffffff` (Solid Clean White).
     - Kontur Border: `1px solid rgba(255, 255, 255, 0.45)` (memisahkan badge secara tegas dari latar biru tombol).
     - Bayangan: `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);`.
     - Text Shadow: `0 1px 1px rgba(0, 0, 0, 0.2);`.
   - **Varian Bersih Putih (`.variant-white` / `.btn-filter-badge-white`):**
     - Latar: `#ffffff`.
     - Teks: `#315B8C` (Deep Brand Blue).
     - Kontur Border: `1px solid rgba(255, 255, 255, 0.85)`.
     - Bayangan: `0 1px 2px rgba(0, 0, 0, 0.15)`.
     - Text Shadow: `none !important`.
4. **Bebas Transform:** State hover/active tombol tidak memicu zoom atau pergeseran posisi pada badge (`transform: none !important;`).

### 13.5. Spesifikasi Tombol Aksi Ikon Tabel & Kartu (.btn-action-icon)

> **STATUS ATURAN: TERKUNCI MUTLAK & HUKUM WAJIB (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Seluruh tombol aksi ikon pada baris tabel transaksi, tabel laporan, kartu mobile, maupun daftar batch (seperti tombol Export Excel baris, Edit Metadata, dan Hapus Batch) **MUTLAK WAJIB** menggunakan kelas terpusat `.btn-action-icon` di `@layer components`.  
> Penggunaan kode utilitas atomik ad-hoc sementara seperti `size-7 ...` atau `size-8 ...` dengan rangkaian kelas Tailwind instan **DIHARAMKAN SECARA MUTLAK (BAGIAN 18)**.

1. **Simetri Modular 1:1 dengan `.btn-primary-sm`:**
   - **Dimensi Terkunci:** Presisi $32\times 32\text{px}$ (`height: 32px; width: 32px; min-width: 32px; min-height: 32px; padding: 0;`).
   - **Radius Kelengkungan:** `border-radius: 7px;` — selaras sempurna 1:1 dengan tombol primer kompak `.btn-primary-sm`.
   - **Pusat Sumbu:** `display: inline-flex; align-items: center; justify-content: center; box-sizing: border-box; line-height: 1;`.
   - **Ukuran Ikon:** `14px` (`font-size: 14px;`) tanpa pembungkus buatan.
2. **Permukaan Taktil & Fisika Gamified Asphalt:**
   - **Latar & Kontur:** Background solid `#ffffff`, border solid `1px solid #d1d5db`, bayangan mikro `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04)`.
   - **Warna Ikon Netral:** `#413333` (Deep Asphalt Brown). DILARANG menggunakan warna alien seperti `text-emerald-600` (Bagian 14).
   - **Hover State:** Background `#F5F2F2` (Asphalt Off-White Base), border `#9ca3af`, ikon `#111827`.
   - **Active State (Tactile Micro-press):** `transform: translateY(1px) !important; background: #e5e7eb; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);`.
   - **Focus State:** `outline: 2px solid #315B8C; outline-offset: 2px;`.
3. **Varian Destruktif Bahaya (.btn-action-icon-danger):**
   - Khusus aksi berbahaya seperti "Hapus Batch" / "Hapus Item".
   - Warna teks & ikon: `#dc2626` (Danger Red).
   - Kontur border: `1px solid #fecaca`.
   - Hover: Background `#fef2f2`, border `#fca5a5`, teks `#b91c1c`.
   - Active: `transform: translateY(1px) !important; background: #fee2e2; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12);`.
   - Focus: `outline: 2px solid #ef4444; outline-offset: 2px;`.

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
     <!-- Ikon Mata: Tampilkan Password (Saat Password Tersembunyi) -->
     <i
         x-show="!showPass"
         @click="showPass = true"
         @keydown.enter.prevent="showPass = true"
         @keydown.space.prevent="showPass = true"
         role="button"
         tabindex="0"
         class="bi bi-eye absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none focus:outline-none transition-colors"
         aria-label="Tampilkan password"
         title="Tampilkan password"
     ></i>

     <!-- Ikon Mata Tercoret: Sembunyikan Password (Saat Password Terlihat) -->
     <i
         x-show="showPass"
         x-cloak
         @click="showPass = false"
         @keydown.enter.prevent="showPass = false"
         @keydown.space.prevent="showPass = false"
         role="button"
         tabindex="0"
         class="bi bi-eye-slash absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none focus:outline-none transition-colors"
         aria-label="Sembunyikan password"
         title="Sembunyikan password"
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

### 16.2. Geometri, Elevasi & Tata Letak Floating Viewport Stack (Hukum Penempatan Bawah Mutlak)
1. **Posisi Bawah Terkunci Mutlak & Penataan Tumpukan (Bottom Floating Viewport Stack Law):**
   - **Hukum Penempatan Bawah Mutlak (Universal Bottom Placement Law):** Seluruh notifikasi toast di seluruh aplikasi **MUTLAK WAJIB DILETAKKAN DI BAWAH VIEWPORT** (`fixed bottom-...`). Penempatan toast di bagian atas layar (`top-...`) **DIHARAMKAN SECARA MUTLAK** karena berbenturan dengan hierarki visual header, search bar, filter formulir, serta mengganggu alur pandang alami pengguna.
   - **Desktop & Tablet (`sm:` dan ke atas):** Menempati sudut kanan bawah layar (`fixed sm:bottom-6 sm:right-6 sm:left-auto z-[9999]`). Posisi ini memberikan ruang lega dan selaras berdampingan tanpa bertabrakan dengan *SPA Bottom Navigation Dock* yang berada di tengah bawah (`fixed bottom-5 left-1/2 -translate-x-1/2 z-40`).
   - **Mobile (`< sm`):** Menempati area bawah horizontal penuh dengan jarak aman di atas bilah tab navigasi mobile (`fixed bottom-20 inset-x-4 z-[9999] w-auto`). Jarak `bottom-20` (80px) menjamin toast tidak pernah menutupi atau tertutup oleh bilah tab navigasi bawah (*Fixed Bottom Tab Bar*).
   - **Tumpukan Vertikal:** Tumpukan toast tersusun rapi secara vertikal dengan celah teratur `space-y-3` (`gap-3`), dengan toast terbaru bertumpuk ke atas secara teratur.
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
- **Spesifikasi Tombol Tutup Kustom Terkunci (`.toast-close`):**
  - Tombol tutup toast **MUTLAK WAJIB** menggunakan kelas terpusat `.toast-close` di `@layer components` (dilarang keras menggunakan rangkaian utilitas ad-hoc sementara / placeholder mematuhi Bagian 18).
  - Geometri: Kotak presisi $28\times 28\text{px}$, radius modular 8px, margin kompensasi `-3px` atas dan kanan untuk alignment presisi.
  - Ikon: Wajib menggunakan ikon tebal terstandarisasi `<i class="bi bi-x-lg text-xs leading-none"></i>` (dilarang menggunakan glyph matematika tipis `bi-x`).
  - Interaksi Fisika: Opacity netral `0.65`, hover `opacity: 1` dengan latar `rgba(0, 0, 0, 0.08)`, tactile active micro-press `translateY(1px)` dengan latar `rgba(0, 0, 0, 0.14)`, serta focus-visible ring `2px solid currentColor`.

### 16.4. Anatomi DOM Toast Kustom Terkunci (Alpine.js)

Setiap elemen toast wajib mengimplementasikan struktur hierarki berikut:

```html
<!-- KONTINER UTAMA TUMPUKAN TOAST (DI LAYOUT APP & GUEST - PENEMPATAN BAWAH TERKUNCI) -->
<div
    class="fixed bottom-20 sm:bottom-6 right-0 sm:right-6 left-0 sm:left-auto z-[9999] w-full sm:max-w-md pointer-events-none space-y-3 px-4 sm:px-0"
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
            x-transition:enter-start="opacity-0 translate-y-[8px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[8px]"
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

                <!-- Tombol Tutup Kustom (.toast-close - Bebas Placeholder & Bebas Style Native Browser) -->
                <button
                    type="button"
                    class="toast-close"
                    @click="dismiss(toast.id)"
                    aria-label="Tutup notifikasi"
                    title="Tutup notifikasi"
                >
                    <i class="bi bi-x-lg text-xs leading-none"></i>
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
2. **Dilarang Keras Efek Bounce/Scale Liar:** Transisi toast dilarang menggunakan `scale-95`, `scale-105`, atau translasi jarak jauh (> 10px). Transisi masuk/keluar wajib murni opasitas dan micro-shift halus dari bawah `translate-y-[8px]`.
3. **Dilarang Keras Menggunakan Warna Gradien atau Efek Kaca:** Background toast wajib solid murni sesuai 4 varian resmi tabel 16.3.
4. **Dilarang Keras Tombol Tutup Native Browser:** Tombol dismiss wajib menggunakan kelas terpusat `.toast-close` dengan Bootstrap Icons `<i class="bi bi-x-lg text-xs leading-none"></i>` (Bagian 20), bukan kontrol silang native OS atau glif kurus.

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
    <!-- Ikon Mata: Tampilkan Password (Saat Password Tersembunyi) -->
    <i
        x-show="!showPass"
        @click="showPass = true"
        @keydown.enter.prevent="showPass = true"
        @keydown.space.prevent="showPass = true"
        role="button"
        tabindex="0"
        class="bi bi-eye absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none focus:outline-none transition-colors"
        aria-label="Tampilkan password"
        title="Tampilkan password"
    ></i>

    <!-- Ikon Mata Tercoret: Sembunyikan Password (Saat Password Terlihat) -->
    <i
        x-show="showPass"
        x-cloak
        @click="showPass = false"
        @keydown.enter.prevent="showPass = false"
        @keydown.space.prevent="showPass = false"
        role="button"
        tabindex="0"
        class="bi bi-eye-slash absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none focus:outline-none transition-colors"
        aria-label="Sembunyikan password"
        title="Sembunyikan password"
    ></i>
</div>
```

### 17.4. Larangan Mutlak & Kepatuhan Anti-Slop Form Input
1. **Dilarang Keras Ketinggian Non-Standar:** Dilarang menggunakan utilitas ketinggian liar seperti `h-11`, `h-12`, atau `h-14` yang membengkak di bawah root scaling 20px desktop. Tinggi standar wajib 40px (`.custom-form-input`) atau 50px untuk varian lansia (`.custom-form-input-lg`).
2. **Dilarang Keras Celah Ikon Renggang:** Padding teks kiri jika ada ikon dilarang melebihi 40px untuk input 40px atau 48px untuk input 50px. Wajib menggunakan modifier `.has-leading-icon` agar teks dan ikon berjarak rapat dan harmonis.
3. **Dilarang Keras Tampilan Datar Putih di Atas Putih:** Input di atas kartu putih wajib memiliki latar cekungan `#F5F2F2` dengan bayangan inset mikro.
4. **Dilarang Keras Warna Alien:** Border fokus dilarang menggunakan warna ungu, pink, atau hijau. Wajib menggunakan warna master `#315B8C` (*Deep Brand Blue*).

### 17.5. Standar Varian Form Aksesibilitas Tinggi & Ramah Lansia (Senior & High-Accessibility Law)

Untuk formulir autentikasi publik, portal login, atau bidang input yang memerlukan keterbacaan tinggi (*high legibility*) bagi pengguna lanjut usia (lansia), aplikasi menyediakan varian resmi terstandarisasi di `@layer components`:

1. **Input Teks Varian Lansia (`.custom-form-input-lg` / `.form-input-lg`):**
   - **Ketinggian:** 50px presisi (`height: 50px; line-height: 50px;`).
   - **Ukuran Huruf:** 16px Roboto Normal (`font-size: 16px; font-weight: 400;`).
   - **Radius Sudut:** 11px modular (`border-radius: 11px;`).
   - **Placeholder Kontras Tinggi:** `#6b7280` dengan bobot normal 400 (memenuhi standar kontras WCAG AAA untuk lansia).
   - **Spasi Ikon Besar:** Padding kiri/kanan 48px (`.has-leading-icon` / `.has-trailing-action`), selaras dengan ikon 20px (`text-xl`) di `left-4` / `right-4`.
2. **Label Form Lansia (Bebas Bold):**
   - Tipografi `text-sm sm:text-base font-normal text-gray-700 mb-1.5` (bebas bold, sangat mudah dibaca, kontras pas terhadap kanvas putih).
3. **Tombol Aksi Utama Lansia (`.btn-primary-lg` / `.btn-lg.btn-primary`):**
   - **Ketinggian:** 50px presisi (selaras 1:1 dengan input 50px).
   - **Ukuran Huruf:** 17px DM Sans Medium (`font-size: 17px; font-weight: 500;`).
   - **Radius Sudut:** 11px modular.
   - **Hit Target Luas:** Memberikan area sentuh yang mantap bagi pengguna lansia di perangkat mobile maupun desktop.
4. **Hukum Larangan Huruf Tebal (Non-Bold Discipline pada Login):**
   - Huruf tebal / bold (`font-weight: 600`, `font-bold`, `font-semibold`) **DIHARAMKAN SECARA MUTLAK pada formulir login**. Huruf tebal menyebabkan penumpukan piksel/tinta digital yang menyulitkan mata lansia dalam membedakan glif karakter. Seluruh teks label dan input wajib `font-weight: 400`, dan tombol aksi `font-weight: 500`.

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
1. **Langkah 1 (Audit Spesifikasi Terkunci):** Periksa apakah elemen yang akan dirombak sudah memiliki kelas baku di `GEMINI.md` (Tombol & Badge Notifikasi di Bagian 13, Input Form di Bagian 17, Search Bar di Bagian 6, Datepicker di Bagian 3, Dropdown di Bagian 4, Toast di Bagian 16, Alert di Bagian 2).
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

---

## 19. Aturan Terkunci: Arsitektur Desain Halaman Login & Autentikasi (Universal Split-Screen Non-Scrollable Login Architecture Law)

> **STATUS ATURAN: HUKUM MUTLAK & TERKUNCI PERMANEN (STRICT COMPLIANCE & NON-NEGOTIABLE)**  
> Halaman login (`/login`, view: `app/Views/auth/login.php`, layout: `app/Views/layouts/guest.php`) adalah pintu gerbang operasional utama aplikasi perbankan dan remitansi **RemitGo!**. Seluruh pengembang dan agen AI **MUTLAK WAJIB** mematuhi konstitusi desain ini secara utuh tanpa modifikasi spekulatif atau pengurangan standar estetika, aksesibilitas, dan fungsionalitas.

### 19.1. Filosofi Arsitektur & Prinsip Pembeda
1. **Pemisahan Visual 50/50 Full-Screen:** Menyeimbangkan visual berwibawa enterprise di sisi kiri dengan ruang kerja formulir masuk yang ergonomis di sisi kanan.
2. **Imunitas Terhadap Scrollbar Desktop (Universal Non-Scrollable Fit Viewport):** Tampilan login pada resolusi desktop ($\ge 1024\text{px}$) **MUTLAK FIT DI DALAM VIEWPORT (100vh)** tanpa memicu scrollbar vertikal pada peramban web klien.
3. **Pemisah Bergelombang Organik (The Wavy Boundary):** Batas pembatas visual art dan kanvas formulir login dilarang berupa garis lurus geometris yang kaku. Wajib berupa kurva gelombang organik dinamis yang menyatu lembut ke kanvas putih formulir.
4. **Ergonomi Ramah Lansia (Senior & High-Accessibility Standard):** Ukuran kontrol diperbesar secara proporsional (ketinggian 50px) dengan tipografi non-bold jernih untuk memfasilitasi pengguna lanjut usia dan operator perbankan.
5. **Universal Naked Action Icon:** Ikon aksi interaktif (khususnya visibilitas password) wajib berdiri telanjang murni sebagai elemen `<i>` semantik tanpa pembungkus dekoratif apa pun.

---

### 19.2. Geometri Tata Letak Split 50/50 & Pengendalian Viewport (Desktop vs Mobile)

```
+-------------------------------------------------------------------------------+
|                                  VIEWPORT 100vh                               |
| +------------------------------------+--------------------------------------+ |
| |        KOLOM KIRI (50% WIDTH)      |        KOLOM KANAN (50% WIDTH)       | |
| |                                    |                                      | |
| |  Visual Art Penuh (Object-Cover)   |   [Logo RemitGo! (Remit: Oranye)]    | |
| |  - AVIF -> WebP -> JPEG Fallback   |                                      | |
| |  - Background: Slate-900           |   [Input Username / Email (50px)]    | |
| |                                    |   - Ikon person (20px, left-4)       | |
| |  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ |                                      | |
| |  PEMISAH BERGELOMBANG ORGANIK      |   [Input Password (50px)]            | |
| |  - Desktop: SVG Vertikal Kanan     |   - Ikon lock (20px, left-4)         | |
| |  - Mobile:  SVG Horizontal Bawah   |   - Ikon mata telanjang (right-4)    | |
| |                                    |                                      | |
| |                                    |   [Tombol Masuk .btn-primary (50px)] | |
| |                                    |   - Non-bold (Font-Medium 500)       | |
| +------------------------------------+--------------------------------------+ |
+-------------------------------------------------------------------------------+
```

1. **Struktur Kontainer Induk:**
   - Kelas Kontainer: `<div class="h-screen max-h-screen w-full flex flex-col lg:flex-row bg-[#F5F2F2] overflow-hidden">`.
   - Layout CSS Global: `html, body.login-body { height: 100%; max-height: 100vh; overflow: hidden; }`.
   - **Larangan Scrollbar Desktop:** Dilarang menggunakan padding atau margin berlebih yang memaksa dokumen melebihi `100vh`. Seluruh elemen formulir dan kartu wajib berada rapi di tengah secara vertikal (`my-auto`).
2. **Responsivitas Perangkat:**
   - **Desktop ($\ge 1024\text{px}$):** Dua kolom berdampingan (`lg:flex-row`), masing-masing mengambil porsi tepat 50% (`w-full lg:w-1/2`). Kolom kiri `lg:h-full lg:min-h-screen`, kolom kanan `lg:h-screen`.
   - **Tablet & Mobile ($< 1024\text{px}$):** Tata letak bertumpuk vertikal (`flex-col`). Kolom visual art menyusut proporsional menjadi banner atas (`h-44 sm:h-56`) dengan pemisah bergelombang horizontal di bagian bawah, sementara kolom formulir mengisi area bawah secara fleksibel (`flex-1`).

---

### 19.3. Kolom Kiri Visual Art & Pemisah Bergelombang (The Wavy Boundary Law)

1. **Rendering Aset Visual Multi-Format:**
   - Wajib menggunakan elemen `<picture class="w-full h-full block">` dengan fallback format berkinerja tinggi:
     1. `<source srcset="<?= e(url('/login_art.avif')) ?>" type="image/avif">`
     2. `<source srcset="<?= e(url('/login_art.webp')) ?>" type="image/webp">`
     3. `<img src="<?= e(url('/login_art.jpeg')) ?>" alt="RemitGo! Visual Art" class="w-full h-full object-cover object-center select-none" loading="eager" width="1696" height="2528">`
   - Kontainer gambar wajib memiliki kelas `relative overflow-hidden bg-slate-900 flex-shrink-0`.
2. **Pemisah Bergelombang Organik (Organic Wavy Divider):**
   - **Hukum Anti-Garis Lurus:** Garis pemisah antara area visual art dan area form login **MUTLAK DILARANG LURUS KAKU**. Wajib menggunakan kurva bergelombang organik berbasis SVG vektor dengan warna teks putih (`text-white`) yang menyatu sempurna tanpa celah ke latar formulir:
   - **Varian Desktop (Vertikal di Tepi Kanan Visual Art):**
     ```html
     <svg class="hidden lg:block absolute inset-y-0 -right-[1px] h-full w-14 lg:w-16 xl:w-20 2xl:w-24 pointer-events-none z-10 text-white" viewBox="0 0 100 1000" preserveAspectRatio="none" aria-hidden="true">
         <path fill="currentColor" d="M 100 0 L 100 1000 L 40 1000 C 15 900, 80 850, 45 750 C 10 650, 75 550, 35 450 C 5 350, 70 250, 40 150 C 15 80, 60 40, 30 0 Z"></path>
     </svg>
     ```
   - **Varian Mobile (Horizontal di Tepi Bawah Visual Art):**
     ```html
     <svg class="block lg:hidden absolute -bottom-[1px] inset-x-0 w-full h-6 sm:h-8 pointer-events-none z-10 text-white" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
         <path fill="currentColor" d="M 0 120 L 1200 120 L 1200 45 C 1050 85, 900 15, 750 50 C 600 85, 450 15, 300 50 C 150 85, 50 20, 0 45 Z"></path>
     </svg>
     ```

---

### 19.4. Spesifikasi Logo Resmi RemitGo! & Cache Invalidation

1. **Geometri & Penempatan:**
   - Diletakkan di bagian atas formulir dengan perataan tengah penuh (`flex flex-col items-center justify-center text-center`).
   - Ukuran terkunci: `class="h-12 sm:h-14 lg:h-16 w-auto max-w-[200px] sm:max-w-[240px] object-contain select-none"`.
   - Atribut wajib: `width="1720" height="744" loading="eager"`.
   - Aksesibilitas: Teks pembaca layar `<span class="sr-only">RemitGo!</span>`.
2. **Kepatuhan Warna Tipografi Logo:**
   - **Segmen Teks "Remit":** **MUTLAK WAJIB BERWARNA ORANYE SOLID (`#F2765E` - Vibrant Coral Accent)**. DILARANG KERAS menggunakan warna putih atau biru karena teks putih akan hilang tertelan latar belakang putih formulir.
   - **Segmen Teks "Go!":** Wajib solid Brand Blue (`#2482FF` / `#315B8C`).
   - **Maskot:** Deep Blue (`#0065F8`), Asphalt Dark (`#383838`), dan sorot mata putih (`#FFFFFF`).
3. **Mekanisme Anti-Cache Browser (Cache-Buster):**
   - URL logo pada view wajib menyertakan query string versi: `src="<?= e(url('/remitgo.png?v=20261027')) ?>"` untuk menjamin pembaruan aset seketika di seluruh peramban pengguna.
4. **Pemusnahan Subjudul & Paragraf Basa-Basi:**
   - Logo berdiri telanjang. **DILARANG KERAS** menambahkan subtitle seperti "Silakan masukkan kredensial Anda", "Sistem Masuk Aman Remitansi", atau slogan promosi lainnya di bawah logo.

---

### 19.5. Standar Varian Ergonomi Ramah Lansia (Senior Accessibility Law)

Berdasarkan demografi pengguna operasional perbankan yang mencakup usia lanjut, elemen formulir pada halaman `/login` beroperasi dengan standar dimensi besar terstandarisasi:

1. **Bidang Input Teks Lansia (`.custom-form-input.custom-form-input-lg`):**
   - **Ketinggian Fisik:** 50px presisi (`height: 50px; line-height: 50px;`).
   - **Ukuran Tipografi:** 16px Roboto Normal (`font-size: 16px; font-weight: 400;`).
   - **Radius Sudut:** 11px modular (`border-radius: 11px;`).
   - **Kedalaman Tekstur:** Permukaan well `#F5F2F2` dengan bayangan cekung mikro `box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04)`.
   - **Batas Kontur & Fokus:** Border `#d1d5db`, transisi fokus ke putih bersih dengan border solid `#315B8C` (*Deep Brand Blue*) dan ring halo `3px rgba(49, 91, 140, 0.15)`.
   - **Padding Ikon:** Kelas `.has-leading-icon` menyematkan padding kiri 44px (`padding-left: 44px;`) untuk memberikan spasi lega terhadap ikon `left-4`.
2. **Tombol Masuk Utama Lansia (`.btn-primary.btn-primary-lg`):**
   - **Ketinggian Fisik:** 50px presisi (selaras 1:1 dengan input 50px).
   - **Ukuran Tipografi:** 17px DM Sans Medium (`font-size: 17px; font-weight: 500;`).
   - **Radius Sudut:** 11px modular (`border-radius: 11px;`).
   - **Padding:** Horizontal 24px (`px-6`).
   - **Elevasi Taktil:** Inset highlight atas `inset 0 1px 0 rgba(255,255,255,0.32)`, tepi bawah `inset 0 -1px 0 rgba(0,0,0,0.16)`, dan bayangan luar `0 1px 2px rgba(0,0,0,0.55)`.
   - **Fisika Taktil Bebas Transform:** Hover brightness 110%, active micro-press `translateY(1px)` dengan bayangan inset.
3. **Dimensi Ikon:**
   - Ikon leading (`bi-person`, `bi-lock`) dan ikon trailing (`bi-eye`, `bi-eye-slash`) menggunakan ukuran 20px (`text-xl`) berwarna netral `#9ca3af` (`text-gray-400`).

---

### 19.6. Hukum Mutlak Pemusnahan Huruf Tebal (Universal Non-Bold Login Law)

1. **Alasan Ergonomi Keterbacaan:**
   Huruf tebal (*bold / font-weight: 600, 700, 800*) memicu penumpukan tinta digital (*pixel bleeding*) yang menyulitkan mata lansia dalam mengenali detail huruf pada formulir berdensitas ringkas.
2. **Spesifikasi Bobot Huruf Terkunci pada `/login`:**
   - Label formulir: `font-normal` (`font-weight: 400; color: #374151; font-size: 0.875rem` hingga `1rem`).
   - Input teks & placeholder: `font-normal` (`font-weight: 400; font-size: 16px;`).
   - Tombol submit CTA: `font-medium` (`font-weight: 500; font-size: 1.0625rem;`).
   - **Larangan Mutlak:** DILARANG KERAS menyematkan kelas `font-bold`, `font-semibold`, atau tag `<b>`, `<strong>` pada label, input, atau tombol di halaman `/login`.

---

### 19.7. Ikon Aksi Telanjang & Toggle Visibilitas Password (Naked Action Icon Law)

1. **Pemusnahan Wrapper Tombol / Kapsul:**
   - Ikon interaktif toggle visibilitas password **MUTLAK DILARANG DIBUNGKUS** menggunakan elemen `<button>`, `<span>`, atau wadah dekoratif berlatar warna.
   - Ikon wajib berdiri telanjang murni sebagai elemen `<i>` interaktif yang diletakkan langsung di dalam kontainer input relatif (`relative flex items-center`).
2. **Pola Dua Ikon Terpisah Berbasis Kondisi Visibilitas:**
   - **Saat Password Tersembunyi (Kondisi Default):**
     Menampilkan ikon mata terbuka (`bi-eye`) untuk memicu aksi melihat password.
   - **Saat Password Terlihat (Kondisi Aktif):**
     Menampilkan ikon mata tercoret (`bi-eye-slash`) untuk memicu aksi menyembunyikan kembali password.
3. **Kepatuhan Aksesibilitas Standar W3C (Keyboard & Screen Reader Accessibility):**
   - Wajib menyertakan `role="button"` dan `tabindex="0"`.
   - Wajib menyertakan keyboard event handler: `@keydown.enter.prevent` dan `@keydown.space.prevent`.
   - Wajib menyertakan `aria-label` dan `title` yang deskriptif ("Tampilkan password" / "Sembunyikan password").
4. **Implementasi DOM Standar:**
   ```html
   <div x-data="{ showPass: false }">
       <label for="loginPassword" class="block text-sm sm:text-base font-normal text-gray-700 mb-1.5 font-sans">
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
               class="custom-form-input custom-form-input-lg has-leading-icon has-trailing-action font-normal"
           >
           <!-- Ikon Mata: Tampilkan Password -->
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

           <!-- Ikon Mata Tercoret: Sembunyikan Password -->
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
   ```

---

### 19.8. Pemusnahan Total AI Slop & Ornamen Seremonial (Zero-Slop Authentication)

1. **Lencana Keamanan Palsu (Security Badges):** DILARANG KERAS menyematkan lencana/badge "Enkripsi TLS 1.3", "Single-Tenant Isolation", "Bank Grade Security", atau logo gembok hijau di halaman login.
2. **Kapsul Status Seremonial:** DILARANG menyematkan kapsul status seperti "Terbuka", "Aktif", "Server Online", atau sejenisnya.
3. **Footer & Hak Cipta Statis:** DILARANG menambahkan baris footer copyright dekoratif di dalam form area yang memakan ruang vertikal dan memicu scrollbar.
4. **Bilah Switcher Mode Tab:** DILARANG membuat tab switcher ("Masuk" vs "Daftar Akun") karena aplikasi perbankan ini bersifat privat dan tidak mendukung pendaftaran publik mandiri.

---

### 19.9. Sistem Notifikasi Flash Sesi & Toast Mengambang

1. **Peniadaan Alert Box Statis:**
   - DILARANG menampilkan kotak alert statis di atas atau di bawah formulir login yang menyebabkan pergeseran layout (*layout shift*) atau memicu munculnya scrollbar vertikal.
2. **Integrasi Floating Toast Global:**
   - Seluruh pesan galat autentikasi (seperti "Kredensial tidak valid" atau "Sesi Anda telah berakhir") wajib dikonversi menjadi popup notifikasi **Toast Kustom Mengambang** (`window.RemitToast` / `remitToastStack`).
   - Toast mengambang di area bawah layar (`fixed bottom-20 sm:bottom-6 right-0 sm:right-6 left-0 sm:left-auto z-[9999] pointer-events-none`), memiliki transisi halus dari arah bawah (`translate-y-[8px]`), border solid 1px, bayangan lembut, tombol tutup kustom, dan penghitung waktu auto-dismiss tanpa mengganggu posisi formulir kerja.

---

### 19.10. Disiplin 4 Warna Solid Murni (Strict Solid Palette Compliance)

Halaman login **MUTLAK TUNDUK** pada Bagian 11:
- **Kanvas Dasar:** `#F5F2F2` (Asphalt Off-White Base).
- **Kanvas Formulir:** `#FFFFFF` (Solid Clean White).
- **Aksen Primer Logo:** `#F2765E` (Vibrant Coral Accent pada teks "Remit").
- **Tombol & Fokus Utama:** `#315B8C` (Deep Brand Blue).
- **Teks Utama & Ikon:** `#111827`, `#374151`, `#413333` (Deep Asphalt Brown), `#9ca3af` (Netral).
- **Bebas Gradien Alien:** Dilarang menggunakan gradien ungu, warna neon, atau efek kaca blur semu pada area formulir login.

---

### 19.11. Cetak Biru DOM Terkunci (The Golden Login View Blueprint)

Berikut adalah struktur kode acuan mutlak untuk [`app/Views/auth/login.php`](file:///home/m4yestik/Projects/client/remittance.raxbyte.com/app/Views/auth/login.php):

```html
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
                        src="<?= e(url('/remitgo.png?v=20261027')) ?>"
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
                    <label for="loginUsername" class="block text-sm sm:text-base font-normal text-gray-700 mb-1.5 font-sans">
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
                            class="custom-form-input custom-form-input-lg has-leading-icon font-normal"
                        >
                    </div>
                </div>

                <!-- FIELD PASSWORD DENGAN VISIBILITY TOGGLE TELANJANG -->
                <div x-data="{ showPass: false }">
                    <label for="loginPassword" class="block text-sm sm:text-base font-normal text-gray-700 mb-1.5 font-sans">
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
                            class="custom-form-input custom-form-input-lg has-leading-icon has-trailing-action font-normal"
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

                <!-- TOMBOL AKSI UTAMA (.btn-primary-lg BEBAS BOLD - HIGH READABILITY) -->
                <div class="pt-2 sm:pt-2.5">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="btn-primary btn-primary-lg w-full justify-center h-[50px] px-6 font-medium text-base sm:text-lg leading-none text-white font-cta rounded-[11px] shadow-[inset_0_1px_0_rgba(255,255,255,0.32),inset_0_-1px_0_rgba(0,0,0,0.16),0_1px_2px_rgba(0,0,0,0.55)] [text-shadow:0_1px_1px_rgba(0,0,0,0.2)] hover:brightness-110 active:translate-y-[1px] active:brightness-95 active:shadow-[inset_0_1px_2px_rgba(0,0,0,0.35)] disabled:opacity-75 disabled:pointer-events-none"
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
```

---

### 19.12. Protokol Audit & Checklist Kepatuhan Mutlak Halaman Login

Sebelum menyerahkan tugas atau melakukan commit pada kode yang menyentuh `/login`, seluruh poin audit berikut **MUTLAK WAJIB BERNILAI YES**:
- [ ] Apakah tata letak desktop terbukti split 50/50 dan 100% fit di dalam viewport (`h-screen overflow-hidden`) tanpa memicu scrollbar vertikal?
- [ ] Apakah garis pemisah antara area visual art dan area form login menggunakan kurva bergelombang dinamis (SVG wavy divider) dan bukan garis lurus kaku?
- [ ] Apakah logo resmi RemitGo! menampilkan teks "Remit" dalam warna oranye solid `#F2765E` (bukan putih atau biru)?
- [ ] Apakah URL logo memuat parameter cache-buster `?v=...`?
- [ ] Apakah bidang input menggunakan varian lansia `.custom-form-input-lg` dengan ketinggian presisi 50px dan radius 11px?
- [ ] Apakah tombol masuk menggunakan varian `.btn-primary-lg` dengan ketinggian presisi 50px dan radius 11px?
- [ ] Apakah seluruh elemen formulir login bebas dari font-weight tebal/bold (label `font-normal` 400, tombol `font-medium` 500)?
- [ ] Apakah ikon mata password berdiri telanjang murni sebagai tag `<i>` semantik tanpa wrapper `<button>` atau `<span>`?
- [ ] Apakah fungsi toggle password bekerja dua arah (`bi-eye` untuk tampilkan, `bi-eye-slash` untuk sembunyikan) dengan aksesibilitas keyboard lengkap?
- [ ] Apakah halaman bersih dari badge keamanan palsu, lencana seremonial, teks deskripsi statis, dan footer copyright?
- [ ] Apakah notifikasi flash sesi menggunakan sistem toast mengambang di area bawah viewport bebas layout shift?

---

## 20. Aturan Terkunci: Sistem Tombol X Penutup & Pembersih Global (Universal Dismiss, Close & Clear Button Discipline)

Seluruh tombol berbasis tanda silang / penutup (Tombol "X") di seluruh aplikasi RemitGo! **MUTLAK WAJIB** menggunakan sistem komponen terstandarisasi di `@layer components`, bebas dari glif matematika kurus `bi-x`, bebas dari karakter mentah teks Unicode `×`, dan bebas dari rangkaian utilitas ad-hoc sementara (*zero-placeholder*).

### 20.1. Matriks Standar 5 Komponen Tombol X

| Komponen | CSS Class | Target Penggunaan | Geometri & Hit-Target | Ikon Resmi | Interaksi Fisika Taktil |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Toast Close** | `.toast-close` | Popup toast notifikasi (`#remitToastContainer`) | $28\times 28\text{px}$, radius 8px, margin `-3px` | `<i class="bi bi-x-lg text-xs leading-none"></i>` | Hover `opacity: 1`, bg `rgba(0,0,0,0.08)`, Active: `translateY(1px)`, bg `rgba(0,0,0,0.14)` |
| **Alert Close** | `.alert-close` | Banner notifikasi inline (`.alert`) | $26\times 26\text{px}$, radius 8px, margin `-2px -4px` | `<i class="bi bi-x-lg text-xs leading-none"></i>` | Hover `opacity: 1`, bg `rgba(0,0,0,0.06)`, Active: `translateY(1px)`, bg `rgba(0,0,0,0.12)` |
| **Modal Close** | `.modal-close` | Modal dialog, popup filter, drawer / mobile sheet | $32\times 32\text{px}$, radius 8px | `<i class="bi bi-x-lg text-xs leading-none"></i>` | Hover color `#111827`, bg `#f3f4f6`, Active: `translateY(1px)`, bg `#e5e7eb` |
| **Search Clear** | `.search-clear-btn` | Input search bar (dashboard & laporan) | $22\times 22\text{px}$, lingkaran penuh (`rounded-full`) | `<i class="bi bi-x-circle-fill text-xs leading-none"></i>` | Hover color `#ef4444`, bg `#fee2e2`, Active: `translateY(1px)`, bg `#fecaca` |
| **FX Detail Close** | `.fx-detail-close` | Modal popover detail kurs IDR | Radius 10px, sudut kanan atas modal | `<i class="bi bi-x-lg text-xs leading-none"></i>` | Hover Zero-Transform (DILARANG rotate/scale), Active: `translateY(1px)` |

### 20.2. Hukum Larangan Glif Draf & Efek Liar
1. **Pemusnahan Glif Tipis `bi-x`:** Dilarang keras menggunakan `<i class="bi bi-x"></i>` karena merupakan glif matematika tipis yang terlihat pudar pada resolusi tinggi. Seluruh tombol penutup aksi wajib menggunakan `<i class="bi bi-x-lg text-xs leading-none"></i>`.
2. **Pemusnahan Karakter Mentah `×`:** Dilarang menulis teks huruf Unicode `×` di dalam HTML tanpa tag ikon `<i>`.
3. **Pemusnahan Efek Transformasi Liar:** Dilarang menggunakan `transform: rotate(...)` atau `scale(...)` pada state hover tombol penutup mana pun (mematuhi Bagian 1.2 dan Bagian 13).
4. **Kewajiban Aksesibilitas Lengkap:** Seluruh tombol penutup wajib menyertakan atribut `aria-label="Tutup..."` dan `title="Tutup..."`.

---

## 21. Aturan Terkunci: Sistem Elemen Art Background Global (Global Ambient Art Background Law)

> **STATUS HUKUM: TERKUNCI MUTLAK & STRICT COMPLIANCE (NON-NEGOTIABLE)**  
> Seluruh antarmuka aplikasi `remittance.raxbyte.com` di semua halaman beroperasi di atas kanvas latar belakang `#F5F2F2` (Asphalt Off-White Base) yang diperkaya oleh elemen visual art RemitGo! samar, elegan, dan menyatu (*subtle ambient blend watermark*). Keberadaan elemen art ini memberikan atmosfer estetika perbankan modern tingkat tinggi tanpa mengorbankan keterbacaan data numerik atau performa rendering sedikit pun.

---

### 21.1. Prinsip Fisika & Geometri Terkunci (.global-ambient-art)

1. **Posisi Viewport Mengambang Tetap (Fixed Viewport Stacking):**
   - Kontainer elemen art dikunci menggunakan `position: fixed; inset: 0; z-index: 0; overflow: hidden; contain: strict;`.
   - Posisi tetap (*fixed*) menjamin visual art tidak ikut melompat atau tersentak saat pengguna melakukan scroll pada tabel data yang panjang.
2. **Nol Interferensi Aksesibilitas (Zero-Interference Guarantee):**
   - Wajib dilengkapi `pointer-events: none; user-select: none;` dan `aria-hidden="true"`.
   - Setiap klik mouse, tap layar sentuh, seleksi teks, atau event keyboard akan 100% menembus ke konten halaman di baliknya tanpa hambatan.
3. **Optimasi Engine & Kompositor GPU (GPU Layer Promotion):**
   - Menggunakan `transform: translateZ(0)` untuk mempromosikan elemen ke layer kompositor GPU terpisah, mencegah terjadinya *repaint cycle* pada CPU saat halaman di-scroll.
   - Atribut CSS `contain: strict;` mengisolasi kalkulasi reflow browser secara mandiri.
4. **Proteksi Cetak (Print Ink Conservation):**
   - Didukung aturan `@media print { .global-ambient-art { display: none !important; } }` sehingga saat pengguna mencetak faktur (*invoice*), laporan audit, atau tanda terima ke kertas/PDF, elemen watermark background secara otomatis ditiadakan demi penghematan tinta dan kejelasan cetak.

---

### 21.2. Spesifikasi Vektor Geometris Lingkaran Seimbang & Nyaman di Mata (Balanced Ambient Circular Vector Asset)

Aset visual art background dikunci menggunakan format vektor murni **`public/ambient-circles.svg`** (`public/assets/img/ambient-circles.svg`):
- **Komposisi Lingkaran Seimbang (15 Busur Geometris Presisi):**
  - Mengeliminasi getaran optik (*moiré effect*) dengan **mengharamkan garis putus-putus (`stroke-dasharray`) dan matriks titik (*dot grids*)**.
  - Mengatur **15 lingkaran konsentris dan busur orbit tenang** berjarak sangat lega (120px) yang terdistribusi secara seimbang:
    1. *Top-Right Ripple Hub*: 5 lingkaran konsentris bertahap (`#315B8C`) + 1 aksen coral (`#F2765E`).
    2. *Bottom-Left Anchor*: 5 lingkaran konsentris bertahap (`#315B8C`) + 1 aksen coral (`#F2765E`).
    3. *Global Sweeping Orbits*: 3 busur orbit finansial melintang kanvas.
- **Keterbacaan Visual Nyata (Anti-Hilang) & Bebas Sakit Mata:**
  - Garis solid dibuat berbobot terdefinisi **`stroke-width="1.5"` s/d `"1.8"`** sehingga tidak lenyap oleh antialiasing subpixel layar.
  - Opasitas stroke terkalibrasi secara optimal **0.08 hingga 0.16** (8% s/d 16%), menjamin visual art terlihat jelas sebagai estetika latar perbankan elegan, tanpa menjadi terlalu pucat hingga hilang dan tanpa menyilaukan mata.
- **Karakteristik Ringan (1.9 KB):** Berukuran sangat kecil **1.9 KB** dengan parameter cache-busting `?v=20261029_v2` agar browser memuat ulang versi terbaru secara instan.

---

### 21.3. Struktur DOM Standar

Elemen art dirender tepat di bawah tag pembuka `<body>` pada seluruh berkas layout utama (`app/Views/layouts/app.php` dan `app/Views/layouts/guest.php`):

```html
<!-- GLOBAL AMBIENT CIRCULAR ART BACKGROUND -->
<div class="global-ambient-art" aria-hidden="true">
    <img
        src="<?= e(url('/ambient-circles.svg?v=20261029_v2')) ?>"
        alt=""
        class="global-ambient-art-svg select-none pointer-events-none"
        loading="eager"
        decoding="async"
    >
</div>
```

Dan elemen pembungkus konten utama (`<main id="content">`) **MUTLAK WAJIB** memiliki kelas `relative z-10` untuk memastikan kartu data, tabel, dan formulir berada di layer atas secara presisi.


