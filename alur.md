# PANDUAN LENGKAP ALUR SISTEM REMITGO!
Spesifikasi alur kerja operasional dan analitik sistem terstruktur berurutan dengan diagram panah visual untuk mempermudah pemahaman.

---

## 1. Dashboard
Pusat kendali, pemantauan metrik transaksi harian, aktivitas tiket, dan kurs valuta asing Bank Indonesia.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Langkah 1 (Autentikasi):** `Klien Request GET /` ➔ `Auth::check()` ➔ Jika valid lanjut, jika belum login ➔ `Redirect /login`.
* **Langkah 2 (Filter Data Tim):** `Auth::isTransactionAdmin()` ➔ Jika `true` ➔ Tambah klausa `WHERE t.team_no = ?` ➔ Jika `false` (Super Admin/Supervisor) ➔ Tampilkan seluruh data PT.
* **Langkah 3 (Kompilasi Metrik):**
  * `transactions (CURDATE())` ➔ Hitung Total Transaksi ➔ Hitung Sukses ➔ Hitung Refund ➔ `% Rasio Keberhasilan`.
  * `complaints (open/in_progress)` ➔ Jumlah Tiket Aktif.
  * `invoices (draft/sent/overdue)` ➔ Jumlah Tagihan Menunggu Pembayaran.
  * `companies (active)` ➔ Total Mitra Korporat Aktif.
* **Langkah 4 (Visualisasi Tren & Status):**
  * Data 14 Hari Transaksi ➔ Kelompokkan per Tanggal ➔ Gambar Smooth Area Chart (Canvas).
  * Data 30 Hari Status ➔ Hitung 8 Status Terbanyak ➔ Gambar Donut Chart & Legend Persentase.
* **Langkah 5 (Kurs Bank Indonesia):**
  * Cek Cache `storage/cache/bi_fx_daily.json` ➔ Jika segar (< 2 jam) ➔ Ambil Cache.
  * Jika kedaluwarsa ➔ Request SOAP XML BI `getSubKursLokal4` ➔ Mundur s.d. 10 hari jika libur ➔ Hitung Kurs Tengah `(Beli+Jual)/2` ➔ Terapkan Spread 0,25% ➔ Simpan Cache & DB ➔ Render Papan Kurs & Sparkline.

---

## 2. Transaksi
Modul eksekusi pengiriman uang (remitansi keluar), baik secara manual maupun impor massal dengan ekspor perbankan standar BRI dan BNI.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Alur 2A: Transaksi Manual**
  `Buka Form /transactions/create` ➔ `Pilih Perusahaan PT` ➔ `Pilih Rekening Debet Payer` ➔ `Isi Data Penerima (Nama, Rekening, Bank, SWIFT, Alamat, Negara)` ➔ `Isi Nominal & Valuta` ➔ `Generate Hash SHA-256` ➔ `Cek Duplikasi` ➔ `INSERT INTO transactions` ➔ `Audit Log Dicatat` ➔ `Redirect /transactions`.
* **Alur 2B: Impor Excel Massal (Data Base Bule / MT103)**
  `Drop File XLSX/CSV ke /transactions/import` ➔ `SpreadsheetReader Membaca Sheet` ➔ `Deteksi Header (14 atau 25 Kolom)` ➔ `Skip Baris Subtotal/Total Otomatis` ➔ `Pembersihan Karakter Non-Alfanumerik` ➔ `Normalisasi Tanggal & Nominal` ➔ `Buat Batch transaction_jobs` ➔ `Batch Insert ke transactions` ➔ `Tampilkan Ringkasan Impor`.
* **Alur 2C: Ekspor Format Bank BRI (Data KE BRI)**
  `Pilih Batch Transaksi` ➔ `Pilih Ekspor XLSX atau XLS Legacy` ➔ `Format Kolom 2: Tgl|Valuta |Nominal (Contoh: 261013|USD |78000)` ➔ `Terapkan Separator Ganda (| dan #)` ➔ `Tambahkan Penutup Wajib ###` ➔ `Sanitasi Tanda Baca Internal (Ubah ke Spasi Rapi)` ➔ `Download File Siap Kliring BRI`.
* **Alur 2D: Transaksi BNI Batch**
  `Unggah Batch BNI` ➔ `Simpan ke bni_transaction_batches & bni_transaction_rows` ➔ `Buka Batch` ➔ `Klik Cetak Slip BNI` ➔ `Tampilan Cetak Format Slip Bank Resmi`.

---

## 3. Rekening Payer
Master data buku rekening debet perusahaan pengirim uang untuk integrasi transaksi dan Standing Instruction.

### Diagram Alur Utama
```
[Buka /payer-accounts] ➔ [Klik Tambah Akun] ➔ [Pilih PT & Isi Detail Bank]
                                                        ↓
                                         [Validasi Unik: PT + No Rek + Bank]
                                                        ↓
[Cek Flag is_default] ── (Jika = 1) ──➔ [Reset is_default Akun Lain Milik PT Ini]
        ↓                                               ↓
[Simpan ke payer_accounts] ➔ [Tersedia Otomatis di Dropdown Transaksi & Surat SI]
```

### Rincian Alur Berpanah:
* **Langkah 1 (Input Akun):** `Akses /payer-accounts/create` ➔ `Pilih PT (company_id)` ➔ `Input No Rekening Debet` ➔ `Nama Akun / Rekening` ➔ `Bank (Contoh: BRI, BNI, Mandiri)` ➔ `Cabang & SWIFT` ➔ `Mata Uang (IDR, USD, EUR, dll)` ➔ `Email & Alamat Kantor`.
* **Langkah 2 (Pengecekan Integritas):** Cek constraint database `uq_payer_account_company_bank` ➔ Jika nomor rekening dan bank sudah ada pada PT yang sama ➔ `Tolak & Munculkan Peringatan`.
* **Langkah 3 (Penetapan Rekening Utama):** Jika dicentang `Jadikan Rekening Default` ➔ Sistem mengeksekusi `UPDATE payer_accounts SET is_default = 0 WHERE company_id = ?` ➔ Akun baru diset `is_default = 1`.
* **Langkah 4 (Pemanfaatan Data):** Saat operator membuka form Transaksi atau membuat Standing Instruction ➔ Sistem otomatis memuat rekening default PT tersebut tanpa perlu mengetik manual.

---

## 4. Report Harian
Subsistem pemantauan dan rekonsiliasi berkas report transaksi bulanan dari klien yang dipisahkan secara murni dari transaksi keuangan riil.

### Diagram Alur Utama
```
[Drop File Excel Klien] ➔ [Isi Nama Report + Bulan YYYY-MM + Nama Klien]
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

### Rincian Alur Berpanah:
* **Langkah 1 (Upload Report):** `Akses /reports/create` ➔ `Masukkan Nama File` ➔ `Tentukan Bulan Acuan (YYYY-MM)` ➔ `Masukkan Nama Klien (Otomatis Jadi Label TEAM)` ➔ `Unggah File XLSX/CSV`.
* **Langkah 2 (Pembacaan Cerdas):** `ReportImportService` memindai seluruh sheet bertanggal ➔ Deteksi kolom wajib (`Bill To`, `Beneficiary`, `Amount`, `Currency`) ➔ Filter baris bertuliskan `Total/Grand Total/Subtotal` ➔ Baris valid disimpan ke `report_rows`.
* **Langkah 3 (Pemisahan Tanggung Jawab):** Data report **TIDAK MASUK** ke tabel `transactions` ➔ Saldo dan pembukuan transaksi tetap aman dari data draft klien.
* **Langkah 4 (Update Status Monitoring):**
  * Operator membuka daftar report ➔ Pilih tanggal ➔ Centang baris transfer.
  * Ubah status kirim: `PENDING` ➔ `SENT`.
  * Ubah status hasil: `PENDING` ➔ `SUCCESS` (atau `FAILED` jika ada tolakan).
* **Langkah 5 (Ekspor Hasil):** `Klik Ekspor XLSX` ➔ Sistem mengunduh kembali file report terkonsolidasi dengan status terbaru.

---

## 5. Data PT
Pusat direktori profil badan hukum korporat (klien/mitra kerja sama) yang terhubung ke seluruh modul operasional.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Langkah 1 (Pendaftaran Entitas):** `Akses /companies/create` ➔ `Input Kode Unik Perusahaan (code)` ➔ `Input Nama Resmi PT (name)` ➔ `Email, Telepon & Alamat` ➔ `Set Status: active`.
* **Langkah 2 (Pengikatan Relasional):** Entitas PT otomatis menjadi *Foreign Key* induk bagi:
  * `payer_accounts` (Buku rekening bank milik PT).
  * `transactions` (Transaksi pengiriman atas nama PT).
  * `complaints` (Tiket aduan yang melibatkan PT).
  * `invoices` (Tagihan pembayaran ke PT).
* **Langkah 3 (Pemeliharaan & Keamanan Data):** Jika mitra berhenti bekerja sama ➔ `Ubah Status ke inactive` ➔ PT tidak lagi muncul di pilihan baru, namun seluruh histori transaksi lama tetap utuh.

---

## 6. Complain
Modul eskalasi, pelacakan, dan penyelesaian kendala transfer dana yang wajib memiliki dasar validitas dari Report Harian.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Langkah 1 (Validasi Sumber Masalah):** Operator membuka Report Harian ➔ Memilih baris transaksi yang bermasalah ➔ Sistem memvalidasi bahwa baris tersebut berstatus `delivery_status = 'SENT'` atau `result_status = 'SUCCESS'`.
* **Langkah 2 (Pembuatan Tiket):** `Buka Form /complaints/create` ➔ `Pilih report_row_id` ➔ Sistem otomatis mengisi ringkasan transfer ➔ `Terbitkan Tiket CMP-YYYYMMDD-XXXX`.
* **Langkah 3 (Penugasan & Prioritas):** Tentukan PIC (`assigned_to`) ➔ Pilih Kategori Kendala (Retur bank, SWIFT pending, salah rekening) ➔ Pilih Prioritas (`low ➔ medium ➔ high ➔ urgent`) ➔ Unggah Bukti lampiran transfer via `UploadService`.
* **Langkah 4 (Siklus Penanganan):**
  * Tiket Baru: `Status = open`.
  * Sedang Ditangani PIC: `Status = in_progress`.
  * Kendala Selesai / Dana Terkirim Ulang / Refund: `Status = resolved`.
  * Kendala Ditolak / Tidak Valid: `Status = rejected`.
* **Langkah 5 (Audit Trail Histori):** Setiap pergantian status dan pengisian teks resolusi ➔ `INSERT INTO complaint_histories` ➔ Linimasa investigasi tersaji permanen di halaman detail.

---

## 7. Invoice
Modul penagihan resmi atas jasa transaksi remitansi kepada perusahaan klien.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Langkah 1 (Pembuatan Tagihan):** `Akses /invoices/create` ➔ `Input No Invoice` ➔ `Pilih Perusahaan PT (company_id)` ➔ `Tanggal Terbit & Jatuh Tempo` ➔ `Pilih Valuta & Masukkan Rincian Nominal (Subtotal, PPN, Diskon)` ➔ `Hitung Total Tagihan Otomatis`.
* **Langkah 2 (Penautan Bukti):** Operator dapat menautkan nomor transaksi (`transaction_id`) serta mengunggah lampiran faktur pendukung (PDF/gambar).
* **Langkah 3 (Transisi Status):**
  * `draft` (Faktur baru disusun di internal).
  * `sent` (Faktur sudah dikirim ke klien).
  * `paid` (Dana fee sudah masuk dan diverifikasi).
  * `overdue` (Pembayaran melewati jatuh tempo tanpa konfirmasi).
  * `cancelled` (Tagihan dibatalkan).
* **Langkah 4 (Cetak & Pelaporan):** `Klik Cetak /invoices/{id}/print` ➔ Menghasilkan dokumen invoice resmi format perbankan siap cetak/ekspor PDF.

---

## 8. DOKUMEN
Label pembatas kategori pada navigasi sidebar yang mengelompokkan modul surat perintah transfer bank resmi dan berkas cetak legal.

---

## 9. Dokumen & Template
Pusat pembuatan surat Standing Instruction ke 7 bank mitra nasional dan manajemen template invoice berbasis web & Word.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Alur 9A: Standing Instruction 7 Bank Nasional (`standing_instruction_7.php`)**
  `Buka Modul SI` ➔ `Pilih Bank Tujuan (BCA, Mandiri, BRI, BNI, Danamon, CIMB Niaga, Permata)` ➔ `Pilih Rekening Debet PT Pengirim` ➔ `Input Baris Daftar Penerima (Nama, No Rekening, Nominal, Berita Acara Transfer)` ➔ `Sistem Menghitung Total Nominal & Terbilang` ➔ `Pratinjau Surat Sesuai Layout Kop Surat Bank yang Dipilih` ➔ `Cetak / Unduh Format A4 PDF`.
* **Alur 9B: Template Word DOCX (`document_template_manager.php`)**
  `Upload Berkas .docx` ➔ `Validasi Format & Arsip ZIP` ➔ `Simpan ke document_templates` ➔ `DocxInvoiceTemplateService Memetakan Placeholder Variabel` ➔ `Ekstrak Gambar/Media` ➔ `Render HTML Pratinjau Bersih`.
* **Alur 9C: Template Invoice Cetak (`invoice_templates.php`)**
  `Pilih Tipe Dokumen: Invoice Asli / Invoice Order / Invoice Terima Uang` ➔ `Isi Variabel Tagihan` ➔ `Tampilkan Pratinjau Interaktif` ➔ `Cetak Dokumen Resmi Perbankan`.

---

## 10. ANALITIK
Label pembatas kategori pada navigasi sidebar untuk modul analitik komputasional keputusan manajerial multi-kriteria.

---

## 11. Entropy–TOPSIS
Sistem Pendukung Keputusan (SPK) terintegrasi untuk mengevaluasi dan merangking kinerja 4 alternatif mitra korporat berdasarkan 6 kriteria benefit dari data riil tahun 2024.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Langkah 1 (Input Dataset):** `Akses /analysis` ➔ Unggah `OLAH_DATA_REPORT_SUKSES_2024.xlsx` (3.473 baris) dan `OLAH_DATA_ACTUAL_REFUND_2024.xlsx` (174 baris) ➔ Disimpan ke `spk_sources` dan `spk_records`.
* **Langkah 2 (Definisi Kriteria Benefit):**
  * $C_1$: Rasio pertumbuhan transaksi $\left(\frac{P_2}{P_1}\right)$ ➔ **Benefit**.
  * $C_2$: Rasio rata-rata transaksi per hari aktif $\left(\frac{\text{Tx}_{P2}/\text{Hari}_{P2}}{\text{Tx}_{P1}/\text{Hari}_{P1}}\right)$ ➔ **Benefit**.
  * $C_3$: Rasio pertumbuhan jangkauan negara beneficiary ➔ **Benefit**.
  * $C_4$: Rasio pertumbuhan jaringan bank / kode SWIFT tujuan ➔ **Benefit**.
  * $C_5$: Rasio pertumbuhan keragaman mata uang transaksi ➔ **Benefit**.
  * $C_6$: Rasio keandalan tanpa retur $\left(1 - \frac{\text{Refund}_{P2}}{\text{Tx}_{P2}}\right)$ ➔ **Benefit**.
* **Langkah 3 (Alternatif Riset):** Memastikan 4 entitas aktif: `PT FABLE TEKNOLOGI NUSANTARA`, `PT JURITA AGROTAMA INDONESIA`, `PT GEMA SAPTA BINTANG`, dan `PT KAIROS SINERGI INDONESIA`.
* **Langkah 4 (Matriks Penilaian $X_{ij}$):** `SpkAnalysisService` menghitung rasio indikator masing-masing PT ➔ Dibulatkan pada **8 desimal** (`ROUND(..., 8)`) ➔ Disimpan ke `spk_assessments.auto_value`.
* **Langkah 5 (Komputasi Matematika):**
  * `EntropyTopsisEngine` menormalisasi nilai $K_{ij} = \frac{X_{ij}}{\max(X_j)}$ ➔ Menghitung probabilitas $a_{ij} = \frac{K_{ij}}{\sum K_{ij}}$ ➔ Menghitung entropi $E_j = -\frac{1}{\ln(m)} \sum a_{ij} \ln(a_{ij})$ ➔ Menghitung dispersi $D_j = 1 - E_j$ ➔ Menghasilkan Bobot Kriteria $W_j = \frac{D_j}{\sum D_j}$.
  * Menormalisasi vektor TOPSIS $R_{ij} = \frac{X_{ij}}{\sqrt{\sum X_{ij}^2}}$ ➔ Matriks terbobot $Y_{ij} = W_j \cdot R_{ij}$ ➔ Menentukan Solusi Ideal Positif $A^+$ (nilai tertinggi) dan Negatif $A^-$ (nilai terendah) ➔ Menghitung Jarak Euclidean $D_i^+$ dan $D_i^-$.
* **Langkah 6 (Penentuan Ranking $V_i$):**
  * Nilai preferensi kedekatan relatif dihitung: $V_i = \frac{D_i^-}{D_i^+ + D_i^-}$ (Presisi **12 desimal**).
  * Urutan Peringkat:
    1. **PT JURITA AGROTAMA INDONESIA:** $V_i = 0,999802023010$ (Peringkat 1)
    2. **PT KAIROS SINERGI INDONESIA:** $V_i = 0,182698955917$ (Peringkat 2)
    3. **PT FABLE TEKNOLOGI NUSANTARA:** $V_i = 0,150053889470$ (Peringkat 3)
    4. **PT GEMA SAPTA BINTANG:** $V_i = 0,049516310507$ (Peringkat 4)
  * Simpan hasil snapshot ke tabel `spk_runs`, `spk_weights`, dan `spk_results`.

---

## 12. ADMINISTRASI
Label pembatas kategori pada navigasi sidebar untuk modul manajemen hak akses pengguna dan pengawasan rekam jejak kepatuhan sistem.

---

## 13. User & Role
Modul kontrol akses berbasis peran (RBAC) dan pengelolaan akun pengguna aplikasi.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Langkah 1 (Registrasi Personel):** `Akses /users/create` ➔ `Input Nama Lengkap` ➔ `Username & Email Unik` ➔ `Input Password Sementara`.
* **Langkah 2 (Penetapan Batasan Wewenang):**
  * Pilih Peran: Super Admin, Supervisor, Admin Transaksi (1 s.d. 4), Admin Complain, atau Admin Invoice.
  * Tetapkan Tim Transaksi: Tim 1, 2, 3, atau 4 (khusus admin transaksi, agar isolasi data antar-tim berjalan).
* **Langkah 3 (Pengamanan Akun):** Password di-hash menggunakan algoritma `password_hash($pass, PASSWORD_DEFAULT)` (Bcrypt) ➔ Flag `must_change_password` diset `1`.
* **Langkah 4 (Siklus Pertama Login Pengguna):** Pengguna login pertama kali ➔ Sistem mendeteksi `must_change_password = 1` ➔ `Redirect ke /profile` ➔ Pengguna memasukkan password baru minimal 8 karakter ➔ Flag diubah menjadi `0` ➔ Pengguna dialihkan ke menu sesuai izinnya.

---

## 14. Audit Log
Modul forensik kepatuhan yang mencatat seluruh riwayat transaksi data dan operasi penting pengguna.

### Diagram Alur Utama
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

### Rincian Alur Berpanah:
* **Langkah 1 (Pemicu Otomatis):** Setiap kali ada fungsi mutating di kontroler apa pun (contoh: tambah transaksi, ubah status komplain, hapus invoice, login pengguna) ➔ Kontroler memanggil `AuditService::log($action, $entityType, $entityId, $before, $after)`.
* **Langkah 2 (Pengumpulan Bukti Forensik):**
  * `user_id` (ID operator yang mengeksekusi).
  * `action` (Jenis aksi: create, update, delete, login, dll).
  * `entity_type` & `entity_id` (Tabel dan ID rekaman yang dimanipulasi).
  * `before_data` (Snapshot rekaman sebelum diedit/dihapus dalam format JSON).
  * `after_data` (Snapshot rekaman baru setelah disimpan dalam format JSON).
  * `ip_address` (Alamat IP pengeksekusi dari `$_SERVER['REMOTE_ADDR']`).
  * `user_agent` (Browser/klien pengeksekusi).
* **Langkah 3 (Penyimpanan Aman Tanpa Gangguan):** Proses pencatatan dibungkus dalam blok `try-catch` terisolasi ➔ Kegagalan log audit tidak akan pernah membatalkan atau memicu rollback transaksi keuangan utama.
* **Langkah 4 (Penelusuran Kepatuhan):** Super Admin dan Supervisor membuka `/audit` ➔ Menyaring linimasa aktivitas ➔ Memeriksa detail perubahan data per detik untuk akuntabilitas operasional perbankan.
