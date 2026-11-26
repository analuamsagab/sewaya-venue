<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/includes/bootstrap.php';

$aksi = (string) ($_GET['action'] ?? '');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_error('Metode tidak diizinkan.', [], 405);
}

csrf_require();

$input = json_input();

switch ($aksi) {
    case 'login':
        $email = mb_strtolower(trim((string) ($input['email'] ?? '')));
        $password = (string) ($input['password'] ?? '');
        $ingat = !empty($input['ingat']);

        $galat = [];
        if (!valid_email($email)) {
            $galat['email'] = 'Format email tidak valid.';
        }
        if ($password === '') {
            $galat['password'] = 'Kata sandi wajib diisi.';
        }
        if ($galat !== []) {
            json_error('Periksa kembali isian Anda.', $galat);
        }

        // Maksimal 5 percobaan gagal per menit per sesi.
        $percobaan = array_values(array_filter(
            (array) ($_SESSION['login_attempts'] ?? []),
            static fn ($waktu): bool => (int) $waktu > time() - 60
        ));
        if (count($percobaan) >= 5) {
            json_error('Terlalu banyak percobaan masuk. Tunggu satu menit.', [], 429);
        }

        $hasil = auth_login($email, $password, $ingat);
        if (!$hasil['ok']) {
            $percobaan[] = time();
            $_SESSION['login_attempts'] = $percobaan;
            json_error((string) $hasil['message'], ['email' => 'Periksa email dan kata sandi.'], 401);
        }

        unset($_SESSION['login_attempts']);
        json_ok(['redirect' => url('dashboard')], 'Berhasil masuk.');

        // no break — json_ok menghentikan eksekusi.
    case 'register':
        $nama = trim((string) ($input['nama_lengkap'] ?? ''));
        $email = mb_strtolower(trim((string) ($input['email'] ?? '')));
        $telp = preg_replace('/[\s\-]/', '', (string) ($input['no_telp'] ?? '')) ?? '';
        $password = (string) ($input['password'] ?? '');
        $ulangi = (string) ($input['ulangi_password'] ?? '');

        $galat = [];
        if (mb_strlen($nama) < 3 || mb_strlen($nama) > 100) {
            $galat['nama_lengkap'] = 'Nama lengkap 3 sampai 100 karakter.';
        }
        if (!valid_email($email) || mb_strlen($email) > 100) {
            $galat['email'] = 'Format email tidak valid.';
        }
        if (!valid_telepon($telp)) {
            $galat['no_telp'] = 'Nomor telepon diawali 0 dan terdiri dari 10 sampai 14 angka.';
        }
        if (strlen($password) < 8) {
            $galat['password'] = 'Kata sandi minimal 8 karakter.';
        }
        if ($password !== $ulangi) {
            $galat['ulangi_password'] = 'Ulangan kata sandi tidak sama.';
        }
        if ($galat !== []) {
            json_error('Periksa kembali isian Anda.', $galat);
        }

        $stmt = db()->prepare('SELECT email, no_telp FROM users WHERE email = ? OR no_telp = ? LIMIT 1');
        $stmt->execute([$email, $telp]);
        $sudahAda = $stmt->fetch();

        if ($sudahAda) {
            $galat = [];
            if (mb_strtolower((string) $sudahAda['email']) === $email) {
                $galat['email'] = 'Email sudah terdaftar.';
            }
            if ((string) $sudahAda['no_telp'] === $telp) {
                $galat['no_telp'] = 'Nomor telepon sudah terdaftar.';
            }
            json_error('Akun dengan data tersebut sudah ada.', $galat);
        }

        $idRole = (int) db()->query("SELECT id_role FROM role WHERE nama_role = 'User' LIMIT 1")->fetchColumn();
        if ($idRole === 0) {
            json_error('Role pengguna belum tersedia. Jalankan seeder database.', [], 500);
        }

        try {
            $stmt = db()->prepare(
                'INSERT INTO users (id_role, nama_lengkap, email, no_telp, password, status)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $idRole,
                $nama,
                $email,
                $telp,
                password_hash($password, PASSWORD_DEFAULT),
                'aktif',
            ]);
        } catch (PDOException $e) {
            if ((string) $e->getCode() === '23000') {
                json_error('Email atau nomor telepon sudah terdaftar.');
            }
            throw $e;
        }

        $hasil = auth_login($email, $password, false);
        if (!$hasil['ok']) {
            json_ok(['redirect' => url('login')], 'Pendaftaran berhasil. Silakan masuk.');
        }

        json_ok(['redirect' => url('dashboard')], 'Pendaftaran berhasil. Selamat datang!');

        // no break
    case 'lupa-sandi':
        $email = mb_strtolower(trim((string) ($input['email'] ?? '')));

        if (!valid_email($email)) {
            json_error('Format email tidak valid.', ['email' => 'Format email tidak valid.']);
        }

        $stmt = db()->prepare("SELECT id_user FROM users WHERE email = ? AND status = 'aktif' LIMIT 1");
        $stmt->execute([$email]);
        $ada = (bool) $stmt->fetch();

        $data = ['reset_url' => null];

        if ($ada) {
            $token = bin2hex(random_bytes(32));
            $menit = (int) config('app.reset_token_minutes', 60);

            // Token lama untuk email ini tidak berlaku lagi.
            $stmt = db()->prepare('UPDATE password_reset SET used_at = NOW() WHERE email = ? AND used_at IS NULL');
            $stmt->execute([$email]);

            $stmt = db()->prepare(
                'INSERT INTO password_reset (email, token_hash, expires_at)
                 VALUES (?, ?, ?)'
            );
            $stmt->execute([
                $email,
                hash('sha256', $token),
                date('Y-m-d H:i:s', time() + ($menit * 60)),
            ]);

            // Mode demo lokal: tanpa SMTP, tautan dikembalikan ke halaman.
            // Di server produksi bagian ini harus diganti pengiriman email.
            $data['reset_url'] = url('sandi-baru', ['token' => $token]);
        }

        json_ok($data, 'Kalau email terdaftar, tautan reset sudah dibuat.');

        // no break
    case 'sandi-baru':
        $token = (string) ($input['token'] ?? '');
        $password = (string) ($input['password'] ?? '');
        $ulangi = (string) ($input['ulangi_password'] ?? '');

        $galat = [];
        if (preg_match('/^[a-f0-9]{64}$/', $token) !== 1) {
            $galat['token'] = 'Token tidak valid.';
        }
        if (strlen($password) < 8) {
            $galat['password'] = 'Kata sandi minimal 8 karakter.';
        }
        if ($password !== $ulangi) {
            $galat['ulangi_password'] = 'Ulangan kata sandi tidak sama.';
        }
        if ($galat !== []) {
            json_error('Periksa kembali isian Anda.', $galat);
        }

        $stmt = db()->prepare(
            'SELECT id_reset, email FROM password_reset
             WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()
             ORDER BY id_reset DESC LIMIT 1'
        );
        $stmt->execute([hash('sha256', $token)]);
        $reset = $stmt->fetch();

        if (!$reset) {
            json_error('Tautan reset sudah kedaluwarsa. Minta tautan baru.', [], 410);
        }

        $stmt = db()->prepare('SELECT id_user FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([(string) $reset['email']]);
        $idUser = (int) $stmt->fetchColumn();

        if ($idUser === 0) {
            json_error('Akun tidak ditemukan.', [], 404);
        }

        $pdo = db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id_user = ?');
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $idUser]);

            $stmt = $pdo->prepare('UPDATE password_reset SET used_at = NOW() WHERE id_reset = ?');
            $stmt->execute([(int) $reset['id_reset']]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        flash_set('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.');
        json_ok(['redirect' => url('login')], 'Kata sandi berhasil diperbarui.');

        // no break
    case 'logout':
        auth_logout();
        json_ok(['redirect' => url('beranda')], 'Anda telah keluar.');

        // no break
    default:
        json_error('Aksi tidak dikenal.', [], 404);
}
