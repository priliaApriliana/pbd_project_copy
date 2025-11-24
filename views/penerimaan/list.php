<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../config/DBConnection.php");
$db = new DBConnection();
$conn = $db->getConnection();

$status = $_GET['status'] ?? 'all';

// Ambil data penerimaan via Stored Procedure
$stmt = $conn->prepare("CALL sp_get_penerimaan(?)");
$stmt->bind_param("s", $status);
$stmt->execute();
$result = $stmt->get_result();
$data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Filter manual
if ($status === 'proses') {
    $data = array_filter($data, fn($row) => $row['status'] === 'P');
} elseif ($status === 'selesai') {
    $data = array_filter($data, fn($row) => $row['status'] === 'S');
}

$stmt->close();
$conn->next_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Penerimaan Barang</title>

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

    <!-- HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
      <h1 class="page-title">
        <img src="https://cdn-icons-png.flaticon.com/512/679/679720.png" width="42">
        Daftar Penerimaan Barang
      </h1>

      <div class="d-flex gap-3">

        <!-- FILTER STATUS -->
        <div class="dropdown">
          <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-funnel"></i>
            <?= $status == 'all' ? 'Semua' : ($status == 'proses' ? 'Sebagian Diterima' : 'Selesai') ?>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item <?= $status=='all'?'active':'' ?>" href="?status=all">Semua</a></li>
            <li><a class="dropdown-item <?= $status=='proses'?'active':'' ?>" href="?status=proses">Sebagian Diterima</a></li>
            <li><a class="dropdown-item <?= $status=='selesai'?'active':'' ?>" href="?status=selesai">Selesai</a></li>
          </ul>
        </div>

      </div>
    </div>

    <!-- TOMBOL TAMBAH -->
    <div class="mb-4">
      <a href="add.php" class="btn-add">
        <i class="bi bi-plus-circle-fill"></i> Tambah Penerimaan
      </a>
    </div>

    <!-- CARD TABLE -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i> Daftar Penerimaan</h5>
        <span class="badge bg-light text-dark fs-6"><?= count($data) ?> item</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">

          <table class="table table-hover table-bordered align-middle mb-0">
            <thead>
              <tr>
                <th>PENERIMAAN</th>
                <th>PENGADAAN</th>
                <th>VENDOR</th>
                <th>PETUGAS</th>
                <th>STATUS</th>
                <th>TANGGAL</th>
                <th class="text-center">AKSI</th>
              </tr>
            </thead>

            <tbody>
              <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                  <tr>

                    <td><strong class="text-success"><?= $row['kode_penerimaan'] ?></strong></td>
                    <td><?= $row['kode_pengadaan'] ?></td>
                    <td><?= $row['vendor'] ?></td>
                    <td><?= $row['petugas'] ?></td>

                    <td>
                      <?php
                      switch ($row['status']) {
                        case 'P': echo '<span class="status pending">Sebagian Diterima</span>'; break;
                        case 'S': echo '<span class="status aktif">Selesai</span>'; break;
                        case 'R': echo '<span class="status revisi">Revisi</span>'; break;
                        case 'C': echo '<span class="status nonaktif">Batal</span>'; break;
                      }
                      ?>
                    </td>

                    <td><?= $row['tanggal_penerimaan'] ?></td>

                    <td class="text-center">
                      <a href="detail.php?id=<?= $row['kode_penerimaan'] ?>" class="action-btn btn-view">
                        <i class="bi bi-eye-fill"></i>
                      </a>
                    </td>

                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1"></i><br>
                    Belum ada data penerimaan.
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
