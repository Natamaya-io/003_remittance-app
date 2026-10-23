# UI Consolidation V14

Perubahan utama:

- Dashboard dipadatkan untuk penggunaan browser pada zoom 100%.
- Akses Cepat menjadi lima kartu kecil dalam satu baris pada desktop.
- Hero menggunakan gradasi navy, biru, dan ungu serta mini chart aktivitas.
- Panel kurs menggunakan latar sangat muda, kartu putih, bendera SVG, smooth area chart, dan indikator tren hijau/merah.
- CSS dan JavaScript dashboard/kurs digabung ke `public/assets/css/app.css` dan `public/assets/js/app.js`.
- Partial kurs lama `fx-board.php` dihapus; hanya `fx-idr-board.php` yang digunakan.
- Aset versi lama yang tidak dipanggil aplikasi telah dihapus.

Aset yang tetap dipertahankan:

- `app.css` dan `app.js`: antarmuka utama aplikasi.
- `modern-banking.css` dan `remitops-v5.css`: masih dipakai halaman standalone dokumen/invoice.
- `smart-table.js`: masih dipakai halaman standalone.
- `img/flags.svg`: sprite bendera lokal agar tidak bergantung pada internet.

Dokumen versi V4/V5 dan catatan hotfix lama yang sudah tidak relevan juga dihapus agar root project lebih bersih.
