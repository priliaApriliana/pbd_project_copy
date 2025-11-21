<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../config/DBConnection.php");
$db = new DBConnection();
$conn = $db->getConnection();

// Ambil ID dari parameter
$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: list.php");
    exit();
}

// Ambil data penerimaan utama
$stmt = $conn->prepare("CALL sp_get_penerimaan_by_id(?)");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$penerimaan = $result ? $result->fetch_assoc() : null;
$stmt->close();
$conn->next_result();

if (!$penerimaan) {
    $_SESSION['error_message'] = "Data penerimaan tidak ditemukan!";
    header("Location: list.php");
    exit();
}

// Ambil detail barang
$stmt = $conn->prepare("CALL sp_get_detail_penerimaan(?)");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$details = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
$conn->next_result();

// Hitung total
$total = 0;
foreach ($details as $d) {
    $total += $d['sub_total_terima'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Penerimaan Barang</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/detail.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container-fluid py-4" style="margin-left:260px; padding-right: 30px;">

  <!-- Tombol Kembali -->
  <div class="mb-3">
    <a href="list.php" class="btn btn-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
  </div>

  <!-- CARD INFORMASI PENERIMAAN -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0"><i class="bi bi-box-seam"></i> Informasi Penerimaan</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-borderless">
            <tr>
              <td width="200" class="fw-bold">Kode Penerimaan:</td>
              <td><?= htmlspecialchars($penerimaan['kode_penerimaan']) ?></td>
            </tr>
            <tr>
              <td class="fw-bold">Kode Pengadaan:</td>
              <td><?= htmlspecialchars($penerimaan['kode_pengadaan']) ?></td>
            </tr>
            <tr>
              <td class="fw-bold">Vendor:</td>
              <td><?= htmlspecialchars($penerimaan['vendor']) ?></td>
            </tr>
            <tr>
              <td class="fw-bold">Petugas:</td>
              <td><?= htmlspecialchars($penerimaan['petugas']) ?></td>
            </tr>
          </table>
        </div>
        
        <div class="col-md-6">
          <table class="table table-borderless">
            <tr>
              <td width="200" class="fw-bold">Tanggal Penerimaan:</td>
              <td><?= date('d-m-Y H:i', strtotime($penerimaan['tanggal_penerimaan'])) ?></td>
            </tr>
            <tr>
              <td class="fw-bold">Status:</td>
              <td>
                <?php
                  switch ($penerimaan['status']) {
                      case 'P': echo '<span class="badge bg-warning text-dark">Pending</span>'; break;
                      case 'S': echo '<span class="badge bg-success">Selesai</span>'; break;
                      case 'R': echo '<span class="badge bg-danger">Revisi</span>'; break;
                      case 'C': echo '<span class="badge bg-secondary">Batal</span>'; break;
                      default: echo '<span class="badge bg-light text-dark">Tidak Diketahui</span>';
                  }
                ?>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- CARD DETAIL BARANG -->
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-list-ul"></i> Detail Barang Diterima</h5>
    </div>
    <div class="card-body p-0">
      
      <?php if (!empty($details)): ?>
        <div class="table-responsive">
          <table class="table table-hover table-striped align-middle mb-0">
            <thead class="table-light text-center">
              <tr>
                <th width="60">No</th>
                <th>Nama Barang</th>
                <th width="100">Satuan</th>
                <th width="100">Jumlah</th>
                <th width="150">Harga Satuan</th>
                <th width="180">Subtotal</th>
              </tr>
            </thead>

            <tbody>
              <?php $no = 1; ?>
              <?php foreach ($details as $d): ?>
                <tr>
                  <td class="text-center"><?= $no++ ?></td>
                  <td><?= htmlspecialchars($d['nama_barang']) ?></td>
                  <td class="text-center"><?= htmlspecialchars($d['nama_satuan']) ?></td>
                  <td class="text-end"><?= number_format($d['jumlah_terima'], 0, ',', '.') ?></td>
                  <td class="text-end">Rp <?= number_format($d['harga_satuan_terima'], 0, ',', '.') ?></td>
                  <td class="text-end fw-bold">Rp <?= number_format($d['sub_total_terima'], 0, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>

            <tfoot class="table-light">
              <tr>
                <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                <td class="text-end fw-bold text-success fs-5">
                  Rp <?= number_format($total, 0, ',', '.') ?>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

      <?php else: ?>
        <div class="alert alert-warning text-center">
          <i class="bi bi-exclamation-triangle"></i> Tidak ada detail barang untuk penerimaan ini.
        </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- TOMBOL AKSI -->
  <div class="mt-4 d-flex justify-content-between">
    <a href="list.php" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
    
    <div>
      <a href="edit.php?id=<?= htmlspecialchars($penerimaan['kode_penerimaan']) ?>" 
         class="btn btn-warning me-2">
        <i class="bi bi-pencil"></i> Edit
      </a>
      
      <button class="btn btn-primary" onclick="window.print()">
        <i class="bi bi-printer"></i> Cetak
      </button>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom CSS untuk Fix Layout -->
<style>
/* Fix container agar tidak terpotong */
body {
  overflow-x: hidden;
}

.container-fluid {
  max-width: calc(100vw - 290px);
}

/* Table responsive */
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.table {
  min-width: 100%;
  white-space: nowrap;
}

/* Print Styling */
@media print {
  body * {
    visibility: hidden;
  }
  .card, .card * {
    visibility: visible;
  }
  .card {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .btn, .sidebar {
    display: none !important;
  }
}
</style>

</body>
</html>