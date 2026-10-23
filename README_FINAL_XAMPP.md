# Fable RemitOps — Final XAMPP Package

## Persyaratan

- XAMPP dengan PHP 8.0 atau lebih baru
- Apache dan MySQL/MariaDB aktif
- Ekstensi PHP `pdo_mysql`, `curl`, `fileinfo`, `mbstring`, dan `zip`

## Instalasi baru

1. Salin folder `AplikasiRemmitance` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL.
3. Buka phpMyAdmin dan impor `database/install_all.sql`.
4. Pastikan file `.env` berisi:

```env
APP_URL=http://localhost/AplikasiRemmitance/public
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=AplikasiRemmitance
DB_USERNAME=root
DB_PASSWORD=
```

5. Buka `http://localhost/AplikasiRemmitance/public/system_check.php` dan pastikan seluruh pemeriksaan utama berstatus OK.
6. Login melalui `http://localhost/AplikasiRemmitance/public/login`.

Akun awal: `superadmin` / `Password123!`.

## Memperbarui instalasi lama tanpa menghapus data

1. Backup folder aplikasi dan database.
2. Timpa source code lama dengan paket ini. Pertahankan `.env` lama.
3. Impor `database/migration_modules.sql` melalui phpMyAdmin, atau buka `public/system_check.php` lalu klik **Lengkapi Tabel Modul**.
4. Restart Apache dan login kembali.

## Modul yang diperbaiki

- Login PHP 8.0/MariaDB menggunakan positional placeholders.
- Dashboard modern dan tabel kurs beli/jual.
- Filter otomatis dan `Ctrl + F` pada tabel aplikasi.
- Report Harian lengkap dengan struktur 24 kolom.
- Paste langsung dari Excel ke transaksi dengan preview, validasi, pembuatan PT otomatis, dan pemeriksaan duplikasi.
- Pusat Dokumen & Template terintegrasi.
- 7 template Standing Instruction dengan cetak PDF A4.
- 3 template Invoice dengan preview, edit, hapus, dan cetak PDF A4.
- Template Complain dengan CRUD file.
- SQL instalasi lengkap serta migration aman untuk data lama.

## URL penting

- Aplikasi: `http://localhost/AplikasiRemmitance/public`
- Pemeriksaan: `http://localhost/AplikasiRemmitance/public/system_check.php`
- Dokumen: `http://localhost/AplikasiRemmitance/public/documents`
- Import paste Excel: `http://localhost/AplikasiRemmitance/public/report_paste_import.php`

Setelah instalasi stabil, hapus atau ubah nama `public/system_check.php` untuk keamanan.
