<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Satuan.php");
$satuanObj = new Satuan();

// Ambil filter
$show = $_GET['show'] ?? 'all';  // default = all

// Normalisasi nilai show
if ($show !== 'aktif' && $show !== 'all') {
    $show = 'all';
}

// Ambil data dari view
$data = $satuanObj->getAll($show);

// Tentukan label tombol filter
$label = ($show === 'aktif') ? "Satuan Aktif" : "Semua Satuan";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Satuan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-3">

      <div class="page-header">
        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828919.png" width="36" alt="icon">
        <h2>Data Satuan</h2>
      </div>

      <!-- Dropdown Filter -->
      <div class="dropdown">
        <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <?= $label ?>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="?show=all">Semua Satuan</a></li>
          <li><a class="dropdown-item" href="?show=aktif">Satuan Aktif</a></li>
        </ul>
      </div>
    </div>

    <a href="add.php" class="btn-add">+ Tambah Satuan</a>

    <!-- Table -->
    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Satuan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($data as $row): ?>
            <tr>
              <td><?= $row['kode_satuan'] ?></td>
              <td><?= htmlspecialchars($row['nama_satuan']) ?></td>

              <td>
                <span class="status <?= $row['status_satuan'] === 'Aktif' ? 'aktif' : 'nonaktif' ?>">
                  <?= $row['status_satuan'] ?>
                </span>
              </td>

              <td>
                <a href="edit.php?id=<?= $row['kode_satuan'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['kode_satuan'] ?>"
                   class="btn-delete"
                   onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (empty($data)): ?>
            <tr><td colspan="4" class="no-data">Belum ada data satuan.</td></tr>
          <?php endif; ?>
        </tbody>

      </table>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
