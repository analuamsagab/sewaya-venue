<?php
/**
 * Salin file ini menjadi config/config.php lalu isi kredensial.
 * config/config.php tidak boleh ikut dibagikan.
 */
declare(strict_types=1);

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'sewaya',
        'user' => 'root',
        'pass' => '',
    ],
    'app' => [
        'name'     => 'SEWAYA',
        'timezone' => 'Asia/Jakarta',
        'debug'    => true,
        'session_idle_minutes' => 30,
        'reset_token_minutes'  => 60,
    ],
];
