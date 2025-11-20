<?php
session_start();

if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualan = new Penjualan();
$dataPenjualan = $penjualan->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penjualan</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Style -->
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">

    <div class="card p-4 shadow-sm">

        <!-- Header Title + Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">💲 Data Penjualan</h4>

            <a href="add.php" class="btn btn-primary btn-sm">
                + Tambah Penjualan
            </a>
        </div>

        <!-- Table -->
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
                        <td><?= htmlspecialchars($p['idpenjualan']) ?></td>
                        <td><?= htmlspecialchars($p['tanggal']) ?></td>
                        <td><?= htmlspecialchars($p['kasir']) ?></td>

                        <td class="text-end">Rp <?= number_format($p['subtotal'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($p['ppn_persen']) ?>%</td>
                        <td class="fw-bold text-end">Rp <?= number_format($p['total'], 0, ',', '.') ?></td>

                        <td>
                            <a href="detail.php?id=<?= $p['idpenjualan'] ?>"
                               class="btn btn-outline-primary btn-sm">
                               📄 Detail
                            </a>

                            <a href="delete.php?id=<?= $p['idpenjualan'] ?>"
                               onclick="return confirm('Yakin ingin menghapus data ini?')"
                               class="btn btn-danger btn-sm">
                               🗑 Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted fst-italic">Tidak ada data penjualan</td></tr>
            <?php endif; ?>
            </tbody>

        </table>

    </div>
</div>

</body>
</html>
