<?php
session_start();

if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualan = new Penjualan();

// --- Ambil filter ---
$kasir = $_GET['kasir'] ?? 'all';
$tanggal = $_GET['tanggal'] ?? 'all';

// Ambil data penjualan (filtered)
$dataPenjualan = $penjualan->getFiltered($kasir, $tanggal);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penjualan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">

    <div class="card p-4 shadow-sm">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="bi bi-cart-check text-success me-2"></i> 
            Data Penjualan
        </h4>
            <a href="add.php" class="btn btn-primary btn-sm">+ Tambah Penjualan</a>
        </div>

        <!-- FILTER -->
        <form method="get" class="row gy-2 gx-3 align-items-center mb-3">

            <div class="col-auto">
                <label class="fw-bold">Kasir:</label>
                <select name="kasir" class="form-select">
                    <option value="all" <?= $kasir == 'all' ? 'selected' : '' ?>>Semua</option>
                    <option value="superadmin" <?= $kasir == 'superadmin' ? 'selected' : '' ?>>superadmin</option>
                    <option value="admin" <?= $kasir == 'admin' ? 'selected' : '' ?>>admin</option>
                </select>
            </div>

            <div class="col-auto">
                <label class="fw-bold">Tanggal:</label>
                <select name="tanggal" class="form-select">
                    <option value="all" <?= $tanggal == 'all' ? 'selected' : '' ?>>Semua</option>
                    <option value="today" <?= $tanggal == 'today' ? 'selected' : '' ?>>Hari ini</option>
                    <option value="week" <?= $tanggal == 'week' ? 'selected' : '' ?>>7 Hari terakhir</option>
                    <option value="month" <?= $tanggal == 'month' ? 'selected' : '' ?>>30 Hari terakhir</option>
                </select>
            </div>

            <div class="col-auto mt-4">
                <button class="btn btn-success btn-sm">Tampilkan</button>
            </div>

        </form>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary text-center">
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Subtotal</th>
                    <th>PPN</th>
                    <th>Total</th>
                    <th>Opsi</th>
                </tr>
                </thead>

                <tbody>
                <?php if (!empty($dataPenjualan)): ?>
                    <?php foreach ($dataPenjualan as $p): ?>
                        <tr class="text-center">
                            <td><?= $p['idpenjualan'] ?></td>
                            <td><?= $p['tanggal'] ?></td>
                            <td><?= $p['kasir'] ?></td>

                            <td class="text-end">Rp <?= number_format($p['subtotal'], 0, ',', '.') ?></td>
                            <td><?= $p['ppn_persen'] ?>%</td>
                            <td class="fw-bold text-end">Rp <?= number_format($p['total'], 0, ',', '.') ?></td>

                            <td>
                                <a href="detail.php?id=<?= $p['idpenjualan'] ?>" class="btn btn-outline-primary btn-sm">Detail</a>
                                <a href="delete.php?id=<?= $p['idpenjualan'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted fst-italic">Tidak ada data penjualan</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>

</div>

</body>
</html>
