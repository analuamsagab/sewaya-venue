# Analisis Awal: Sistem Informasi Reservasi dan Pemesanan Ruangan

## 1. Latar Belakang / Masalah

- Proses reservasi ruangan masih kurang terorganisir dan rentan terjadi kesalahan.
- Sering terjadi double booking (satu ruangan dipesan lebih dari satu pihak di waktu yang sama).
- Sulit untuk mengecek ketersediaan ruangan secara cepat dan akurat.
- Proses persetujuan (approval) masih manual dan kurang transparan.
- Pengelolaan data ruangan, fasilitas, add-ons, dan laporan belum terpusat.
- Evaluasi terhadap pelayanan, kondisi ruangan, serta kepuasan pengguna belum terkelola dengan baik.

## 2. Dampak Masalah

- Konflik antar pemesan karena jadwal bentrok.
- Ruangan tidak termanfaatkan secara optimal.
- Proses administrasi menjadi lambat dan rawan kesalahan.
- Sulit melakukan evaluasi pelayanan dan kondisi ruangan.
- Potensi kerugian karena tidak adanya pencatatan denda/ganti rugi dan laporan kerusakan.

## 3. Tujuan Sistem

- Menyediakan sistem reservasi ruangan yang terintegrasi dan mudah digunakan.
- Mencegah terjadinya double booking secara otomatis.
- Mempermudah pengecekan ketersediaan ruangan berdasarkan tanggal dan durasi.
- Menyediakan proses approval yang jelas dan tercatat.
- Mengelola fasilitas tambahan, konsumsi, dan biaya secara otomatis.
- Mencatat laporan kerusakan/pelanggaran serta rating pengguna untuk evaluasi.
- Menyediakan pengelolaan akun dan hak akses berdasarkan role.

## 4. Aktor Sistem

- **Pengguna (User):** Melakukan pemesanan, melihat status pemesanan, memilih fasilitas tambahan, memberikan rating dan ulasan, serta membuat laporan.
- **Admin:** Mengelola data ruangan dan fasilitas, menyetujui atau menolak pemesanan, menangani laporan, menetapkan denda/ganti rugi, serta melihat hasil evaluasi.
- **IT/Superadmin:** Mengelola akun dan hak akses pengguna maupun admin, mengatur konfigurasi sistem, serta bertanggung jawab atas pemeliharaan, pencadangan (backup), dan pemulihan (recovery) data sistem.

## 5. Kebutuhan Utama (Functional Requirements)

- Mengelola data ruangan (ID, nama, kapasitas, fasilitas, tata letak, status).
- Registrasi dan login pengguna.
- Mengelola akun dan hak akses berdasarkan role.
- Melakukan pemesanan dengan pilihan durasi (Per Jam, Per Sesi/Harian, Multi-Hari).
- Menghitung total durasi dan biaya sewa secara otomatis.
- Memberikan diskon otomatis berdasarkan durasi.
- Memilih fasilitas tambahan (add-ons) dan paket konsumsi.
- Mengelola penggunaan vendor luar beserta corkage fee.
- Proses approval pemesanan oleh Admin.
- Mencegah double booking melalui validasi ketersediaan ruangan.
- Melihat dan mengubah status pemesanan (Menunggu, Disetujui, Ditolak, Dibatalkan).
- Membuat dan menindaklanjuti laporan (kerusakan, kehilangan, pelanggaran, dan sebagainya).
- Memberikan rating dan ulasan setelah pemesanan selesai.
- Menampilkan rata-rata rating ruangan.
- Mencari dan memfilter data pemesanan.
- Melihat riwayat aktivitas Admin dan Superadmin.
- Mengelola konfigurasi sistem.
- Melakukan pencadangan (backup) dan pemulihan (recovery) data.

## 6. Business Rules

### A. Data Ruangan & Pengguna

1. Setiap ruangan memiliki ID, nama, kapasitas, fasilitas, tata letak, dan status ketersediaan.
2. Setiap pengguna harus terdaftar sebelum dapat melakukan pemesanan.

### B. Pemesanan

3. Setiap pemesanan wajib mencantumkan ruangan, tanggal penggunaan, jenis durasi sewa, dan kebutuhan tambahan (jika ada).
4. Tersedia 3 jenis durasi pemesanan, yaitu Per Jam, Per Sesi/Harian, dan Multi-Hari.
5. Sistem menghitung total durasi dan biaya sewa secara otomatis berdasarkan jenis durasi yang dipilih.
6. Sistem dapat memberikan diskon otomatis berdasarkan durasi pemesanan sesuai ketentuan pengelola.
7. Pengguna dapat memilih fasilitas dan layanan tambahan (add-ons).
8. Pengguna dapat memilih paket konsumsi (coffee break, lunch box, dan sebagainya).
9. Pengguna dapat menggunakan vendor sendiri jika diizinkan oleh pengelola.
10. Jika menggunakan vendor luar, sistem menambahkan corkage fee ke total tagihan.

### C. Persetujuan & Ketersediaan

11. Setiap pemesanan harus melalui proses persetujuan Admin.
12. Ruangan hanya dapat dipesan jika tersedia pada tanggal dan periode waktu yang dipilih.
13. Satu ruangan tidak boleh memiliki lebih dari satu pemesanan yang disetujui pada periode waktu yang saling bertabrakan (Anti Double Booking).
14. Admin berhak menyetujui atau menolak pemesanan.
15. Pengguna dapat melihat status pemesanan (Menunggu, Disetujui, Ditolak, Dibatalkan).
16. Setiap perubahan status pemesanan harus tercatat di sistem.

### D. Laporan & Penanganan

17. Admin dapat membuat laporan terkait kerusakan, kehilangan, keterlambatan, atau pelanggaran yang dilakukan pengguna.
18. Pengguna dapat membuat laporan terkait kerusakan fasilitas, ketidaksesuaian informasi, kebersihan, atau pelayanan.
19. Setiap laporan wajib mencantumkan kategori, deskripsi, tanggal, dan bukti dokumentasi jika ada.
20. Laporan dapat dikaitkan dengan data pemesanan terkait.
21. Admin bertanggung jawab memeriksa dan menindaklanjuti laporan.
22. Admin dapat menetapkan denda atau biaya ganti rugi kepada pengguna apabila terbukti melakukan pelanggaran.
23. Setiap laporan memiliki status penanganan, yaitu Menunggu, Diproses, dan Selesai.

### E. Rating & Evaluasi

24. Pengguna dapat memberikan rating dan ulasan setelah pemesanan selesai.
25. Setiap pemesanan hanya dapat diberikan satu penilaian oleh pengguna yang melakukan pemesanan tersebut.
26. Sistem menghitung dan menampilkan rata-rata rating berdasarkan penilaian pengguna.
27. Admin dapat melihat laporan dan rating sebagai bahan evaluasi serta peningkatan kualitas layanan perusahaan.

### F. Pengelolaan Sistem (IT/Superadmin)

28. Sistem memiliki role IT/Superadmin yang bertanggung jawab atas pengelolaan teknis dan pemeliharaan sistem.
29. Superadmin memiliki hak akses untuk mengelola akun pengguna dan Admin, termasuk menambahkan, mengubah, menonaktifkan, serta mengatur hak akses akun.
30. Superadmin dapat mengelola konfigurasi sistem dan data master yang berkaitan dengan pengaturan aplikasi.
31. Superadmin bertanggung jawab terhadap pemeliharaan sistem, termasuk pencadangan (backup) dan pemulihan (recovery) data.
32. Setiap aktivitas penting yang dilakukan Admin dan Superadmin harus tercatat dalam riwayat aktivitas sistem (activity log).
