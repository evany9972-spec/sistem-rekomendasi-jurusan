# 🎓 Sistem Rekomendasi Pemilihan Jurusan Kuliah

Sistem pendukung keputusan berbasis web untuk membantu siswa SMA/SMK menentukan jurusan kuliah yang paling sesuai dengan kemampuan dan minat mereka, menggunakan metode **Profile Matching**.

## 📌 Tentang Project

Project ini dibuat sebagai bagian dari persiapan tugas akhir/skripsi di bidang Informatika. Sistem ini membantu calon mahasiswa mengambil keputusan berbasis data dengan membandingkan profil nilai siswa terhadap profil ideal setiap jurusan.

## ✨ Fitur

- 🔐 Login admin dengan autentikasi aman
- 👥 Manajemen data siswa beserta nilai per kriteria
- 📋 Manajemen kriteria penilaian (Core Factor & Secondary Factor)
- 🎓 Manajemen data jurusan beserta profil ideal
- ⭐ Perhitungan otomatis rekomendasi jurusan menggunakan algoritma Profile Matching
- 📊 Laporan hasil rekomendasi yang bisa dicetak
- 📈 Dashboard dengan statistik ringkas

## 🧮 Metode: Profile Matching

Profile Matching adalah metode pengambilan keputusan yang membandingkan kompetensi individu (siswa) dengan kompetensi ideal (jurusan), sehingga dapat diketahui perbedaan kompetensinya (disebut **gap**). Semakin kecil gap, semakin besar peluang individu cocok dengan jurusan tersebut.

Proses perhitungan meliputi:
1. Pemetaan **GAP** (selisih nilai siswa dengan nilai ideal jurusan)
2. Konversi GAP menjadi bobot nilai
3. Pengelompokan kriteria menjadi **Core Factor** (60%) dan **Secondary Factor** (40%)
4. Perhitungan nilai total dan perankingan jurusan

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP Native
- **Database**: MySQL
- **Frontend**: HTML, CSS, Bootstrap 5
- **Icons**: Bootstrap Icons

## 🚀 Cara Menjalankan

1. Clone repository ini
   ```bash
   git clone https://github.com/username/sistem-rekomendasi-jurusan.git
   ```
2. Pindahkan folder ke direktori server lokal (contoh: `htdocs` untuk XAMPP)
3. Aktifkan Apache dan MySQL melalui XAMPP Control Panel
4. Buat database baru bernama `db_rekomendasi_jurusan`
5. Import file `database.sql` ke database tersebut
6. Akses melalui browser: `http://localhost/sistem-rekomendasi-jurusan/`
7. Login menggunakan:
   - Username: `admin`
   - Password: `admin123`

## 📂 Struktur Project

```
├── includes/
│   ├── config.php           # Koneksi database & helper functions
│   ├── header.php           # Template header (sidebar & navbar)
│   ├── footer.php           # Template footer
│   └── profile_matching.php # Logika algoritma Profile Matching
├── login.php
├── dashboard.php
├── siswa.php
├── kriteria.php
├── jurusan.php
├── rekomendasi.php
├── laporan.php
├── logout.php
├── index.php
└── database.sql
```

## 📸 Tampilan

*(Tambahkan screenshot aplikasi di sini setelah dijalankan)*

## 👤 Pengembang

Dikembangkan sebagai bagian dari riset awal tugas akhir mahasiswa Informatika.

## 📄 Lisensi

Project ini dibuat untuk keperluan pembelajaran dan akademik.
