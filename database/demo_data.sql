USE AplikasiRemmitance;

INSERT INTO transactions (team_no,invoice_no,reference_no,transaction_date,payer_company_id,beneficiary_name,beneficiary_bank,beneficiary_bank_location,amount,beneficiary_country,beneficiary_city,swift,payment_details,currency,payer_bank,status,refund_status,created_by)
VALUES
(1,'INV-DEMO-001','REF-DEMO-001',CURDATE(),(SELECT id FROM companies WHERE code='FABLE'),'Demo Beneficiary A','Bank of China','Shanghai, China',12500,'China','Shanghai','BKCHCNBJ','Payment for demo goods','USD','BRI','SUCCESS','none',(SELECT id FROM users WHERE username='transaksi1')),
(2,'INV-DEMO-002','REF-DEMO-002',CURDATE(),(SELECT id FROM companies WHERE code='JURITA'),'Demo Beneficiary B','DBS Bank','Singapore',9800,'Singapore','Singapore','DBSSSGSG','Payment for demo services','USD','BNI','PENDING','none',(SELECT id FROM users WHERE username='transaksi2')),
(3,'INV-DEMO-003','REF-DEMO-003',DATE_SUB(CURDATE(),INTERVAL 1 DAY),(SELECT id FROM companies WHERE code='GSB'),'Demo Beneficiary C','HSBC','Hong Kong',65000,'Hong Kong','Hong Kong','HSBCHKHH','Payment for demo invoice','CNY','BRI','SUCCESS','success',(SELECT id FROM users WHERE username='transaksi3'));

INSERT INTO complaints (ticket_no,company_id,transaction_id,subject,description,priority,status,assigned_to,created_by)
VALUES ('CMP-DEMO-0001',(SELECT id FROM companies WHERE code='FABLE'),(SELECT id FROM transactions WHERE reference_no='REF-DEMO-001'),'Konfirmasi status transfer','Contoh complain untuk demonstrasi.','medium','open',(SELECT id FROM users WHERE username='complain'),(SELECT id FROM users WHERE username='complain'));

INSERT INTO invoices (invoice_no,company_id,transaction_id,issue_date,due_date,amount,currency,status,notes,created_by)
VALUES ('BILL-DEMO-001',(SELECT id FROM companies WHERE code='FABLE'),(SELECT id FROM transactions WHERE reference_no='REF-DEMO-001'),CURDATE(),DATE_ADD(CURDATE(),INTERVAL 14 DAY),12500,'USD','sent','Contoh invoice demonstrasi.',(SELECT id FROM users WHERE username='invoice'));
