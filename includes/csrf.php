<?php
declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_valid(): bool
{
    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['_token'] ?? '');

    return is_string($sent)
        && $sent !== ''
        && hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $sent);
}

function csrf_require(): void
{
    if (!csrf_valid()) {
        json_error('Sesi Anda sudah kedaluwarsa. Muat ulang halaman.', [], 419);
    }
}
