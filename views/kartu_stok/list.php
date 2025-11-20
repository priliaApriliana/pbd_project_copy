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

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">

<style>
    body {
      background: #f4f6f9;
      font-family: "Poppins", sans-serif;
    }
    .main-content {
      margin-left: 250px;
      padding: 30px 40px;
    }
    .search-bar-wrapper {
      display: flex;
      gap: 10px;
    }
    .search-box {
      width: 260px;
      border-radius: 10px;
      padding: 8px 12px;
      font-size: 14px;
      border: 1px solid #ced4da;
    }
    @media(max-width: 768px) {
      .main-content { margin-left: 0; padding: 20px; }
      .search-bar-wrapper { flex-direction: column; }
    }
</style>
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">

  <div class="card p-4">
    
    <!-- HEADER + SEARCH -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>📦 Kartu Stok Barang</h4>

      <div class="search-bar-wrapper">
        <!-- 🔍 SEARCH BOX -->
        <input 
          type="text" 
          id="searchInput" 
          class="search-box"
          placeholder="Cari barang / transaksi / tanggal ..."
        >

        <!-- BACK BUTTON -->
        <a href="../dashboard.php" class="btn btn-outline-success">
          ⬅ Kembali ke Dashboard
        </a>
      </div>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
      <table id="stokTable" class="table table-bordered table-hover align-middle text-center">
        <thead class="table-success">
          <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Barang</th>
            <th>Satuan</th>
            <th>Transaksi</th>
            <th>Masuk</th>
            <th>Keluar</th>
            <th>Stok Akhir</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($data)): ?>
            <?php foreach ($data as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['idstok']) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>

                <td class="text-start ps-3"><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= htmlspecialchars($row['nama_satuan']) ?></td>

                <td>
                  <?php if ($row['transaksi'] === 'Penerimaan'): ?>
                    <span class="badge bg-success">Penerimaan</span>
                  <?php elseif ($row['transaksi'] === 'Penjualan'): ?>
                    <span class="badge bg-danger">Penjualan</span>
                  <?php elseif ($row['transaksi'] === 'Retur Barang'): ?>
                    <span class="badge bg-warning text-dark">Retur Barang</span>
                  <?php endif; ?>
                </td>

                <td class="text-success fw-semibold">
                  <?= $row['masuk'] > 0 ? $row['masuk'] : '-' ?>
                </td>

                <td class="text-danger fw-semibold">
                  <?= $row['keluar'] > 0 ? $row['keluar'] : '-' ?>
                </td>

                <td class="fw-bold"><?= $row['stok_akhir'] ?></td>
              </tr>
            <?php endforeach; ?>

          <?php else: ?>
              <tr><td colspan="8" class="text-muted">Belum ada data kartu stok</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<!-- 🔍 SEARCH FILTER SCRIPT -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#stokTable tbody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>
