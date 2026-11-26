<?php
declare(strict_types=1);

const UPLOAD_MAX_BYTES = 2 * 1024 * 1024;

/** MIME yang diizinkan => ekstensi yang dipakai di disk. */
const UPLOAD_MIME_MAP = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

/**
 * Menyimpan gambar upload. Ekstensi diturunkan dari MIME asli, bukan dari nama file
 * klien, sehingga file .php yang menyamar sebagai gambar tetap tersimpan sebagai gambar.
 *
 * @param array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int} $file
 * @return array{ok: bool, path?: string, message?: string}
 */
function simpan_gambar(array $file, string $folder = 'uploads'): array
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($error !== UPLOAD_ERR_OK) {
        $pesan = match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran berkas terlalu besar.',
            UPLOAD_ERR_NO_FILE                        => 'Tidak ada berkas yang dipilih.',
            default                                   => 'Berkas gagal diunggah.',
        };

        return ['ok' => false, 'message' => $pesan];
    }

    $tmp  = (string) ($file['tmp_name'] ?? '');
    $size = (int) ($file['size'] ?? 0);

    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return ['ok' => false, 'message' => 'Berkas tidak valid.'];
    }

    if ($size <= 0 || $size > UPLOAD_MAX_BYTES) {
        return ['ok' => false, 'message' => 'Ukuran gambar maksimal 2 MB.'];
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);

    if (!is_string($mime) || !isset(UPLOAD_MIME_MAP[$mime])) {
        return ['ok' => false, 'message' => 'Format harus JPG, PNG, atau WebP.'];
    }

    if (@getimagesize($tmp) === false) {
        return ['ok' => false, 'message' => 'Berkas bukan gambar yang valid.'];
    }

    $dir = APP_ROOT . '/' . trim($folder, '/');

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return ['ok' => false, 'message' => 'Folder penyimpanan tidak dapat dibuat.'];
    }

    $nama = bin2hex(random_bytes(16)) . '.' . UPLOAD_MIME_MAP[$mime];
    $path = trim($folder, '/') . '/' . $nama;

    if (!move_uploaded_file($tmp, $dir . '/' . $nama)) {
        return ['ok' => false, 'message' => 'Gambar gagal disimpan.'];
    }

    return ['ok' => true, 'path' => $path];
}

function hapus_gambar(string $path): bool
{
    $absolute = APP_ROOT . '/' . ltrim($path, '/');

    return is_file($absolute) && unlink($absolute);
}
