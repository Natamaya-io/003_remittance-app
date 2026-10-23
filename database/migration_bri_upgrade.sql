-- Upgrade Transaksi BRI
-- Menambahkan metadata audit untuk batch import BRI tanpa mengubah tabel transaksi lama

CREATE TABLE IF NOT EXISTS bri_import_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_job_id INT NOT NULL,
    bank_code VARCHAR(20) DEFAULT 'BRI',
    validation_status VARCHAR(30) DEFAULT 'READY',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_bri_job (transaction_job_id)
);
