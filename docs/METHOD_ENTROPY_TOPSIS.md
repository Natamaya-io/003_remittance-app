# Metode Entropy–TOPSIS — Mode Penelitian

## Sumber data

Perhitungan hanya menggunakan dua sumber:

1. **Report Sukses 2024** untuk membentuk C1–C5.
2. **Actual LIST MT199** untuk data refund C6, hanya sheet **REFUND BNI** dan **REFUND BRI**.

`Daily Update - Tiara` tidak digunakan pada perhitungan penelitian.

## Periode

- P1: 1 September 2024 s.d. 31 Oktober 2024.
- P2: 1 November 2024 s.d. 31 Desember 2024.

## Kriteria

1. C1 = transaksi P2 / transaksi P1.
2. C2 = (transaksi P2 / hari aktif P2) / (transaksi P1 / hari aktif P1).
3. C3 = negara unik P2 / negara unik P1.
4. C4 = bank/SWIFT unik P2 / bank/SWIFT unik P1.
5. C5 = mata uang unik P2 / mata uang unik P1.
6. C6 = 1 − (refund P2 / transaksi P2).

Seluruh kriteria merupakan **benefit**.

## Entropy

Metode mengikuti modul PDF yang digunakan pada penelitian:

- Kij = Xij / max(Xj) untuk kriteria benefit.
- aij = Kij / ΣKij.
- Ej = −1/ln(m) × Σ(aij × ln(aij)).
- Dj = 1 − Ej.
- Wj = Dj / ΣDj.

## TOPSIS

- Rij = Xij / √Σ(Xij²).
- Yij = Wj × Rij.
- A+ = maksimum setiap kolom karena seluruh kriteria benefit.
- A− = minimum setiap kolom.
- Hitung D+ dan D− dengan jarak Euclidean.
- Vi = D− / (D+ + D−).
- Vi terbesar mendapat peringkat terbaik.

## Aturan refund Actual

Tanggal refund untuk atribusi periode memakai urutan:

1. Transaction Date.
2. ACK Date jika Transaction Date kosong.
3. Refund Date jika keduanya kosong.

Aturan fallback tersebut disimpan pada `status_text` sebagai `DATE_BASIS=...` agar dapat ditelusuri.
