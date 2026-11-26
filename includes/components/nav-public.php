<?php
declare(strict_types=1);

function nav_public(string $aktif = 'beranda'): string
{
    $masuk = auth_check();

    $tautan = [
        ['beranda', 'Beranda',  url('beranda'), false],
        ['katalog', 'Katalog',  null,           true],
        ['riwayat', 'Riwayat',  null,           true],
        ['profil',  'Profil',   null,           true],
    ];

    $html = '<header class="site-header"><nav class="site-nav" aria-label="Navigasi utama">';
    $html .= '<a class="brand" href="' . e(url('beranda')) . '">SEWAYA</a>';
    $html .= '<div class="site-nav__links">';

    foreach ($tautan as [$kunci, $label, $href, $nonaktif]) {
        $kelas = 'site-nav__link' . ($aktif === $kunci ? ' is-active' : '');

        if ($nonaktif) {
            $html .= '<span class="' . $kelas . ' is-disabled" aria-disabled="true" '
                . 'data-tooltip="Belum tersedia">' . e($label) . '</span>';
            continue;
        }

        $html .= '<a class="' . $kelas . '" href="' . e((string) $href) . '"'
            . ($aktif === $kunci ? ' aria-current="page"' : '') . '>' . e($label) . '</a>';
    }

    $html .= '</div>';

    if ($masuk) {
        $html .= '<a class="btn btn--primary site-nav__cta" href="' . e(url('dashboard')) . '">Dashboard</a>';
    } else {
        $html .= '<a class="btn btn--primary site-nav__cta" href="' . e(url('register')) . '">Daftar</a>';
    }

    return $html . '</nav></header>';
}
