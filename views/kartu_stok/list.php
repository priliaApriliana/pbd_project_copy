<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . '/../../config/DBConnection.php');
$db = new DBConnection();
$conn = $db->getConnection();

$query = "SELECT * FROM v_kartu_stok_detail ORDER BY tanggal DESC";
$result = $conn->query($query);
$data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kartu Stok Barang</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Custom Theme -->
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/list-table.css">

  <style>
    .badge-trans {
      padding: 6px 15px;
      font-size: 12px;
      border-radius: 20px;
      font-weight: 600;
    }
    .badge-penerimaan { background:#c8e6c9; color:#1b5e20; }
    .badge-penjualan  { background:#ffebee; color:#c62828; }
    .badge-retur      { background:#fff3cd; color:#8a6d3b; }
  </style>
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
      <h1 class="page-title">
        <img src="https://cdn-icons-png.flaticon.com/512/2331/2331943.png" width="42">
        Kartu Stok Barang
      </h1>

      <div class="d-flex gap-3 flex-wrap">

        <!-- Search -->
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="searchInput" class="form-control" placeholder="Cari barang, transaksi, tanggal...">
        </div>

        <!-- Back -->
        <a href="../dashboard.php" class="btn btn-outline-success" style="height:48px; border-radius:14px;">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>

      </div>
    </div>

    <!-- CARD TABLE -->
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i> Riwayat Kartu Stok</h5>
        <span class="badge bg-light text-dark fs-6"><?= count($data) ?> item</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle mb-0" id="kartuStokTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>TANGGAL</th>
                <th>BARANG</th>
                <th>SATUAN</th>
                <th>TRANSAKSI</th>
                <th>MASUK</th>
                <th>KELUAR</th>
                <th>STOK AKHIR</th>
              </tr>
            </thead>

            <tbody>
              <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                  <tr>
                    <td><strong class="text-success"><?= $row['idstok'] ?></strong></td>

                    <td><?= $row['tanggal'] ?></td>

                    <td class="fw-medium"><?= htmlspecialchars($row['nama_barang']) ?></td>

                    <td><?= htmlspecialchars($row['nama_satuan']) ?></td>

                    <td>
                      <?php if ($row['transaksi'] == 'Penerimaan'): ?>
                        <span class="badge-trans badge-penerimaan">Penerimaan</span>

                      <?php elseif ($row['transaksi'] == 'Penjualan'): ?>
                        <span class="badge-trans badge-penjualan">Penjualan</span>

                      <?php elseif ($row['transaksi'] == 'Retur Barang'): ?>
                        <span class="badge-trans badge-retur">Retur Barang</span>

                      <?php endif; ?>
                    </td>

                    <td class="text-success fw-bold">
                      <?= $row['masuk'] > 0 ? $row['masuk'] : '-' ?>
                    </td>

                    <td class="text-danger fw-bold">
                      <?= $row['keluar'] > 0 ? $row['keluar'] : '-' ?>
                    </td>

                    <td class="fw-bold"><?= $row['stok_akhir'] ?></td>

                  </tr>
                <?php endforeach; ?>

              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1"></i><br>
                    Belum ada riwayat kartu stok.
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

<!-- SEARCH FILTER -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#kartuStokTable tbody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>
