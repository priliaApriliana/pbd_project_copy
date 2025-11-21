<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/Barang.php");
$barangObj = new Barang();

// Ambil filter status
$show = $_GET['show'] ?? 'aktif';
$show = in_array($show, ['all', 'aktif']) ? $show : 'aktif';

$data = $barangObj->getAll($show);
$label = ($show === 'all') ? 'Semua Barang' : 'Barang Aktif';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang | Sistem Farmasi</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Style Utama -->
    <link rel="stylesheet" href="../../assets/style/dashboard.css">

    <!-- Style Tabel & Search -->
    <link rel="stylesheet" href="../../assets/style/list-table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
    <div class="container-fluid">

        <!-- Header + Search + Filter -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">

            <h1 class="page-title">
                <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" width="42" alt="Barang">
                Data Barang
            </h1>

            <div class="d-flex gap-3 flex-wrap">

                <!-- Search Box -->
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari kode, nama, atau jenis barang...">
                </div>

                <!-- Filter Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel me-1"></i> <?= $label ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?= $show === 'all' ? 'active' : '' ?>" href="?show=all">Semua Barang</a></li>
                        <li><a class="dropdown-item <?= $show === 'aktif' ? 'active' : '' ?>" href="?show=aktif">Barang Aktif</a></li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Tombol Tambah -->
        <div class="mb-4">
            <a href="add.php" class="btn-add">
                <i class="bi bi-plus-circle-fill"></i> Tambah Barang
            </a>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i> Daftar Barang</h5>
                <span class="badge bg-light text-dark fs-6"><?= count($data) ?> item</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <!-- tabel mu tetap -->

                    <table class="table table-hover align-middle mb-0" id="barangTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>JENIS</th>
                                <th>NAMA</th>
                                <th>SATUAN</th>
                                <th>STATUS</th>
                                <th>HARGA</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php foreach ($data as $row): ?>
                                    <tr>
                                        <td><strong class="text-success"><?= htmlspecialchars($row['kode_barang']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['kategori'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['satuan'] ?? '-') ?></td>
                                        <td>
                                            <span class="status <?= $row['status_barang'] === 'Aktif' ? 'aktif' : 'nonaktif' ?>">
                                                <?= $row['status_barang'] ?>
                                            </span>
                                        </td>
                                        <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <a href="edit.php?id=<?= $row['kode_barang'] ?>" class="action-btn btn-edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="delete.php?id=<?= $row['kode_barang'] ?>" class="action-btn btn-delete ms-2"
                                                onclick="return confirm('Yakin ingin menghapus barang &quot;<?= htmlspecialchars($row['nama_barang']) ?>&quot;?')"
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Belum ada data barang.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div> <!-- table-responsive -->
            </div> <!-- card-body -->
        </div> <!-- card -->

    </div> <!-- container-fluid -->
</div> <!-- main -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Table Search -->
<script src="../../assets/js/table-search.js"></script>

</body>
</html>