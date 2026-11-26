<?php
declare(strict_types=1);

auth_guest();

$token = (string) ($_GET['token'] ?? '');
$reset = null;

if ($token !== '' && preg_match('/^[a-f0-9]{64}$/', $token) === 1) {
    $stmt = db()->prepare(
        'SELECT id_reset, email FROM password_reset
         WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()
         ORDER BY id_reset DESC LIMIT 1'
    );
    $stmt->execute([hash('sha256', $token)]);
    $reset = $stmt->fetch() ?: null;
}

halaman_mulai('Kata Sandi Baru', 'page-auth', ['/assets/css/pages/auth.css']);
auth_buka();
?>

<?php if ($reset === null): ?>
    <?= auth_judul('Kata Sandi Baru', 'Tautan Tidak Berlaku', 'Tautan reset sudah kedaluwarsa atau pernah dipakai. Minta tautan baru untuk melanjutkan.') ?>
    <div class="alert alert--error">Tautan reset tidak valid.</div>
    <p class="auth__footer auth__footer--tengah">
        <a class="link" href="<?= e(url('lupa-sandi')) ?>">Minta tautan baru</a>
    </p>
<?php else: ?>
    <?= auth_judul('Kata Sandi Baru', 'Selamat Datang', 'Buat kata sandi baru untuk akun ' . (string) $reset['email'] . '.') ?>

    <form class="auth__form auth__form--rapat" method="post" action="<?= e(api_auth('sandi-baru')) ?>" data-ajax novalidate>
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <?= auth_alert() ?>

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

        <?= tombol('Buat Kata Sandi', [
            'tipe'  => 'submit',
            'kelas' => 'auth__submit auth__submit--tengah',
        ]) ?>
    </form>
<?php endif; ?>

<?php
auth_tutup();
halaman_selesai();
