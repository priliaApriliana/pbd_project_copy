<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: login.php");
  exit();
}
require_once(__DIR__. "/../classes/Dashboard.php");
$dash = new dashboard();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard PBD Modern</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/dashboard.css">
</head>
<body>

  <!-- SIDEBAR -->
  <?php include(__DIR__ . "/layout/sidebar.php"); ?>

  <!-- MAIN -->
  <div class="main">
    <div class="header">
      <div>
        <h2>Overview</h2>
        <p>Ringkasan data keseluruhan sistem</p>
      </div>
      <div class="header-right">
        <input type="text" placeholder="Cari data...">
        <button>+ Tambah</button>
      </div>
    </div>

    <!-- TOP CARDS -->
    <div class="top-cards">
      <div class="card green">
        <h6>My Balance</h6>
        <h3>Rp <?= number_format($dash->getTotalPenjualan(), 0, ',', '.') ?></h3>
        <p style="font-size: 0.8rem; opacity: 0.9;">Total Penjualan & Pengadaan</p>
      </div>

      <div class="card">
        <h6>Total User</h6>
        <h3><?= $dash->getCount('user') ?></h3>
        <p class="text-muted">User terdaftar</p>
      </div>

      <div class="card">
        <h6>Total Vendor</h6>
        <h3><?= $dash->getCount('vendor') ?></h3>
        <p class="text-muted">Vendor aktif</p>
      </div>
    </div>

    <!-- DATA SECTION -->
    <div class="data-section">
      <!-- Kiri -->
      <div class="data-card">
        <h5>🔥 Top 5 Barang Terjual</h5>
        <table class="table">
          <thead>
            <tr>
              <th>Nama Barang</th>
              <th class="text-center">Total Terjual</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($dash->getTopBarang() as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['nama']) ?></td>
                <td class="text-center fw-semibold"><?= $item['total_terjual'] ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($dash->getTopBarang()) === 0): ?>
              <tr><td colspan="2" class="text-center text-muted">Belum ada data penjualan.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Kanan -->
      <div class="data-card">
        <h5>📊 Cash Flow</h5>
        <div class="chart-placeholder">
          <div class="chart-bar" style="height: 30%;"></div>
          <div class="chart-bar" style="height: 60%;"></div>
          <div class="chart-bar" style="height: 80%;"></div>
          <div class="chart-bar" style="height: 50%;"></div>
          <div class="chart-bar" style="height: 40%;"></div>
          <div class="chart-bar" style="height: 70%;"></div>
        </div>
      </div>
    </div>

    <!-- RECENT ACTIVITY -->
    <div class="data-card" style="margin-top: 24px;">
      <h5>🕒 Aktivitas Terbaru</h5>
      <table class="table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Deskripsi</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>2025-10-25</td>
            <td>Menambahkan data vendor baru</td>
            <td><span class="text-success">Sukses</span></td>
          </tr>
          <tr>
            <td>2025-10-24</td>
            <td>Melakukan transaksi pengadaan</td>
            <td><span class="text-success">Sukses</span></td>
          </tr>
          <tr>
            <td>2025-10-23</td>
            <td>Memperbarui stok barang</td>
            <td><span class="text-muted">Pending</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
