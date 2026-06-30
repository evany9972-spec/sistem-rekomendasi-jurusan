<?php
require_once 'includes/config.php';
require_once 'includes/profile_matching.php';
cekLogin();
$pageTitle = 'Rekomendasi Jurusan';

$hasil      = [];
$siswaData  = null;
$siswaList  = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama");

// Proses hitung jika ada siswa_id
$siswa_id = isset($_GET['siswa_id']) ? (int)$_GET['siswa_id'] : 0;

if ($siswa_id > 0) {
    $q = mysqli_query($conn, "SELECT * FROM siswa WHERE id = $siswa_id");
    $siswaData = mysqli_fetch_assoc($q);

    if ($siswaData) {
        $hasil = hitungProfileMatching($conn, $siswa_id);
        simpanHasil($conn, $siswa_id, $hasil);
    }
}

require_once 'includes/header.php';
?>

<!-- Form Pilih Siswa -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label fw-semibold small">Pilih Siswa yang Akan Diproses</label>
                <select name="siswa_id" class="form-select" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php
                    mysqli_data_seek($siswaList, 0);
                    while ($s = mysqli_fetch_assoc($siswaList)): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id']==$siswa_id?'selected':'' ?>>
                        <?= htmlspecialchars($s['nama']) ?> - <?= htmlspecialchars($s['asal_sekolah']??'') ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-calculator me-1"></i> Hitung Rekomendasi
            </button>
        </form>
    </div>
</div>

<?php if ($siswaData && !empty($hasil)): ?>

<!-- Info Siswa -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-person-circle me-2 text-primary"></i>Informasi Siswa</h6>
        <div class="row">
            <div class="col-md-4"><small class="text-muted">Nama</small><div class="fw-semibold"><?= htmlspecialchars($siswaData['nama']) ?></div></div>
            <div class="col-md-4"><small class="text-muted">Asal Sekolah</small><div class="fw-semibold"><?= htmlspecialchars($siswaData['asal_sekolah']??'-') ?></div></div>
            <div class="col-md-4"><small class="text-muted">Jenis Kelamin</small><div class="fw-semibold"><?= $siswaData['jenis_kelamin']=='L'?'Laki-laki':'Perempuan' ?></div></div>
        </div>
    </div>
</div>

<!-- Hasil Rekomendasi -->
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-stars me-2 text-warning"></i>Hasil Rekomendasi Jurusan</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Ranking</th>
                        <th>Jurusan</th>
                        <th>Nilai Core Factor (60%)</th>
                        <th>Nilai Secondary Factor (40%)</th>
                        <th>Nilai Total</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hasil as $h): ?>
                    <tr <?= $h['ranking']==1?'class="table-success"':'' ?>>
                        <td class="px-4">
                            <?php
                            $badgeClass = ['badge-rank-1','badge-rank-2','badge-rank-3'];
                            $icon = $h['ranking']==1?'🥇':($h['ranking']==2?'🥈':($h['ranking']==3?'🥉':''));
                            ?>
                            <?= $icon ?> <span class="badge <?= $badgeClass[$h['ranking']-1] ?? 'bg-secondary' ?>">#<?= $h['ranking'] ?></span>
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($h['nama_jurusan']) ?></td>
                        <td><?= number_format($h['nilai_cf'], 4) ?></td>
                        <td><?= number_format($h['nilai_sf'], 4) ?></td>
                        <td class="fw-bold text-primary"><?= number_format($h['nilai_total'], 4) ?></td>
                        <td>
                            <?php if ($h['ranking']==1): ?>
                            <span class="badge bg-success">⭐ Sangat Direkomendasikan</span>
                            <?php elseif ($h['ranking']==2): ?>
                            <span class="badge bg-info">Direkomendasikan</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Alternatif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail GAP per Jurusan -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-2 text-primary"></i>Detail Perhitungan GAP</h6>
    </div>
    <div class="card-body">
        <?php foreach ($hasil as $h): ?>
        <h6 class="fw-bold mt-3 mb-2">
            <?= $h['ranking']==1?'🥇':($h['ranking']==2?'🥈':'🥉') ?>
            <?= htmlspecialchars($h['nama_jurusan']) ?>
            <small class="text-muted">(Nilai Total: <?= number_format($h['nilai_total'],4) ?>)</small>
        </h6>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Kriteria</th>
                        <th>Tipe</th>
                        <th>Nilai Siswa</th>
                        <th>Nilai Ideal</th>
                        <th>GAP</th>
                        <th>Bobot GAP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($h['detail_gap'] as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td>
                            <span class="badge bg-<?= $d['tipe']=='core_factor'?'primary':'secondary' ?>">
                                <?= $d['tipe']=='core_factor'?'Core Factor':'Secondary Factor' ?>
                            </span>
                        </td>
                        <td><?= $d['nilai'] ?></td>
                        <td><?= $d['ideal'] ?></td>
                        <td class="<?= $d['gap']>=0?'text-success':'text-danger' ?> fw-semibold"><?= $d['gap'] >= 0 ? '+'.$d['gap'] : $d['gap'] ?></td>
                        <td class="fw-bold"><?= $d['bobot'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php elseif ($siswa_id > 0 && !$siswaData): ?>
<div class="alert alert-warning">Siswa tidak ditemukan.</div>
<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-stars fs-1 d-block mb-3 text-warning"></i>
        <h6>Pilih siswa di atas untuk memulai proses rekomendasi</h6>
        <p class="small">Sistem akan menghitung dan menampilkan ranking jurusan yang sesuai</p>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
