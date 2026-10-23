# Fable RemitOps — Sistem Operasional Remitansi & Analisis Multi-Kriteria

Aplikasi operasional remitansi perbankan berbasis PHP 8 dan MariaDB untuk pemrosesan transaksi, rekonsiliasi laporan harian, penagihan, standing instruction 7 bank nasional, serta sistem pendukung keputusan metode Entropy–TOPSIS.

---

## Panduan Menjalankan Sistem

### Persyaratan
* PHP 8.1 atau lebih baru
* Ekstensi PHP aktif: `pdo_mysql`, `curl`, `zip`, `dom`, `simplexml`, `bcmath`, `fileinfo`
* MariaDB 10.4+ atau MySQL 8.0+

### Langkah Instalasi

1. **Siapkan Database**
   Buat database baru bernama `AplikasiRemmitance` dan impor file database:
   ```bash
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS AplikasiRemmitance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p AplikasiRemmitance < aplikasiremmitance.sql
   ```

2. **Atur Variabel Lingkungan**
   Salin berkas contoh konfigurasi:
   ```bash
   cp .env.example .env
   ```
   Sesuaikan parameter koneksi pada `.env`:
   ```ini
   APP_NAME="Fable RemitOps"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000
   APP_TIMEZONE=Asia/Jakarta

   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=AplikasiRemmitance
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Jalankan Server Lokal**
   Gunakan router bawaan yang menyajikan aset publik dan fallback penulisan URL:
   ```bash
   php -S 127.0.0.1:8000 -t public public/router.php
   ```
   Akses aplikasi melalui peramban di `http://127.0.0.1:8000`.

### Kredensial Akun Default

| Peran | Username | Password Default | Cakupan Akses |
|---|---|---|---|
| Super Admin | `superadmin` | `password123` | Akses penuh seluruh modul dan konfigurasi sistem |
| Supervisor | `supervisor` | `password123` | Monitoring transaksi lintas tim, audit log, persetujuan |
| Admin Transaksi Tim 1 | `admin1` | `password123` | Input dan eksekusi transaksi khusus Tim 1 |
| Admin Transaksi Tim 2 | `admin2` | `password123` | Input dan eksekusi transaksi khusus Tim 2 |
| Admin Transaksi Tim 3 | `admin3` | `password123` | Input dan eksekusi transaksi khusus Tim 3 |
| Admin Transaksi Tim 4 | `admin4` | `password123` | Input dan eksekusi transaksi khusus Tim 4 |
| Admin Complain | `admincomplain` | `password123` | Investigasi dan penyelesaian tiket komplain remitansi |
| Admin Invoice | `admininvoice` | `password123` | Pembuatan, pengiriman, dan pencetakan faktur tagihan |

---

## Spesifikasi & Diagram Alur 14 Fitur

---

### 1. Dashboard

Pusat kendali, pemantauan metrik transaksi harian, aktivitas tiket komplain, invoice, dan kurs valuta asing Bank Indonesia secara aktual.

#### Diagram Alur
```
[Akses Sistem GET /]
        ↓
[Verifikasi Sesi & Peran Pengguna]
        ↓
[Filter Nomor Tim] ─── (Admin Transaksi Tim 1-4) ───➔ [Kunci Data Khusus Tim Sendiri]
        ↓
[Query Database: Transaksi Hari Ini + Tiket + Invoice + Kurs BI]
        ↓
[Render Dasbor: KPI Cards + Tren 14 Hari + Donut 30 Hari + IDR FX Board]
```

#### Alur Eksekusi
* **Langkah 1 (Autentikasi):** `Klien Request GET /` ➔ `Auth::check()` ➔ Jika sesi aktif berlanjut ke kontroler; jika tidak valid ➔ Dialihkan ke `/login`.
* **Langkah 2 (Isolasi Data Tim):** `Auth::isTransactionAdmin()` ➔ Jika `true` ➔ Terapkan klausa `WHERE t.team_no = ?` ➔ Jika `false` (Super Admin/Supervisor) ➔ Tampilkan seluruh data perusahaan secara menyeluruh.
* **Langkah 3 (Kompilasi Metrik):**
  * `transactions (CURDATE())` ➔ Hitung Total Transaksi ➔ Hitung Transaksi Sukses ➔ Hitung Refund ➔ Hitung Rasio Keberhasilan.
  * `complaints (open/in_progress)` ➔ Hitung Total Tiket Aktif.
  * `invoices (draft/sent/overdue)` ➔ Hitung Total Tagihan Menunggu Pembayaran.
  * `companies (active)` ➔ Hitung Total Mitra Korporat Aktif.
* **Langkah 4 (Visualisasi Tren & Status):**
  * Data 14 Hari Terakhir ➔ Dikelompokkan per tanggal ➔ Digambar pada Smooth Area Chart native Canvas.
  * Data 30 Hari Terakhir ➔ Dihitung 8 status transaksi terbanyak ➔ Digambar pada Donut Chart native Canvas.
* **Langkah 5 (Kurs Bank Indonesia):**
  * Cek berkas cache `storage/cache/fx_bi_daily.json` ➔ Jika usia cache masih di bawah ambang batas (`FX_CACHE_MINUTES`) ➔ Muat data cache.
  * Jika kedaluwarsa ➔ Kirim request SOAP XML Bank Indonesia (`getSubKursLokal4`) ➔ Mundur hingga 10 hari jika tanggal acuan adalah hari libur bursa ➔ Hitung Kurs Tengah `(Beli + Jual) / 2` ➔ Tambahkan spread persentase ➔ Perbarui cache dan database ➔ Render papan kurs valas.

---

### 2. Transaksi

Modul eksekusi pengiriman dana keluar (*outward remittance*), baik melalui input formulir manual, impor massal berkas Excel, maupun pemrosesan transaksi perbankan BRI dan BNI.

#### Diagram Alur
```
               ┌──➔ [Input Manual /transactions/create] ──────┐
               │                                              │
[Pilih Jalur] ─┼──➔ [Impor Excel Template Data Base Bule] ───┼──➔ [Validasi & Deduplikasi Hash]
               │                                              │                    ↓
               └──➔ [Impor Batch BNI /transactions/bni] ──────┘          [Simpan ke Database]
                                                                                   ↓
                                      ┌──➔ [Ekspor Format Kliring BRI Data KE BRI (.xlsx/.xls)]
                                      │
               [Pilih Tindakan Lanjut]─┼──➔ [Cetak Slip Remitansi BNI Batch]
                                      │
                                      └──➔ [Update Status: PENDING ➔ SUCCESS / FAILED]
```

#### Alur Eksekusi
* **Alur 2A: Transaksi Manual**
  `Buka Form /transactions/create` ➔ `Pilih Perusahaan PT` ➔ `Pilih Rekening Debet Payer` ➔ `Isi Data Penerima (Nama, No Rekening, Bank, SWIFT, Alamat, Negara)` ➔ `Isi Nominal & Valuta` ➔ `Generate Hash SHA-256 Transaksi` ➔ `Cek Duplikasi` ➔ `INSERT INTO transactions` ➔ `Catat Audit Log` ➔ `Redirect /transactions`.
* **Alur 2B: Impor Excel Massal (Data Base Bule / MT103)**
  `Unggah File XLSX/CSV ke /transactions/import` ➔ `SpreadsheetReader Membaca Lembar Kerja` ➔ `Deteksi Header (Format 14 atau 25 Kolom)` ➔ `Abaikan Baris Subtotal/Total Otomatis` ➔ `Pembersihan Karakter Non-Alfanumerik` ➔ `Normalisasi Tanggal & Nominal` ➔ `Buat Batch transaction_jobs` ➔ `Batch Insert ke transactions` ➔ `Tampilkan Ringkasan Impor`.
* **Alur 2C: Ekspor Format Bank BRI (Data KE BRI)**
  `Pilih Batch Transaksi` ➔ `Pilih Ekspor XLSX atau XLS Legacy` ➔ `Format Kolom 2: Tgl|Valuta|Nominal (Contoh: 261013|USD|78000)` ➔ `Terapkan Separator Ganda (| dan #)` ➔ `Tambahkan Penutup Wajib ###` ➔ `Sanitasi Tanda Baca Internal` ➔ `Unduh Berkas Siap Kliring BRI`.
* **Alur 2D: Transaksi Batch BNI**
  `Unggah Berkas Batch BNI` ➔ `Simpan ke bni_transaction_batches & bni_transaction_rows` ➔ `Buka Detail Batch` ➔ `Klik Cetak Slip BNI` ➔ `Sistem Merender Format Slip Transfer Resmi BNI`.

---

### 3. Rekening Payer

Buku master rekening debet korporat pengirim dana untuk integrasi formulir transaksi dan surat Standing Instruction.

#### Diagram Alur
```
[Buka /payer-accounts] ➔ [Klik Tambah Akun] ➔ [Pilih PT & Isi Detail Bank]
                                                        ↓
                                         [Validasi Unik: PT + No Rek + Bank]
                                                        ↓
[Cek Flag is_default] ── (Jika = 1) ──➔ [Reset is_default Akun Lain Milik PT Ini]
        ↓                                               ↓
[Simpan ke payer_accounts] ➔ [Tersedia Otomatis di Dropdown Transaksi & Surat SI]
```

#### Alur Eksekusi
* **Langkah 1 (Input Akun):** `Akses /payer-accounts/create` ➔ `Pilih PT (company_id)` ➔ `Input No Rekening Debet` ➔ `Nama Pemilik Rekening` ➔ `Pilih Bank (BRI, BNI, Mandiri, BCA, dll)` ➔ `Cabang & SWIFT` ➔ `Mata Uang Rekening` ➔ `Alamat & Email Korespondensi`.
* **Langkah 2 (Pengecekan Integritas):** Sistem mengevaluasi pembatas unik `uq_payer_account_company_bank` ➔ Jika kombinasi nomor rekening dan bank sudah terdaftar pada PT yang sama ➔ Permintaan ditolak dengan pesan kesalahan.
* **Langkah 3 (Penetapan Rekening Utama):** Jika dicentang opsi `Jadikan Rekening Default` ➔ Sistem mengeksekusi `UPDATE payer_accounts SET is_default = 0 WHERE company_id = ?` ➔ Akun baru disimpan dengan status `is_default = 1`.
* **Langkah 4 (Pemanfaatan Otomatis):** Saat operator membuka formulir transaksi atau membuat Standing Instruction ➔ Sistem otomatis mengisi data rekening default tanpa input ulang.

---

### 4. Report Harian

Subsistem pemantauan dan rekonsiliasi berkas laporan transaksi bulanan klien yang dipisahkan secara murni dari tabel transaksi keuangan riil.

#### Diagram Alur
```
[Unggah Berkas Excel Klien] ➔ [Isi Nama Report + Bulan YYYY-MM + Nama Klien]
                                    ↓
                 [Parsing Seluruh Sheet Bertanggal Sekaligus]
                                    ↓
              [Abaikan Baris Ringkasan Total & Filter Kolom Wajib]
                                    ↓
                 [Simpan ke report_batches & report_rows]
                                    ↓
         [Monitoring Kalender: Per Bulan /reports/month/{bln} atau Per Tanggal]
                                    ↓
                 [Pembaruan Status Pengiriman & Hasil Eksekusi]
                     Status Kirim : PENDING ──➔ SENT
                     Status Hasil : PENDING ──➔ SUCCESS / FAILED
                                    ↓
             [Data Sukses Terkirim Siap Jadi Sumber Komplain]
```

#### Alur Eksekusi
* **Langkah 1 (Upload Berkas):** `Akses /reports/create` ➔ `Tentukan Nama Report` ➔ `Tentukan Bulan Acuan (YYYY-MM)` ➔ `Masukkan Nama Klien (Otomatis Menjadi Label Tim)` ➔ `Unggah Berkas XLSX/CSV`.
* **Langkah 2 (Parsing Multi-Sheet):** `ReportImportService` memindai seluruh lembar kerja (*sheets*) yang memuat tanggal ➔ Memvalidasi kolom wajib (`Bill To`, `Beneficiary`, `Amount`, `Currency`) ➔ Mengabaikan baris `Total`, `Grand Total`, atau `Subtotal` ➔ Menyimpan baris valid ke `report_rows`.
* **Langkah 3 (Isolasi Saldo):** Seluruh data laporan harian disimpan pada tabel terpisah `report_batches` dan `report_rows` tanpa menyentuh tabel `transactions` guna menjaga integritas buku besar.
* **Langkah 4 (Pembaruan Status):**
  * Operator memilih tanggal transaksi pada kalender laporan ➔ Menandai baris kiriman dana.
  * Status Pengiriman diubah: `PENDING` ➔ `SENT`.
  * Status Hasil diubah: `PENDING` ➔ `SUCCESS` (atau `FAILED` jika terjadi retur bank).
* **Langkah 5 (Ekspor Hasil):** Operator dapat mengekspor kembali data laporan yang telah diperbarui statusnya ke dalam format XLSX.

---

### 5. Data PT

Direktori master profil badan hukum korporat (klien dan mitra kerja sama) yang menjadi entitas induk relasional bagi seluruh modul operasional.

#### Diagram Alur
```
[Buka /companies] ➔ [Klik Tambah Perusahaan]
                           ↓
     [Input Kode PT Unik + Nama Resmi + Kontak + Alamat]
                           ↓
                 [INSERT INTO companies]
                           ↓
   ┌───────────────────────┴───────────────────────┐
   ↓                                               ↓
[Master Data Rekening, Transaksi & Invoice]   [Entitas Alternatif Riset SPK]
```

#### Alur Eksekusi
* **Langkah 1 (Pendaftaran Mitra):** `Akses /companies/create` ➔ `Input Kode Unik PT (code)` ➔ `Input Nama Resmi Perusahaan (name)` ➔ `Email, Telepon, dan Alamat Kantor` ➔ `Set Status: active`.
* **Langkah 2 (Pengikatan Relasi):** Entitas PT terhubung sebagai kunci induk (*foreign key*) untuk:
  * `payer_accounts` (Daftar rekening bank perusahaan pengirim).
  * `transactions` (Riwayat pengiriman remitansi atas nama PT).
  * `complaints` (Tiket investigasi transfer yang melibatkan PT).
  * `invoices` (Faktur penagihan jasa transaksi ke PT).
* **Langkah 3 (Manajemen Status):** Jika kerja sama berakhir, status diubah menjadi `inactive`. PT tidak lagi muncul dalam opsi pembuatan transaksi baru, namun rekam jejak transaksi lama tetap tersimpan utuh.

---

### 6. Complain

Modul pelacakan, eskalasi, dan penyelesaian kendala transfer dana yang wajib merujuk secara valid pada baris laporan transaksi berstatus terkirim/sukses.

#### Diagram Alur
```
[Pilih Baris Report Harian yang SUKSES/TERKIRIM]
                       ↓
         [Klik Buat Complain /complaints/create]
                       ↓
   [Sistem Auto-Fill: Beneficiary, Nominal, Valuta, Bank]
                       ↓
 [Generate No Tiket: CMP-YYYYMMDD-XXXX + Upload Bukti PDF/Gambar]
                       ↓
 [Penetapan Status: open ➔ in_progress ➔ resolved / rejected]
                       ↓
[Perekaman Linimasa Investigasi ke complaint_histories]
```

#### Alur Eksekusi
* **Langkah 1 (Validasi Data Acuan):** Operator memilih baris transaksi yang bermasalah pada menu Report Harian ➔ Sistem memeriksa bahwa baris tersebut telah berstatus `delivery_status = 'SENT'` atau `result_status = 'SUCCESS'`.
* **Langkah 2 (Penerbitan Tiket):** `Buka Form /complaints/create` dengan parameter `report_row_id` ➔ Formulir terisi otomatis dengan ringkasan transaksi ➔ Sistem membuat nomor tiket dengan format `CMP-YYYYMMDD-XXXX`.
* **Langkah 3 (Penugasan & Bukti):** Tetapkan penanggung jawab (`assigned_to`) ➔ Pilih kategori masalah (Retur bank tujuan, SWIFT pending, salah nomor rekening) ➔ Pilih tingkat prioritas (`low`, `medium`, `high`, `urgent`) ➔ Lampirkan bukti berkas melalui `UploadService`.
* **Langkah 4 (Siklus Penanganan):**
  * Tiket Baru: Status `open`.
  * Dalam Investigasi PIC: Status `in_progress`.
  * Masalah Tuntas / Dana Terkirim Ulang / Refund: Status `resolved`.
  * Aduan Tidak Valid / Dibatalkan: Status `rejected`.
* **Langkah 5 (Linimasa Forensik):** Setiap perubahan status dan catatan resolusi otomatis dicatat ke tabel `complaint_histories` dan ditampilkan sebagai linimasa kronologis.

---

### 7. Invoice

Modul penagihan resmi atas jasa pemrosesan remitansi kepada korporat mitra dengan format cetak faktur standar perbankan.

#### Diagram Alur
```
[Buka /invoices] ➔ [Klik Buat Invoice /invoices/create]
                           ↓
  [Pilih Perusahaan PT + Tanggal Terbit + Jatuh Tempo]
                           ↓
[Input Komponen: Subtotal Fee + PPN/Pajak - Potongan Biaya]
                           ↓
                [Tautkan ke Transaksi (Opsional)]
                           ↓
              [Status Awal: draft] ──➔ [Kirim ke Klien: sent]
                                             ↓
                      ┌──────────────────────┴──────────────────────┐
                      ↓                                             ↓
             [Klien Membayar: paid]                     [Lewat Batas Waktu: overdue]
                      ↓
          [Cetak Format Faktur Resmi A4/PDF]
```

#### Alur Eksekusi
* **Langkah 1 (Pembuatan Faktur):** `Akses /invoices/create` ➔ `Input No Invoice` ➔ `Pilih Perusahaan PT (company_id)` ➔ `Tentukan Tanggal Terbit & Jatuh Tempo` ➔ `Pilih Valuta` ➔ `Input Rincian Biaya (Subtotal, PPN, Diskon)` ➔ `Sistem Menghitung Total Akhir Tagihan`.
* **Langkah 2 (Penautan Dokumen):** Operator dapat menautkan ID transaksi spesifik serta mengunggah berkas pendukung penagihan.
* **Langkah 3 (Transisi Status):**
  * `draft`: Faktur selesai dibuat di internal.
  * `sent`: Faktur telah dikirimkan ke korporat klien.
  * `paid`: Pembayaran tagihan telah diterima dan diverifikasi.
  * `overdue`: Pembayaran melewati batas jatuh tempo.
  * `cancelled`: Tagihan dibatalkan secara administratif.
* **Langkah 4 (Pencetakan):** `Akses /invoices/{id}/print` ➔ Sistem merender lembar faktur resmi ukuran A4 yang siap dicetak langsung atau disimpan sebagai PDF.

---

### 8. DOKUMEN

Pemisah kategori pada navigasi bilah samping (*sidebar*) yang mengelompokkan instrumen surat perintah transfer bank resmi dan pengelola cetak dokumen.

---

### 9. Dokumen & Template

Pusat pembuatan surat Standing Instruction ke 7 bank nasional mitra serta pengelola template invoice berbasis Word DOCX dan HTML.

#### Diagram Alur
```
                                 [Buka /documents]
                                         ↓
                     ┌───────────────────┴───────────────────┐
                     ↓                                       ↓
     [Standing Instruction 7 Bank]               [Template Word & Invoice HTML]
                     ↓                                       ↓
   [Pilih 1 Bank: BCA/Mandiri/BRI/BNI/          [Upload File .docx ke Manager]
    Danamon/CIMB Niaga/Permata]                              ↓
                     ↓                         [Engine Ganti Variabel ${TAG}]
   [Pilih Rekening Debet Sumber PT]                          ↓
                     ↓                         [Pratinjau & Cetak Invoice Asli/
   [Input Rincian Penerima & Nominal]           Order/Terima Uang]
                     ↓
   [Pratinjau Kop Surat Resmi Bank]
                     ↓
   [Cetak / Unduh Format A4 PDF]
```

#### Alur Eksekusi
* **Alur 9A: Standing Instruction 7 Bank Nasional (`standing_instruction_7.php`)**
  `Buka Modul SI` ➔ `Pilih Bank Tujuan (BCA, Mandiri, BRI, BNI, Danamon, CIMB Niaga, Permata)` ➔ `Pilih Rekening Debet PT Pengirim` ➔ `Input Daftar Penerima (Nama, No Rekening, Nominal, Berita Acara)` ➔ `Sistem Menghitung Total Nominal & Terbilang Otomatis` ➔ `Pratinjau Tata Letak Kop Surat Resmi Bank Terpilih` ➔ `Cetak Lembar Instruksi A4`.
* **Alur 9B: Template Dokumen Word DOCX (`document_template_manager.php`)**
  `Unggah Berkas .docx` ➔ `Validasi Format & Arsip ZIP Berkas` ➔ `Simpan ke document_templates` ➔ `DocxInvoiceTemplateService Memetakan Tag Placeholder ${TAG}` ➔ `Ekstrak Aset Gambar` ➔ `Render Pratinjau HTML Bersih`.
* **Alur 9C: Template Faktur Cetak (`invoice_templates.php`)**
  `Pilih Jenis: Invoice Asli / Invoice Order / Invoice Terima Uang` ➔ `Isi Variabel Tagihan` ➔ `Tampilkan Pratinjau Interaktif` ➔ `Cetak Dokumen`.

---

### 10. ANALITIK

Pemisah kategori pada navigasi bilah samping untuk modul sistem pendukung keputusan komputasi multi-kriteria manajerial.

---

### 11. Entropy–TOPSIS

Sistem Pendukung Keputusan (SPK) untuk mengevaluasi dan menetapkan peringkat kinerja 4 korporat mitra berdasarkan 6 kriteria manfaat (*benefit*) menggunakan dataset aktual tahun 2024.

#### Diagram Alur
```
[1. Upload Sumber Data /analysis]
Report Sukses 2024 (C1-C5) + Actual LIST MT199 Refund BNI/BRI (C6)
                ↓
[2. Validasi Kriteria /analysis/criteria]
Penguncian 6 Kriteria Benefit: C1, C2, C3, C4, C5, C6
                ↓
[3. Verifikasi Alternatif /analysis/alternatives]
Penguncian 4 PT Mitra: FABLE, JURITA, GSB, KAIROS
                ↓
[4. Pembentukan Matriks Keputusan /analysis/assessment]
Agregasi Transaksi P1 (Sep-Okt) & P2 (Nov-Des) ➔ Matriks Xij (Presisi 8 Desimal)
                ↓
[5. Kalkulasi Engine Komputasi /analysis/calculation]
Metode Entropy (Normalisasi Kij ➔ Probabilitas aij ➔ Entropi Ej ➔ Bobot Wj)
                                +
Metode TOPSIS (Normalisasi Vektor Rij ➔ Bobot Yij ➔ Solusi Ideal A+/A- ➔ Jarak D+/D-)
                ↓
[6. Hasil Akhir & Perangkingan /analysis/results]
Kedekatan Relatif (Vi) Presisi 12 Desimal ➔ Pengurutan Peringkat 1 s.d. 4 ➔ Simpan Riwayat
```

#### Alur Eksekusi & Rumus Perhitungan

* **Langkah 1 (Input Dataset):** `Akses /analysis` ➔ Unggah `OLAH_DATA_REPORT_SUKSES_2024.xlsx` (3.473 baris transaksi) dan `OLAH_DATA_ACTUAL_REFUND_2024.xlsx` (174 baris retur MT199) ➔ Disimpan ke tabel `spk_sources` dan `spk_records`.
* **Langkah 2 (Definisi 6 Kriteria Manfaat):**
  * $C_1$: Rasio pertumbuhan total transaksi periode 2 terhadap periode 1 $\left(\frac{P_2}{P_1}\right)$ ➔ **Benefit**.
  * $C_2$: Rasio rata-rata frekuensi transaksi per hari aktif $\left(\frac{\text{Tx}_{P2}/\text{Hari}_{P2}}{\text{Tx}_{P1}/\text{Hari}_{P1}}\right)$ ➔ **Benefit**.
  * $C_3$: Rasio pertumbuhan variasi negara penerima dana (*beneficiary countries*) ➔ **Benefit**.
  * $C_4$: Rasio pertumbuhan jaringan bank tujuan transfer (*SWIFT codes*) ➔ **Benefit**.
  * $C_5$: Rasio pertumbuhan keragaman jenis mata uang transaksi ➔ **Benefit**.
  * $C_6$: Rasio keandalan transaksi tanpa retur dana $\left(1 - \frac{\text{Refund}_{P2}}{\text{Tx}_{P2}}\right)$ ➔ **Benefit**.
* **Langkah 3 (Alternatif Korporat):** Melibatkan 4 badan hukum mitra aktif: `PT JURITA AGROTAMA INDONESIA`, `PT KAIROS SINERGI INDONESIA`, `PT FABLE TEKNOLOGI NUSANTARA`, dan `PT GEMA SAPTA BINTANG`.
* **Langkah 4 (Matriks Keputusan $X_{ij}$):** `SpkAnalysisService` mengagregasi data transaksi kedua periode ➔ Menghitung rasio keenam kriteria ➔ Membulatkan nilai pada **8 angka desimal** (`ROUND(..., 8)`) ➔ Menyimpan nilai ke `spk_assessments.auto_value`.
* **Langkah 5 (Komputasi Matematika):**
  1. **Pembobotan Shannon Entropy:**
     * Normalisasi nilai: $K_{ij} = \frac{X_{ij}}{\max_i(X_{ij})}$
     * Matriks probabilitas: $a_{ij} = \frac{K_{ij}}{\sum_{i=1}^m K_{ij}}$
     * Nilai entropi kriteria: $E_j = -\frac{1}{\ln(m)} \sum_{i=1}^m a_{ij} \ln(a_{ij})$
     * Tingkat divergensi/dispersi: $D_j = 1 - E_j$
     * Bobot objektif kriteria: $W_j = \frac{D_j}{\sum_{j=1}^n D_j}$
  2. **Perangkingan TOPSIS:**
     * Normalisasi vektor: $R_{ij} = \frac{X_{ij}}{\sqrt{\sum_{i=1}^m X_{ij}^2}}$
     * Matriks keputusan terbobot: $Y_{ij} = W_j \cdot R_{ij}$
     * Solusi Ideal Positif ($A^+$) dan Solusi Ideal Negatif ($A^-$):
       $$A^+ = \{\max_i(Y_{ij}) \mid j \in J\}, \quad A^- = \{\min_i(Y_{ij}) \mid j \in J\}$$
     * Jarak Euclidean Solusi:
       $$D_i^+ = \sqrt{\sum_{j=1}^n (Y_{ij} - A_j^+)^2}, \quad D_i^- = \sqrt{\sum_{j=1}^n (Y_{ij} - A_j^-)^2}$$
* **Langkah 6 (Nilai Kedekatan Relatif $V_i$ & Urutan Peringkat):**
  * Preferensi dihitung dengan presisi **12 angka desimal**:
    $$V_i = \frac{D_i^-}{D_i^+ + D_i^-}$$
  * Hasil Peringkat Definitif Data 2024:
    1. **PT JURITA AGROTAMA INDONESIA:** $V_i = 0{,}999802023010$ (Peringkat 1)
    2. **PT KAIROS SINERGI INDONESIA:** $V_i = 0{,}182698955917$ (Peringkat 2)
    3. **PT FABLE TEKNOLOGI NUSANTARA:** $V_i = 0{,}150053889470$ (Peringkat 3)
    4. **PT GEMA SAPTA BINTANG:** $V_i = 0{,}049516310507$ (Peringkat 4)
  * Snapshot hasil komputasi disimpan permanen ke tabel `spk_runs`, `spk_weights`, dan `spk_results`.

---

### 12. ADMINISTRASI

Pemisah kategori pada navigasi bilah samping untuk modul pengelolaan identitas pengguna dan rekam jejak kepatuhan operasional.

---

### 13. User & Role

Manajemen akun pengguna dan pengendalian hak akses berbasis peran (*Role-Based Access Control* / RBAC).

#### Diagram Alur
```
[Super Admin Buka /users] ➔ [Klik Tambah User /users/create]
                                   ↓
        [Input Nama + Username Unik + Email Unik + Password Awal]
                                   ↓
        [Pilih 1 dari 8 Peran RBAC + Tentukan Nomor Tim (Tim 1-4)]
                                   ↓
      [Enkripsi Password via Bcrypt + Set must_change_password = 1]
                                   ↓
                         [Simpan ke users]
                                   ↓
  [User Login Perdana] ──➔ [Wajib Ubah Password di /profile] ──➔ [Akses Dashboard Sesuai Izin]
```

#### Alur Eksekusi
* **Langkah 1 (Pendaftaran Pengguna):** `Akses /users/create` ➔ `Input Nama Lengkap` ➔ `Username Unik` ➔ `Alamat Email Unik` ➔ `Tentukan Password Awal`.
* **Langkah 2 (Pengaturan Wewenang):**
  * Pilih peran: `superadmin`, `supervisor`, `admin_transaksi`, `admin_complain`, atau `admin_invoice`.
  * Khusus admin transaksi: Tetapkan tim penugasan (Tim 1, Tim 2, Tim 3, atau Tim 4) untuk mengaktifkan isolasi data antar-tim.
* **Langkah 3 (Pengamanan Kredensial):** Password dienkripsi menggunakan `password_hash($password, PASSWORD_DEFAULT)` (Bcrypt) ➔ Penanda `must_change_password` otomatis diset ke nilai `1`.
* **Langkah 4 (Login Perdana):** Saat pengguna masuk untuk pertama kali, sistem mendeteksi penanda `must_change_password = 1` ➔ Mengalihkan pengguna secara paksa ke `/profile` ➔ Pengguna wajib memperbarui kata sandi dengan minimal 8 karakter ➔ Sistem mengubah penanda menjadi `0` ➔ Akses modul terbuka sesuai peran yang dimiliki.

---

### 14. Audit Log

Instrumen forensik kepatuhan yang mencatat seluruh mutasi data dan operasi pengguna secara terperinci.

#### Diagram Alur
```
[User Melakukan Aksi: create / update / delete / login / password_change]
                                   ↓
           [Trigger AuditService::log() Otomatis di Controller]
                                   ↓
      [Ekstrak Data Snapshot: before_data (JSON) & after_data (JSON)]
                                   ↓
           [Tangkap Parameter: user_id + REMOTE_ADDR + User-Agent]
                                   ↓
      [INSERT INTO audit_logs Secara Asinkron/Non-Blocking via Try-Catch]
                                   ↓
   [Super Admin / Supervisor Akses /audit] ──➔ [Inspeksi Linimasa Forensik & JSON Diff]
```

#### Alur Eksekusi
* **Langkah 1 (Pemicu Operasi):** Setiap pemanggilan fungsi pengubah data pada kontroler (pembuatan transaksi, penyesuaian komplain, penerbitan invoice, login) memicu eksekusi `AuditService::log($action, $entityType, $entityId, $before, $after)`.
* **Langkah 2 (Pengumpulan Parameter):**
  * `user_id`: Identitas pengguna yang melakukan operasi.
  * `action`: Jenis aksi (`create`, `update`, `delete`, `login`, `logout`, `password_change`).
  * `entity_type` & `entity_id`: Nama tabel dan ID rekaman yang dimutasi.
  * `before_data`: Snapshot data sebelum perubahan dalam format JSON.
  * `after_data`: Snapshot data setelah perubahan dalam format JSON.
  * `ip_address`: Alamat IP klien pengeksekusi.
  * `user_agent`: Peramban atau klien yang digunakan.
* **Langkah 3 (Pencatatan Non-Blocking):** Proses penyimpanan ke tabel `audit_logs` diisolasi dalam blok `try-catch`. Kegagalan pencatatan log tidak akan pernah menggagalkan eksekusi transaksi utama.
* **Langkah 4 (Pemeriksaan Forensik):** Super Admin dan Supervisor membuka menu `/audit` untuk memeriksa linimasa aktivitas, menyaring berdasarkan pengguna atau tanggal, dan menganalisis perbedaan data (*JSON diff*) sebelum dan sesudah perubahan.

---

## Struktur Direktori Utama

```
.
├── app/
│   ├── Config/          # Konfigurasi sistem dan pembacaan .env
│   ├── Controllers/     # Kontroler untuk 14 modul aplikasi
│   ├── Core/            # Komponen inti: Router, Database, Auth, View, CSRF
│   ├── Middleware/      # Pengecekan autentikasi dan otorisasi RBAC
│   ├── Models/          # Model entitas database (35 tabel)
│   ├── Services/        # Engine logika: Audit, FX BI, SPK Entropy-TOPSIS, Template
│   └── Views/           # Antarmuka tampilan (PHP + Canvas Native)
├── database/            # Skrip migrasi dan skema instalasi
├── public/              # Titik masuk publik (index.php, router.php, aset CSS/JS/SVG)
├── storage/
│   ├── cache/           # Cache berkas lokal (Kurs BI)
│   └── logs/            # Berkas log aplikasi
├── tools/               # Skrip utilitas CLI database dan verifikasi
├── aplikasiremmitance.sql # Dump basis data lengkap
└── alur.md              # Dokumentasi alur kerja sistem
```

---

## Lisensi & Kepatuhan
Hak Cipta (c) Natamaya. Seluruh hak cipta dilindungi. Sistem ini diperuntukkan bagi operasional perbankan dan remitansi berlisensi.
