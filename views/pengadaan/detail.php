<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/Pengadaan.php");
$pengadaan = new Pengadaan();

$idpengadaan = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idpengadaan <= 0) {
    echo "<h4 style='color:red;text-align:center;margin-top:50px;'>❌ ID Pengadaan tidak valid!</h4>";
    exit();
}

$details = $pengadaan->getDetailById($idpengadaan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Pengadaan</title>

<!-- FONT + ICON -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CUSTOM THEME -->
<link rel="stylesheet" href="../../assets/style/dashboard.css">
<link rel="stylesheet" href="../../assets/style/list-table.css">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%);
    }

    .main-container {
        margin-left: 280px;
        padding: 30px 40px;
    }

    .detail-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 35px rgba(0,0,0,0.08);
        border: 1px solid rgba(76,175,80,0.2);
    }

    .detail-title {
        font-size: 28px;
        font-weight: 700;
        color: #1e6b2a;
        margin-bottom: 25px;
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .detail-table th {
        background: #e8f5e8 !important;
        color: #256029;
        text-transform: uppercase;
        font-size: 13px;
        border-color: #c8e6c9 !important;
        font-weight: 600;
    }

    .detail-table tbody tr:hover {
        background: #f1f8f4;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .detail-table td {
        border-color: #e0e0e0 !important;
        vertical-align: middle;
    }

    .tfoot-total td {
        font-size: 15px;
        font-weight: 600;
        color: #1b5e20;
        background: #f3faf4 !important;
    }

    .btn-back {
        background: #2e7d32;
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-back:hover {
        background: #1b5e20;
        color: white;
        transform: translateY(-2px);
    }
</style>

</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-container">
    <div class="detail-card">

        <h2 class="detail-title">
            <i class="bi bi-journal-text"></i>
            Detail Pengadaan #<?= htmlspecialchars($idpengadaan) ?>
        </h2>

        <?php if (!empty($details)): ?>

        <div class="table-responsive">
        <table class="table table-bordered detail-table align-middle">
            <thead class="text-center">
                <tr>
                    <th>Kode Detail</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
            <?php 
                $subtotal = 0;
                foreach ($details as $row):
                    $subtotal += $row['sub_total'];
            ?>
                <tr>
                    <td class="text-center"><?= $row['iddetail_pengadaan'] ?></td>
                    <td class="text-center"><?= $row['idbarang'] ?></td>
                    <td><?= $row['nama_barang'] ?></td>
                    <td class="text-center"><?= $row['nama_satuan'] ?></td>
                    <td class="text-end"><?= number_format($row['jumlah']) ?></td>
                    <td class="text-end">Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                    <td class="text-end fw-bold">Rp <?= number_format($row['sub_total'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

            <tfoot class="tfoot-total">
                <?php 
                    $ppn = round($subtotal * 0.10);
                    $total = $subtotal + $ppn;
                ?>
                <tr>
                    <td colspan="6" class="text-end">Subtotal</td>
                    <td class="text-end">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-end">PPN (10%)</td>
                    <td class="text-end">Rp <?= number_format($ppn, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-end text-success">Total Nilai Pengadaan</td>
                    <td class="text-end text-success">Rp <?= number_format($total, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
        </div>

        <?php else: ?>
            <div class="alert alert-warning text-center">
                ⚠️ Tidak ada detail barang dalam pengadaan ini.
            </div>
        <?php endif; ?>

        <div class="text-end mt-4">
            <a href="list.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Pengadaan</a>
        </div>

    </div>
</div>

</body>
</html>
