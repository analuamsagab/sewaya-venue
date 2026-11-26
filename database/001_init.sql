-- 001_init.sql
-- Menyesuaikan skema yang sudah ada tanpa menghapus tabel lama.
-- Aman dijalankan berulang.

SET time_zone = '+07:00';

-- Password hash bcrypt butuh 60 karakter, kolom lama hanya 16.
ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL;

-- Email valid bisa lebih dari 50 karakter; validasi aplikasi memakai batas 100.
ALTER TABLE users MODIFY COLUMN email VARCHAR(100) NOT NULL;

-- Deskripsi ruangan tidak muat di VARCHAR(100).
ALTER TABLE room MODIFY COLUMN deskripsi TEXT NOT NULL;

DROP PROCEDURE IF EXISTS sewaya_add_column;

DELIMITER //
CREATE PROCEDURE sewaya_add_column(IN tbl VARCHAR(64), IN col VARCHAR(64), IN ddl TEXT)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', ddl);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL sewaya_add_column('room', 'kategori',      "VARCHAR(50) NOT NULL DEFAULT 'Ruang rapat'");
CALL sewaya_add_column('room', 'lantai',         "VARCHAR(20) NOT NULL DEFAULT '1'");
CALL sewaya_add_column('room', 'harga_per_jam',  "DECIMAL(12,2) NOT NULL DEFAULT 0");
CALL sewaya_add_column('room', 'foto',           "VARCHAR(255) NULL");
CALL sewaya_add_column('room', 'status',         "VARCHAR(20) NOT NULL DEFAULT 'tersedia'");

DROP PROCEDURE sewaya_add_column;

CREATE TABLE IF NOT EXISTS booking (
    id_booking       BIGINT       NOT NULL AUTO_INCREMENT,
    kode             VARCHAR(20)  NOT NULL,
    id_user          BIGINT       NOT NULL,
    id_room          INT          NOT NULL,
    jenis_durasi     VARCHAR(20)  NOT NULL DEFAULT 'per_jam',
    mulai            DATETIME     NOT NULL,
    selesai          DATETIME     NOT NULL,
    jumlah_peserta   INT          NOT NULL DEFAULT 1,
    total_biaya      DECIMAL(12,2) NOT NULL DEFAULT 0,
    status           VARCHAR(20)  NOT NULL DEFAULT 'Menunggu',
    alasan_penolakan TEXT         NULL,
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_booking),
    UNIQUE KEY uq_booking_kode (kode),
    KEY idx_booking_room_period (id_room, status, mulai, selesai),
    KEY idx_booking_user (id_user),
    CONSTRAINT fk_booking_user FOREIGN KEY (id_user) REFERENCES users (id_user) ON UPDATE CASCADE,
    CONSTRAINT fk_booking_room FOREIGN KEY (id_room) REFERENCES room (id_room) ON UPDATE CASCADE,
    CONSTRAINT chk_booking_period CHECK (selesai > mulai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS booking_status_log (
    id_log      BIGINT       NOT NULL AUTO_INCREMENT,
    id_booking  BIGINT       NOT NULL,
    status_lama VARCHAR(20)  NULL,
    status_baru VARCHAR(20)  NOT NULL,
    catatan     VARCHAR(255) NULL,
    dibaca      TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_log),
    KEY idx_logs_booking (id_booking, created_at),
    KEY idx_logs_unread (dibaca, created_at),
    CONSTRAINT fk_logs_booking FOREIGN KEY (id_booking) REFERENCES booking (id_booking)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS password_reset (
    id_reset   BIGINT     NOT NULL AUTO_INCREMENT,
    email      VARCHAR(100) NOT NULL,
    token_hash CHAR(64)   NOT NULL,
    expires_at DATETIME   NOT NULL,
    used_at    DATETIME   NULL,
    created_at TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_reset),
    KEY idx_reset_email (email),
    KEY idx_reset_token (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE password_reset MODIFY COLUMN email VARCHAR(100) NOT NULL;
