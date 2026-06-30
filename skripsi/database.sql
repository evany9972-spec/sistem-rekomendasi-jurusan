-- ============================================
-- DATABASE: db_rekomendasi_jurusan
-- Sistem Rekomendasi Pemilihan Jurusan
-- Menggunakan Metode Profile Matching
-- ============================================

CREATE DATABASE IF NOT EXISTS db_rekomendasi_jurusan;
USE db_rekomendasi_jurusan;

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Jurusan
CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jurusan VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kriteria
CREATE TABLE IF NOT EXISTS kriteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kriteria VARCHAR(100) NOT NULL,
    bobot DECIMAL(5,2) NOT NULL,
    tipe ENUM('core_factor', 'secondary_factor') NOT NULL,
    keterangan TEXT
);

-- Tabel Profil Jurusan (nilai ideal tiap kriteria per jurusan)
CREATE TABLE IF NOT EXISTS profil_jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jurusan_id INT NOT NULL,
    kriteria_id INT NOT NULL,
    nilai_ideal DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE,
    FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE
);

-- Tabel Siswa
CREATE TABLE IF NOT EXISTS siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    asal_sekolah VARCHAR(100),
    jenis_kelamin ENUM('L','P') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Nilai Siswa (input per kriteria)
CREATE TABLE IF NOT EXISTS nilai_siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    kriteria_id INT NOT NULL,
    nilai DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE
);

-- Tabel Hasil Rekomendasi
CREATE TABLE IF NOT EXISTS hasil_rekomendasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    jurusan_id INT NOT NULL,
    nilai_cf DECIMAL(8,4),
    nilai_sf DECIMAL(8,4),
    nilai_total DECIMAL(8,4),
    ranking INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE
);

-- ============================================
-- DATA AWAL
-- ============================================

-- Admin default (password: admin123)
INSERT INTO admin (username, password, nama) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Data Jurusan
INSERT INTO jurusan (nama_jurusan, deskripsi) VALUES
('Teknik Informatika', 'Mempelajari ilmu komputer, pemrograman, dan pengembangan perangkat lunak'),
('Sistem Informasi', 'Mempelajari pengelolaan informasi dan teknologi untuk bisnis'),
('Teknik Komputer', 'Mempelajari hardware, jaringan, dan sistem komputer'),
('Ilmu Komputer', 'Mempelajari teori komputasi, algoritma, dan kecerdasan buatan');

-- Data Kriteria
INSERT INTO kriteria (nama_kriteria, bobot, tipe, keterangan) VALUES
('Nilai Matematika', 30.00, 'core_factor', 'Nilai rata-rata matematika'),
('Nilai IPA/Fisika', 25.00, 'core_factor', 'Nilai rata-rata IPA atau Fisika'),
('Minat Teknologi', 20.00, 'secondary_factor', 'Skala minat terhadap teknologi (1-10)'),
('Kemampuan Logika', 15.00, 'secondary_factor', 'Kemampuan berpikir logis (1-10)'),
('Nilai Bahasa Inggris', 10.00, 'secondary_factor', 'Nilai rata-rata Bahasa Inggris');

-- Profil ideal Teknik Informatika
INSERT INTO profil_jurusan (jurusan_id, kriteria_id, nilai_ideal) VALUES
(1, 1, 85), (1, 2, 80), (1, 3, 9), (1, 4, 9), (1, 5, 80),
-- Profil ideal Sistem Informasi
(2, 1, 75), (2, 2, 70), (2, 3, 8), (2, 4, 7), (2, 5, 75),
-- Profil ideal Teknik Komputer
(3, 1, 80), (3, 2, 85), (3, 3, 8), (3, 4, 8), (3, 5, 70),
-- Profil ideal Ilmu Komputer
(4, 1, 90), (4, 2, 80), (4, 3, 9), (4, 4, 10), (4, 5, 75);
