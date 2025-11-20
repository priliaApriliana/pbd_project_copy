<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Vendor.php");
$vendorObj = new Vendor();

$show = $_GET['show'] ?? 'aktif';
$data = $vendorObj->getAll($show);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Vendor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div class="page-header">
        <img src="https://cdn-icons-png.flaticon.com/512/3209/3209265.png" width="36" style="margin-right:8px;">
        <h2>Data Vendor</h2>
      </div>

      <div class="dropdown">
        <button class="btn btn-light border dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
          <?= $show === 'all' ? 'Semua Vendor' : ($show === 'nonaktif' ? 'Vendor Nonaktif' : 'Vendor Aktif'); ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item <?= $show === 'all' ? 'active' : '' ?>" href="?show=all">Semua Vendor</a></li>
        <li><a class="dropdown-item <?= $show === 'aktif' ? 'active' : '' ?>" href="?show=aktif">Vendor Aktif</a></li>
        </ul>
      </div>
    </div>

    <a href="add.php" class="btn-add w-100 mb-3">+ Tambah Vendor</a>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Vendor</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $row): ?>
            <tr>
              <td><?= $row['kode_vendor'] ?></td>
              <td><?= htmlspecialchars($row['nama_vendor']) ?></td>
              <td><?= htmlspecialchars($row['jenis_vendor']) ?></td>
              <td><span class="status <?= $row['status_vendor'] === 'Aktif' ? 'aktif' : 'nonaktif' ?>"><?= $row['status_vendor'] ?></span></td>
              <td>
                <a href="edit.php?id=<?= $row['kode_vendor'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['kode_vendor'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus vendor ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($data)): ?>
            <tr><td colspan="5" class="no-data">Belum ada data vendor.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
