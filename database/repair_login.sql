-- HOTFIX LOGIN FABLE REMITOPS
-- Jalankan melalui phpMyAdmin > SQL.

CREATE DATABASE IF NOT EXISTS AplikasiRemmitance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE AplikasiRemmitance;

CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO roles (code,name,description)
VALUES ('super_admin','Super Admin','Akses penuh dan pengelolaan user')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    transaction_team TINYINT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    must_change_password TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role_id),
    INDEX idx_users_team (transaction_team)
) ENGINE=InnoDB;

-- Tambahkan kolom yang mungkin belum ada pada instalasi lama.
ALTER TABLE users ADD COLUMN IF NOT EXISTS transaction_team TINYINT UNSIGNED NULL AFTER password;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER transaction_team;
ALTER TABLE users ADD COLUMN IF NOT EXISTS must_change_password TINYINT(1) NOT NULL DEFAULT 1 AFTER is_active;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at DATETIME NULL AFTER must_change_password;
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER last_login_at;
ALTER TABLE users ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Pastikan akun superadmin ada dan password kembali ke Password123!
INSERT INTO users (role_id,name,username,email,password,transaction_team,is_active,must_change_password)
VALUES (
    (SELECT id FROM roles WHERE code='super_admin' LIMIT 1),
    'Super Administrator',
    'superadmin',
    'superadmin@fable.local',
    '$2y$12$q4W713FdsFLcK/QEkTIwPeYqXjYNAFo3q8dHIaY0ESFtqNUPrcD5C',
    NULL,
    1,
    1
)
ON DUPLICATE KEY UPDATE
    role_id=VALUES(role_id),
    name=VALUES(name),
    email=VALUES(email),
    password=VALUES(password),
    is_active=1,
    must_change_password=1;

SELECT u.id,u.username,u.email,u.is_active,u.must_change_password,r.code AS role_code
FROM users u JOIN roles r ON r.id=u.role_id
WHERE u.username='superadmin';
