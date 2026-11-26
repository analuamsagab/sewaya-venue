<?php
declare(strict_types=1);

auth_require();

$pdo = db();
$pengguna = auth_user();
$idUser = (int) $pengguna['id_user'];

/* ---------- Angka kartu statistik ---------- */
$stmt = $pdo->prepare('SELECT status, COUNT(*) AS jumlah FROM booking WHERE id_user = ? GROUP BY status');
$stmt->execute([$idUser]);

$jumlah = ['Menunggu' => 0, 'Disetujui' => 0, 'Ditolak' => 0, 'Dibatalkan' => 0];
$total = 0;
foreach ($stmt->fetchAll() as $baris) {
    $status = (string) $baris['status'];
    $nilai = (int) $baris['jumlah'];
    if (array_key_exists($status, $jumlah)) {
        $jumlah[$status] = $nilai;
    }
    $total += $nilai;
}

/* ---------- Notifikasi belum dibaca ---------- */
$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM booking_status_log l
     JOIN booking b ON b.id_booking = l.id_booking
     WHERE b.id_user = ? AND l.dibaca = 0'
);
$stmt->execute([$idUser]);
$notifikasi = (int) $stmt->fetchColumn();

/* ---------- Pemesanan terdekat yang disetujui ---------- */
$stmt = $pdo->prepare(
    "SELECT b.*, r.nama AS nama_room, r.lantai, r.kategori
     FROM booking b
     JOIN room r ON r.id_room = b.id_room
     WHERE b.id_user = ? AND b.status = 'Disetujui' AND b.mulai >= NOW()
     ORDER BY b.mulai ASC LIMIT 1"
);
$stmt->execute([$idUser]);
$terdekat = $stmt->fetch();

if (!$terdekat) {
    $stmt = $pdo->prepare(
        "SELECT b.*, r.nama AS nama_room, r.lantai, r.kategori
         FROM booking b
         JOIN room r ON r.id_room = b.id_room
         WHERE b.id_user = ? AND b.status = 'Disetujui'
         ORDER BY b.mulai DESC LIMIT 1"
    );
    $stmt->execute([$idUser]);
    $terdekat = $stmt->fetch();
}

/* ---------- Dua pemesanan terbaru ---------- */
$stmt = $pdo->prepare(
    'SELECT b.kode, b.mulai, b.selesai, b.total_biaya, b.status, r.nama AS nama_room
     FROM booking b
     JOIN room r ON r.id_room = b.id_room
     WHERE b.id_user = ?
     ORDER BY b.created_at DESC, b.id_booking DESC
     LIMIT 2'
);
$stmt->execute([$idUser]);
$terbaru = $stmt->fetchAll();

/* ---------- Dua pembaruan status terbaru ---------- */
$stmt = $pdo->prepare(
    'SELECT l.status_baru, l.catatan, l.created_at, b.kode, r.nama AS nama_room
     FROM booking_status_log l
     JOIN booking b ON b.id_booking = l.id_booking
     JOIN room r ON r.id_room = b.id_room
     WHERE b.id_user = ?
     ORDER BY l.created_at DESC, l.id_log DESC
     LIMIT 2'
);
$stmt->execute([$idUser]);
$pembaruan = $stmt->fetchAll();

/* ---------- Ruangan tersedia ---------- */
$ruangan = $pdo->query(
    "SELECT id_room, nama, kategori, kapasitas, harga_per_jam, foto
     FROM room WHERE status = 'tersedia'
     ORDER BY id_room ASC LIMIT 3"
)->fetchAll();

$judulPembaruan = static function (string $status): string {
    return match ($status) {
        'Disetujui'  => 'Pemesanan Anda disetujui',
        'Menunggu'   => 'Pemesanan sedang ditinjau',
        'Ditolak'    => 'Pemesanan Anda ditolak',
        'Dibatalkan' => 'Pemesanan dibatalkan',
        default      => 'Status pemesanan diperbarui',
    };
};

$taglineRuangan = [
    'Arunika'  => 'Rapat strategi, ruang lebih inspiratif.',
    'Nawasena' => 'Workshop besar, ide yang lebih luas.',
    'Aksara'   => 'Diskusi intim, gagasan yang tumbuh.',
];

$fotoRuangan = static function (?string $berkas): string {
    $nama = is_string($berkas) && $berkas !== '' ? $berkas : 'room-arunika.jpg';

    return '/assets/img/' . rawurlencode($nama);
};

halaman_mulai('Dashboard', 'page-dashboard', ['/assets/css/pages/dashboard.css']);
?>

<div class="app">
    <?= sidebar('dashboard', $notifikasi) ?>

    <div class="app__main">
        <?= topbar('Dashboard', $notifikasi) ?>

        <main class="content">
            <div class="content__head">
                <div>
                    <p class="content__date"><?= e(tanggal_indonesia(date('Y-m-d'))) ?></p>
                    <h1 class="content__greeting">
                        <?= e(salam() . ', ' . nama_depan((string) $pengguna['nama_lengkap'])) ?>
                    </h1>
                    <p class="content__lead">Ruang yang tepat untuk ide besar Anda berikutnya.</p>
                </div>

                <?= tombol('Buat pemesanan', [
                    'ikon'     => 'plus',
                    'nonaktif' => true,
                ]) ?>
            </div>

            <div class="stats">
                <?= kartu_statistik('Total pemesanan', (string) $total, 'Semua pemesanan Anda') ?>
                <?= kartu_statistik('Menunggu', str_pad((string) $jumlah['Menunggu'], 2, '0', STR_PAD_LEFT), 'Menunggu persetujuan', 'accent') ?>
                <?= kartu_statistik('Disetujui', str_pad((string) $jumlah['Disetujui'], 2, '0', STR_PAD_LEFT), 'Siap untuk digunakan') ?>
                <?= kartu_statistik('Ditolak', str_pad((string) $jumlah['Ditolak'], 2, '0', STR_PAD_LEFT), 'Lihat alasan penolakan') ?>
                <?= kartu_statistik('Dibatalkan', str_pad((string) $jumlah['Dibatalkan'], 2, '0', STR_PAD_LEFT), 'Dibatalkan oleh Anda') ?>
            </div>

            <div class="grid-feature">
                <?php if ($terdekat): ?>
                    <article class="feature">
                        <p class="feature__eyebrow">Pemesanan terdekat</p>
                        <h2 class="feature__title">
                            <?= e($taglineRuangan[(string) $terdekat['nama_room']] ?? 'Ruang siap dipakai, agenda lebih tenang.') ?>
                        </h2>
                        <p class="feature__meta">
                            Ruang <?= e((string) $terdekat['nama_room']) ?>
                            <span class="dot">&bull;</span><?= (int) $terdekat['jumlah_peserta'] ?> peserta
                            <span class="dot">&bull;</span>Lantai <?= e((string) $terdekat['lantai']) ?>
                        </p>

                        <div class="feature__bottom">
                            <span class="feature__fact">
                                <?= ikon('calendar-days', 'icon icon--sm') ?>
                                <?= e(tanggal_singkat((string) $terdekat['mulai'])) ?>
                            </span>
                            <span class="feature__fact">
                                <?= ikon('clock', 'icon icon--sm') ?>
                                <?= e(jam_rentang((string) $terdekat['mulai'], (string) $terdekat['selesai'])) ?>
                            </span>
                            <span class="feature__cta is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">
                                Lihat detail <?= ikon('arrow-up-right', 'icon icon--sm') ?>
                            </span>
                        </div>
                    </article>
                <?php else: ?>
                    <article class="feature feature--kosong">
                        <p class="feature__eyebrow">Pemesanan terdekat</p>
                        <h2 class="feature__title">Belum ada pemesanan yang disetujui.</h2>
                        <p class="feature__meta">Ajukan pemesanan ruangan untuk melihat jadwal Anda di sini.</p>
                        <div class="feature__bottom">
                            <span class="feature__cta is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">
                                Buat pemesanan <?= ikon('arrow-up-right', 'icon icon--sm') ?>
                            </span>
                        </div>
                    </article>
                <?php endif; ?>

                <section class="updates">
                    <h2 class="updates__title">Pembaruan terbaru</h2>
                    <ul class="updates__list">
                        <?php if ($pembaruan === []): ?>
                            <li class="update">
                                <span class="update__icon"><?= ikon('info', 'icon icon--sm') ?></span>
                                <div>
                                    <p class="update__title">Belum ada pembaruan</p>
                                    <p class="update__meta">Perubahan status pemesanan akan muncul di sini.</p>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php foreach ($pembaruan as $log): ?>
                            <?php $disetujui = $log['status_baru'] === 'Disetujui'; ?>
                            <li class="update">
                                <span class="update__icon<?= $disetujui ? '' : ' update__icon--tunggu' ?>">
                                    <?= ikon($disetujui ? 'check' : 'clock', 'icon icon--sm') ?>
                                </span>
                                <div>
                                    <p class="update__title"><?= e($judulPembaruan((string) $log['status_baru'])) ?></p>
                                    <p class="update__meta">
                                        <?= e((string) $log['nama_room']) ?> &middot; <?= e((string) $log['kode']) ?>
                                        &middot; <?= e(waktu_relatif((string) $log['created_at'])) ?>
                                    </p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>

            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">Pemesanan terbaru</h2>
                    <span class="panel__link is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">
                        Lihat semua <?= ikon('arrow-up-right', 'icon icon--sm') ?>
                    </span>
                </div>

                <div class="table-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Ruangan / ID</th>
                                <th scope="col">Tanggal &amp; waktu</th>
                                <th scope="col">Total biaya</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($terbaru === []): ?>
                                <tr>
                                    <td colspan="5" class="muted">Belum ada pemesanan.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($terbaru as $pesanan): ?>
                                <tr>
                                    <td>
                                        <div class="table__room">
                                            <span class="table__room-icon"><?= ikon('door-open', 'icon icon--sm') ?></span>
                                            <span>
                                                <span class="table__room-name">Ruang <?= e((string) $pesanan['nama_room']) ?></span><br>
                                                <span class="table__room-code">#<?= e((string) $pesanan['kode']) ?></span>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?= e(tanggal_singkat((string) $pesanan['mulai'])) ?>
                                        &middot; <?= e(date('H.i', strtotime((string) $pesanan['mulai']))) ?>&ndash;<?= e(date('H.i', strtotime((string) $pesanan['selesai']))) ?>
                                    </td>
                                    <td><?= e(rupiah((string) $pesanan['total_biaya'])) ?></td>
                                    <td><?= pil_status((string) $pesanan['status']) ?></td>
                                    <td class="table__action">
                                        <span class="panel__link is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">
                                            Lihat detail <?= ikon('arrow-up-right', 'icon icon--sm') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rooms-section">
                <div class="rooms-section__head">
                    <div>
                        <h2 class="rooms-section__title">Pilihan ruang untuk Anda</h2>
                        <p class="rooms-section__lead">Temukan suasana baru untuk kolaborasi berikutnya.</p>
                    </div>
                    <span class="panel__link is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">
                        Jelajahi semua ruangan <?= ikon('arrow-up-right', 'icon icon--sm') ?>
                    </span>
                </div>

                <div class="rooms-grid">
                    <?php foreach ($ruangan as $kamar): ?>
                        <article class="room-card">
                            <img class="room-card__photo"
                                 src="<?= e($fotoRuangan($kamar['foto'] ?? null)) ?>"
                                 alt="Ruangan <?= e((string) $kamar['nama']) ?>"
                                 width="154" height="210" loading="lazy">

                            <div class="room-card__body">
                                <h3 class="room-card__name"><?= e((string) $kamar['nama']) ?></h3>
                                <p class="room-card__meta">
                                    <?= (int) $kamar['kapasitas'] ?> orang
                                    <span class="dot">&bull;</span><?= e((string) $kamar['kategori']) ?>
                                </p>

                                <div class="room-card__price">
                                    <span class="room-card__price-label">Mulai dari</span>
                                    <strong class="room-card__value"><?= e(rupiah((string) $kamar['harga_per_jam'])) ?></strong>
                                    <span class="room-card__unit is-disabled" aria-disabled="true" data-tooltip="Belum tersedia">
                                        / jam &middot; Lihat ruang <?= ikon('arrow-up-right', 'icon') ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</div>

<?php
halaman_selesai();
