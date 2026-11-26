<?php
declare(strict_types=1);

auth_guest();

halaman_mulai('Masuk', 'page-auth', ['/assets/css/pages/auth.css']);
auth_buka('auth--kaps');
?>

<?= auth_judul('Masuk', 'Selamat Datang', 'Kelola pemesanan venue dan ruangan Anda') ?>

<form class="auth__form" method="post" action="<?= e(api_auth('login')) ?>" data-ajax novalidate>
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <?= auth_alert() ?>

    <?= kolom_input([
        'name'         => 'email',
        'label'        => 'Email',
        'tipe'         => 'email',
        'placeholder'  => 'nama@gmail.com',
        'autocomplete' => 'email',
        'wajib'        => true,
    ]) ?>

    <?= kolom_input([
        'name'         => 'password',
        'label'        => 'Kata Sandi',
        'tipe'         => 'password',
        'placeholder'  => 'Masukkan kata sandi',
        'autocomplete' => 'current-password',
        'wajib'        => true,
    ]) ?>

    <div class="auth__row auth__row--kanan">
        <a class="link" href="<?= e(url('lupa-sandi')) ?>">Lupa kata sandi?</a>
    </div>

    <label class="checkbox">
        <input type="checkbox" name="ingat" value="1">
        <span>Ingat saya</span>
    </label>

    <?= tombol('Masuk', [
        'tipe'  => 'submit',
        'blok'  => true,
        'kelas' => 'auth__submit auth__submit--kaps',
    ]) ?>
</form>

<p class="auth__footer">
    Belum punya akun? <a class="link" href="<?= e(url('register')) ?>">Daftar</a>
</p>

<?php
auth_tutup();
halaman_selesai();
