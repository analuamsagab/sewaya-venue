<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $page = 'beranda', array $params = []): string
{
    $query = array_merge(['page' => $page], $params);
    return '/?' . http_build_query($query);
}

function api_auth(string $aksi): string
{
    return '/api/auth.php?action=' . rawurlencode($aksi);
}

function asset(string $path): string
{
    $relative = ltrim($path, '/');
    $absolute = APP_ROOT . '/' . $relative;
    $version  = is_file($absolute) ? filemtime($absolute) : 0;

    return '/' . $relative . '?v=' . $version;
}

function redirect(string $page, array $params = []): never
{
    header('Location: ' . url($page, $params));
    exit;
}

function rupiah(float|int|string $amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

/** "Rabu, 25 November 2026" */
function tanggal_indonesia(string $datetime): string
{
    $ts    = strtotime($datetime);
    $hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    return $hari[(int) date('w', $ts)] . ', ' . (int) date('j', $ts) . ' '
        . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

/** "26 Nov 2026" */
function tanggal_singkat(string $datetime): string
{
    $ts    = strtotime($datetime);
    $bulan = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
    ];

    return (int) date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

/** "09.00 – 12.00 WIB" */
function jam_rentang(string $mulai, string $selesai): string
{
    return date('H.i', strtotime($mulai)) . ' – ' . date('H.i', strtotime($selesai)) . ' WIB';
}

function waktu_relatif(string $datetime): string
{
    $diff = time() - strtotime($datetime);

    if ($diff < 60) {
        return 'baru saja';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' menit lalu';
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . ' jam lalu';
    }
    if ($diff < 172800) {
        return 'kemarin';
    }

    return floor($diff / 86400) . ' hari lalu';
}

function nama_depan(string $nama): string
{
    $bagian = preg_split('/\s+/', trim($nama));

    return $bagian[0] ?? $nama;
}

/** "Aditya Pratama" -> "Aditya P."; "Rizqi Hasan F" -> "Rizqi H. F." */
function nama_singkat(string $nama): string
{
    $bagian = preg_split('/\s+/', trim($nama)) ?: [];
    if (count($bagian) < 2) {
        return trim($nama);
    }

    $awal = array_shift($bagian);
    $inisial = array_map(
        static fn (string $kata): string => mb_strtoupper(mb_substr($kata, 0, 1)) . '.',
        $bagian
    );

    return $awal . ' ' . implode(' ', $inisial);
}

function salam(): string
{
    $jam = (int) date('G');

    if ($jam < 11) {
        return 'Selamat pagi';
    }
    if ($jam < 15) {
        return 'Selamat siang';
    }
    if ($jam < 18) {
        return 'Selamat sore';
    }

    return 'Selamat malam';
}

function flash_set(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);

    return $message;
}

function valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function valid_telepon(string $nomor): bool
{
    return (bool) preg_match('/^0[0-9]{9,13}$/', $nomor);
}
