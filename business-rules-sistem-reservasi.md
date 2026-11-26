# Business Rules Sistem Reservasi

1. Setiap ruangan memiliki ID ruangan, nama ruangan, kapasitas, fasilitas, tata letak, dan status ketersediaan.

2. Setiap pengguna harus terdaftar dalam sistem sebelum dapat melakukan pemesanan ruangan.

3. Setiap pemesanan harus mencantumkan ruangan, tanggal penggunaan, jenis durasi sewa, dan informasi kebutuhan tambahan jika diperlukan.

4. Sistem menyediakan tiga jenis durasi pemesanan, yaitu:
   - **Per Jam (Hourly):** Pengguna menentukan tanggal, jam mulai, dan jam selesai sesuai batas waktu operasional.
   - **Per Sesi/Harian (Half-Day/Full-Day):** Pengguna memilih paket waktu yang telah ditentukan oleh sistem.
   - **Multi-Hari (Multi-Day):** Pengguna menentukan tanggal mulai dan tanggal selesai penggunaan ruangan.

5. Sistem menghitung total durasi dan biaya sewa berdasarkan jenis durasi serta tarif ruangan yang dipilih.

6. Sistem dapat memberikan diskon otomatis berdasarkan durasi pemesanan sesuai ketentuan yang telah ditetapkan pengelola.

7. Setiap ruangan memiliki pilihan fasilitas dan layanan tambahan (add-ons) yang dapat dipilih pengguna saat melakukan pemesanan.

8. Pengguna dapat memilih paket konsumsi tambahan, seperti coffee break dan lunch box, berdasarkan pilihan menu yang disediakan oleh pengelola.

9. Pengguna dapat menggunakan vendor atau katering sendiri apabila diizinkan oleh pengelola ruangan.

10. Apabila penggunaan vendor luar dikenakan biaya tambahan (corkage fee), sistem akan memasukkan biaya tersebut ke dalam total tagihan pemesanan.

11. Setiap pemesanan harus melalui proses persetujuan dari admin sebelum dinyatakan berhasil.

12. Ruangan hanya dapat dipesan apabila tersedia pada tanggal dan periode waktu yang dipilih.

13. Satu ruangan tidak boleh memiliki lebih dari satu pemesanan yang disetujui pada periode waktu yang saling bertabrakan.

14. Admin memiliki hak untuk menyetujui atau menolak permintaan pemesanan berdasarkan ketersediaan ruangan dan ketentuan pengelola.

15. Pengguna dapat melihat status pemesanan, yaitu menunggu persetujuan, disetujui, ditolak, atau dibatalkan.

16. Setiap perubahan status pemesanan harus tercatat dalam sistem.

17. Admin dapat membuat laporan apabila ditemukan kerusakan, kehilangan properti, keterlambatan penggunaan, atau pelanggaran aturan oleh pengguna.

18. Pengguna dapat membuat laporan terkait kerusakan fasilitas, ketidaksesuaian informasi ruangan, kebersihan, maupun pelayanan pengelola.

19. Setiap laporan harus mencantumkan kategori, deskripsi kejadian, tanggal laporan, serta bukti dokumentasi jika diperlukan.

20. Setiap laporan dapat dikaitkan dengan data pemesanan yang bersangkutan apabila berkaitan dengan transaksi tertentu.

21. Admin bertanggung jawab memeriksa dan menindaklanjuti laporan yang masuk.

22. Admin dapat menetapkan denda atau biaya ganti rugi kepada pengguna apabila terbukti melakukan pelanggaran yang merugikan pihak pengelola.

23. Setiap laporan memiliki status penanganan, seperti menunggu, diproses, dan selesai.

24. Pengguna dapat memberikan rating dan ulasan terhadap ruangan serta pelayanan setelah pemesanan selesai.

25. Setiap pemesanan hanya dapat diberikan satu penilaian oleh pengguna yang melakukan pemesanan tersebut.

26. Sistem menghitung dan menampilkan nilai rata-rata rating berdasarkan penilaian yang diberikan pengguna.

27. Admin dapat melihat laporan dan penilaian pengguna sebagai bahan evaluasi serta peningkatan kualitas layanan perusahaan.

28. Sistem memiliki role Superadmin/IT yang bertanggung jawab atas pengelolaan teknis dan pemeliharaan sistem.

29. Superadmin memiliki hak akses untuk mengelola akun pengguna dan admin, termasuk menambahkan, mengubah, menonaktifkan, serta mengatur hak akses akun.

30. Superadmin bertanggung jawab terhadap pemeliharaan sistem, termasuk pengaturan konfigurasi, pencadangan data (backup), dan pemulihan data (recovery).
