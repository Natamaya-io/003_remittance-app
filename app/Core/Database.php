<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    /**
     * Membuat koneksi PDO ke MariaDB/MySQL XAMPP.
     */
    public static function connect(array $config): PDO
    {
        $host = (string) ($config['host'] ?? '127.0.0.1');
        $port = (string) ($config['port'] ?? '3306');
        $database = (string) ($config['database'] ?? 'AplikasiRemmitance');
        $username = (string) ($config['username'] ?? 'root');
        $password = (string) ($config['password'] ?? '');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $database
        );

        try {
            $pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE
                        => PDO::ERRMODE_EXCEPTION,

                    PDO::ATTR_DEFAULT_FETCH_MODE
                        => PDO::FETCH_ASSOC,

                    /*
                     * Diaktifkan untuk kompatibilitas PHP 8.0
                     * dan MariaDB bawaan XAMPP.
                     */
                    PDO::ATTR_EMULATE_PREPARES
                        => true,

                    PDO::ATTR_STRINGIFY_FETCHES
                        => false,
                ]
            );

            /*
             * Memastikan mode SQL kompatibel.
             */
            $pdo->exec(
                "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            );

            return $pdo;
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Koneksi database gagal. '
                . 'Pastikan MySQL XAMPP aktif dan file .env benar. '
                . 'Detail: '
                . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}