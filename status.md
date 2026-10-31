# Pemetaan Lengkap Status ke Halaman Aplikasi RemitGo!

Dokumen ini memetakan seluruh status sistem ke halaman web (URL route), berkas view, dan bentuk komponen antarmukanya.

---

## 1. Modul Laporan Remitansi (`/reports`)

Modul operasional harian untuk memproses berkas laporan Excel remitansi perbankan.

### A. Daftar Nilai Status
- **`delivery_status` (Basis Data):** `PENDING` (Belum kirim), `SENT` (Sudah kirim).
- **`result_status` (Basis Data):** `PENDING` (Menunggu bank), `SUCCESS` (Sukses), `FAILED` (Gagal / Retur).
- **Status Operasional Terpadu (Tampilan UI):**
  - **`Antrean Baru` (`NEW`)**: `delivery_status = PENDING` & `result_status = PENDING`
  - **`Terkirim (Proses Bank)` (`SENT`)**: `delivery_status = SENT` & `result_status = PENDING`
  - **`Sukses` (`SUCCESS`)**: `delivery_status = SENT` & `result_status = SUCCESS`
  - **`Gagal / Retur` (`FAILED`)**: `delivery_status = SENT` & `result_status = FAILED`

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /reports`** (Index Bulanan) | `app/Views/reports/index.php` | - **Kartu & Tabel:** Menampilkan akumulasi metrik baris `Terkirim` (Emerald) dan `Pending` (Amber).<br>- **Bilah Filter:** Dropdown filter `delivery_status` (`Semua`, `Pending`, `Terkirim`). |
| **`GET /reports/month/{month}`** (Daftar Tanggal Bulanan) | `app/Views/reports/month.php` | - **Kartu & Tabel Tanggal:** Menampilkan progress pengiriman serta jumlah baris `Terkirim` dan `Pending`.<br>- **Bilah Filter:** Dropdown filter pengiriman status. |
| **`GET /reports/date/{date}`** (Daftar Batch per Tanggal) | `app/Views/reports/date.php` | - **Tabel Batch:** Kolom hasil menampilkan badge jumlah baris `Berhasil Diproses` (`SUCCESS`), `Gagal / Retur` (`FAILED`), dan `Progress Kirim`.<br>- **Kartu Batch:** Kisi KPI menampilkan `Terkirim`, `Success`, dan `Failed`.<br>- **Bilah Filter:** Dropdown filter status operasional terpadu (`Antrean Baru`, `Terkirim`, `Sukses`, `Gagal / Retur`). |
| **`GET /reports/{id}`** (Rincian Baris Batch) | `app/Views/reports/show.php` | - **Tabel Baris Transaksi:** Kolom "Status" menampilkan badge terpadu: `Antrean Baru` (Kuning), `Terkirim (Proses Bank)` (Biru), `Sukses` (Hijau), atau `Gagal / Retur` (Merah).<br>- **Bilah Filter:** Filter status baris individual.<br>- **Aksi Baris:** Modal / dropdown update status untuk mengubah status per baris (`POST /reports/{id}/rows/{rowId}/status`).<br>- **Aksi Massal:** Modal bulk status update (`POST /reports/{id}/bulk-status`). |
| **`GET /reports/search`** (Pencarian Rekening/Nama) | `app/Views/reports/search.php` | - **Tabel Hasil Pencarian:** Kolom status dengan badge operasional terpadu.<br>- **Form Filter:** Dropdown pemilihan filter status. |

---

## 2. Modul Dashboard Utama (`/`)

Pusat kendali ringkasan metrik dan analitik eksekutif.

### A. Status yang Digunakan
- Agregasi status transaksi: `SUCCESS`, `PENDING`, `FAILED`.
- Agregasi refund: `refund_status = 'success'`.
- Agregasi komplain aktif: `complaints.status IN ('open', 'in_progress')`.
- Agregasi tagihan aktif: `invoices.status IN ('draft', 'sent', 'overdue')`.

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /`** atau **`GET /dashboard`** | `app/Views/dashboard/index.php` | - **Kartu Metrik KPI:** Angka ringkasan `Sukses`, `Pending`, `Gagal`, dan `Refund`.<br>- **Donut / Bar Chart:** Grafik visualisasi komposisi status transaksi harian.<br>- **Tabel Transaksi Terbaru:** Kolom status dengan badge warna solid. |

---

## 3. Modul Transaksi Standar (`/transactions`)

Modul pengelolaan transaksi umum dan eksekusi transfer langsung.

### A. Daftar Nilai Status
- **`status`:** `PENDING` (Menunggu), `SUCCESS` (Berhasil), `FAILED` (Gagal), `CANCELLED` (Dibatalkan).
- **`refund_status`:** `none` (Tidak ada refund), `requested` (Diajukan), `processed` (Diproses), `success` (Berhasil), `failed` (Gagal).

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /transactions`** (Daftar Transaksi) | `app/Views/transactions/index.php` | - **Tabel Transaksi:** Badge status transaksi (`PENDING`, `SUCCESS`, `FAILED`, `CANCELLED`) dan badge refund.<br>- **Filter Bar:** Dropdown filter status transaksi dan filter refund. |
| **`GET /transactions/create`** (Form Transaksi Baru) | `app/Views/transactions/form.php` | - **Form Inisiasi:** Transaksi baru otomatis tersimpan dengan status awal `PENDING` dan `refund_status = 'none'`. |
| **`GET /transactions/{id}`** (Detail Transaksi) | `app/Views/transactions/show.php` | - **Header & Panel Detail:** Badge status transaksi besar, rincian kronologi, dan tombol aksi pembatalan/refund. |
| **`GET /transactions/{id}/edit`** (Edit Transaksi) | `app/Views/transactions/form.php` | - **Form Edit:** Dropdown pemilihan status transaksi dan status refund (khusus role berwenang). |
| **`GET /transactions/bri`** & **`GET /transactions/bni`** | `app/Views/transactions/bri.php`, `bni.php` | - **Tabel Jalur Bank:** Kolom status transaksi khusus jalur bank tertentu. |

---

## 4. Modul Job Impor Excel (`/transactions/jobs`)

Pemrosesan dan pemetaan berkas Excel impor ke transaksi.

### A. Daftar Nilai Status
- **`status`:** `READY` (Siap proses), `EMPTY` (Berkas kosong), `ARCHIVED` (Diarsipkan).

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /transactions/jobs/{id}`** | `app/Views/transactions/job.php` | - **Header Batch:** Badge status job (`READY`, `EMPTY`, `ARCHIVED`).<br>- **Kartu Statistik:** Jumlah baris dibaca, baris valid, dan baris kolom output. |

---

## 5. Modul Komplain Pelanggan (`/complaints`)

Penanganan tiket masalah transaksi atau klaim pelanggan.

### A. Daftar Nilai Status
- **`status`:** `open` (Baru), `in_progress` (Sedang Ditangani), `resolved` (Selesai), `closed` (Ditutup).

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /complaints`** (Daftar Tiket) | `app/Views/complaints/index.php` | - **Tabel Tiket:** Badge status (`OPEN`, `IN PROGRESS`, `RESOLVED`, `CLOSED`).<br>- **Filter Bar:** Dropdown filter status komplain. |
| **`GET /complaints/create`** (Buat Tiket) | `app/Views/complaints/form.php` | - **Formulir:** Tiket baru otomatis tersimpan dengan status awal `open`. |
| **`GET /complaints/{id}`** (Detail Tiket Komplain) | `app/Views/complaints/show.php` | - **Panel Status & Timeline:** Badge status saat ini dan riwayat perpindahan status (`status_from` $\rightarrow$ `status_to`).<br>- **Form Ubah Status:** Dropdown untuk mengubah status tiket ke `in_progress`, `resolved`, atau `closed`. |

---

## 6. Modul Faktur & Penagihan (`/invoices`)

Penerbitan faktur tagihan biaya remitansi kepada perusahaan klien.

### A. Daftar Nilai Status
- **`status`:** `draft` (Draf), `sent` (Terkirim), `paid` (Lunas), `overdue` (Jatuh Tempo), `cancelled` (Batal).

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /invoices`** (Daftar Faktur) | `app/Views/invoices/index.php` | - **Tabel Faktur:** Badge status (`DRAFT`, `SENT`, `PAID`, `OVERDUE`, `CANCELLED`).<br>- **Filter Bar:** Dropdown filter status pembayaran faktur. |
| **`GET /invoices/{id}`** (Detail Faktur) | `app/Views/invoices/show.php` | - **Panel Dokumen:** Status faktur di header dokumen faktur.<br>- **Aksi Pembayaran:** Tombol/form untuk menandai faktur sebagai `paid` atau `cancelled`. |
| **`GET /invoices/{id}/print`** (Cetak Faktur) | `app/Views/invoices/print.php` | - **Dokumen Cetak:** Cap stempel status (misal: lencana teks `PAID` atau `DRAFT`). |

---

## 7. Modul Dokumen Instruksi & Template (`/documents`)

Manajemen dokumen instruksi pengiriman (*Standing Instruction*) dan template dokumen resmi.

### A. Daftar Nilai Status
- **`si_documents.status`:** `draft`, `final`, `cancelled`.
- **`document_templates.status`:** `draft`, `active`, `inactive`.

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /documents`** (Pusat Dokumen) | `app/Views/documents/index.php` | - **Daftar Dokumen SI:** Kolom badge status SI (`DRAFT`, `FINAL`, `CANCELLED`).<br>- **Daftar Template:** Kolom badge status template (`ACTIVE`, `INACTIVE`, `DRAFT`). |

---

## 8. Modul Pengguna & Autentikasi (`/users`, `/login`, `/profile`)

Manajemen akun operator dan alur keamanan kredensial.

### A. Daftar Nilai Status
- **`is_active`:** `1` (Akun Aktif) / `0` (Akun Non-aktif / Diblokir).
- **`must_change_password`:** `1` (Wajib Ganti Password) / `0` (Normal).

### B. Pemetaan ke Halaman
| URL Route | Berkas View / Controller | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /users`** (Manajemen Pengguna) | `app/Views/users/index.php` | - **Tabel Pengguna:** Kolom status akun menampilkan badge `Aktif` (Hijau) atau `Nonaktif` (Abu-abu/Merah). |
| **`GET /users/create`** & **`{id}/edit`** | `app/Views/users/form.php` | - **Form Akun:** Switch toggle / radio status aktifasi akun (`is_active`). |
| **`POST /login`** (Proses Masuk) | `app/Controllers/AuthController.php` | - **Logika Autentikasi:** Memeriksa `must_change_password`. Jika bernilai `1`, mengalihkan pengguna ke `/profile`. Jika `0`, mengarahkan ke `/`. |
| **`GET /profile`** (Halaman Profil) | `app/Views/profile/index.php` | - **Form Ubah Password:** Mengeksekusi perubahan password (`POST /profile/password`) yang secara otomatis mengubah status `must_change_password` dari `1` menjadi `0`. |

---

## 9. Modul Master Data Entitas (`/companies`, `/payer-accounts`)

Manajemen profil perusahaan dan rekening pengirim operasional.

### A. Daftar Nilai Status
- **`status`:** `active` (Aktif Digunakan), `inactive` (Non-aktif).

### B. Pemetaan ke Halaman
| URL Route | Berkas View | Bentuk Visual & Peran Status |
| :--- | :--- | :--- |
| **`GET /companies`** (Daftar Perusahaan) | `app/Views/companies/index.php` | - **Tabel Perusahaan:** Kolom status dengan badge `Aktif` atau `Nonaktif`. |
| **`GET /companies/{id}/edit`** | `app/Views/companies/form.php` | - **Form Perusahaan:** Pilihan status `active` / `inactive`. |
| **`GET /payer-accounts`** (Rekening Pengirim) | `app/Views/payer_accounts/index.php` | - **Tabel Rekening:** Kolom status rekening `Aktif` / `Nonaktif`. |
| **`GET /payer-accounts/{id}/edit`** | `app/Views/payer_accounts/form.php` | - **Form Rekening:** Pilihan status `active` / `inactive`. |
