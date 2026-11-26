<?php
declare(strict_types=1);

function sidebar(string $aktif, int $notifikasi = 0): string
{
    $user = auth_user();
    $nama = (string) ($user['nama_lengkap'] ?? 'Pengguna');

    $menu = [
        ['dashboard',  'Dashboard',        'layout-grid',   url('dashboard'), false],
        ['katalog',    'Jelajahi ruangan', 'door-open',    null,             true],
        ['pesanan',    'Pemesanan saya',   'calendar-days', null,             true],
        ['notifikasi', 'Notifikasi',       'bell',         null,             true],
    ];

    ob_start();
    ?>
    <aside class="sidebar">
        <a class="brand sidebar__brand" href="<?= e(url('beranda')) ?>">SEWAYA</a>
        <p class="sidebar__tagline">Penyewa Venue Terpercaya</p>
        <p class="sidebar__label">Workspace</p>

        <nav class="sidebar__menu" aria-label="Menu aplikasi">
            <?php foreach ($menu as [$kunci, $label, $namaIkon, $href, $nonaktif]): ?>
                <?php if ($nonaktif): ?>
                    <span class="nav-item is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">
                        <?= ikon($namaIkon, 'icon nav-item__icon') ?>
                        <span><?= e($label) ?></span>
                        <?php if ($kunci === 'notifikasi' && $notifikasi > 0): ?>
                            <span class="nav-item__badge"><?= $notifikasi ?></span>
                        <?php endif; ?>
                    </span>
                <?php else: ?>
                    <a class="nav-item<?= $aktif === $kunci ? ' is-active' : '' ?>"
                       href="<?= e((string) $href) ?>"
                       <?= $aktif === $kunci ? 'aria-current="page"' : '' ?>>
                        <?= ikon($namaIkon, 'icon nav-item__icon') ?>
                        <span><?= e($label) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar__spacer"></div>

        <div class="sidebar__user">
            <?= ikon('user-round', 'icon icon--sm') ?>
            <div>
                <p class="sidebar__user-name"><?= e($nama) ?></p>
                <p class="sidebar__user-role">Akun pengguna</p>
            </div>
        </div>

        <form method="post" action="<?= e(api_auth('logout')) ?>" data-ajax>
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <button class="sidebar__logout" type="submit">
                <?= ikon('log-out', 'icon icon--sm') ?> Keluar
            </button>
        </form>
    </aside>
    <?php

    return (string) ob_get_clean();
}
