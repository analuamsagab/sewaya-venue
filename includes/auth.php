<?php
declare(strict_types=1);

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_check(): bool
{
    return isset($_SESSION['user']);
}

/**
 * @return array{ok: bool, message: string}
 */
function auth_login(string $email, string $password, bool $ingat = false): array
{
    $stmt = db()->prepare(
        'SELECT u.id_user, u.nama_lengkap, u.email, u.password, u.status,
                r.id_role, r.nama_role, r.permission
         FROM users u
         JOIN role r ON r.id_role = u.id_role
         WHERE u.email = ?
         LIMIT 1'
    );
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($password, (string) $row['password'])) {
        return ['ok' => false, 'message' => 'Email atau kata sandi salah.'];
    }

    if ($row['status'] !== 'aktif') {
        return ['ok' => false, 'message' => 'Akun Anda tidak aktif. Hubungi admin.'];
    }

    $permission = json_decode((string) $row['permission'], true);

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id_user'      => (int) $row['id_user'],
        'nama_lengkap' => (string) $row['nama_lengkap'],
        'email'        => (string) $row['email'],
        'id_role'      => (int) $row['id_role'],
        'role'         => (string) $row['nama_role'],
        'permission'   => is_array($permission) ? $permission : [],
    ];
    $_SESSION['last_activity'] = time();

    if ($ingat) {
        $_SESSION['remember'] = true;
        setcookie('sewaya_ingat', '1', [
            'expires'  => time() + 2592000,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    return ['ok' => true, 'message' => 'Berhasil masuk.'];
}

function auth_logout(): void
{
    unset($_SESSION['user'], $_SESSION['last_activity'], $_SESSION['remember']);
    setcookie('sewaya_ingat', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_regenerate_id(true);
}

function auth_require(): void
{
    if (!auth_check()) {
        flash_set('info', 'Silakan masuk dulu untuk memesan ruangan.');
        redirect('login');
    }

    $idle = (int) config('app.session_idle_minutes', 30) * 60;
    $last = (int) ($_SESSION['last_activity'] ?? time());

    if (empty($_SESSION['remember']) && $idle > 0 && (time() - $last) > $idle) {
        auth_logout();
        flash_set('error', 'Sesi berakhir karena tidak ada aktivitas. Silakan masuk lagi.');
        redirect('login');
    }

    $_SESSION['last_activity'] = time();
}

function auth_guest(): void
{
    if (auth_check()) {
        redirect('dashboard');
    }
}

function can(string $permission): bool
{
    $user = auth_user();
    if ($user === null) {
        return false;
    }

    $list = $user['permission'] ?? [];

    return in_array('*', $list, true) || in_array($permission, $list, true);
}

function has_role(string $role): bool
{
    return (auth_user()['role'] ?? '') === $role;
}
