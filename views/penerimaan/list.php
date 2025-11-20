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
$stmt->close();
$conn->next_result();

// Ambil detail penerimaan jika diminta
$details = [];
if (isset($_GET['detail']) && !empty($_GET['detail'])) {
    $idp = $_GET['detail'];

    $stmt = $conn->prepare("CALL sp_get_detail_penerimaan(?)");
    $stmt->bind_param("i", $idp);
    $stmt->execute();
    $res = $stmt->get_result();
    $details = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    $conn->next_result();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Penerimaan Barang</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom -->
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">

  <div class="card p-4 shadow-sm">

    <!-- Judul + Tombol Tambah -->
    <h4 class="fw-bold mb-3">📦 Daftar Penerimaan Barang</h4>

    <!-- Tombol Notifikasi -->
    <?php if(isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- FILTER STATUS + ADD BUTTON -->
    <form method="get" class="mb-3 position-relative">
      <label class="fw-bold me-2">Filter Status:</label>
      <select name="status" class="form-select w-auto d-inline">
        <option value="all"      <?= $status == 'all' ? 'selected' : '' ?>>Semua</option>
        <option value="aktif"    <?= $status == 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= $status == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
      </select>

      <button class="btn btn-success btn-sm ms-2">Tampilkan</button>

      <!-- 🔥 Tombol di kanan seperti Pengadaan -->
      <a href="add.php" class="btn btn-primary btn-sm float-end">+ Tambah Penerimaan</a>
    </form>

    <!-- TABLE UTAMA -->
    <table class="table table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th>Penerimaan</th>
          <th>Pengadaan</th>
          <th>Vendor</th>
          <th>Petugas</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Detail</th>
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php if (!empty($data)): ?>
          <?php foreach ($data as $row): ?>
            <tr>
              <td class="text-center"><?= $row['kode_penerimaan'] ?></td>
              <td class="text-center"><?= $row['kode_pengadaan'] ?></td>
              <td><?= $row['vendor'] ?></td>
              <td><?= $row['petugas'] ?></td>

              <td class="text-center">
                <?php
                  switch ($row['status']) {
                      case 'P': echo '<span class="badge bg-warning text-dark">Pending</span>'; break;
                      case 'S': echo '<span class="badge bg-success">Selesai</span>'; break;
                      case 'R': echo '<span class="badge bg-danger">Revisi</span>'; break;
                      case 'C': echo '<span class="badge bg-secondary">Batal</span>'; break;
                      default: echo '<span class="badge bg-light text-dark">Tidak Diketahui</span>';
                  }
                ?>
              </td>

              <td class="text-center"><?= $row['tanggal_penerimaan'] ?></td>

              <td class="text-center">
                <a href="?detail=<?= $row['kode_penerimaan'] ?>"
                   class="btn btn-outline-primary btn-sm">Lihat</a>
              </td>

              <td class="text-center">
                <a href="edit.php?id=<?= $row['kode_penerimaan'] ?>"
                   class="btn btn-warning btn-sm">Edit</a>

                <a href="delete.php?id=<?= $row['kode_penerimaan'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>

        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted fst-italic">Belum ada data penerimaan.</td></tr>
        <?php endif; ?>
      </tbody>

    </table>

  </div>

  <!-- DETAIL SECTION -->
  <?php if (!empty($details)): ?>
    <div class="card p-4 shadow-sm mt-4">
      <h5 class="fw-bold mb-3">📑 Detail Barang Diterima</h5>

      <table class="table table-striped align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>ID Detail</th>
            <th>Barang</th>
            <th>Satuan</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Subtotal</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($details as $d): ?>
            <tr>
              <td class="text-center"><?= $d['iddetail_penerimaan'] ?></td>
              <td><?= $d['nama_barang'] ?></td>
              <td class="text-center"><?= $d['nama_satuan'] ?></td>
              <td class="text-end"><?= number_format($d['jumlah_terima']) ?></td>
              <td class="text-end">Rp <?= number_format($d['harga_satuan_terima']) ?></td>
              <td class="text-end fw-bold">Rp <?= number_format($d['sub_total_terima']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>

  <?php elseif (isset($_GET['detail'])): ?>
    <div class="alert alert-warning mt-3">⚠ Detail tidak ditemukan.</div>
  <?php endif; ?>

</div>

</body>
</html>
