<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . '/../../config/DBConnection.php');
$db = new DBConnection();
$conn = $db->getConnection();

// 1️⃣ Ambil Data Pengadaan dari View
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

if ($status === 'pending') {
    $query = "SELECT * FROM v_pengadaan_pending ORDER BY tanggal_pengadaan DESC";
} else {
    $query = "SELECT * FROM v_pengadaan_all ORDER BY tanggal_pengadaan DESC";
}

$result = $conn->query($query);
$data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Pengadaan Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">
  <div class="card p-4 shadow-sm">
    <h4 class="fw-bold mb-3">📋 Daftar Pengadaan Barang</h4>

    <!-- 🔔 Notifikasi -->
    <?php if(isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
      </div>
    <?php endif; ?>
    
    <!-- Filter Status -->
    <form method="get" class="mb-3">
      <label class="fw-bold me-2">Filter Status:</label>
      <select name="status" class="form-select w-auto d-inline">
        <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>Semua</option>
        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
      </select>
      <button class="btn btn-success btn-sm ms-2">Tampilkan</button>
      <a href="add.php" class="btn btn-primary btn-sm float-end">+ Tambah Pengadaan</a>
    </form>

    <!-- Tabel Data -->
    <table class="table table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th>Kode</th>
          <th>Tanggal</th>
          <th>Vendor</th>
          <th>Petugas</th>
          <th>Subtotal</th>
          <th>PPN (10%)</th>
          <th>Total</th>
          <th>Status</th>
          <th>Detail</th>
          <th>Aksi</th> <!-- 🔹 Tambahkan kolom Aksi -->
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($data)): ?>
          <?php foreach ($data as $row): ?>
          <tr>
            <td class="text-center"><?= htmlspecialchars($row['kode_pengadaan']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['tanggal_pengadaan']) ?></td>
            <td><?= htmlspecialchars($row['nama_vendor']) ?></td>
            <td><?= htmlspecialchars($row['petugas']) ?></td>
            <td class="text-end">Rp <?= number_format($row['subtotal_nilai'], 0, ',', '.') ?></td>
            <td class="text-end">Rp <?= number_format($row['ppn'], 0, ',', '.') ?></td>
            <td class="text-end fw-bold">Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></td>
            <td class="text-center">
              <?php
                switch ($row['status']) {
                  case 'P': 
                  case 'A': // 💥 TAMBAHKAN INI
                      echo '<span class="badge bg-warning text-dark">Pending</span>'; 
                      break;
                  case 'S': 
                      echo '<span class="badge bg-success">Selesai</span>'; 
                      break;
                  case 'R': 
                      echo '<span class="badge bg-danger">Revisi</span>'; 
                      break;
                  case 'C': 
                      echo '<span class="badge bg-secondary">Batal</span>'; 
                      break;
                  default: 
                      echo '<span class="badge bg-light text-dark">Tidak Diketahui</span>';
                  }
              ?>
            </td>
            <td class="text-center">
              <a href="detail.php?id=<?= htmlspecialchars($row['kode_pengadaan']) ?>" 
                 class="btn btn-outline-primary btn-sm">Lihat</a>
            </td>
            <td class="text-center">
              <!-- 🔹 Tombol Hapus -->
              <a href="delete.php?id=<?= htmlspecialchars($row['kode_pengadaan']) ?>" 
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Yakin ingin menghapus pengadaan ini?')">Hapus</a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-center text-muted fst-italic">Belum ada data pengadaan</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
