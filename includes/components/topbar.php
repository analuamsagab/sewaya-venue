<?php
declare(strict_types=1);

function topbar(string $judul, int $notifikasi = 0): string
{
    $user = auth_user();
    $nama = nama_singkat((string) ($user['nama_lengkap'] ?? ''));

    ob_start();
    ?>
    <header class="topbar">
        <h1 class="topbar__title"><?= e($judul) ?></h1>

        <div class="topbar__search" data-tooltip="Belum tersedia">
            <?= ikon('search') ?>
            <input type="search" placeholder="Cari ruangan atau pemesanan" aria-label="Cari ruangan atau pemesanan" disabled>
        </div>

        <button class="topbar__icon-btn" type="button" aria-label="Notifikasi" disabled data-tooltip="Belum tersedia">
            <?= ikon('bell') ?>
            <?php if ($notifikasi > 0): ?>
                <span class="topbar__dot"></span>
            <?php endif; ?>
        </button>

        <div class="topbar__user">
            <?= ikon('user-round') ?>
            <span><?= e($nama) ?></span>
        </div>
    </header>
    <?php

    return (string) ob_get_clean();
}
