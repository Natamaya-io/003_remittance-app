# UI V17

Perubahan utama:

- Halaman login dibangun ulang sebagai layout dua panel yang pas pada browser zoom 100%.
- Panel kiri memakai gradient navy–biru–ungu, skyline, orbit finansial, aurora, dan parallax pointer.
- Kartu login memiliki focus state, password toggle, submit loading state, dan tilt ringan.
- Login dan dashboard memakai satu partial kurs: `app/Views/partials/fx-idr-board.php`.
- Partial kurs mendukung konteks login/dashboard, batas jumlah kartu, toolbar, sinkronisasi, dan buy/sell.
- Panel kurs menggunakan latar terang, kartu putih, bendera SVG lokal, smooth area chart, dan indikator tren.
- Akses Cepat dashboard dipadatkan menjadi satu baris lima kartu pada desktop.
- Aset UI lama yang tidak pernah dimuat oleh runtime telah dihapus.

Aset yang tetap dipertahankan:

- `modern-banking.css` dan `remitops-v5.css`, karena masih dipakai halaman standalone dokumen/invoice.
- `smart-table.js`, karena masih dipakai tabel aplikasi dan halaman standalone.
