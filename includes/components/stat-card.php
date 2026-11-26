<?php
declare(strict_types=1);

function kartu_statistik(string $label, string $nilai, string $sub, string $variasi = ''): string
{
    $kelas = 'stat' . ($variasi !== '' ? ' stat--' . $variasi : '');

    return '<article class="' . e($kelas) . '">'
        . '<p class="stat__label">' . e($label) . '</p>'
        . '<p class="stat__value">' . e($nilai) . '</p>'
        . '<p class="stat__sub">' . e($sub) . '</p>'
        . '</article>';
}
