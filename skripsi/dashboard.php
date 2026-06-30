<?php
require_once 'includes/config.php';
cekLogin();
$pageTitle = 'Dashboard';

$totalSiswa   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM siswa"))[0];
$totalJurusan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM jurusan"))[0];
$totalKriteria= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM kriteria"))[0];
$totalHasil   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT siswa_id) FROM hasil_rekomendasi"))[0];

// Siswa terbaru
$siswaTerbaru = mysqli_query($conn, "SELECT s.*, j.nama_jurusan
    FROM siswa s
    LEFT JOIN hasil_rekomendasi h ON s.id = h.siswa_id AND h.ranking = 1
    LEFT JOIN jurusan j ON h.jurusan_id = j.id
    ORDER BY s.created_at DESC LIMIT 5");

require_once 'includes/header.php';
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#1a56db,#3b82f6);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="opacity-75 small mb-1">Total Siswa</div>
                    <div class="fs-2 fw-bold"><?= $totalSiswa ?></div>
                </div>
                <i class="bi bi-people fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#059669,#34d399);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="opacity-75 small mb-1">Jurusan Tersedia</div>
                    <div class="fs-2 fw-bold"><?= $totalJurusan ?></div>
                </div>
                <i class="bi bi-journal-bookmark fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#d97706,#fbbf24);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="opacity-75 small mb-1">Total Kriteria</div>
                    <div class="fs-2 fw-bold"><?= $totalKriteria ?></div>
                </div>
                <i class="bi bi-list-check fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#7c3aed,#a78bfa);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="opacity-75 small mb-1">Sudah Diproses</div>
                    <div class="fs-2 fw-bold"><?= $totalHasil ?></div>
                </div>
                <i class="bi bi-stars fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Siswa Terbaru -->
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Siswa Terbaru</h6>
        <a href="siswa.php" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Nama Siswa</th>
                        <th>Asal Sekolah</th>
                        <th>Rekomendasi Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($s = mysqli_fetch_assoc($siswaTerbaru)): ?>
                    <tr>
                        <td class="px-4 fw-semibold"><?= htmlspecialchars($s['nama']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($s['asal_sekolah'] ?? '-') ?></td>
                        <td>
                            <?php if ($s['nama_jurusan']): ?>
                            <span class="badge bg-success"><?= htmlspecialchars($s['nama_jurusan']) ?></span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Belum diproses</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="rekomendasi.php?siswa_id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-stars"></i> Proses
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($totalSiswa == 0): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data siswa</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
