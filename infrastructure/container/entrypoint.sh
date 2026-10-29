#!/bin/sh
set -e

echo "==> [RemitGo! Container] Memulai inisialisasi lingkungan..."

# 1. Pastikan direktori runtime tersedia dengan kepemilikan yang benar
mkdir -p /run/mysqld /run/nginx /var/log/supervisor
chown -R mysql:mysql /run/mysqld /var/lib/mysql
chown -R nginx:nginx /run/nginx /var/www/html/storage /var/www/html/public/uploads

# 2. Inisialisasi Database jika direktori /var/lib/mysql masih kosong/baru
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "==> [RemitGo! DB] Direktori data kosong. Menginisialisasi basis data MariaDB baru..."
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/dev/null 2>&1

    echo "==> [RemitGo! DB] Menjalankan MariaDB daemon sementara untuk konfigurasi awal..."
    /usr/bin/mysqld_safe --datadir=/var/lib/mysql --skip-networking &
    temp_pid="$!"

    # Tunggu MariaDB siap menerima koneksi
    counter=0
    until mariadb-admin ping --silent || [ "$counter" -gt 30 ]; do
        sleep 1
        counter=$((counter + 1))
    done

    if [ "$counter" -gt 30 ]; then
        echo "==> [RemitGo! DB ERROR] MariaDB gagal dimulai dalam 30 detik."
        exit 1
    fi

    echo "==> [RemitGo! DB] Menyiapkan database dan kredensial..."
    mariadb -e "CREATE DATABASE IF NOT EXISTS AplikasiRemmitance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mariadb -e "CREATE USER IF NOT EXISTS 'remitops'@'127.0.0.1' IDENTIFIED BY 'remitops123';"
    mariadb -e "CREATE USER IF NOT EXISTS 'remitops'@'localhost' IDENTIFIED BY 'remitops123';"
    mariadb -e "GRANT ALL PRIVILEGES ON AplikasiRemmitance.* TO 'remitops'@'127.0.0.1';"
    mariadb -e "GRANT ALL PRIVILEGES ON AplikasiRemmitance.* TO 'remitops'@'localhost';"
    mariadb -e "FLUSH PRIVILEGES;"

    if [ -f "/var/www/html/aplikasiremmitance.sql" ]; then
        echo "==> [RemitGo! DB] Mengimpor skema awal aplikasiremmitance.sql..."
        mariadb AplikasiRemmitance < /var/www/html/aplikasiremmitance.sql
        echo "==> [RemitGo! DB] Import skema awal selesai."
    fi

    echo "==> [RemitGo! DB] Menghentikan daemon sementara..."
    mariadb-admin shutdown
    wait "$temp_pid"
    echo "==> [RemitGo! DB] Inisialisasi basis data berhasil tuntas."
fi

# 3. Sinkronisasi file konfigurasi .env
if [ ! -f "/var/www/html/.env" ] && [ -f "/var/www/html/.env.example" ]; then
    echo "==> [RemitGo! App] Menyiapkan berkas .env dari template..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

echo "==> [RemitGo! Container] Semua komponen siap. Menyerahkan kontrol ke Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
