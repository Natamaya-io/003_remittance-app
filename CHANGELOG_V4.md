# Fable RemitOps V4 Professional

## Perubahan utama

- UI modern dan responsif untuk login, dashboard, formulir, tabel, kartu, dan modul dokumen.
- Kurs realtime menggunakan Frankfurter/ExchangeRate-API dengan cache, histori, indikator naik/turun, persentase perubahan, dan sparkline.
- Dashboard kurs dan distribusi status diperbarui.
- Import transaksi XLSX/CSV memakai validasi ketat; baris valid tersimpan dan dapat diekspor ke format BRI XLSX/XLS. Transaksi manual juga dapat diunduh sebagai file BRI.
- Report Harian dikelompokkan per bulan. Baris total, duplikat, kolom wajib kosong, currency tidak valid, dan tanggal di luar bulan otomatis dilewati serta dicatat.
- Complain dapat dibuat langsung dari baris Report Harian.
- Invoice cetak A4 dan pusat dokumen diperhalus.
- Entropy–TOPSIS dapat memakai sumber Transaksi atau Report Harian, dengan validasi periode, bobot Entropy, peringkat TOPSIS, dan analisis sensitivitas.
- Migrasi database V4 tersedia di `database/migration_upgrade_v4.sql`.

## Cara upgrade database

1. Backup database lama.
2. Buka phpMyAdmin.
3. Pilih menu **Import** dan jalankan `database/migration_upgrade_v4.sql`.
4. Salin seluruh folder aplikasi V4 ke folder XAMPP/htdocs.
5. Periksa `.env`, kemudian login dan tekan **Perbarui Kurs**.

## Prasyarat XAMPP

- PHP 8.0+
- MariaDB/MySQL
- ekstensi `pdo_mysql`, `zip`, `dom`, `mbstring`, dan `curl` atau `allow_url_fopen`
