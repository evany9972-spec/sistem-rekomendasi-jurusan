<?php
require_once 'includes/config.php';
cekLogin();
$pageTitle = 'Data Siswa';

// Ambil daftar kriteria
$kriteriaList = [];
$qK = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id");
while ($k = mysqli_fetch_assoc($qK)) $kriteriaList[] = $k;

// PROSES TAMBAH SISWA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] == 'tambah') {
        $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
        $sekolah= mysqli_real_escape_string($conn, $_POST['asal_sekolah']);
        $jk     = $_POST['jenis_kelamin'];

        mysqli_query($conn, "INSERT INTO siswa (nama, asal_sekolah, jenis_kelamin) VALUES ('$nama','$sekolah','$jk')");
        $siswa_id = mysqli_insert_id($conn);

        // Simpan nilai kriteria
        foreach ($kriteriaList as $kr) {
            $kid   = $kr['id'];
            $nilai = (float)$_POST["nilai_$kid"];
            mysqli_query($conn, "INSERT INTO nilai_siswa (siswa_id, kriteria_id, nilai) VALUES ($siswa_id, $kid, $nilai)");
        }
        setAlert('success', 'Data siswa berhasil ditambahkan!');
        redirect('siswa.php');
    }

    if ($_POST['action'] == 'hapus') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM siswa WHERE id = $id");
        setAlert('success', 'Data siswa berhasil dihapus!');
        redirect('siswa.php');
    }
}

// Ambil semua siswa
$siswaList = mysqli_query($conn, "SELECT s.*, h.nama_jurusan
    FROM siswa s
    LEFT JOIN (
        SELECT hr.siswa_id, j.nama_jurusan FROM hasil_rekomendasi hr
        JOIN jurusan j ON hr.jurusan_id = j.id WHERE hr.ranking = 1
    ) h ON s.id = h.siswa_id
    ORDER BY s.created_at DESC");

require_once 'includes/header.php';
?>

<div class="card mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-people me-2 text-primary"></i>Daftar Siswa</h6>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
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
                        <th>Rekomendasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($s = mysqli_fetch_assoc($siswaList)): ?>
                    <tr>
                        <td class="px-4"><?= $no++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($s['nama']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($s['asal_sekolah'] ?? '-') ?></td>
                        <td><?= $s['jenis_kelamin'] == 'L' ? '<span class="badge bg-info">Laki-laki</span>' : '<span class="badge bg-pink" style="background:#ec4899">Perempuan</span>' ?></td>
                        <td>
                            <?php if ($s['nama_jurusan']): ?>
                            <span class="badge bg-success"><?= htmlspecialchars($s['nama_jurusan']) ?></span>
                            <?php else: ?>
                            <span class="badge bg-warning text-dark">Belum diproses</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="rekomendasi.php?siswa_id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-stars"></i>
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Tambah Data Siswa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Siswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Asal Sekolah</label>
                            <input type="text" name="asal_sekolah" class="form-control" placeholder="Nama sekolah">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-semibold mb-3"><i class="bi bi-list-check me-2 text-primary"></i>Nilai Kriteria</h6>
                    <div class="row g-3">
                        <?php foreach ($kriteriaList as $kr): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                <?= htmlspecialchars($kr['nama_kriteria']) ?>
                                <span class="badge bg-<?= $kr['tipe']=='core_factor'?'primary':'secondary' ?> ms-1" style="font-size:0.65rem;">
                                    <?= $kr['tipe']=='core_factor'?'CF':'SF' ?>
                                </span>
                            </label>
                            <input type="number" name="nilai_<?= $kr['id'] ?>" class="form-control"
                                min="0" max="100" step="0.5" required
                                placeholder="<?= strpos($kr['nama_kriteria'],'Minat')!==false||strpos($kr['nama_kriteria'],'Logika')!==false ? '1-10' : '0-100' ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
