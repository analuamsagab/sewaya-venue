<?php
declare(strict_types=1);

/**
 * Ikon dari sprite lokal. Sumber path: Lucide (MIT), disalin ke
 * assets/icons/sprite.svg supaya demo tidak butuh internet.
 */
function ikon(string $nama, string $kelas = 'icon'): string
{
    return '<svg class="' . e($kelas) . '" aria-hidden="true" focusable="false">'
        . '<use href="/assets/icons/sprite.svg#icon-' . e($nama) . '"></use>'
        . '</svg>';
}
