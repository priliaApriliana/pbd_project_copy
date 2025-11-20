<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/MarginPenjualan.php");
$marginObj = new MarginPenjualan();

// filter hanya all & aktif
$show = $_GET['show'] ?? 'all';
if ($show !== 'aktif' && $show !== 'all') {
    $show = 'all';
}

$data = $marginObj->getAll($show);

// label tombol
$label = ($show === 'aktif') ? 'Margin Aktif' : 'Semua Margin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Margin Penjualan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container">

    <div class="d-flex justify-content-between align-items-start mb-3">
      <div class="page-header d-flex align-items-center">
        <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" width="36" style="margin-right:8px;">
        <h2>Data Margin Penjualan</h2>
      </div>

      <!-- FILTER -->
      <div class="dropdown">
        <button class="btn btn-light border dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
          <?= $label ?>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item <?= $show === 'all' ? 'active' : '' ?>" href="?show=all">Semua Margin</a></li>
          <li><a class="dropdown-item <?= $show === 'aktif' ? 'active' : '' ?>" href="?show=aktif">Margin Aktif</a></li>
        </ul>
      </div>
    </div>

    <a href="add.php" class="btn-add w-100 mb-3">+ Tambah Margin</a>

    <!-- TABLE -->
    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID Margin</th>
            <th>Persen</th>
            <th>Dibuat Oleh</th>
            <th>Created</th>
            <th>Updated</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($data as $row): ?>
            <tr>
              <td><?= $row['kode_margin'] ?></td>
              <td><?= $row['persen_margin'] ?>%</td>
              <td><?= $row['dibuat_oleh'] ?></td>
              <td><?= $row['tanggal_dibuat'] ?></td>
              <td><?= $row['tanggal_diupdate'] ?></td>

              <td>
                <span class="status <?= $row['status_margin'] === 'Aktif' ? 'aktif' : 'nonaktif' ?>">
                  <?= $row['status_margin'] ?>
                </span>
              </td>

              <td>
                <a href="edit.php?id=<?= $row['kode_margin'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['kode_margin'] ?>" 
                class="btn-delete"
                onclick="return confirm('Yakin ingin menghapus margin ini?')">
                Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (empty($data)): ?>
            <tr><td colspan="7" class="no-data">Belum ada data margin penjualan.</td></tr>
          <?php endif; ?>
        </tbody>

      </table>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
