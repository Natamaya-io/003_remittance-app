# Validasi Fable RemitOps V5

## Pemeriksaan otomatis yang lulus

- PHP syntax: **83 file** pada `app/` dan `public/` lulus `php -l`.
- JavaScript syntax:
  - `public/assets/js/app.js`
  - `public/assets/js/remitops-v4.js`
  - `public/assets/js/remitops-v5.js`
- Struktur route, policy, controller, view, dan migrasi untuk master Rekening Payer tersedia.
- Output bank diuji dengan karakter tanda baca dan menghasilkan token rekening/SWIFT alfanumerik, separator `|`, `#`, `###`, serta nominal koma desimal.
- Parser Report Harian CSV diuji untuk menerima baris valid dan menolak baris total, kolom wajib kosong, duplikat, currency tidak valid, serta tanggal di luar bulan.
- Entropy–TOPSIS diuji menggunakan matriks penelitian: bobot berjumlah 1,000000 dan peringkat `[3, 1, 4, 2]`, konsisten dengan urutan PT Jurita, PT Kairos, PT Fable, dan PT Gema.

## Pemeriksaan yang wajib dilakukan setelah instalasi XAMPP

1. Buka `public/system_check.php` dan pastikan `pdo_mysql`, `zip`, `dom`, `mbstring`, serta `curl` aktif.
2. Jalankan migrasi V4 dan V5 pada salinan database.
3. Uji login setiap role.
4. Uji import XLSX transaksi dan download XLSX/XLS BRI.
5. Uji import Report Harian untuk satu bulan yang berisi beberapa sheet/tanggal.
6. Uji refresh kurs dengan dan tanpa `FX_API_KEY`.
7. Uji pembuatan complaint dari report berstatus terkirim/sukses.
8. Uji cetak PDF invoice dan dokumen pada Chrome/Edge.
9. Jalankan Entropy–TOPSIS pada perusahaan yang memiliki data lengkap di P1 dan P2.

## Batas validasi lingkungan pengerjaan

Runtime pemeriksaan tidak menyediakan server MariaDB dan driver PHP `pdo_mysql`; karena itu, pengujian end-to-end berbasis database dan API eksternal belum dapat dinyatakan lulus di lingkungan ini. Source telah disiapkan agar pemeriksaan tersebut dapat dilakukan melalui XAMPP dan `system_check.php`.
