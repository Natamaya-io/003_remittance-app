USE AplikasiRemmitance;

INSERT INTO roles (code,name,description) VALUES
('super_admin','Super Admin','Akses penuh dan pengelolaan user'),
('supervisor','Supervisor','Monitoring, laporan, dan analisis keputusan'),
('admin_transaksi_1','Admin Transaksi 1','Pengelolaan transaksi Tim 1'),
('admin_transaksi_2','Admin Transaksi 2','Pengelolaan transaksi Tim 2'),
('admin_transaksi_3','Admin Transaksi 3','Pengelolaan transaksi Tim 3'),
('admin_transaksi_4','Admin Transaksi 4','Pengelolaan transaksi Tim 4'),
('admin_complain','Admin Complain','Pengelolaan complain pelanggan'),
('admin_invoice','Admin Invoice','Pengelolaan invoice');

INSERT INTO users (role_id,name,username,email,password,transaction_team,is_active,must_change_password) VALUES
((SELECT id FROM roles WHERE code='super_admin'),'Super Administrator','superadmin','superadmin@fable.local','$2y$12$q4W713FdsFLcK/QEkTIwPeYqXjYNAFo3q8dHIaY0ESFtqNUPrcD5C',NULL,1,1),
((SELECT id FROM roles WHERE code='supervisor'),'Supervisor Operasional','supervisor','supervisor@fable.local','$2y$12$XmJnSzl/VaCxau0jvst.OesIPvg4dzYs0OiiOuUCvOWfDd5/YEWJS',NULL,1,1),
((SELECT id FROM roles WHERE code='admin_transaksi_1'),'Admin Transaksi 1','transaksi1','transaksi1@fable.local','$2y$12$l7escPnUN69mD18rDPSQh.igtSHL5DA.agIOhsfQVh968eoIVEip.',1,1,1),
((SELECT id FROM roles WHERE code='admin_transaksi_2'),'Admin Transaksi 2','transaksi2','transaksi2@fable.local','$2y$12$9rt0YCTvg4HjY7A7al0qGedG.Tkaj8Xv/PTTF9iP09Ba7zQ5/DFP2',2,1,1),
((SELECT id FROM roles WHERE code='admin_transaksi_3'),'Admin Transaksi 3','transaksi3','transaksi3@fable.local','$2y$12$IlHR2i55x.Px9Z1Xz3WHueedDqIis9jzKTaaffUwnyPTHaqk01RYq',3,1,1),
((SELECT id FROM roles WHERE code='admin_transaksi_4'),'Admin Transaksi 4','transaksi4','transaksi4@fable.local','$2y$12$cEdREHgTdRUwNi5ZYcbJZuRkUonM1x8FBhTg5KEv5XLTBQ6j/KeDW',4,1,1),
((SELECT id FROM roles WHERE code='admin_complain'),'Admin Complain','complain','complain@fable.local','$2y$12$xVvN8hrj2fw1oOJjMBnqF.wvFGyauN1xicq4wyvOZoJDgWzhYgyDW',NULL,1,1),
((SELECT id FROM roles WHERE code='admin_invoice'),'Admin Invoice','invoice','invoice@fable.local','$2y$12$AlsCCuRm1MZGRcwwqs4e4.NJ8NALEO4gTNoxDINQP7E23OsQ9h2I6',NULL,1,1);

INSERT INTO companies (code,name,status,created_by) VALUES
('FABLE','PT FABLE TEKNOLOGI NUSANTARA','active',(SELECT id FROM users WHERE username='superadmin')),
('JURITA','PT JURITA AGROTAMA INDONESIA','active',(SELECT id FROM users WHERE username='superadmin')),
('GSB','PT GEMA SAPTA BINTANG','active',(SELECT id FROM users WHERE username='superadmin')),
('KAIROS','PT KAIROS SINERGI INDONESIA','active',(SELECT id FROM users WHERE username='superadmin'));
