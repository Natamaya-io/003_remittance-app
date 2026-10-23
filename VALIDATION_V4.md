# Validasi Fable RemitOps V4

Pemeriksaan yang telah dilakukan pada source revisi:

- Seluruh file PHP pada folder `app` dan `public` lolos `php -l`.
- JavaScript `app.js` dan `remitops-v4.js` lolos pemeriksaan sintaks Node.js.
- Parser Report Harian diuji dengan CSV yang memuat baris valid, total, kolom wajib kosong, dan tanggal di luar bulan. Baris tidak sesuai berhasil dilewati dan alasan penolakan tercatat.
- Rumus Entropy–TOPSIS diuji menggunakan matriks keputusan penelitian. Bobot, nilai preferensi, dan peringkat konsisten dengan hasil validasi skripsi.
- Template transaksi diperiksa: sheet `Data Base Bule` digunakan sebagai sumber impor dan hasil dapat diekspor ke struktur `Data KE BRI`.

## Batas pengujian lingkungan ini

Pengujian end-to-end terhadap MariaDB, API kurs eksternal, dan pembacaan XLSX melalui ekstensi PHP tidak dijalankan karena lingkungan pemeriksaan tidak menyediakan server MySQL serta ekstensi `pdo_mysql` dan `zip`. Jalankan `public/system_check.php` setelah dipasang pada XAMPP untuk memastikan seluruh prasyarat aktif.
