<?php
// views/satuan/edit.php
require_once(__DIR__ . "/../../classes/Satuan.php");

$satuanObj = new Satuan();
$error = "";

// Cek ID
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$kode_satuan = $_GET['id'];

// Ambil semua satuan dari view
$satuanList = $satuanObj->getAll();
$currentSatuan = null;

foreach ($satuanList as $s) {
    if ($s['kode_satuan'] == $kode_satuan) {
        $currentSatuan = $s;
        break;
    }
}

if (!$currentSatuan) {
    header("Location: list.php?msg=notfound");
    exit();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_satuan = trim($_POST['nama_satuan']);
    $status = trim($_POST['status']);

    if (empty($nama_satuan) || $status === "") {
        $error = "Semua field wajib diisi!";
    } else {
        if ($satuanObj->update($kode_satuan, $nama_satuan, $status)) {
            header("Location: list.php?msg=updated");
            exit();
        } else {
            $error = "Gagal mengupdate satuan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Satuan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/edit.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">
    <div class="container">

        <div class="header">
            <div class="header-icon">✏️</div>
            <h1>Edit Satuan</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>ID Satuan</label>
                <input type="text" value="<?= $currentSatuan['kode_satuan']; ?>" disabled>
                <div class="form-hint">ID satuan bersifat tetap</div>
            </div>

            <div class="form-group">
                <label>Nama Satuan <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="nama_satuan" 
                    value="<?= htmlspecialchars($currentSatuan['nama_satuan']); ?>" 
                    required
                >
            </div>

            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <select name="status" required>
                    <option value="1" <?= $currentSatuan['status_satuan'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= $currentSatuan['status_satuan'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-submit">💾 Update Data</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='list.php'">✖️ Batal</button>
            </div>

        </form>

    </div>
</div>

</body>
</html>
