<?php
require_once 'includes/config.php';
cekLogin();
$pageTitle = 'Kelola Kriteria';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'tambah') {
        $nama   = mysqli_real_escape_string($conn, $_POST['nama_kriteria']);
        $bobot  = (float)$_POST['bobot'];
        $tipe   = $_POST['tipe'];
        $ket    = mysqli_real_escape_string($conn, $_POST['keterangan']);
        mysqli_query($conn, "INSERT INTO kriteria (nama_kriteria, bobot, tipe, keterangan) VALUES ('$nama',$bobot,'$tipe','$ket')");
        setAlert('success', 'Kriteria berhasil ditambahkan!');
    } elseif ($action == 'hapus') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM kriteria WHERE id = $id");
        setAlert('success', 'Kriteria berhasil dihapus!');
    }
    redirect('kriteria.php');
}

$kriteriaList = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY tipe, id");
$totalBobot   = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(bobot) FROM kriteria"))[0];

require_once 'includes/header.php';
?>

<?php if ($totalBobot != 100): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Total bobot kriteria saat ini: <strong><?= $totalBobot ?>%</strong>. Pastikan total bobot = <strong>100%</strong> agar perhitungan akurat.
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-list-check me-2 text-primary"></i>Daftar Kriteria</h6>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kriteria
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">No</th>
                        <th>Nama Kriteria</th>
                        <th>Bobot (%)</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while ($k = mysqli_fetch_assoc($kriteriaList)): ?>
                    <tr>
                        <td class="px-4"><?= $no++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                        <td><?= $k['bobot'] ?>%</td>
                        <td>
                            <span class="badge bg-<?= $k['tipe']=='core_factor'?'primary':'secondary' ?>">
                                <?= $k['tipe']=='core_factor'?'Core Factor':'Secondary Factor' ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= htmlspecialchars($k['keterangan']??'-') ?></td>
                        <td>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus kriteria ini?')">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="px-4 fw-bold text-end">Total Bobot:</td>
                        <td class="fw-bold <?= $totalBobot==100?'text-success':'text-danger' ?>"><?= $totalBobot ?>%</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Tambah Kriteria</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Kriteria</label>
                        <input type="text" name="nama_kriteria" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bobot (%)</label>
                        <input type="number" name="bobot" class="form-control" min="1" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tipe</label>
                        <select name="tipe" class="form-select" required>
                            <option value="core_factor">Core Factor</option>
                            <option value="secondary_factor">Secondary Factor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
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
