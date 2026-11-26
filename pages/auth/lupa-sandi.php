<?php
declare(strict_types=1);

auth_guest();

halaman_mulai('Lupa Kata Sandi', 'page-auth', ['/assets/css/pages/auth.css']);
auth_buka('auth--kaps');
?>

<?= auth_judul('Lupa Kata Sandi', 'Selamat Datang', 'Masukkan email terdaftar. Kami akan membuat tautan untuk mengatur ulang kata sandi Anda.') ?>

<form class="auth__form" method="post" action="<?= e(api_auth('lupa-sandi')) ?>" data-ajax novalidate>
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

    <?= tombol('Kirim Tautan Reset', [
        'tipe'  => 'submit',
        'kelas' => 'auth__submit auth__submit--tengah',
    ]) ?>
</form>

<p class="auth__footer auth__footer--tengah">
    <a class="link" href="<?= e(url('login')) ?>">Kembali ke halaman masuk</a>
</p>

<?php
auth_tutup();
halaman_selesai();
