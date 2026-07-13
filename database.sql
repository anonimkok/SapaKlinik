-- ============================================
-- SapaKlinik - Database Setup
-- Sistem Antrian Klinik Modern
-- ============================================

CREATE DATABASE IF NOT EXISTS sapaklinik
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sapaklinik;

-- ============================================
-- Tabel Users (Admin & Pasien)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pasien') NOT NULL DEFAULT 'pasien',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Tabel Poli (Master Data Poli/Layanan)
-- ============================================
CREATE TABLE IF NOT EXISTS poli (
    id_poli INT AUTO_INCREMENT PRIMARY KEY,
    nama_poli VARCHAR(100) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Tabel Antrian (Transaksi Booking)
-- ============================================
CREATE TABLE IF NOT EXISTS antrian (
    id_antrian INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_poli INT NOT NULL,
    tanggal_berobat DATE NOT NULL,
    no_antrian INT NOT NULL,
    status ENUM('Menunggu', 'Diproses', 'Selesai', 'Batal') NOT NULL DEFAULT 'Menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_poli) REFERENCES poli(id_poli) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Data Default: Admin Account
-- Password: admin123 (hashed with password_hash)
-- ============================================
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Administrator', 'admin@sapaklinik.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================
-- Data Default: Sample Poli
-- ============================================
INSERT INTO poli (nama_poli, keterangan) VALUES
('Poli Umum', 'Layanan pemeriksaan kesehatan umum, konsultasi dokter, dan penanganan penyakit ringan.'),
('Poli Gigi', 'Layanan perawatan gigi dan mulut, termasuk pembersihan, penambalan, dan pencabutan gigi.'),
('Poli Anak', 'Layanan kesehatan khusus untuk bayi, anak-anak, dan remaja termasuk imunisasi.'),
('Poli Mata', 'Layanan pemeriksaan dan perawatan gangguan penglihatan serta penyakit mata.');
