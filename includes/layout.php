<?php
declare(strict_types=1);

/**
 * Kerangka HTML. Setiap halaman memanggil halaman_mulai() lalu halaman_selesai().
 */
function halaman_mulai(string $judul, string $kelasBody = '', array $stylesheet = []): void
{
    $stylesheet = array_merge(
        ['/assets/css/tokens.css', '/assets/css/base.css', '/assets/css/layout.css'],
        $stylesheet
    );
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($judul) ?> &middot; SEWAYA</title>
    <?php foreach ($stylesheet as $berkas): ?>
    <link rel="stylesheet" href="<?= e(asset($berkas)) ?>">
    <?php endforeach; ?>
</head>
<body<?= $kelasBody !== '' ? ' class="' . e($kelasBody) . '"' : '' ?>>
    <?php
}

function halaman_selesai(): void
{
    ?>
    <script src="<?= e(asset('/assets/js/app.js')) ?>"></script>
</body>
</html>
    <?php
}

function flash_render(): string
{
    $peta = [
        'success' => 'alert--success',
        'error'   => 'alert--error',
        'info'    => 'alert--info',
    ];

    $html = '';
    foreach ($peta as $kunci => $kelas) {
        $pesan = flash_get($kunci);
        if ($pesan !== null) {
            $html .= '<div class="alert ' . $kelas . '">' . e($pesan) . '</div>';
        }
    }

    return $html;
}
