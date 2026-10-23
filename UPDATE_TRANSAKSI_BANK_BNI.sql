USE `AplikasiRemmitance`;

CREATE TABLE IF NOT EXISTS `bni_transaction_batches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `total_rows` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bni_batch_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bni_transaction_rows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `batch_id` BIGINT UNSIGNED NOT NULL,
  `row_no` INT UNSIGNED NOT NULL DEFAULT 1,
  `transaction_date` DATE NULL,
  `beneficiary_name` VARCHAR(255) NULL,
  `remitter_name` VARCHAR(255) NULL,
  `currency` VARCHAR(10) NULL,
  `amount` DECIMAL(22,4) NOT NULL DEFAULT 0,
  `payload_json` LONGTEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bni_rows_batch` (`batch_id`,`row_no`),
  CONSTRAINT `fk_bni_rows_batch`
    FOREIGN KEY (`batch_id`) REFERENCES `bni_transaction_batches` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
