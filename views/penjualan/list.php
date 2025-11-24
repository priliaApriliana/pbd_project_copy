<?php
session_start();
if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualan = new Penjualan();

$kasir = $_GET['kasir'] ?? 'all';
$tanggal = $_GET['tanggal'] ?? 'all';

$dataPenjualan = $penjualan->getFiltered($kasir, $tanggal);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penjualan | Sistem Farmasi</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Theme -->
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/list-table.css">

</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <h1 class="page-title">
                <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png">
                Data Penjualan
            </h1>

            <a href="add.php" class="btn-add">
                <i class="bi bi-plus-circle-fill"></i> Tambah Penjualan
            </a>
        </div>

        <!-- FILTER MODERN -->
        <form method="get" class="card p-4 shadow-sm rounded-4 mb-4">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="fw-bold mb-1">Kasir:</label>
                    <select name="kasir" class="form-select rounded-3">
                        <option value="all" <?= $kasir=='all'? 'selected':'' ?>>Semua</option>
                        <option value="superadmin" <?= $kasir=='superadmin'? 'selected':'' ?>>Superadmin</option>
                        <option value="admin" <?= $kasir=='admin'? 'selected':'' ?>>Admin</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold mb-1">Tanggal:</label>
                    <select name="tanggal" class="form-select rounded-3">
                        <option value="all" <?= $tanggal=='all'? 'selected':'' ?>>Semua</option>
                        <option value="today" <?= $tanggal=='today'? 'selected':'' ?>>Hari ini</option>
                        <option value="week" <?= $tanggal=='week'? 'selected':'' ?>>7 Hari Terakhir</option>
                        <option value="month" <?= $tanggal=='month'? 'selected':'' ?>>30 Hari Terakhir</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100 rounded-3">Tampilkan</button>
                </div>

            </div>
        </form>

        <!-- TABLE -->
        <div class="card shadow-sm rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cart-check me-2"></i> Daftar Penjualan</h5>
                <span class="badge bg-light text-dark fs-6"><?= count($dataPenjualan) ?> item</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>TANGGAL</th>
                            <th>KASIR</th>
                            <th>SUBTOTAL</th>
                            <th>PPN</th>
                            <th>TOTAL</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (!empty($dataPenjualan)): ?>
                            <?php foreach ($dataPenjualan as $p): ?>
                                <tr>
                                    <td><strong class="text-success"><?= $p['idpenjualan'] ?></strong></td>
                                    <td><?= $p['tanggal'] ?></td>
                                    <td><?= $p['kasir'] ?></td>

                                    <td class="text-end">Rp <?= number_format($p['subtotal'], 0, ',', '.') ?></td>
                                    <td><?= $p['ppn_persen'] ?>%</td>
                                    <td class="fw-bold text-end text-success">
                                        Rp <?= number_format($p['total'], 0, ',', '.') ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="detail.php?id=<?= $p['idpenjualan'] ?>" class="action-btn btn-view">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2"></i><br>
                                    Tidak ada data penjualan.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
