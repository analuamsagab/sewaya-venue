<?php
declare(strict_types=1);

/**
 * Koneksi PDO tunggal.
 * MySQL di container berjalan pada UTC, jadi sesi dipaksa ke WIB
 * supaya NOW() dan CURRENT_TIMESTAMP konsisten dengan waktu yang ditampilkan.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        config('db.host', '127.0.0.1'),
        (int) config('db.port', 3306),
        config('db.name', 'sewaya')
    );

    $pdo = new PDO($dsn, (string) config('db.user'), (string) config('db.pass'), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    $pdo->exec("SET time_zone = '+07:00'");

    return $pdo;
}
