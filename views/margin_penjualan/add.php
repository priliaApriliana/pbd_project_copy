<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/MarginPenjualan.php");
$marginObj = new MarginPenjualan();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['idmargin']);
    $persen = $_POST['persen'];
    $iduser = $_SESSION['user']['iduser'];

    // Validasi ID tidak boleh kosong
    if (empty($id)) {
        $message = "ID Margin harus diisi!";
    }
    // Validasi ID belum ada di database
    elseif ($marginObj->isIdExists($id)) {
        $message = "ID Margin '$id' sudah digunakan!";
    }
    // Proses insert
    else {
        if ($marginObj->create($id, $persen, $iduser)) {
            header("Location: list.php?success=1");
            exit();
        } else {
            $message = "Gagal menambahkan margin.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Margin Penjualan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/add.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container">

    <h2 class="mt-4 mb-3">Tambah Margin Penjualan</h2>

    <?php if ($message): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-card">

      <div class="mb-3">
        <label class="form-label">ID Margin <span class="text-danger">*</span></label>
        <input type="text" name="idmargin" class="form-control" 
               placeholder="Contoh: M004" 
               value="<?= isset($_POST['idmargin']) ? htmlspecialchars($_POST['idmargin']) : '' ?>"
               required maxlength="10">
        <small class="text-muted">Format: M001, M002, M003, dst. Maksimal 10 karakter</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Persentase Margin (%) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="persen" class="form-control" 
               placeholder="Contoh: 15.5" 
               value="<?= isset($_POST['persen']) ? htmlspecialchars($_POST['persen']) : '' ?>"
               required min="0" max="100">
        <small class="text-muted">Margin baru akan otomatis diset sebagai margin aktif</small>
      </div>

      <button type="submit" class="btn btn-primary w-100">💾 Simpan</button>
      <a href="list.php" class="btn btn-secondary w-100 mt-2">✖️ Kembali</a>

    </form>

  </div>
</div>

</body>
</html>