FABLE REMITOPS - HOTFIX PHP 8.0 / MARIADB XAMPP

Perbaikan:
1. Kompatibel dengan PHP 8.0.30 (menghapus return type never).
2. Mengganti array_is_list dengan fungsi kompatibel PHP 8.0.
3. Memperbaiki pemeriksaan tabel/kolom MariaDB yang sebelumnya menggunakan SHOW ... LIKE ?.
4. system_check.php sekarang memakai information_schema dengan prepared statement standar.

INSTALASI:
- Timpa seluruh isi folder aplikasi lama dengan isi paket ini.
- Pastikan folder akhir: C:\xampp\htdocs\AplikasiRemmitance\public\index.php
- Restart Apache dan MySQL.
- Buka http://localhost/AplikasiRemmitance/public/system_check.php
- Klik Perbaiki Login & Reset Superadmin bila diperlukan.
- Login: superadmin / Password123!

Setelah sistem normal, hapus public/system_check.php.
