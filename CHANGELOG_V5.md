# Fable RemitOps V5 — Module Alignment & Professional UI

Versi V5 menyempurnakan aplikasi berdasarkan **MODUL APLIKASI** yang diberikan, dengan fokus pada antarmuka profesional, kurs referensi, transaksi bank, report bulanan, complaint workflow, template operasional, dan kesiapan Entropy–TOPSIS.

## 1. UI/UX dan dashboard

- Lapisan desain V5 responsif untuk login, dashboard, formulir, tabel, kartu, status, serta modul dokumen.
- Halaman login menampilkan ringkasan sistem dan kartu kurs dengan indikator naik/turun, persentase perubahan, sparkline, serta posisi kurs pada rentang historinya.
- Dashboard memiliki command center, kartu KPI, grafik tren, distribusi status berbentuk donut, serta papan kurs beli/jual.
- Kurs diperoleh dari ExchangeRate-API bila API key tersedia, dengan fallback Frankfurter. Cache dan histori dipakai agar aplikasi stabil ketika provider tidak dapat dihubungi.

> Kurs adalah kurs referensi provider publik, bukan feed dealing bank per detik. Frekuensi aktual mengikuti waktu pembaruan provider dan cache aplikasi.

## 2. Transaksi dan format bank

- Input transaksi manual tetap tersedia.
- Import XLSX/CSV membaca sheet `Data Base Bule` atau header ekuivalen.
- Baris yang tidak memenuhi tanggal, payer, beneficiary, rekening, nominal, dan currency tidak dimasukkan.
- Output bank dapat diunduh dalam XLSX/XLS dengan struktur `Data KE BRI`.
- Pembersihan output menghapus tanda baca yang tidak diizinkan, tetapi mempertahankan separator bank `|`, `#`, dan `###`; nominal menggunakan koma desimal.
- Ditambahkan master **Rekening Payer** dengan CRUD, status aktif/nonaktif, rekening default, serta pemilihan otomatis pada input transaksi. Pengguna masih dapat mengisi rekening secara manual.

## 3. Report Harian bulanan

- Report Harian tetap terpisah dari tabel transaksi dan digunakan sebagai modul monitoring.
- Import dibentuk berdasarkan bulan; beberapa tanggal dan sheet dapat dihimpun dalam satu report bulanan.
- Baris total/ringkasan, duplikat, kolom wajib kosong, currency tidak valid, atau tanggal di luar bulan tidak disimpan.
- Ringkasan penerimaan dan alasan penolakan baris ditampilkan setelah import.
- Status pengiriman dan hasil transaksi dapat dipantau dan diekspor.

## 4. Complaint

- Complaint dapat dibuat dari baris Report Harian yang sudah berstatus diproses/terkirim/sukses.
- Ditambahkan histori perubahan complaint: status, prioritas, penanggung jawab, dan resolusi.
- Pencarian, filter, lampiran, template, dan audit aktivitas tetap tersedia.

## 5. Invoice, dokumen, dan template

- Tampilan cetak A4 invoice dan dokumen diperhalus.
- Pusat template mendukung Standing Instruction, Invoice, dan Complaint.
- Template dapat ditambah, diubah, dihapus, dipratinjau, serta dicetak/diunduh sebagai PDF melalui dialog cetak browser.
- CSS khusus dokumen standalone ditambahkan agar halaman template konsisten dengan aplikasi utama.

## 6. Entropy–TOPSIS

- Analisis dapat menggunakan data Transaksi atau Report Harian.
- Sistem memvalidasi dua periode yang sama panjang dan minimal dua perusahaan.
- Sistem membentuk C1–C6, menghitung normalisasi Entropy, proporsi, entropy, dispersi, bobot, normalisasi TOPSIS, matriks berbobot, solusi ideal, jarak, preferensi, peringkat, serta sensitivitas.
- Hasil dapat disimpan sebagai riwayat analisis.
- Panduan metode tersedia di `docs/METHOD_ENTROPY_TOPSIS.md`.

## 7. Database V5

Migrasi `database/migration_upgrade_v5.sql` menambahkan:

- tabel `payer_accounts`;
- kolom `transactions.payer_account_id`;
- tabel `complaint_histories`;
- seed awal rekening payer dari snapshot transaksi lama.

## 8. Validasi source

- 83 file PHP di folder `app` dan `public` lolos `php -l`.
- `app.js`, `remitops-v4.js`, dan `remitops-v5.js` lolos `node --check`.
- Sanitasi output transaksi bank dan parser report CSV diuji secara lokal tanpa database.
- Pengujian koneksi MariaDB, API kurs, dan XLSX harus diselesaikan pada XAMPP karena runtime pemeriksaan tidak menyediakan `pdo_mysql`, server MariaDB, dan ekstensi ZIP PHP.
