<?php
declare(strict_types=1);

define('APP_ROOT', __DIR__);

require APP_ROOT . '/includes/bootstrap.php';

$halaman = (string) ($_GET['page'] ?? 'beranda');

/**
 * Daftar putih halaman. Tidak ada include dari nilai $_GET secara langsung.
 * Halaman baru harus didaftarkan di sini.
 */
$daftarHalaman = [
    'beranda'    => 'pages/beranda.php',
    'login'      => 'pages/auth/login.php',
    'register'   => 'pages/auth/register.php',
    'lupa-sandi' => 'pages/auth/lupa-sandi.php',
    'sandi-baru' => 'pages/auth/sandi-baru.php',
    'dashboard'  => 'pages/dashboard.php',
];

if (!isset($daftarHalaman[$halaman])) {
    http_response_code(404);
    require APP_ROOT . '/pages/404.php';
    exit;
}

require APP_ROOT . '/' . $daftarHalaman[$halaman];
