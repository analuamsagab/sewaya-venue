<?php
declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

function config(string $key, mixed $default = null): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require APP_ROOT . '/config/config.php';
    }
    $value = $cfg;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

date_default_timezone_set((string) config('app.timezone', 'Asia/Jakarta'));
mb_internal_encoding('UTF-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    // "Ingat saya" menyimpan cookie sesi 30 hari, bukan cookie biasa.
    $ingat = isset($_COOKIE['sewaya_ingat']);
    if ($ingat) {
        ini_set('session.gc_maxlifetime', '2592000');
    }

    session_set_cookie_params([
        'lifetime' => $ingat ? 2592000 : 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('sewaya_session');
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/components.php';
require_once __DIR__ . '/layout.php';
