<?php
declare(strict_types=1);

halaman_mulai('Halaman tidak ditemukan', 'page-auth', ['/assets/css/pages/auth.css']);
?>
<main class="auth">
    <div class="auth__panel">
        <a class="brand auth__brand" href="<?= e(url('beranda')) ?>">SEWAYA</a>
        <div class="auth__content">
            <p class="auth__eyebrow">Error 404</p>
            <h1 class="auth__title">Halaman tidak ditemukan</h1>
            <span class="auth__rule" aria-hidden="true"></span>
            <p class="auth__lead">Alamat yang Anda buka tidak tersedia di SEWAYA.</p>
            <p class="auth__footer">
                <a class="link" href="<?= e(url('beranda')) ?>">Kembali ke beranda</a>
            </p>
        </div>
    </div>
    <div class="auth__media" role="img" aria-label="Lorong venue SEWAYA"></div>
</main>
<?php
halaman_selesai();
