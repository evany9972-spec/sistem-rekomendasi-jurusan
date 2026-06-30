<?php
require_once 'includes/config.php';
cekLogin();
$pageTitle = 'Laporan Rekomendasi';

$laporanList = mysqli_query($conn, "
    SELECT s.nama, s.asal_sekolah, s.jenis_kelamin,
           j.nama_jurusan, h.nilai_cf, h.nilai_sf, h.nilai_total, h.ranking, h.created_at
    FROM hasil_rekomendasi h
    JOIN siswa s ON h.siswa_id = s.id
    JOIN jurusan j ON h.jurusan_id = j.id
    WHERE h.ranking = 1
    ORDER BY h.created_at DESC
");

require_once 'includes/header.php';
?>

<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Hasil Rekomendasi</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">No</th>
                        <th>Nama Siswa</th>
                        <th>Asal Sekolah</th>
                        <th>JK</th>
                        <th>Rekomendasi Jurusan</th>
                        <th>Nilai CF</th>
                        <th>Nilai SF</th>
                        <th>Nilai Total</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while ($l = mysqli_fetch_assoc($laporanList)): ?>
                    <tr>
                        <td class="px-4"><?= $no++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($l['nama']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($l['asal_sekolah']??'-') ?></td>
                        <td><?= $l['jenis_kelamin']=='L'?'L':'P' ?></td>
                        <td><span class="badge bg-success"><?= htmlspecialchars($l['nama_jurusan']) ?></span></td>
                        <td><?= number_format($l['nilai_cf'],4) ?></td>
                        <td><?= number_format($l['nilai_sf'],4) ?></td>
                        <td class="fw-bold text-primary"><?= number_format($l['nilai_total'],4) ?></td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($l['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
