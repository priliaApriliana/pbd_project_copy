<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualanObj = new Penjualan();

$message = '';

// Ambil daftar margin aktif untuk dropdown
$marginList = $penjualanObj->getMarginAktif();

// Simpan Data Penjualan (Header)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_penjualan'])) {
    try {
        $iduser = $_SESSION['user']['iduser'] ?? 'U002';
        $idmargin_penjualan = $_POST['idmargin_penjualan'];
        
        // Insert header penjualan
        $idpenjualan = $penjualanObj->insertPenjualan($iduser, $idmargin_penjualan);
        
        // Redirect ke halaman detail
        $_SESSION['success_message'] = "Header penjualan berhasil dibuat. Silakan tambahkan detail barang.";
        header("Location: detail.php?id=" . $idpenjualan);
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'> " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Penjualan Baru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">

  
  <!-- Fonts + Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/icons/bootstrap-icons.min.css">
  
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">
  <div class="card p-4 shadow-sm">
    <div class="d-flex align-items-center mb-4">
      <img src="https://cdn-icons-png.flaticon.com/512/2331/2331943.png" width="40" class="me-3">
      <h4 class="fw-bold mb-0">📝 Buat Penjualan Baru</h4>
    </div>

    <?= $message ?>

    <form method="POST">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-bold">Petugas (Username)</label>
          <input type="text" class="form-control" 
                value="<?= htmlspecialchars($_SESSION['user']['username'] ?? 'unknown') ?>" readonly>
        </div>
        
        <div class="col-md-4">
          <label class="form-label fw-bold">Pilih Margin Penjualan</label>
          <select name="idmargin_penjualan" class="form-select" required>
            <option value="">-- Pilih Margin --</option>
            <?php foreach($marginList as $margin): ?>
              <option value="<?= htmlspecialchars($margin['idmargin_penjualan']) ?>">
                <?= htmlspecialchars($margin['display_text']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="text-muted">Margin menentukan harga jual barang</small>
        </div>
        
        <div class="col-md-4">
          <label class="form-label fw-bold">Tanggal</label>
          <input type="text" class="form-control" 
                value="<?= date('d/m/Y H:i:s') ?>" readonly>
        </div>
      </div>

      <div class="alert alert-info mt-3">
        <strong>ℹ️ Info:</strong> Setelah membuat header penjualan, Anda akan diarahkan ke halaman detail untuk menambahkan barang yang dijual.
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" name="submit_penjualan" class="btn btn-success px-4">
           Buat Penjualan & Lanjut ke Detail
        </button>
        <a href="list.php" class="btn btn-secondary px-4">
          ← Kembali ke List
        </a>
      </div>
    </form>
  </div>
</div>

</body>
</html>