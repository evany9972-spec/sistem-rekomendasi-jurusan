<?php
require_once 'includes/config.php';
cekLogin();
$pageTitle = 'Kelola Jurusan';

$kriteriaList = [];
$qK = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id");
while ($k = mysqli_fetch_assoc($qK)) $kriteriaList[] = $k;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'tambah') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama_jurusan']);
        $desk = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        mysqli_query($conn, "INSERT INTO jurusan (nama_jurusan, deskripsi) VALUES ('$nama','$desk')");
        $jid = mysqli_insert_id($conn);

        foreach ($kriteriaList as $kr) {
            $kid   = $kr['id'];
            $ideal = (float)$_POST["ideal_$kid"];
            mysqli_query($conn, "INSERT INTO profil_jurusan (jurusan_id, kriteria_id, nilai_ideal) VALUES ($jid,$kid,$ideal)");
        }
        setAlert('success', 'Jurusan berhasil ditambahkan!');
    } elseif ($action == 'hapus') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM jurusan WHERE id = $id");
        setAlert('success', 'Jurusan berhasil dihapus!');
    }
    redirect('jurusan.php');
}

$jurusanList = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY id");
require_once 'includes/header.php';
?>

<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Daftar Jurusan</h6>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jurusan
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">No</th>
                        <th>Nama Jurusan</th>
                        <th>Deskripsi</th>
                        <th>Profil Ideal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while ($j = mysqli_fetch_assoc($jurusanList)): ?>
                    <?php
                    $profil = [];
                    $qP = mysqli_query($conn, "SELECT pj.nilai_ideal, k.nama_kriteria FROM profil_jurusan pj
                        JOIN kriteria k ON pj.kriteria_id = k.id WHERE pj.jurusan_id = {$j['id']}");
                    while ($p = mysqli_fetch_assoc($qP)) $profil[] = $p;
                    ?>
                    <tr>
                        <td class="px-4"><?= $no++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($j['nama_jurusan']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($j['deskripsi']??'-') ?></td>
                        <td>
                            <?php foreach ($profil as $p): ?>
                            <span class="badge bg-light text-dark border me-1" style="font-size:0.7rem;">
                                <?= htmlspecialchars($p['nama_kriteria']) ?>: <?= $p['nilai_ideal'] ?>
                            </span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus jurusan ini?')">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= $j['id'] ?>">
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

<!-- Modal -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Tambah Jurusan + Profil Ideal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <hr>
                    <h6 class="fw-semibold mb-3">Nilai Ideal per Kriteria</h6>
                    <div class="row g-3">
                        <?php foreach ($kriteriaList as $kr): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold"><?= htmlspecialchars($kr['nama_kriteria']) ?></label>
                            <input type="number" name="ideal_<?= $kr['id'] ?>" class="form-control"
                                min="0" max="100" step="0.5" required placeholder="Nilai ideal">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
