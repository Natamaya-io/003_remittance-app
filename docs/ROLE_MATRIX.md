# Role Matrix

| Modul | Super Admin | Supervisor | Admin Transaksi 1–4 | Admin Complain | Admin Invoice |
|---|---:|---:|---:|---:|---:|
| Dashboard | RW | R | R | R | R |
| Data PT | RW | RW | R | R | R |
| Transaksi | RW | R | RW tim sendiri | R | R |
| Import transaksi | RW | - | RW tim sendiri | - | - |
| Master rekening payer | RW | R | RW | - | - |
| Report harian | R | R | R tim sendiri | - | - |
| Complain | RW | R | - | RW | - |
| Invoice | RW | R | - | - | RW |
| Entropy–TOPSIS | RW | RW | - | - | - |
| Kurs refresh | RW | RW | - | - | - |
| User | RW | - | - | - | - |
| Audit log | R | R | - | - | - |

R = read, RW = read/write.
