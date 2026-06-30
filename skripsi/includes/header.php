<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistem Rekomendasi Jurusan' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a56db;
            --sidebar-bg: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #1a56db;
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: 260px; min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed; top: 0; left: 0; z-index: 100;
        }
        .sidebar-brand {
            padding: 20px; background: #0f172a;
            font-weight: 700; font-size: 1rem; color: #fff;
            border-bottom: 1px solid #334155;
        }
        .sidebar-brand small { color: #64748b; font-size: 0.75rem; font-weight: 400; display: block; }
        .sidebar .nav-link {
            color: var(--sidebar-text); padding: 12px 20px;
            border-radius: 8px; margin: 2px 10px;
            font-size: 0.875rem; transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sidebar-active); color: #fff;
        }
        .sidebar .nav-link i { width: 20px; margin-right: 8px; }
        .main-content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        .topbar {
            background: #fff; border-radius: 12px;
            padding: 15px 20px; margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            display: flex; justify-content: space-between; align-items: center;
        }
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .card-header { border-radius: 12px 12px 0 0 !important; }
        .stat-card { padding: 24px; border-radius: 12px; color: #fff; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .badge-rank-1 { background: #f59e0b; color: #fff; }
        .badge-rank-2 { background: #94a3b8; color: #fff; }
        .badge-rank-3 { background: #b45309; color: #fff; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-mortarboard-fill me-2 text-primary"></i>RekomendasiJurusan
        <small>Sistem Pendukung Keputusan</small>
    </div>
    <nav class="nav flex-column mt-3">
        <a href="dashboard.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])=='dashboard.php')?'active':'' ?>">
            <i class="bi bi-grid"></i> Dashboard
        </a>
        <a href="siswa.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])=='siswa.php')?'active':'' ?>">
            <i class="bi bi-people"></i> Data Siswa
        </a>
        <a href="kriteria.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])=='kriteria.php')?'active':'' ?>">
            <i class="bi bi-list-check"></i> Kriteria
        </a>
        <a href="jurusan.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])=='jurusan.php')?'active':'' ?>">
            <i class="bi bi-journal-bookmark"></i> Jurusan
        </a>
        <a href="rekomendasi.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])=='rekomendasi.php')?'active':'' ?>">
            <i class="bi bi-stars"></i> Rekomendasi
        </a>
        <a href="laporan.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])=='laporan.php')?'active':'' ?>">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan
        </a>
        <hr style="border-color:#334155; margin: 10px 20px;">
        <a href="logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div>
            <h5 class="mb-0 fw-semibold"><?= $pageTitle ?? 'Dashboard' ?></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><i class="bi bi-person-circle me-1"></i><?= $_SESSION['admin_nama'] ?? 'Admin' ?></span>
        </div>
    </div>

    <!-- Alert -->
    <?php if (isset($_SESSION['alert_message'])): ?>
    <div class="alert alert-<?= $_SESSION['alert_type'] ?> alert-dismissible fade show mb-4" role="alert">
        <?= $_SESSION['alert_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['alert_message'], $_SESSION['alert_type']); endif; ?>
