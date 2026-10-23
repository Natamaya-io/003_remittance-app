-- Fable RemitOps Upgrade V4
-- Jalankan satu kali pada database yang sudah ada melalui phpMyAdmin.
USE AplikasiRemmitance;

CREATE TABLE IF NOT EXISTS fx_rate_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    base_currency CHAR(3) NOT NULL,
    quote_currency CHAR(3) NOT NULL,
    rate DECIMAL(30,16) NOT NULL,
    provider VARCHAR(80) NOT NULL,
    rate_date DATETIME NULL,
    fetched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_fx_history_pair_time (base_currency, quote_currency, fetched_at),
    KEY idx_fx_history_fetched (fetched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE report_batches
    ADD COLUMN IF NOT EXISTS skipped_rows INT UNSIGNED NOT NULL DEFAULT 0 AFTER pending_rows,
    ADD COLUMN IF NOT EXISTS import_summary_json LONGTEXT NULL AFTER skipped_rows;

ALTER TABLE report_rows
    ADD COLUMN IF NOT EXISTS source_hash CHAR(64) NULL AFTER source_json,
    ADD INDEX IF NOT EXISTS idx_report_rows_source_hash (batch_id, source_hash);

ALTER TABLE transaction_jobs
    ADD COLUMN IF NOT EXISTS validation_json LONGTEXT NULL AFTER skipped_rows;

ALTER TABLE complaints
    ADD COLUMN IF NOT EXISTS report_row_id BIGINT UNSIGNED NULL AFTER transaction_id,
    ADD INDEX IF NOT EXISTS idx_complaints_report_row (report_row_id);

ALTER TABLE analysis_runs
    ADD COLUMN IF NOT EXISTS data_source ENUM('transactions','reports') NOT NULL DEFAULT 'transactions' AFTER name;

-- Isi histori awal dari cache kurs saat ini agar indikator naik/turun langsung tersedia.
INSERT INTO fx_rate_history (base_currency, quote_currency, rate, provider, rate_date, fetched_at)
SELECT r.base_currency, r.quote_currency, r.rate, r.provider, r.rate_date, r.fetched_at
FROM fx_rates r
WHERE NOT EXISTS (
    SELECT 1 FROM fx_rate_history h
    WHERE h.base_currency=r.base_currency
      AND h.quote_currency=r.quote_currency
      AND h.fetched_at=r.fetched_at
);

-- Bersihkan baris ringkasan lama yang sebelumnya dapat ikut terimpor.
DELETE FROM report_rows
WHERE (
    LOWER(TRIM(COALESCE(bill_to,''))) REGEXP '^(total|grand total|subtotal|jumlah|итого|итог)[: ]*$'
    OR LOWER(TRIM(COALESCE(beneficiary_name,''))) REGEXP '^(total|grand total|subtotal|jumlah|итого|итог)[: ]*$'
    OR LOWER(TRIM(COALESCE(item_no,''))) REGEXP '^(total|grand total|subtotal|jumlah|итого|итог)[: ]*$'
)
AND amount = 0;
