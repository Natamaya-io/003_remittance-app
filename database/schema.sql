CREATE DATABASE IF NOT EXISTS AplikasiRemmitance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE AplikasiRemmitance;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS fx_rate_history;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS analysis_results;
DROP TABLE IF EXISTS analysis_weights;
DROP TABLE IF EXISTS analysis_runs;
DROP TABLE IF EXISTS fx_rates;
DROP TABLE IF EXISTS complaints;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
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
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id),
    INDEX idx_users_role (role_id),
    INDEX idx_users_team (transaction_team)
) ENGINE=InnoDB;

CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    tax_id VARCHAR(100) NULL,
    address TEXT NULL,
    contact_name VARCHAR(150) NULL,
    contact_phone VARCHAR(50) NULL,
    email VARCHAR(190) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_companies_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_companies_name (name),
    INDEX idx_companies_status (status)
) ENGINE=InnoDB;

CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_no TINYINT UNSIGNED NOT NULL DEFAULT 1,
    invoice_no VARCHAR(120) NULL,
    reference_no VARCHAR(120) NULL,
    transaction_date DATE NOT NULL,
    payer_company_id BIGINT UNSIGNED NOT NULL,
    beneficiary_name VARCHAR(255) NULL,
    beneficiary_account VARCHAR(255) NULL,
    beneficiary_bank VARCHAR(255) NULL,
    beneficiary_bank_location VARCHAR(255) NULL,
    amount DECIMAL(22,4) NOT NULL DEFAULT 0,
    beneficiary_country VARCHAR(120) NULL,
    beneficiary_city VARCHAR(120) NULL,
    beneficiary_address TEXT NULL,
    swift VARCHAR(50) NULL,
    payment_details TEXT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    correspondent_bank VARCHAR(255) NULL,
    correspondent_swift VARCHAR(50) NULL,
    correspondent_location VARCHAR(255) NULL,
    payer_bank VARCHAR(120) NULL,
    invoice_link VARCHAR(500) NULL,
    status ENUM('PENDING','SUCCESS','FAILED','CANCELLED') NOT NULL DEFAULT 'PENDING',
    refund_status ENUM('none','requested','processed','success','failed') NOT NULL DEFAULT 'none',
    notes TEXT NULL,
    source_hash CHAR(64) NULL UNIQUE,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_transactions_company FOREIGN KEY (payer_company_id) REFERENCES companies(id),
    CONSTRAINT fk_transactions_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_transactions_date (transaction_date),
    INDEX idx_transactions_company (payer_company_id),
    INDEX idx_transactions_team (team_no),
    INDEX idx_transactions_status (status),
    INDEX idx_transactions_refund (refund_status),
    INDEX idx_transactions_reference (reference_no),
    INDEX idx_transactions_currency (currency),
    INDEX idx_transactions_country (beneficiary_country),
    INDEX idx_transactions_swift (swift)
) ENGINE=InnoDB;

CREATE TABLE complaints (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_no VARCHAR(60) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NULL,
    priority ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
    status ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
    assigned_to BIGINT UNSIGNED NULL,
    resolution TEXT NULL,
    attachment_path VARCHAR(500) NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_complaints_company FOREIGN KEY (company_id) REFERENCES companies(id),
    CONSTRAINT fk_complaints_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    CONSTRAINT fk_complaints_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_complaints_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_complaints_status (status),
    INDEX idx_complaints_priority (priority),
    INDEX idx_complaints_company (company_id)
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(100) NOT NULL UNIQUE,
    company_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NULL,
    issue_date DATE NOT NULL,
    due_date DATE NULL,
    amount DECIMAL(22,4) NOT NULL DEFAULT 0,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('draft','sent','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
    notes TEXT NULL,
    attachment_path VARCHAR(500) NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoices_company FOREIGN KEY (company_id) REFERENCES companies(id),
    CONSTRAINT fk_invoices_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    CONSTRAINT fk_invoices_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_invoices_status (status),
    INDEX idx_invoices_due (due_date)
) ENGINE=InnoDB;

CREATE TABLE fx_rates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    base_currency CHAR(3) NOT NULL,
    quote_currency CHAR(3) NOT NULL,
    rate DECIMAL(24,10) NOT NULL,
    provider VARCHAR(80) NOT NULL,
    rate_date DATETIME NULL,
    fetched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_fx_pair (base_currency, quote_currency),
    INDEX idx_fx_fetched (fetched_at)
) ENGINE=InnoDB;

CREATE TABLE analysis_runs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    period1_start DATE NOT NULL,
    period1_end DATE NOT NULL,
    period2_start DATE NOT NULL,
    period2_end DATE NOT NULL,
    criteria_json JSON NOT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_analysis_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE analysis_weights (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    analysis_run_id BIGINT UNSIGNED NOT NULL,
    criterion_code VARCHAR(10) NOT NULL,
    entropy_value DECIMAL(20,12) NOT NULL,
    dispersion_value DECIMAL(20,12) NOT NULL,
    weight_value DECIMAL(20,12) NOT NULL,
    CONSTRAINT fk_weights_run FOREIGN KEY (analysis_run_id) REFERENCES analysis_runs(id) ON DELETE CASCADE,
    UNIQUE KEY uq_weight_run_code (analysis_run_id, criterion_code)
) ENGINE=InnoDB;

CREATE TABLE analysis_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    analysis_run_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    decision_values_json JSON NOT NULL,
    distance_positive DECIMAL(20,12) NOT NULL,
    distance_negative DECIMAL(20,12) NOT NULL,
    preference_value DECIMAL(20,12) NOT NULL,
    rank_position INT NOT NULL,
    CONSTRAINT fk_results_run FOREIGN KEY (analysis_run_id) REFERENCES analysis_runs(id) ON DELETE CASCADE,
    CONSTRAINT fk_results_company FOREIGN KEY (company_id) REFERENCES companies(id),
    UNIQUE KEY uq_result_run_company (analysis_run_id, company_id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    entity_type VARCHAR(80) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    before_data JSON NULL,
    after_data JSON NULL,
    ip_address VARCHAR(64) NULL,
    user_agent VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_created (created_at),
    INDEX idx_audit_entity (entity_type, entity_id),
    INDEX idx_audit_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS document_template_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    module_code VARCHAR(50) NOT NULL,
    type_code VARCHAR(80) NOT NULL,
    type_name VARCHAR(150) NOT NULL,
    owner_role VARCHAR(80) NOT NULL,
    description VARCHAR(255) NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_document_template_type_code (type_code),
    KEY idx_document_template_types_module (module_code),
    KEY idx_document_template_types_owner_role (owner_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS document_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    type_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NULL,
    file_path VARCHAR(500) NULL,
    file_original_name VARCHAR(255) NULL,
    file_mime VARCHAR(150) NULL,
    file_size BIGINT UNSIGNED NULL,
    version_label VARCHAR(20) NOT NULL DEFAULT '1.0',
    status ENUM('draft','active','inactive') NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_document_templates_type (type_id),
    KEY idx_document_templates_status (status),
    KEY idx_document_templates_created_by (created_by),
    CONSTRAINT fk_document_templates_type FOREIGN KEY (type_id) REFERENCES document_template_types(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_document_templates_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_document_templates_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoice_template_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    template_type VARCHAR(50) NOT NULL,
    document_no VARCHAR(120) NULL,
    invoice_no VARCHAR(120) NULL,
    value_date DATE NULL,
    invoice_date DATE NULL,
    due_date DATE NULL,
    currency VARCHAR(10) NULL,
    amount DECIMAL(22,4) NOT NULL DEFAULT 0,
    qty DECIMAL(18,4) NOT NULL DEFAULT 1,
    rate DECIMAL(22,4) NOT NULL DEFAULT 0,
    company_name VARCHAR(200) NULL,
    company_address TEXT NULL,
    company_phone VARCHAR(100) NULL,
    company_email VARCHAR(150) NULL,
    bill_to_name VARCHAR(200) NULL,
    bill_to_address TEXT NULL,
    beneficiary_name VARCHAR(200) NULL,
    beneficiary_address TEXT NULL,
    beneficiary_country VARCHAR(120) NULL,
    beneficiary_account VARCHAR(120) NULL,
    beneficiary_bank VARCHAR(200) NULL,
    beneficiary_bank_address TEXT NULL,
    beneficiary_swift VARCHAR(80) NULL,
    bank_name VARCHAR(200) NULL,
    bank_swift VARCHAR(80) NULL,
    bank_account VARCHAR(120) NULL,
    bank_address TEXT NULL,
    correspondent_bank VARCHAR(200) NULL,
    correspondent_bank_address TEXT NULL,
    correspondent_bank_swift VARCHAR(80) NULL,
    correspondent_bank_account VARCHAR(120) NULL,
    payment_details TEXT NULL,
    item_description TEXT NULL,
    terms VARCHAR(120) NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_invoice_template_type (template_type),
    KEY idx_invoice_template_invoice_no (invoice_no),
    CONSTRAINT fk_invoice_template_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_invoice_template_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS si_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(80) NOT NULL,
    name VARCHAR(180) NOT NULL,
    default_company_name VARCHAR(200) NULL,
    default_company_address TEXT NULL,
    default_company_phone VARCHAR(100) NULL,
    default_logo_path VARCHAR(500) NULL,
    default_bank_name VARCHAR(200) NULL,
    default_bank_branch VARCHAR(150) NULL,
    default_bank_city VARCHAR(150) NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_si_templates_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS si_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    template_id BIGINT UNSIGNED NOT NULL,
    document_number VARCHAR(150) NULL,
    document_date DATE NOT NULL,
    effective_date DATE NULL,
    document_city VARCHAR(120) NOT NULL DEFAULT 'Jakarta',
    company_name VARCHAR(200) NULL,
    company_address TEXT NULL,
    company_phone VARCHAR(100) NULL,
    logo_path VARCHAR(500) NULL,
    bank_name VARCHAR(200) NULL,
    bank_branch VARCHAR(150) NULL,
    bank_city VARCHAR(150) NULL,
    attachment_text VARCHAR(255) NULL,
    subject VARCHAR(255) NULL,
    signatory_title VARCHAR(150) NULL,
    second_signatory_title VARCHAR(150) NULL,
    notes TEXT NULL,
    status ENUM('draft','final','cancelled') NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_si_documents_template (template_id),
    KEY idx_si_documents_date (document_date),
    KEY idx_si_documents_status (status),
    CONSTRAINT fk_si_documents_template FOREIGN KEY (template_id) REFERENCES si_templates(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_si_documents_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_si_documents_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS si_document_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    document_id BIGINT UNSIGNED NOT NULL,
    item_order INT UNSIGNED NOT NULL DEFAULT 1,
    sender_name VARCHAR(255) NULL,
    sender_account VARCHAR(150) NULL,
    receiver_name VARCHAR(255) NULL,
    receiver_account VARCHAR(150) NULL,
    receiver_bank VARCHAR(255) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'IDR',
    amount DECIMAL(22,4) NOT NULL DEFAULT 0,
    transaction_count INT UNSIGNED NOT NULL DEFAULT 1,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_si_items_document (document_id),
    CONSTRAINT fk_si_items_document FOREIGN KEY (document_id) REFERENCES si_documents(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO document_template_types (module_code,type_code,type_name,owner_role,description,sort_order,is_active) VALUES
('invoice','invoice_order','Invoice Order','admin_invoice','Template Invoice Order.',10,1),
('invoice','invoice_asli','Invoice Asli','admin_invoice','Template Invoice Asli.',20,1),
('invoice','invoice_terima_uang','Invoice Terima Uang','admin_invoice','Template Invoice Terima Uang.',30,1),
('complain','complain_surat_refund','Surat Refund','admin_complain','Template Surat Refund.',10,1),
('complain','complain_surat_amend','Surat Amend','admin_complain','Template Surat Amend.',20,1),
('complain','complain_mt199','MT 199','admin_complain','Template MT 199.',30,1),
('complain','complain_mt103','MT 103','admin_complain','Template MT 103.',40,1)
ON DUPLICATE KEY UPDATE type_name=VALUES(type_name),owner_role=VALUES(owner_role),description=VALUES(description),sort_order=VALUES(sort_order),is_active=1,updated_at=CURRENT_TIMESTAMP;

INSERT INTO si_templates (code,name,default_company_name,default_company_address,default_company_phone,default_logo_path,default_bank_name,default_bank_branch,default_bank_city,sort_order,is_active) VALUES
('dealing_bri','Dealing Transaksi BRI','PT JURITA AGROTAMA INDONESIA','Jl. Setra Wangi, Ruko Setra Sari Mega Square B-18 Kec. Sukajadi Kel. Suka Galih Bandung 40163','(022) 20284801','assets/img/letterheads/logo_jurita.png','PT. Bank Rakyat Indonesia (Persero) Tbk.','KCP Cikini','Jakarta Pusat',10,1),
('overbooking','Overbooking','PT FABLE TEKNOLOGI NUSANTARA','Menara Rajawali Lt. 16, Jl. Dr. Ide Anak Agung Gde Agung, Mega Kuningan, Jakarta Selatan, DKI Jakarta, Indonesia',NULL,'assets/img/letterheads/logo_fable.png','PT. Bank Rakyat Indonesia (Persero) Tbk.','KCP Cikini','Jakarta Pusat',20,1),
('pemindah_bukuan','Pemindah Bukuan','PT FABLE TEKNOLOGI NUSANTARA','Menara Rajawali Lt. 16, Jl. Dr. Ide Anak Agung Gde Agung, Mega Kuningan, Jakarta Selatan, DKI Jakarta, Indonesia',NULL,'assets/img/letterheads/logo_fable.png','PT. Bank Rakyat Indonesia (Persero) Tbk.','KCP Cikini','Jakarta Pusat',30,1),
('rtgs','RTGS','PT FABLE TEKNOLOGI NUSANTARA','Menara Rajawali Lt. 16, Jl. Dr. Ide Anak Agung Gde Agung, Mega Kuningan, Jakarta Selatan, DKI Jakarta, Indonesia',NULL,'assets/img/letterheads/logo_fable.png','PT. Bank Rakyat Indonesia (Persero) Tbk.','KCP Cikini','Jakarta Pusat',40,1),
('tarik_setor','Tarik Setor','PT DIGITAL KRIPTO INDONESIA','Axa Tower Kuningan City Lt. 37 Suite 6(A), Jalan Prof. Dr. Satrio Kavling 18, Kuningan, Jakarta Selatan','(021) 50101858','assets/img/letterheads/logo_pgx.svg','PT. Bank Rakyat Indonesia (Persero) Tbk.','KCP Cikini','Jakarta Pusat',50,1),
('transfer','Transfer','PT JURITA AGROTAMA INDONESIA','Jl. Setra Wangi, Ruko Setra Sari Mega Square B-18 Kec. Sukajadi Kel. Suka Galih Bandung 40163','(022) 20284801','assets/img/letterheads/logo_jurita.png','PT. Bank Rakyat Indonesia (Persero) Tbk.','KCP Cikini','Jakarta Pusat',60,1),
('permohonan_debet','Permohonan Debet Rekening',NULL,NULL,NULL,NULL,'PT. Bank Rakyat Indonesia (Persero) Tbk.','KCP Cikini','Jakarta Pusat',70,1)
ON DUPLICATE KEY UPDATE name=VALUES(name),default_company_name=VALUES(default_company_name),default_company_address=VALUES(default_company_address),default_company_phone=VALUES(default_company_phone),default_logo_path=VALUES(default_logo_path),default_bank_name=VALUES(default_bank_name),default_bank_branch=VALUES(default_bank_branch),default_bank_city=VALUES(default_bank_city),sort_order=VALUES(sort_order),is_active=1,updated_at=CURRENT_TIMESTAMP;

-- Modul Report Harian Independen dan Batch Transaksi V3

USE AplikasiRemmitance;

CREATE TABLE IF NOT EXISTS report_batches (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    month_key CHAR(7) NOT NULL,
    report_date DATE NULL,
    client_name VARCHAR(200) NULL,
    source_filename VARCHAR(255) NOT NULL,
    source_path VARCHAR(500) NOT NULL,
    total_rows INT UNSIGNED NOT NULL DEFAULT 0,
    sent_rows INT UNSIGNED NOT NULL DEFAULT 0,
    pending_rows INT UNSIGNED NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_report_batches_month (month_key),
    KEY idx_report_batches_date (report_date),
    KEY idx_report_batches_creator (created_by),
    CONSTRAINT fk_report_batches_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS report_rows (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    batch_id BIGINT UNSIGNED NOT NULL,
    source_sheet VARCHAR(120) NULL,
    source_row INT UNSIGNED NULL,
    team_label VARCHAR(50) NULL,
    item_no VARCHAR(50) NULL,
    transaction_date DATE NULL,
    bill_to VARCHAR(255) NULL,
    beneficiary_name VARCHAR(255) NULL,
    beneficiary_account VARCHAR(255) NULL,
    beneficiary_bank VARCHAR(255) NULL,
    beneficiary_bank_address TEXT NULL,
    amount DECIMAL(22,4) NOT NULL DEFAULT 0,
    beneficiary_country VARCHAR(120) NULL,
    beneficiary_city VARCHAR(120) NULL,
    beneficiary_address TEXT NULL,
    swift VARCHAR(80) NULL,
    payment_details TEXT NULL,
    currency CHAR(3) NULL,
    invoice_no VARCHAR(255) NULL,
    invoice_date DATE NULL,
    contract_no VARCHAR(255) NULL,
    contract_date DATE NULL,
    correspondent_bank VARCHAR(255) NULL,
    correspondent_swift VARCHAR(80) NULL,
    correspondent_country VARCHAR(255) NULL,
    bank_of_payer VARCHAR(120) NULL,
    invoice_link VARCHAR(700) NULL,
    processing_bank VARCHAR(120) NULL,
    delivery_status ENUM('PENDING','SENT') NOT NULL DEFAULT 'PENDING',
    result_status ENUM('PENDING','SUCCESS','FAILED') NOT NULL DEFAULT 'PENDING',
    refund_status VARCHAR(80) NULL,
    notes TEXT NULL,
    source_json JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_report_rows_batch (batch_id),
    KEY idx_report_rows_date (transaction_date),
    KEY idx_report_rows_delivery (delivery_status),
    KEY idx_report_rows_result (result_status),
    KEY idx_report_rows_swift (swift),
    CONSTRAINT fk_report_rows_batch FOREIGN KEY (batch_id) REFERENCES report_batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transaction_jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    team_no TINYINT UNSIGNED NOT NULL DEFAULT 1,
    source_filename VARCHAR(255) NOT NULL,
    source_path VARCHAR(500) NOT NULL,
    source_sheet VARCHAR(120) NULL,
    output_email VARCHAR(190) NOT NULL DEFAULT 'remit.service09@gmail.com',
    status ENUM('READY','EMPTY','ARCHIVED') NOT NULL DEFAULT 'READY',
    total_rows INT UNSIGNED NOT NULL DEFAULT 0,
    imported_rows INT UNSIGNED NOT NULL DEFAULT 0,
    skipped_rows INT UNSIGNED NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_transaction_jobs_team (team_no),
    KEY idx_transaction_jobs_created (created_at),
    CONSTRAINT fk_transaction_jobs_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- MariaDB XAMPP mendukung IF NOT EXISTS pada ADD COLUMN/INDEX.

-- Perbaikan aman apabila tabel modul pernah dibuat oleh versi sebelumnya.
ALTER TABLE report_batches
    ADD COLUMN IF NOT EXISTS name VARCHAR(200) NOT NULL DEFAULT 'REPORT HARIAN' AFTER id,
    ADD COLUMN IF NOT EXISTS month_key CHAR(7) NOT NULL DEFAULT '2026-01' AFTER name,
    ADD COLUMN IF NOT EXISTS report_date DATE NULL AFTER month_key,
    ADD COLUMN IF NOT EXISTS client_name VARCHAR(200) NULL AFTER report_date,
    ADD COLUMN IF NOT EXISTS source_filename VARCHAR(255) NOT NULL DEFAULT 'report.xlsx' AFTER client_name,
    ADD COLUMN IF NOT EXISTS source_path VARCHAR(500) NOT NULL DEFAULT '' AFTER source_filename,
    ADD COLUMN IF NOT EXISTS total_rows INT UNSIGNED NOT NULL DEFAULT 0 AFTER source_path,
    ADD COLUMN IF NOT EXISTS sent_rows INT UNSIGNED NOT NULL DEFAULT 0 AFTER total_rows,
    ADD COLUMN IF NOT EXISTS pending_rows INT UNSIGNED NOT NULL DEFAULT 0 AFTER sent_rows,
    ADD COLUMN IF NOT EXISTS created_by BIGINT UNSIGNED NULL AFTER pending_rows,
    ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_by,
    ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE report_rows
    ADD COLUMN IF NOT EXISTS source_sheet VARCHAR(120) NULL AFTER batch_id,
    ADD COLUMN IF NOT EXISTS source_row INT UNSIGNED NULL AFTER source_sheet,
    ADD COLUMN IF NOT EXISTS team_label VARCHAR(50) NULL AFTER source_row,
    ADD COLUMN IF NOT EXISTS item_no VARCHAR(50) NULL AFTER team_label,
    ADD COLUMN IF NOT EXISTS transaction_date DATE NULL AFTER item_no,
    ADD COLUMN IF NOT EXISTS bill_to VARCHAR(255) NULL AFTER transaction_date,
    ADD COLUMN IF NOT EXISTS beneficiary_name VARCHAR(255) NULL AFTER bill_to,
    ADD COLUMN IF NOT EXISTS beneficiary_account VARCHAR(255) NULL AFTER beneficiary_name,
    ADD COLUMN IF NOT EXISTS beneficiary_bank VARCHAR(255) NULL AFTER beneficiary_account,
    ADD COLUMN IF NOT EXISTS beneficiary_bank_address TEXT NULL AFTER beneficiary_bank,
    ADD COLUMN IF NOT EXISTS amount DECIMAL(22,4) NOT NULL DEFAULT 0 AFTER beneficiary_bank_address,
    ADD COLUMN IF NOT EXISTS beneficiary_country VARCHAR(120) NULL AFTER amount,
    ADD COLUMN IF NOT EXISTS beneficiary_city VARCHAR(120) NULL AFTER beneficiary_country,
    ADD COLUMN IF NOT EXISTS beneficiary_address TEXT NULL AFTER beneficiary_city,
    ADD COLUMN IF NOT EXISTS swift VARCHAR(80) NULL AFTER beneficiary_address,
    ADD COLUMN IF NOT EXISTS payment_details TEXT NULL AFTER swift,
    ADD COLUMN IF NOT EXISTS currency CHAR(3) NULL AFTER payment_details,
    ADD COLUMN IF NOT EXISTS invoice_no VARCHAR(255) NULL AFTER currency,
    ADD COLUMN IF NOT EXISTS invoice_date DATE NULL AFTER invoice_no,
    ADD COLUMN IF NOT EXISTS contract_no VARCHAR(255) NULL AFTER invoice_date,
    ADD COLUMN IF NOT EXISTS contract_date DATE NULL AFTER contract_no,
    ADD COLUMN IF NOT EXISTS correspondent_bank VARCHAR(255) NULL AFTER contract_date,
    ADD COLUMN IF NOT EXISTS correspondent_swift VARCHAR(80) NULL AFTER correspondent_bank,
    ADD COLUMN IF NOT EXISTS correspondent_country VARCHAR(255) NULL AFTER correspondent_swift,
    ADD COLUMN IF NOT EXISTS bank_of_payer VARCHAR(120) NULL AFTER correspondent_country,
    ADD COLUMN IF NOT EXISTS invoice_link VARCHAR(700) NULL AFTER bank_of_payer,
    ADD COLUMN IF NOT EXISTS processing_bank VARCHAR(120) NULL AFTER invoice_link,
    ADD COLUMN IF NOT EXISTS delivery_status ENUM('PENDING','SENT') NOT NULL DEFAULT 'PENDING' AFTER processing_bank,
    ADD COLUMN IF NOT EXISTS result_status ENUM('PENDING','SUCCESS','FAILED') NOT NULL DEFAULT 'PENDING' AFTER delivery_status,
    ADD COLUMN IF NOT EXISTS refund_status VARCHAR(80) NULL AFTER result_status,
    ADD COLUMN IF NOT EXISTS notes TEXT NULL AFTER refund_status,
    ADD COLUMN IF NOT EXISTS source_json JSON NULL AFTER notes,
    ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER source_json,
    ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE transaction_jobs
    ADD COLUMN IF NOT EXISTS name VARCHAR(200) NOT NULL DEFAULT 'TRANSAKSI' AFTER id,
    ADD COLUMN IF NOT EXISTS team_no TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER name,
    ADD COLUMN IF NOT EXISTS source_filename VARCHAR(255) NOT NULL DEFAULT 'template.xlsx' AFTER team_no,
    ADD COLUMN IF NOT EXISTS source_path VARCHAR(500) NOT NULL DEFAULT '' AFTER source_filename,
    ADD COLUMN IF NOT EXISTS source_sheet VARCHAR(120) NULL AFTER source_path,
    ADD COLUMN IF NOT EXISTS output_email VARCHAR(190) NOT NULL DEFAULT 'remit.service09@gmail.com' AFTER source_sheet,
    ADD COLUMN IF NOT EXISTS status ENUM('READY','EMPTY','ARCHIVED') NOT NULL DEFAULT 'READY' AFTER output_email,
    ADD COLUMN IF NOT EXISTS total_rows INT UNSIGNED NOT NULL DEFAULT 0 AFTER status,
    ADD COLUMN IF NOT EXISTS imported_rows INT UNSIGNED NOT NULL DEFAULT 0 AFTER total_rows,
    ADD COLUMN IF NOT EXISTS skipped_rows INT UNSIGNED NOT NULL DEFAULT 0 AFTER imported_rows,
    ADD COLUMN IF NOT EXISTS created_by BIGINT UNSIGNED NULL AFTER skipped_rows,
    ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_by,
    ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE transactions
    ADD COLUMN IF NOT EXISTS transaction_job_id BIGINT UNSIGNED NULL AFTER id,
    ADD COLUMN IF NOT EXISTS sequence_no VARCHAR(50) NULL AFTER transaction_job_id,
    ADD COLUMN IF NOT EXISTS payer_account VARCHAR(255) NULL AFTER payer_company_id,
    ADD COLUMN IF NOT EXISTS payer_address TEXT NULL AFTER payer_account,
    ADD COLUMN IF NOT EXISTS payer_city VARCHAR(120) NULL AFTER payer_address,
    ADD COLUMN IF NOT EXISTS payer_country VARCHAR(120) NULL AFTER payer_city,
    ADD COLUMN IF NOT EXISTS payer_email VARCHAR(190) NULL AFTER payer_country;

ALTER TABLE transactions
    ADD INDEX IF NOT EXISTS idx_transactions_job (transaction_job_id);


-- ============================================================
-- UPGRADE V4: UI/UX, FX realtime, strict import, complaint/report, analysis
-- ============================================================
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


-- =========================================================
-- Fable RemitOps V5: Master Rekening Payer
-- =========================================================
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
