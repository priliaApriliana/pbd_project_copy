<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Barang.php");
$barangObj = new Barang();

// Ambil filter (default = aktif)
$show = $_GET['show'] ?? 'aktif';

// Normalisasi filter (hanya all | aktif)
if ($show !== 'all' && $show !== 'aktif') {
    $show = 'aktif';
}

$data = $barangObj->getAll($show);

// Label tombol
$label = ($show === 'all') ? 'Semua Barang' : 'Barang Aktif';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container">

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-start mb-3">

      <!-- Judul Halaman -->
      <div class="page-header">
        <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" width="36" style="margin-right:8px;">
        <h2>Data Barang</h2>
      </div>

      <!-- Dropdown Filter -->
      <div class="dropdown">
        <button class="btn btn-light border dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
          <?= $label ?>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item <?= $show === 'all' ? 'active' : '' ?>" href="?show=all">
              Semua Barang
            </a>
          </li>

          <li>
            <a class="dropdown-item <?= $show === 'aktif' ? 'active' : '' ?>" href="?show=aktif">
              Barang Aktif
            </a>
          </li>
        </ul>
      </div>

    </div>

    <!-- Tombol Tambah -->
    <a href="add.php" class="btn-add w-100 mb-3">+ Tambah Barang</a>

    <!-- TABLE -->
    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Jenis</th>
            <th>Nama</th>
            <th>Satuan</th>
            <th>Status</th>
            <th>Harga</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($data as $row): ?>
            <tr>
              <td><?= $row['kode_barang'] ?></td>
              <td><?= htmlspecialchars($row['kategori']) ?></td>
              <td><?= htmlspecialchars($row['nama_barang']) ?></td>

              <td><?= htmlspecialchars($row['satuan'] ?? '-') ?></td>

              <td>
                <span class="status <?= $row['status_barang'] === 'Aktif' ? 'aktif' : 'nonaktif' ?>">
                  <?= $row['status_barang'] ?>
                </span>
              </td>

              <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>

              <td>
                <a href="edit.php?id=<?= $row['kode_barang'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['kode_barang'] ?>" class="btn-delete"
                   onclick="return confirm('Yakin ingin menghapus?')">
                   Hapus
                </a>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (empty($data)): ?>
            <tr><td colspan="7" class="no-data">Belum ada data barang.</td></tr>
          <?php endif; ?>

        </tbody>
      </table>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
