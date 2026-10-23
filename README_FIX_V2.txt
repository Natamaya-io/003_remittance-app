PERBAIKAN LOGIN V2 — FABLE REMITOPS

1. Salin seluruh isi paket ke:
   C:\xampp\htdocs\AplikasiRemmitance
   Timpa file lama.

2. Pastikan Apache dan MySQL aktif.

3. Buka:
   http://localhost/AplikasiRemmitance/public/system_check.php

4. Klik:
   - Buat .env, Folder Log & Upload
   - Perbaiki Login & Reset Superadmin

5. Pastikan semua status login berwarna hijau/OK.

6. Login:
   superadmin
   Password123!

Catatan:
- Jangan membuka app.log menggunakan alamat file:/// pada browser.
- Log dapat dilihat langsung di halaman system_check.php atau melalui Notepad.
- Jika tabel companies/transactions/complaints/invoices/audit_logs belum ada,
  gunakan Instal Database Baru hanya jika tidak ada data yang perlu dipertahankan.
- Setelah sistem berfungsi, hapus public/system_check.php.
