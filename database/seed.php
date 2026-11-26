<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Seeder hanya boleh dijalankan dari CLI.\n");
}

define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/includes/bootstrap.php';

$pdo = db();

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (['booking_status_log', 'booking', 'password_reset', 'users', 'room', 'role'] as $tabel) {
    $pdo->exec("TRUNCATE TABLE `{$tabel}`");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$hash = password_hash('password', PASSWORD_DEFAULT);

$roles = [
    [1, 'User', ['dashboard.view', 'booking.create', 'booking.cancel', 'report.create', 'rating.create']],
    [2, 'Admin', ['dashboard.view', 'booking.approve', 'booking.reject', 'room.manage', 'report.manage', 'rating.view', 'activity.view']],
    [3, 'Superadmin', ['*']],
];
$stmt = $pdo->prepare('INSERT INTO role (id_role, nama_role, permission) VALUES (?, ?, ?)');
foreach ($roles as [$id, $nama, $permission]) {
    $stmt->execute([$id, $nama, json_encode($permission, JSON_UNESCAPED_UNICODE)]);
}

$users = [
    [1, 1, 'Aditya Pratama', 'aditya@sewaya.test', '081234567890', 'aktif'],
    [2, 2, 'Admin Sewaya', 'admin@sewaya.test', '081234567891', 'aktif'],
    [3, 3, 'Superadmin Sewaya', 'superadmin@sewaya.test', '081234567892', 'aktif'],
];
$stmt = $pdo->prepare(
    'INSERT INTO users (id_user, id_role, nama_lengkap, email, no_telp, password, status)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
foreach ($users as [$id, $role, $nama, $email, $telp, $status]) {
    $stmt->execute([$id, $role, $nama, $email, $telp, $hash, $status]);
}

$rooms = [
    [1, 'Arunika', 'Ruang rapat hangat dengan pencahayaan alami, cocok untuk rapat strategi dan presentasi tim.', 12, 'Ruang rapat', '2', 350000, 'room-arunika.jpg', 'tersedia'],
    [2, 'Nawasena', 'Aula serbaguna dengan proyektor besar dan tata letak fleksibel untuk workshop serta seminar.', 30, 'Workshop', '1', 600000, 'room-nawasena.jpg', 'tersedia'],
    [3, 'Aksara', 'Ruang diskusi intim dengan meja bundar, ideal untuk sesi ide dan pertemuan kecil.', 8, 'Diskusi', '3', 250000, 'room-aksara.jpg', 'tersedia'],
];
$stmt = $pdo->prepare(
    'INSERT INTO room (id_room, nama, deskripsi, kapasitas, kategori, lantai, harga_per_jam, foto, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
foreach ($rooms as $room) {
    $stmt->execute($room);
}

/** Tanggal relatif supaya dashboard tetap relevan kapan pun demo dijalankan. */
$hari = static fn (int $offset): string => date('Y-m-d', strtotime(($offset >= 0 ? '+' : '') . $offset . ' day'));

$booking = [
    // kode, id_user, id_room, mulai, selesai, peserta, biaya, status, dibuat_detik_lalu
    ['SW-0261', 1, 1, $hari(1) . ' 09:00:00', $hari(1) . ' 12:00:00', 12, 1530000, 'Disetujui', 600],
    ['SW-0262', 1, 2, $hari(3) . ' 13:00:00', $hari(3) . ' 17:00:00', 30, 2400000, 'Menunggu', 3600],
    ['SW-0263', 1, 3, $hari(2) . ' 10:00:00', $hari(2) . ' 12:00:00', 8, 500000, 'Disetujui', 7200],
    ['SW-0264', 1, 1, $hari(5) . ' 08:00:00', $hari(5) . ' 17:00:00', 10, 3150000, 'Disetujui', 10800],
    ['SW-0265', 1, 2, $hari(7) . ' 09:00:00', $hari(7) . ' 15:00:00', 24, 3600000, 'Menunggu', 14400],
    ['SW-0266', 1, 3, $hari(8) . ' 13:00:00', $hari(8) . ' 16:00:00', 6, 750000, 'Disetujui', 18000],
    ['SW-0267', 1, 1, $hari(10) . ' 09:00:00', $hari(10) . ' 12:00:00', 9, 1050000, 'Disetujui', 21600],
    ['SW-0268', 1, 2, $hari(13) . ' 08:00:00', $hari(13) . ' 12:00:00', 20, 2400000, 'Ditolak', 25200],
    ['SW-0269', 1, 3, $hari(15) . ' 10:00:00', $hari(15) . ' 12:00:00', 5, 500000, 'Dibatalkan', 28800],
    ['SW-0270', 1, 1, $hari(17) . ' 13:00:00', $hari(17) . ' 17:00:00', 11, 1400000, 'Disetujui', 32400],
    ['SW-0271', 1, 2, $hari(20) . ' 09:00:00', $hari(20) . ' 16:00:00', 28, 4200000, 'Menunggu', 36000],
    ['SW-0272', 1, 3, $hari(23) . ' 08:00:00', $hari(23) . ' 11:00:00', 7, 750000, 'Disetujui', 39600],
];

$stmt = $pdo->prepare(
    'INSERT INTO booking
        (kode, id_user, id_room, jenis_durasi, mulai, selesai, jumlah_peserta,
         total_biaya, status, alasan_penolakan, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
foreach ($booking as [$kode, $user, $room, $mulai, $selesai, $peserta, $biaya, $status, $detik]) {
    $dibuat = date('Y-m-d H:i:s', time() - $detik);
    $alasan = $status === 'Ditolak' ? 'Jadwal bertabrakan dengan pemesanan lain yang sudah disetujui.' : null;
    $stmt->execute([
        $kode, $user, $room, 'per_jam', $mulai, $selesai, $peserta,
        $biaya, $status, $alasan, $dibuat, $dibuat,
    ]);
}

$stmt = $pdo->prepare('SELECT id_booking, kode, status FROM booking ORDER BY id_booking');
$stmt->execute();
$id = [];
foreach ($stmt->fetchAll() as $row) {
    $id[$row['kode']] = ['id' => (int) $row['id_booking'], 'status' => $row['status']];
}

$logs = [
    [$id['SW-0261']['id'], 'Menunggu', 'Disetujui', 'Pemesanan disetujui admin.', 0, 600],
    [$id['SW-0262']['id'], null, 'Menunggu', 'Pemesanan sedang ditinjau.', 0, 3600],
    [$id['SW-0263']['id'], 'Menunggu', 'Disetujui', 'Pemesanan disetujui admin.', 0, 7200],
    [$id['SW-0268']['id'], 'Menunggu', 'Ditolak', 'Jadwal bertabrakan dengan pemesanan lain.', 1, 25200],
    [$id['SW-0269']['id'], 'Menunggu', 'Dibatalkan', 'Dibatalkan oleh pengguna.', 1, 28800],
];

$stmt = $pdo->prepare(
    'INSERT INTO booking_status_log (id_booking, status_lama, status_baru, catatan, dibaca, created_at)
     VALUES (?, ?, ?, ?, ?, ?)'
);
foreach ($logs as [$booking, $lama, $baru, $catatan, $dibaca, $detik]) {
    $stmt->execute([$booking, $lama, $baru, $catatan, $dibaca, date('Y-m-d H:i:s', time() - $detik)]);
}

$jumlah = static fn (string $tabel): int => (int) $pdo->query("SELECT COUNT(*) FROM `{$tabel}`")->fetchColumn();

printf(
    "Seed selesai: %d role, %d user, %d ruangan, %d booking, %d log.\n",
    $jumlah('role'),
    $jumlah('users'),
    $jumlah('room'),
    $jumlah('booking'),
    $jumlah('booking_status_log')
);
