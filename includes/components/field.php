<?php
declare(strict_types=1);

/**
 * Satu baris isian: label, input, hint. Semua teks di-escape di sini.
 */
function kolom_input(array $o): string
{
    $id          = (string) ($o['id'] ?? $o['name'] ?? '');
    $nama        = (string) ($o['name'] ?? $id);
    $tipe        = (string) ($o['tipe'] ?? 'text');
    $label       = (string) ($o['label'] ?? '');
    $nilai       = (string) ($o['nilai'] ?? '');
    $placeholder = (string) ($o['placeholder'] ?? '');
    $hint        = (string) ($o['hint'] ?? '');
    $autocomplete = (string) ($o['autocomplete'] ?? '');
    $wajib       = !empty($o['wajib']);
    $kelas       = 'field' . (!empty($o['kelas']) ? ' ' . $o['kelas'] : '');

    $atribut = ' id="' . e($id) . '" name="' . e($nama) . '" type="' . e($tipe) . '"';
    $atribut .= ' value="' . e($nilai) . '"';

    if ($placeholder !== '') {
        $atribut .= ' placeholder="' . e($placeholder) . '"';
    }
    if ($autocomplete !== '') {
        $atribut .= ' autocomplete="' . e($autocomplete) . '"';
    }
    if ($wajib) {
        $atribut .= ' required';
    }
    if (isset($o['min'])) {
        $atribut .= ' minlength="' . (int) $o['min'] . '"';
    }
    if (isset($o['maks'])) {
        $atribut .= ' maxlength="' . (int) $o['maks'] . '"';
    }
    if (isset($o['inputmode'])) {
        $atribut .= ' inputmode="' . e((string) $o['inputmode']) . '"';
    }

    $html = '<div class="' . e($kelas) . '">';
    if ($label !== '') {
        $html .= '<label class="field__label" for="' . e($id) . '">' . e($label) . '</label>';
    }
    $html .= '<input class="input"' . $atribut . '>';
    if ($hint !== '') {
        $html .= '<p class="field__hint">' . e($hint) . '</p>';
    }

    return $html . '</div>';
}
