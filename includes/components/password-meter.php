<?php
declare(strict_types=1);

/**
 * Indikator kekuatan kata sandi. Nilainya diisi oleh assets/js/app.js
 * lewat atribut data-level (0 = kosong, 1..4 = terisi).
 */
function meter_sandi(string $untuk = 'password'): string
{
    return '<div class="meter" data-meter data-untuk="' . e($untuk) . '" data-level="0">'
        . '<span class="meter__label">Kekuatan kata sandi:</span>'
        . '<span class="meter__bars" aria-hidden="true"><i></i><i></i><i></i><i></i></span>'
        . '<span class="meter__text" data-meter-text>Belum diisi</span>'
        . '</div>';
}
