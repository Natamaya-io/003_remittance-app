<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

/**
 * Mesin perhitungan Entropy + TOPSIS.
 *
 * Rumus mengikuti dokumen "Perhitungan Kombinasi Metode ENTROPY Dan TOPSIS":
 * - Entropy: Kij (benefit Xij/max, cost min/Xij), Aij, Ej, Dj, Wj.
 * - TOPSIS: normalisasi vektor Rij, Yij = Wj*Rij, A+/A-, D+/D-, Vi, ranking.
 */
final class EntropyTopsisEngine
{
    /**
     * @param array<int,array<int,float|int>> $matrix
     * @param array<int,string> $types benefit|cost
     * @return array<string,mixed>
     */
    public function calculate(array $matrix, array $types): array
    {
        $this->validate($matrix, $types);

        $entropy = $this->entropy($matrix, $types);
        $topsis = $this->topsis($matrix, $entropy['weights'], $types);

        return [
            'entropy' => $entropy,
            'topsis' => $topsis,
        ];
    }

    /**
     * @param array<int,array<int,float|int>> $matrix
     * @param array<int,string> $types
     */
    private function validate(array $matrix, array $types): void
    {
        if (count($matrix) < 2) {
            throw new InvalidArgumentException('Minimal dua alternatif diperlukan.');
        }

        $n = count($matrix[0] ?? []);
        if ($n < 1 || count($types) !== $n) {
            throw new InvalidArgumentException('Jumlah tipe kriteria tidak sesuai matriks keputusan.');
        }

        foreach ($matrix as $row) {
            if (count($row) !== $n) {
                throw new InvalidArgumentException('Matriks keputusan tidak berbentuk persegi panjang.');
            }
            foreach ($row as $value) {
                if (!is_numeric($value) || !is_finite((float) $value) || (float) $value < 0) {
                    throw new InvalidArgumentException('Nilai kriteria harus berupa angka >= 0.');
                }
            }
        }

        foreach ($types as $type) {
            if (!in_array(strtolower($type), ['benefit', 'cost'], true)) {
                throw new InvalidArgumentException('Jenis kriteria harus benefit atau cost.');
            }
        }
    }

    /**
     * @param array<int,array<int,float|int>> $matrix
     * @param array<int,string> $types
     * @return array<string,mixed>
     */
    private function entropy(array $matrix, array $types): array
    {
        $m = count($matrix);
        $n = count($matrix[0]);

        $normalized = array_fill(0, $m, array_fill(0, $n, 0.0)); // Kij
        $columnReference = [];
        $columnTotals = array_fill(0, $n, 0.0);

        for ($j = 0; $j < $n; $j++) {
            $column = array_map(static fn (array $row): float => (float) $row[$j], $matrix);
            $type = strtolower($types[$j]);

            if ($type === 'benefit') {
                $reference = max($column);
                $columnReference[$j] = $reference;
                for ($i = 0; $i < $m; $i++) {
                    $normalized[$i][$j] = $reference > 0 ? ((float) $matrix[$i][$j] / $reference) : 0.0;
                }
            } else {
                $minimum = min($column);
                $columnReference[$j] = $minimum;

                if ($minimum > 0) {
                    for ($i = 0; $i < $m; $i++) {
                        $x = (float) $matrix[$i][$j];
                        $normalized[$i][$j] = $x > 0 ? ($minimum / $x) : 1.0;
                    }
                } else {
                    /*
                     * Perluasan aman untuk data cost bernilai 0.
                     * Pada rumus min/X, 0 adalah nilai cost terbaik tetapi menyebabkan 0/0.
                     * Nilai 0 diberi Kij=1, sedangkan nilai positif memakai min positif / X.
                     * Untuk data tanpa nol, hasil sama persis dengan rumus PDF.
                     */
                    $positive = array_values(array_filter($column, static fn (float $v): bool => $v > 0));
                    if ($positive === []) {
                        for ($i = 0; $i < $m; $i++) {
                            $normalized[$i][$j] = 1.0;
                        }
                    } else {
                        $minPositive = min($positive);
                        for ($i = 0; $i < $m; $i++) {
                            $x = (float) $matrix[$i][$j];
                            $normalized[$i][$j] = $x <= 0 ? 1.0 : ($minPositive / $x);
                        }
                    }
                }
            }

            $columnTotals[$j] = array_sum(array_column($normalized, $j));
        }

        // Aij = Kij / Sum Kij
        $aij = array_fill(0, $m, array_fill(0, $n, 0.0));
        for ($j = 0; $j < $n; $j++) {
            $sum = $columnTotals[$j];
            for ($i = 0; $i < $m; $i++) {
                $aij[$i][$j] = $sum > 0 ? ($normalized[$i][$j] / $sum) : (1.0 / $m);
            }
        }

        // Ej = -1/ln(m) * Sum(aij * ln(aij))
        $entropy = array_fill(0, $n, 0.0);
        $dispersion = array_fill(0, $n, 0.0);
        $k = -1.0 / log((float) $m);

        for ($j = 0; $j < $n; $j++) {
            $sum = 0.0;
            for ($i = 0; $i < $m; $i++) {
                $a = (float) $aij[$i][$j];
                if ($a > 0) {
                    $sum += $a * log($a);
                }
            }
            $entropy[$j] = max(0.0, min(1.0, $k * $sum));
            $dispersion[$j] = 1.0 - $entropy[$j];
        }

        $sumDispersion = array_sum($dispersion);
        $weights = $sumDispersion > 0
            ? array_map(static fn (float $d): float => $d / $sumDispersion, $dispersion)
            : array_fill(0, $n, 1.0 / $n);

        return [
            'normalized' => $normalized,
            'column_reference' => $columnReference,
            'column_totals' => $columnTotals,
            'aij' => $aij,
            'entropy' => $entropy,
            'dispersion' => $dispersion,
            'weights' => $weights,
            'k' => $k,
        ];
    }

    /**
     * @param array<int,array<int,float|int>> $matrix
     * @param array<int,float> $weights
     * @param array<int,string> $types
     * @return array<string,mixed>
     */
    private function topsis(array $matrix, array $weights, array $types): array
    {
        $m = count($matrix);
        $n = count($matrix[0]);

        // Rij = Xij / sqrt(sum Xij^2)
        $normalized = array_fill(0, $m, array_fill(0, $n, 0.0));
        $divisors = array_fill(0, $n, 0.0);

        for ($j = 0; $j < $n; $j++) {
            $sumSquares = 0.0;
            for ($i = 0; $i < $m; $i++) {
                $x = (float) $matrix[$i][$j];
                $sumSquares += $x * $x;
            }
            $divisor = sqrt($sumSquares);
            $divisors[$j] = $divisor;
            for ($i = 0; $i < $m; $i++) {
                $normalized[$i][$j] = $divisor > 0 ? ((float) $matrix[$i][$j] / $divisor) : 0.0;
            }
        }

        // Yij = Wj * Rij
        $weighted = array_fill(0, $m, array_fill(0, $n, 0.0));
        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weighted[$i][$j] = $normalized[$i][$j] * (float) $weights[$j];
            }
        }

        // A+ dan A- berdasarkan benefit/cost.
        $idealPositive = array_fill(0, $n, 0.0);
        $idealNegative = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            $column = array_column($weighted, $j);
            if (strtolower($types[$j]) === 'benefit') {
                $idealPositive[$j] = max($column);
                $idealNegative[$j] = min($column);
            } else {
                $idealPositive[$j] = min($column);
                $idealNegative[$j] = max($column);
            }
        }

        // Jarak Euclidean terhadap A+ dan A-.
        $distancePositive = array_fill(0, $m, 0.0);
        $distanceNegative = array_fill(0, $m, 0.0);
        $preferences = array_fill(0, $m, 0.0);

        for ($i = 0; $i < $m; $i++) {
            $plus = 0.0;
            $minus = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $plus += ($weighted[$i][$j] - $idealPositive[$j]) ** 2;
                $minus += ($weighted[$i][$j] - $idealNegative[$j]) ** 2;
            }
            $distancePositive[$i] = sqrt($plus);
            $distanceNegative[$i] = sqrt($minus);
            $den = $distancePositive[$i] + $distanceNegative[$i];
            $preferences[$i] = $den > 0 ? ($distanceNegative[$i] / $den) : 0.0;
        }

        $sorted = $preferences;
        arsort($sorted, SORT_NUMERIC);
        $ranks = array_fill(0, $m, 0);
        $rank = 1;
        foreach (array_keys($sorted) as $index) {
            $ranks[(int) $index] = $rank++;
        }

        return [
            'divisors' => $divisors,
            'normalized' => $normalized,
            'weighted' => $weighted,
            'ideal_positive' => $idealPositive,
            'ideal_negative' => $idealNegative,
            'distance_positive' => $distancePositive,
            'distance_negative' => $distanceNegative,
            'preferences' => $preferences,
            'ranks' => $ranks,
        ];
    }
}
