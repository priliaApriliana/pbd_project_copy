<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

// Gunakan class Pengadaan
require_once(__DIR__ . '/../../classes/Pengadaan.php');
$pengadaanObj = new Pengadaan();

// Ambil filter status
$status = $_GET['status'] ?? 'all';
$validStatus = ['all', 'pending', 'selesai', 'revisi', 'cancel'];
$status = in_array($status, $validStatus) ? $status : 'all';

// Ambil data
$data = $pengadaanObj->getFiltered($status);

// Label dropdown
$statusLabels = [
    'all' => 'Semua',
    'pending' => 'Pending',
    'selesai' => 'Selesai',
    'revisi' => 'Revisi',
    'cancel' => 'Cancel'
];
$label = $statusLabels[$status] ?? 'Semua';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Pengadaan Barang | Sistem Farmasi</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/list-table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container-fluid">

    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
      <h1 class="page-title">
        <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" width="42">
        Daftar Pengadaan Barang
      </h1>

      <div class="d-flex gap-3 flex-wrap">

        <!-- Search -->
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="searchInput" class="form-control" placeholder="Cari kode, vendor, atau petugas...">
        </div>

        <!-- Dropdown Filter -->
        <div class="dropdown">
          <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-funnel"></i> <?= $label ?>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item <?= $status=='all'?'active':'' ?>" href="?status=all">Semua</a></li>
            <li><a class="dropdown-item <?= $status=='pending'?'active':'' ?>" href="?status=pending">Pending</a></li>
            <li><a class="dropdown-item <?= $status=='selesai'?'active':'' ?>" href="?status=selesai">Selesai</a></li>
            <li><a class="dropdown-item <?= $status=='revisi'?'active':'' ?>" href="?status=revisi">Revisi</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item <?= $status=='cancel'?'active':'' ?>" href="?status=cancel">Cancel</a></li>
          </ul>
        </div>

      </div>
    </div>

    <!-- Alerts -->
    <?php if(isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Tombol Tambah -->
    <div class="mb-4">
      <a href="add.php" class="btn-add">
        <i class="bi bi-plus-circle-fill"></i> Tambah Pengadaan
      </a>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i> Daftar Pengadaan</h5>
        <span class="badge bg-light text-dark fs-6"><?= count($data) ?> item</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle mb-0" id="pengadaanTable">
            <thead>
              <tr>
                <th>KODE</th>
                <th>TANGGAL</th>
                <th>VENDOR</th>
                <th>PETUGAS</th>
                <th>SUBTOTAL</th>
                <th>PPN</th>
                <th>TOTAL</th>
                <th>STATUS</th>
                <th class="text-center">AKSI</th>
              </tr>
            </thead>

            <tbody>
              <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                  <tr>

                    <td><strong class="text-success"><?= $row['kode_pengadaan'] ?></strong></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pengadaan'])) ?></td>
                    <td class="fw-medium"><?= $row['nama_vendor'] ?></td>
                    <td><?= $row['petugas'] ?></td>

                    <td>Rp <?= number_format($row['subtotal_nilai'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($row['ppn'], 0, ',', '.') ?></td>
                    <td class="fw-bold">Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></td>

                    <td>
                      <?php
                        switch ($row['status']) {
                          case 'P': echo '<span class="status pending">Pending</span>'; break;
                          case 'S': echo '<span class="status aktif">Selesai</span>'; break;
                          case 'R': echo '<span class="status revisi">Revisi</span>'; break;
                          case 'C': echo '<span class="status nonaktif">Cancel</span>'; break;
                        }
                      ?>
                    </td>

                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">

                        <!-- DETAIL -->
                        <a href="detail.php?id=<?= $row['kode_pengadaan'] ?>" class="action-btn btn-view">
                          <i class="bi bi-eye-fill"></i>
                        </a>

                        <!-- CANCEL (hanya jika belum Cancel) -->
                        <?php if ($row['status'] !== 'C'): ?>
                          <a href="cancel.php?id=<?= $row['kode_pengadaan'] ?>"
                             class="action-btn btn-cancel"
                             onclick="return confirm('Yakin ingin membatalkan pengadaan <?= $row['kode_pengadaan'] ?>?')">
                            <i class="bi bi-x-circle-fill"></i>
                          </a>
                        <?php endif; ?>

                      </div>
                    </td>

                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1"></i><br>
                    Belum ada data pengadaan.
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

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/table-search.js"></script>

<!-- Custom Cancel Button Style -->
<style>
.btn-cancel {
  background: #fce4ec;
  color: #c2185b;
  border: 1px solid #f8bbd0;
}
.btn-cancel:hover {
  background: #e91e63;
  color: white;
  border-color: #e91e63;
  transform: translateY(-2px);
}
</style>

</body>
</html>
