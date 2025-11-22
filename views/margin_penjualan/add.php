<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/MarginPenjualan.php");
$marginObj = new MarginPenjualan();

$message = "";

// Proses Insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idmargin = trim($_POST['idmargin']);
    $persen   = trim($_POST['persen']);
    $iduser   = $_SESSION['user']['iduser'];

    if ($idmargin === "") {
        $message = "ID Margin harus diisi!";
    } 
    elseif ($marginObj->isIdExists($idmargin)) {
        $message = "ID Margin '$idmargin' sudah digunakan!";
    }
    else {
        if ($marginObj->create($idmargin, $persen, $iduser)) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Margin Penjualan</title>

    <!-- Fonts + Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/icons/bootstrap-icons.min.css">

    <!-- CSS Form Global -->
    <link rel="stylesheet" href="../../assets/style/add.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">

    <div class="form-container">

        <!-- HEADER -->
        <div class="form-header">
            <div class="header-icon">%</div>
            <div>
                <h1>Tambah Margin Penjualan</h1>
                <p>Menambahkan margin baru untuk harga penjualan barang</p>
            </div>
        </div>

        <!-- ERROR MESSAGE -->
        <?php if (!empty($message)): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST">

            <div class="form-group">
                <label>ID Margin <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="idmargin" 
                    placeholder="Contoh: M004"
                    maxlength="10"
                    value="<?= isset($_POST['idmargin']) ? htmlspecialchars($_POST['idmargin']) : '' ?>"
                    required
                >
                <div class="form-hint">Format: M001, M002, M003, dst.</div>
            </div>

            <div class="form-group">
                <label>Persentase Margin (%) <span class="required">*</span></label>
                <input 
                    type="number"
                    name="persen"
                    placeholder="Contoh: 15.5"
                    step="0.01"
                    min="0"
                    max="100"
                    value="<?= isset($_POST['persen']) ? htmlspecialchars($_POST['persen']) : '' ?>"
                    required
                >
                <div class="form-hint">Margin baru otomatis diset sebagai margin aktif</div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Simpan Data
                </button>

                <button type="button" class="btn btn-secondary" onclick="window.location.href='list.php'">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>
