<?php
declare(strict_types=1);

halaman_mulai('Beranda', 'page-landing', ['/assets/css/pages/landing.css']);
echo nav_public('beranda');

$layanan = [
    [
        'nomor' => '01',
        'judul' => 'Pendaftaran Anggota',
        'desk'  => 'Buat akun dan mulai rencanakan momen istimewa Anda.',
        'href'  => url('register'),
        'isi'   => false,
    ],
    [
        'nomor' => '02',
        'judul' => 'Pemesanan Venue',
        'desk'  => 'Pilih ruang, tentukan tanggal, dan ajukan pemesanan.',
        'href'  => null,
        'isi'   => true,
    ],
    [
        'nomor' => '03',
        'judul' => 'Katalog Venue',
        'desk'  => 'Jelajahi tempat yang sesuai dengan kebutuhan acara.',
        'href'  => null,
        'isi'   => false,
    ],
    [
        'nomor' => '04',
        'judul' => 'Riwayat Pemesanan',
        'desk'  => 'Lihat kembali dan kelola semua pemesanan Anda.',
        'href'  => null,
        'isi'   => true,
    ],
    [
        'nomor' => '05',
        'judul' => 'Profil Pengguna',
        'desk'  => 'Kelola informasi pribadi dan akun keanggotaan.',
        'href'  => null,
        'isi'   => false,
    ],
];

$kartuLayanan = static function (array $kartu): string {
    $kelas = 'service-card' . ($kartu['isi'] ? ' service-card--filled' : '');
    $isi = '<span class="service-card__top">'
        . '<span class="service-card__num">' . e($kartu['nomor']) . '</span>'
        . ikon('arrow-up-right', 'service-card__arrow')
        . '</span>'
        . '<span class="service-card__body">'
        . '<span class="service-card__title">' . e($kartu['judul']) . '</span>'
        . '<span class="service-card__desc">' . e($kartu['desk']) . '</span>'
        . '</span>';

    if ($kartu['href'] !== null) {
        return '<a class="' . $kelas . '" href="' . e((string) $kartu['href']) . '">' . $isi . '</a>';
    }

    return '<div class="' . $kelas . ' is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">'
        . $isi . '</div>';
};
?>

<main>
    <section class="hero">
        <div class="hero__bg" aria-hidden="true"></div>

        <div class="hero__inner">
            <p class="hero__eyebrow">Ruang istimewa, momen bermakna</p>
            <h1 class="hero__title">Temukan ruang<br>untuk cerita Anda.</h1>
            <span class="hero__rule" aria-hidden="true"></span>
            <p class="hero__lead">
                Selamat datang di SEWAYA. Dari rapat yang menginspirasi hingga
                perayaan berkesan, temukan venue pilihan Anda.
            </p>
        </div>
    </section>

    <div class="hero__search">
        <div class="searchbar">
            <div class="searchbar__field">
                <label class="searchbar__label" for="cari-venue">Nama venue atau lokasi</label>
                <input class="searchbar__control" id="cari-venue" type="text"
                       placeholder="Cari venue atau kota" disabled>
            </div>

            <div class="searchbar__field">
                <label class="searchbar__label" for="cari-jenis">Jenis acara</label>
                <div class="searchbar__select">
                    <select class="searchbar__control" id="cari-jenis" disabled>
                        <option>Semua acara</option>
                        <option>Rapat &amp; konferensi</option>
                        <option>Pernikahan</option>
                        <option>Acara privat</option>
                    </select>
                    <?= ikon('chevron-down') ?>
                </div>
            </div>

            <div class="searchbar__field">
                <label class="searchbar__label" for="cari-tanggal">Tanggal acara</label>
                <input class="searchbar__control" id="cari-tanggal" type="text"
                       placeholder="Pilih tanggal" disabled>
            </div>

            <a class="searchbar__button" href="<?= e(url('dashboard')) ?>">
                Pesan Ruangan
            </a>
        </div>
    </div>

    <div class="wrap">
        <div class="kategori">
            <span>Rapat &amp; konferensi</span>
            <span class="kategori__pemisah">/</span>
            <span>Pernikahan</span>
            <span class="kategori__pemisah">/</span>
            <span>Acara privat</span>
        </div>

        <h2 class="section-title">Semua kebutuhan, satu tempat.</h2>
    </div>

    <section class="services">
        <div class="wrap">
            <div class="services__head">
                <p class="services__label">Layanan anggota SEWAYA</p>
            </div>

            <div class="services__grid">
                <?php foreach ($layanan as $kartu): ?>
                    <?= $kartuLayanan($kartu) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="site-footer__inner">
        <div>
            <p class="brand">SEWAYA</p>
            <p class="site-footer__tagline">Penyewa Venue Terpercaya</p>
            <p class="site-footer__contact">Hubungi Kami : 081234567890</p>
        </div>

        <div class="testimonial">
            <div class="testimonial__author">
                <span class="testimonial__avatar"><?= ikon('user-round', 'icon icon--sm') ?></span>
                <span>Mischa Andanu &middot; Tiktok</span>
            </div>
            <p class="testimonial__quote">&ldquo;Pelayanan sangat ramah&rdquo;</p>
            <div class="testimonial__rating">
                <span>4,5</span>
                <span class="testimonial__stars" aria-label="4,5 dari 5">
                    &#9733;&#9733;&#9733;&#9733;<span class="testimonial__half">&#9733;</span>
                </span>
            </div>
        </div>
    </div>

    <p class="site-footer__bottom">&copy; 2026 SEWAYA. Seluruh hak cipta dilindungi.</p>
</footer>

<?php
halaman_selesai();
