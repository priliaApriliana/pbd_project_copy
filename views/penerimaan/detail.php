<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../config/DBConnection.php");
$db = new DBConnection();
$conn = $db->getConnection();

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: list.php");
    exit();
}

// Ambil data penerimaan
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

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Tema -->
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/list-table.css">

  <style>
    .card-header-custom {
      background: #1f8f40;
      color: white;
      font-weight: 600;
      padding: 15px 20px;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    .section-title {
      background: #0d6efd;
      color: white;
      padding: 14px 20px;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
      font-size: 18px;
      font-weight: 600;
    }

    .btn-print {
      background: #0d6efd;
      color: white;
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: 500;
    }
    .btn-print:hover {
      background: #0b5cd6;
      color: white;
    }
  </style>

</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container-fluid">

    <!-- Tombol Kembali -->
    <div class="mb-3">
      <a href="list.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
      </a>
    </div>

    <!-- INFORMASI PENERIMAAN -->
    <div class="card mb-4 shadow-sm rounded-4">
      <div class="card-header-custom">
        <i class="bi bi-box-seam"></i> Informasi Penerimaan
      </div>

      <div class="card-body">

        <div class="row">
          <div class="col-md-6">
            <table class="table table-borderless">
              <tr>
                <td class="fw-bold" width="200">Kode Penerimaan:</td>
                <td><?= $penerimaan['kode_penerimaan'] ?></td>
              </tr>
              <tr>
                <td class="fw-bold">Kode Pengadaan:</td>
                <td><?= $penerimaan['kode_pengadaan'] ?></td>
              </tr>
              <tr>
                <td class="fw-bold">Vendor:</td>
                <td><?= $penerimaan['vendor'] ?></td>
              </tr>
              <tr>
                <td class="fw-bold">Petugas:</td>
                <td><?= $penerimaan['petugas'] ?></td>
              </tr>
            </table>
          </div>

          <div class="col-md-6">
            <table class="table table-borderless">
              <tr>
                <td class="fw-bold">Tanggal Penerimaan:</td>
                <td><?= date('d-m-Y H:i', strtotime($penerimaan['tanggal_penerimaan'])) ?></td>
              </tr>
              <tr>
                <td class="fw-bold">Status:</td>
                <td>
                  <?php
                    switch ($penerimaan['status']) {
                      case 'P': echo '<span class="status pending">Sebagian Diterima</span>'; break;
                      case 'S': echo '<span class="status aktif">Selesai</span>'; break;
                      case 'R': echo '<span class="status revisi">Revisi</span>'; break;
                      case 'C': echo '<span class="status nonaktif">Batal</span>'; break;
                    }
                  ?>
                </td>
              </tr>
            </table>
          </div>
        </div>

      </div>
    </div>

    <!-- DETAIL BARANG DITERIMA -->
    <div class="card shadow-sm rounded-4 mb-4">
      <div class="section-title">
        <i class="bi bi-list-ul"></i> Detail Barang Diterima
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">

          <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-center">
              <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
              </tr>
            </thead>

            <tbody>
              <?php $no = 1; foreach ($details as $d): ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $d['nama_barang'] ?></td>
                <td class="text-center"><?= $d['nama_satuan'] ?></td>
                <td class="text-end"><?= number_format($d['jumlah_terima']) ?></td>
                <td class="text-end">Rp <?= number_format($d['harga_satuan_terima']) ?></td>
                <td class="text-end fw-bold">Rp <?= number_format($d['sub_total_terima']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>

            <tfoot class="table-light">
              <tr>
                <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                <td class="text-end text-success fw-bold fs-5">Rp <?= number_format($total) ?></td>
              </tr>
            </tfoot>

          </table>

        </div>
      </div>
    </div>

    <!-- TOMBOL CETAK -->
    <div class="d-flex justify-content-end mt-3">
      <button onclick="window.print()" class="btn-print">
        <i class="bi bi-printer"></i> Cetak
      </button>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
