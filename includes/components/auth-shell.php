<?php
declare(strict_types=1);

/**
 * Kerangka halaman auth: panel kiri berisi form, panel kanan foto.
 * Judul 40px Inter seperti di Figma.
 */
function auth_buka(string $kelas = ''): void
{
    ?>
<div class="auth<?= $kelas !== '' ? ' ' . e($kelas) : '' ?>">
    <div class="auth__panel">
        <a class="brand auth__brand" href="<?= e(url('beranda')) ?>">SEWAYA</a>
        <div class="auth__content">
    <?php
}

function auth_tutup(): void
{
    ?>
        </div>
    </div>
    <div class="auth__media" role="img" aria-label="Lorong venue SEWAYA"></div>
</div>
    <?php
}

function auth_judul(string $eyebrow, string $judul, string $lead = '', bool $garis = true): string
{
    $html = '<p class="auth__eyebrow">' . e($eyebrow) . '</p>';
    $html .= '<h1 class="auth__title">' . e($judul) . '</h1>';

    if ($garis) {
        $html .= '<span class="auth__rule" aria-hidden="true"></span>';
    }
    if ($lead !== '') {
        $html .= '<p class="auth__lead">' . e($lead) . '</p>';
    }

    return $html;
}

function auth_alert(): string
{
    return '<div class="auth__flash">' . flash_render() . '</div>'
        . '<div class="alert alert--error" data-form-alert hidden></div>';
}
