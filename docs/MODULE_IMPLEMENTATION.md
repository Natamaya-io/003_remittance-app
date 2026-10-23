# Pemetaan Modul Aplikasi ke Fable RemitOps

| Kebutuhan modul | Implementasi |
|---|---|
| Tampilan modern dan profesional | `app.css`, `app.js`, layout app/guest, login, dashboard compact, dan panel kurs light mode |
| Kurs naik/turun dan skala | FX provider + cache + histori + indikator tren + sparkline + range scale |
| Distribusi status bulat | Chart donut dashboard dengan persentase dan legend |
| Transaksi manual | Form transaksi dan ekspor bank per transaksi |
| Import Excel transaksi | Parser XLSX/CSV dengan validasi kolom minimum |
| Download format BRI | Export XLSX/XLS dengan struktur Data KE BRI |
| Master nomor rekening payer | CRUD `payer_accounts`, default account, pilihan otomatis dan manual override |
| Report bulanan | `report_batches.month_key`, gabungan sheet/tanggal dalam satu report |
| Tolak baris tidak sesuai | Validasi header, kolom wajib, total, duplikat, currency, dan batas bulan |
| Report hanya monitoring | Data report disimpan pada `report_batches`/`report_rows`, tidak otomatis menjadi transaksi |
| Complaint dari report | Source `report_row_id`, hanya report yang sudah diproses/terkirim/sukses |
| History complaint | `complaint_histories` dan timeline pada halaman edit |
| Invoice dan template | CRUD, preview, lampiran, layout cetak A4/PDF |
| Standing Instruction/template dokumen | Pusat dokumen, CRUD template, preview dan print PDF |
| Entropy–TOPSIS lengkap | C1–C6, Entropy, TOPSIS, sensitivitas, simpan riwayat dan panduan |
| User/role dan audit | Dipertahankan dari aplikasi sebelumnya |
