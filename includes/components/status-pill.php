<?php
declare(strict_types=1);

function pil_status(string $status): string
{
    $peta = [
        'Disetujui'  => 'approved',
        'Menunggu'   => 'pending',
        'Ditolak'    => 'rejected',
        'Dibatalkan' => 'cancelled',
    ];

    $variasi = $peta[$status] ?? 'cancelled';

    return '<span class="pill pill--' . $variasi . '">' . e($status) . '</span>';
}
