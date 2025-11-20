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

// ambil data detail lewat stored procedure
$details = $pengadaan->getDetailById($idpengadaan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Pengadaan Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">
  <div class="card shadow-sm p-4">
    <h4 class="fw-bold mb-3">🧾 Detail Pengadaan #<?= htmlspecialchars($idpengadaan) ?></h4>

    <?php if (!empty($details)): ?>
    <table class="table table-bordered align-middle">
      <thead class="table-success text-center">
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
          <td class="text-center"><?= htmlspecialchars($row['iddetail_pengadaan']) ?></td>
          <td class="text-center"><?= htmlspecialchars($row['idbarang']) ?></td>
          <td><?= htmlspecialchars($row['nama_barang']) ?></td>
          <td class="text-center"><?= htmlspecialchars($row['nama_satuan']) ?></td>
          <td class="text-end"><?= number_format($row['jumlah']) ?></td>
          <td class="text-end">Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
          <td class="text-end fw-bold">Rp <?= number_format($row['sub_total'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot class="fw-bold">
        <?php 
          $ppn = round($subtotal * 0.10);
          $total = $subtotal + $ppn;
        ?>
        <tr class="table-light">
          <td colspan="6" class="text-end">Subtotal</td>
          <td class="text-end">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
        </tr>
        <tr class="table-light">
          <td colspan="6" class="text-end">PPN (10%)</td>
          <td class="text-end">Rp <?= number_format($ppn, 0, ',', '.') ?></td>
        </tr>
        <tr class="table-light text-success">
          <td colspan="6" class="text-end">Total Nilai Pengadaan</td>
          <td class="text-end">Rp <?= number_format($total, 0, ',', '.') ?></td>
        </tr>
      </tfoot>
    </table>
    <?php else: ?>
      <div class="alert alert-warning text-center">⚠️ Belum ada detail barang untuk pengadaan ini.</div>
    <?php endif; ?>

    <div class="text-end mt-3">
      <a href="list.php" class="btn btn-secondary">← Kembali ke Daftar Pengadaan</a>
    </div>
  </div>
</div>

</body>
</html>
