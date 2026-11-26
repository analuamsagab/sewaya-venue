<?php
declare(strict_types=1);

/**
 * Tombol seragam. Kalau $o['nonaktif'] diisi, hasilnya <span> yang tidak bisa
 * diklik dan menampilkan tooltip "Belum tersedia" — dipakai untuk fitur
 * yang belum punya layar di Figma.
 */
function tombol(string $teks, array $o = []): string
{
    $variasi = $o['variasi'] ?? 'primary';
    $kelas    = 'btn btn--' . $variasi;

    if (!empty($o['blok'])) {
        $kelas .= ' btn--block';
    }
    if (!empty($o['kecil'])) {
        $kelas .= ' btn--sm';
    }
    if (!empty($o['kelas'])) {
        $kelas .= ' ' . $o['kelas'];
    }

    $isi = (isset($o['ikon']) ? ikon($o['ikon']) : '')
        . '<span>' . e($teks) . '</span>'
        . (isset($o['ikon_kanan']) ? ikon($o['ikon_kanan'], 'icon icon--sm') : '');

    if (!empty($o['nonaktif'])) {
        $kelas .= ' is-disabled';

        return '<span class="' . e($kelas) . '" aria-disabled="true" data-tooltip="'
            . e($o['tooltip'] ?? 'Belum tersedia') . '">' . $isi . '</span>';
    }

    if (isset($o['href'])) {
        return '<a class="' . e($kelas) . '" href="' . e($o['href']) . '">' . $isi . '</a>';
    }

    $tipe = $o['tipe'] ?? 'button';
    $atribut = $o['atribut'] ?? '';

    return '<button class="' . e($kelas) . '" type="' . e($tipe) . '"'
        . ($atribut !== '' ? ' ' . $atribut : '') . '>' . $isi . '</button>';
}
