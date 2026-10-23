-- Fable RemitOps V5 Professional
-- Jalankan setelah backup database lama.
-- Kompatibel dengan MariaDB 10.4+ / MySQL 8.
USE AplikasiRemmitance;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS payer_accounts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    company_id BIGINT UNSIGNED NOT NULL,
    account_no VARCHAR(120) NOT NULL,
    account_name VARCHAR(200) NULL,
    bank_name VARCHAR(200) NOT NULL,
    bank_branch VARCHAR(200) NULL,
    swift VARCHAR(30) NULL,
    currency CHAR(3) NOT NULL DEFAULT 'IDR',
    email VARCHAR(190) NULL,
    address TEXT NULL,
    city VARCHAR(150) NULL,
    country VARCHAR(150) NOT NULL DEFAULT 'INDONESIA',
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_payer_account_company_bank (company_id,account_no,bank_name),
    KEY idx_payer_account_company (company_id),
    KEY idx_payer_account_status (status),
    KEY idx_payer_account_default (company_id,is_default)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE transactions
    ADD COLUMN IF NOT EXISTS payer_account_id BIGINT UNSIGNED NULL AFTER payer_company_id;

SET @v5_index_exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='transactions' AND INDEX_NAME='idx_transactions_payer_account'
);
SET @v5_index_sql := IF(@v5_index_exists=0,
    'CREATE INDEX idx_transactions_payer_account ON transactions (payer_account_id)',
    'SELECT 1'
);
PREPARE v5_index_stmt FROM @v5_index_sql;
EXECUTE v5_index_stmt;
DEALLOCATE PREPARE v5_index_stmt;

-- Membentuk master awal dari snapshot rekening transaksi lama.
INSERT IGNORE INTO payer_accounts
(
    company_id,account_no,account_name,bank_name,currency,email,address,city,country,
    is_default,status,created_by,created_at,updated_at
)
SELECT
    t.payer_company_id,
    TRIM(t.payer_account),
    c.name,
    COALESCE(NULLIF(TRIM(t.payer_bank),''),'BANK PAYER'),
    'IDR',
    NULLIF(TRIM(t.payer_email),''),
    NULLIF(TRIM(t.payer_address),''),
    NULLIF(TRIM(t.payer_city),''),
    COALESCE(NULLIF(TRIM(t.payer_country),''),'INDONESIA'),
    0,
    'active',
    t.created_by,
    NOW(),NOW()
FROM transactions t
JOIN companies c ON c.id=t.payer_company_id
WHERE t.deleted_at IS NULL
  AND NULLIF(TRIM(t.payer_account),'') IS NOT NULL;

-- Tandai satu rekening pertama per perusahaan sebagai default bila belum ada.
UPDATE payer_accounts pa
JOIN (
    SELECT company_id,MIN(id) first_id
    FROM payer_accounts
    WHERE status='active'
    GROUP BY company_id
) first_account ON first_account.first_id=pa.id
LEFT JOIN (
    SELECT company_id,MAX(is_default) has_default
    FROM payer_accounts
    GROUP BY company_id
) defaults ON defaults.company_id=pa.company_id
SET pa.is_default=1
WHERE COALESCE(defaults.has_default,0)=0;

SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE IF NOT EXISTS complaint_histories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    complaint_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(80) NOT NULL,
    note TEXT NULL,
    status_from VARCHAR(40) NULL,
    status_to VARCHAR(40) NULL,
    user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_complaint_history_complaint (complaint_id),
    KEY idx_complaint_history_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
