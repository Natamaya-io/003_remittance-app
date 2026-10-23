
-- ============================================================
-- MODUL SPK ENTROPY - TOPSIS STANDALONE
-- Tidak memiliki FK / integrasi ke transactions, report_rows,
-- companies, invoices, complaints, atau tabel operasional lainnya.
-- ============================================================

CREATE TABLE IF NOT EXISTS spk_sources (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    source_type ENUM('report_2024','actual','daily_tiara') NOT NULL,
    name VARCHAR(200) NOT NULL,
    source_filename VARCHAR(255) NOT NULL,
    source_path VARCHAR(500) NOT NULL,
    origin ENUM('bundled','upload') NOT NULL DEFAULT 'upload',
    total_rows INT UNSIGNED NOT NULL DEFAULT 0,
    imported_rows INT UNSIGNED NOT NULL DEFAULT 0,
    ignored_rows INT UNSIGNED NOT NULL DEFAULT 0,
    summary_json LONGTEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_spk_source_type_active (source_type,is_active),
    CONSTRAINT fk_spk_source_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_records (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    source_id BIGINT UNSIGNED NOT NULL,
    sheet_name VARCHAR(120) NOT NULL,
    source_row_no INT UNSIGNED NOT NULL,
    payer_key VARCHAR(80) NOT NULL,
    payer_name VARCHAR(255) NOT NULL,
    transaction_date DATE NULL,
    reference_no VARCHAR(255) NULL,
    event_type ENUM('SUCCESS','REFUND','TRACE','OFAC','OTHER') NOT NULL DEFAULT 'OTHER',
    beneficiary_country VARCHAR(150) NULL,
    beneficiary_bank VARCHAR(255) NULL,
    swift VARCHAR(80) NULL,
    currency VARCHAR(10) NULL,
    amount DECIMAL(24,4) NOT NULL DEFAULT 0,
    status_text VARCHAR(500) NULL,
    resolved TINYINT(1) NOT NULL DEFAULT 0,
    source_hash CHAR(64) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_spk_source_hash (source_id,source_hash),
    KEY idx_spk_records_payer (payer_key),
    KEY idx_spk_records_event (event_type),
    KEY idx_spk_records_source_payer (source_id,payer_key),
    CONSTRAINT fk_spk_record_source FOREIGN KEY (source_id) REFERENCES spk_sources(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_criteria (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(10) NOT NULL,
    name VARCHAR(200) NOT NULL,
    criterion_type ENUM('benefit','cost') NOT NULL DEFAULT 'benefit',
    data_rule VARCHAR(80) NOT NULL DEFAULT 'manual',
    description VARCHAR(500) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_spk_criteria_code (code),
    CONSTRAINT fk_spk_criteria_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_subcriteria (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    criterion_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    value DECIMAL(20,8) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_spk_sub_criterion (criterion_id),
    CONSTRAINT fk_spk_sub_criterion FOREIGN KEY (criterion_id) REFERENCES spk_criteria(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_alternatives (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    alternative_key VARCHAR(80) NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    source_note VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_spk_alt_key (alternative_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_assessments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    alternative_id BIGINT UNSIGNED NOT NULL,
    criterion_id BIGINT UNSIGNED NOT NULL,
    auto_value DECIMAL(24,8) NOT NULL DEFAULT 0,
    override_value DECIMAL(24,8) NULL,
    source_detail_json LONGTEXT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_spk_assessment (alternative_id,criterion_id),
    CONSTRAINT fk_spk_assessment_alt FOREIGN KEY (alternative_id) REFERENCES spk_alternatives(id) ON DELETE CASCADE,
    CONSTRAINT fk_spk_assessment_criterion FOREIGN KEY (criterion_id) REFERENCES spk_criteria(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_runs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    criteria_snapshot_json LONGTEXT NOT NULL,
    matrix_json LONGTEXT NOT NULL,
    entropy_json LONGTEXT NOT NULL,
    topsis_json LONGTEXT NOT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_spk_run_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_weights (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    run_id BIGINT UNSIGNED NOT NULL,
    criterion_code VARCHAR(10) NOT NULL,
    entropy_value DECIMAL(20,12) NOT NULL,
    dispersion_value DECIMAL(20,12) NOT NULL,
    weight_value DECIMAL(20,12) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_spk_weight_run FOREIGN KEY (run_id) REFERENCES spk_runs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spk_results (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    run_id BIGINT UNSIGNED NOT NULL,
    alternative_id BIGINT UNSIGNED NULL,
    alternative_name VARCHAR(255) NOT NULL,
    values_json LONGTEXT NOT NULL,
    distance_positive DECIMAL(20,12) NOT NULL,
    distance_negative DECIMAL(20,12) NOT NULL,
    preference_value DECIMAL(20,12) NOT NULL,
    rank_position INT NOT NULL,
    PRIMARY KEY (id),
    KEY idx_spk_results_rank (run_id,rank_position),
    CONSTRAINT fk_spk_result_run FOREIGN KEY (run_id) REFERENCES spk_runs(id) ON DELETE CASCADE,
    CONSTRAINT fk_spk_result_alt FOREIGN KEY (alternative_id) REFERENCES spk_alternatives(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
