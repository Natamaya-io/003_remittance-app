/* Fable RemitOps V22 — migrasi Kurs Transaksi Bank Indonesia */

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
    KEY idx_fx_history_fetched (fetched_at),
    KEY idx_fx_history_provider_date (provider, rate_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

DELETE FROM fx_rate_history
WHERE LOWER(provider) LIKE '%frankfurter%'
   OR LOWER(provider) LIKE '%exchangerate-api%'
   OR LOWER(provider) LIKE '%offline-cache%';

DELETE FROM fx_rates
WHERE LOWER(provider) LIKE '%frankfurter%'
   OR LOWER(provider) LIKE '%exchangerate-api%'
   OR LOWER(provider) LIKE '%offline-cache%';

COMMIT;

SELECT provider, COUNT(*) AS jumlah
FROM fx_rates
GROUP BY provider
ORDER BY provider;
