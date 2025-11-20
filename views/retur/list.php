<?php
session_start();

if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/ReturBarang.php");
$retur = new ReturBarang();
$dataRetur = $retur->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Retur Barang</title>

    <!-- BOOSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CUSTOM STYLE -->
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">

    <div class="card p-4 shadow-sm">

        <!-- Header Title + Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">♻️ Data Retur Barang</h4>

            <a href="add.php" class="btn btn-primary btn-sm">
                + Tambah Retur
            </a>
        </div>

        <!-- Notifikasi -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= $_GET['success'] ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= $_GET['error'] ?></div>
        <?php endif; ?>

        <!-- TABLE -->
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th>ID RETUR</th>
                    <th>TANGGAL</th>
                    <th>ID PENERIMAAN</th>
                    <th>PETUGAS</th>
                    <th>OPSI</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($dataRetur)): ?>
                <?php foreach ($dataRetur as $r): ?>
                <tr class="text-center">
                    <td><?= htmlspecialchars($r['idretur']) ?></td>
                    <td><?= htmlspecialchars($r['tanggal']) ?></td>
                    <td><?= htmlspecialchars($r['idpenerimaan']) ?></td>
                    <td><?= htmlspecialchars($r['petugas']) ?></td>

                    <td class="text-center">
                        <a href="detail.php?id=<?= $r['idretur'] ?>"
                           class="btn btn-outline-info btn-sm">
                           📄 Detail
                        </a>

                        <a href="delete.php?id=<?= $r['idretur'] ?>"
                           onclick="return confirm('Yakin ingin menghapus retur ini?')"
                           class="btn btn-danger btn-sm">
                           🗑 Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted fst-italic">
                        Belum ada data retur barang.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
