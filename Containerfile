# ==============================================================================
# Containerfile: RemitGo! All-in-One Podman Image (PHP 8.3 + Nginx + MariaDB)
# Standar Arsitektur: NATAMAYA Podman Rootless / Single-Writer Compliance
# ==============================================================================
FROM alpine:3.20

# 1. Pasang dependensi esensial (Zero-Bloat)
RUN apk update && apk add --no-cache \
    nginx \
    mariadb \
    mariadb-client \
    supervisor \
    curl \
    tzdata \
    php83 \
    php83-fpm \
    php83-pdo_mysql \
    php83-mbstring \
    php83-gd \
    php83-zip \
    php83-curl \
    php83-fileinfo \
    php83-session \
    php83-json \
    php83-opcache \
    php83-ctype \
    php83-dom \
    php83-xml \
    php83-xmlreader \
    php83-xmlwriter \
    && rm -rf /var/cache/apk/*

# 2. Atur Zona Waktu Indonesia Barat (WIB)
ENV TZ=Asia/Jakarta
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 3. Konfigurasi MariaDB internal loopback (127.0.0.1:3306 murni internal kontainer, anti-collision dengan host)
RUN sed -i 's/skip-networking/bind-address=127.0.0.1\nport=3306/' /etc/my.cnf.d/mariadb-server.cnf

# 4. Konfigurasi PHP-FPM untuk mendengarkan di 127.0.0.1:9000
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 127.0.0.1:9000/' /etc/php83/php-fpm.d/www.conf && \
    sed -i 's/;catch_workers_output = yes/catch_workers_output = yes/' /etc/php83/php-fpm.d/www.conf && \
    sed -i 's/;clear_env = no/clear_env = no/' /etc/php83/php-fpm.d/www.conf

# 5. Konfigurasi Direktori Kerja
WORKDIR /var/www/html

# 6. Salin Berkas Konfigurasi Infrastruktur
COPY infrastructure/container/nginx.conf /etc/nginx/http.d/default.conf
COPY infrastructure/container/supervisord.conf /etc/supervisord.conf
COPY infrastructure/container/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# 7. Salin Kode Aplikasi
COPY . /var/www/html/

# 8. Penyiapan Direktori Runtime & Perizinan
RUN mkdir -p /run/mysqld /var/lib/mysql /run/nginx /var/log/supervisor /var/www/html/storage /var/www/html/public/uploads && \
    chown -R mysql:mysql /run/mysqld /var/lib/mysql && \
    chown -R nginx:nginx /run/nginx /var/www/html/storage /var/www/html/public/uploads

# 9. Expose Port HTTP Aplikasi Saja (Port 3306 MariaDB TIDAK DI-EXPOSE demi Anti-Collision)
EXPOSE 8000

# 10. Deklarasi Volume untuk Persistensi Data
VOLUME ["/var/lib/mysql", "/var/www/html/storage", "/var/www/html/public/uploads"]

# 11. Titik Masuk (Bootstrap)
ENTRYPOINT ["/entrypoint.sh"]
