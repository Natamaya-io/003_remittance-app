# UI V14 — Dashboard Compact dan FX Light Mode

- Dashboard dipadatkan untuk penggunaan browser pada zoom 100%.
- Akses Cepat menggunakan kartu kecil satu baris pada desktop.
- Hero memakai gradasi navy, biru, dan ungu dengan mini chart aktivitas.
- Panel kurs memakai latar `#F8FAFC`, kartu putih, bendera SVG lokal, smooth area chart, serta warna tren hijau/merah.
- Aset CSS/JavaScript lama yang tidak dipakai dihapus dan antarmuka utama dikonsolidasikan ke `app.css` serta `app.js`.

# Final Repair Changelog

- Fixed layout asset paths and invalid HTML ordering.
- Added modern banking theme to authenticated and guest layouts.
- Integrated Document Center into the existing application layout.
- Kept a direct compatibility entry point at `public/document_templates.php`.
- Fixed Standing Instruction and Invoice links.
- Added complete database tables and seeds for documents, invoice templates, and seven Standing Instruction templates.
- Added safe module migration for existing databases.
- Added safe module migration button to System Check.
- Rebuilt Report Harian with all report columns, filters, print, CSV, and smart table search.
- Rebuilt Excel paste importer to resolve/create payer companies, validate rows, normalize dates and amounts, and skip duplicate transactions.
- Fixed FX board data binding, buy/sell display, refresh action, and spread configuration.
- Added CRUD edit support to document/complain templates.
- Restricted Supervisor to read-only access in invoice templates.
- Added smart table search and CSV export to standalone document modules.
- Removed hard-coded application paths where applicable.
- Added standardized letterhead assets.
- Verified PHP syntax across all application files and JavaScript syntax for application scripts.

## Final Skripsi — Report 2024 + Actual Only

- Analysis sekarang hanya menerima `report_2024` dan `actual`.
- Daily Update - Tiara tidak lagi menjadi sumber perhitungan.
- P1 dikunci ke 2024-09-01 s.d. 2024-10-31.
- P2 dikunci ke 2024-11-01 s.d. 2024-12-31.
- Empat alternatif penelitian dikunci: FABLE, JURITA, GSB, KAIROS.
- Kriteria dikembalikan ke C1-C6 pertumbuhan penelitian dan seluruhnya Benefit.
- C1-C5 berasal dari Report Sukses 2024.
- C6 berasal dari Report Sukses 2024 + refund Actual LIST MT199 (REFUND BNI/BRI).
- Entropy mengikuti modul PDF: `Kij = Xij / max(Xj)`, lalu `aij`, `Ej`, `Dj`, `Wj`.
- TOPSIS mengikuti modul PDF: `Rij`, `Yij`, `A+/A-`, `D+/D-`, `Vi`, ranking.
- Actual BNI memakai fallback tanggal Transaction Date -> ACK Date -> Refund Date agar refund dapat diatribusikan ke periode.
- Dashboard, Data Kriteria, Data Penilaian, dan Perhitungan diperjelas untuk mode penelitian.
- Seed database final hanya memakai Report 2024 + Actual.
