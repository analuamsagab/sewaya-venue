<?php
declare(strict_types=1);

auth_guest();

halaman_mulai('Daftar', 'page-auth', ['/assets/css/pages/auth.css']);
auth_buka('auth--rapat');
?>

<h1 class="auth__title">Selamat Datang</h1>
<p class="auth__subjudul">Pendaftaran</p>

<form class="auth__form auth__form--rapat" method="post" action="<?= e(api_auth('register')) ?>" data-ajax novalidate>
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <?= auth_alert() ?>

    <?= kolom_input([
        'name'         => 'nama_lengkap',
        'label'        => 'Nama Lengkap',
        'placeholder'  => 'Nama sesuai identitas',
        'autocomplete' => 'name',
        'maks'         => 100,
        'wajib'        => true,
    ]) ?>

    <?= kolom_input([
        'name'         => 'email',
        'label'        => 'Alamat Email',
        'tipe'         => 'email',
        'placeholder'  => 'nama@gmail.com',
        'autocomplete' => 'email',
        'maks'         => 100,
        'wajib'        => true,
    ]) ?>

    <?= kolom_input([
        'name'        => 'no_telp',
        'label'       => 'Nomor Telepon',
        'tipe'        => 'tel',
        'placeholder' => '08xxxxxxxxxx',
        'inputmode'   => 'numeric',
        'maks'        => 14,
        'wajib'       => true,
    ]) ?>

    <?= kolom_input([
        'name'         => 'password',
        'label'        => 'Kata Sandi',
        'tipe'         => 'password',
        'placeholder'  => 'Minimal 8 karakter',
        'autocomplete' => 'new-password',
        'min'          => 8,
        'wajib'        => true,
    ]) ?>
    <?= meter_sandi('password') ?>

    <?= kolom_input([
        'name'         => 'ulangi_password',
        'label'        => 'Ulangi Kata Sandi',
        'tipe'         => 'password',
        'placeholder'  => 'Ketik ulang kata sandi',
        'autocomplete' => 'new-password',
        'min'          => 8,
        'wajib'        => true,
    ]) ?>

    <?= tombol('Daftar Sekarang', [
        'tipe'  => 'submit',
        'kelas' => 'auth__submit auth__submit--tengah',
    ]) ?>
</form>

<p class="auth__footer auth__footer--tengah">
    Sudah punya akun? <a class="link" href="<?= e(url('login')) ?>">Masuk</a>
</p>

<?php
auth_tutup();
halaman_selesai();
