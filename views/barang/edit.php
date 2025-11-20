<?php
// views/barang/edit.php
require_once(__DIR__ . "/../../classes/Barang.php");

$barangObj = new Barang();
$error = "";

// Cek ID barang
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$idbarang = $_GET['id'];

// Ambil data barang asli
$currentBarang = $barangObj->getById($idbarang);

if (!$currentBarang) {
    header("Location: list.php?msg=notfound");
    exit();
}

$satuanList = $barangObj->getSatuanOptions();

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $jenis     = trim($_POST['jenis']);
    $nama      = trim($_POST['nama']);
    $idsatuan  = trim($_POST['idsatuan']);
    $status    = trim($_POST['status']);
    $harga     = trim($_POST['harga']);

    if ($jenis === "" || $nama === "" || $idsatuan === "" || $status === "" || $harga === "") {
        $error = "Semua field wajib diisi!";
    } else {
        if ($barangObj->update($idbarang, $jenis, $nama, $idsatuan, (int)$status, (int)$harga)) {
            header("Location: list.php?msg=updated");
            exit();
        } else {
            $error = "Gagal mengupdate data barang.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>

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
        <h1>Edit Barang</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>ID Barang</label>
            <input type="text" value="<?= htmlspecialchars($currentBarang['idbarang']); ?>" disabled>
        </div>

        <div class="form-group">
            <label>Jenis *</label>
            <select name="jenis" required>
                <option value="F" <?= $currentBarang['jenis'] == 'F' ? 'selected' : '' ?>>Food & Beverage</option>
                <option value="H" <?= $currentBarang['jenis'] == 'H' ? 'selected' : '' ?>>Health & Beauty</option>
                <option value="S" <?= $currentBarang['jenis'] == 'S' ? 'selected' : '' ?>>Stationary</option>
                <option value="C" <?= $currentBarang['jenis'] == 'C' ? 'selected' : '' ?>>Cleaning & Household</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nama Barang *</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($currentBarang['nama']); ?>" required>
        </div>

        <div class="form-group">
            <label>Satuan *</label>
            <select name="idsatuan" required>
                <?php foreach ($satuanList as $s): ?>
                    <option value="<?= $s['kode_satuan']; ?>"
                        <?= $currentBarang['idsatuan'] == $s['kode_satuan'] ? 'selected' : '' ?>>
                        <?= $s['kode_satuan']; ?> - <?= $s['nama_satuan']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Harga *</label>
            <input type="number" name="harga" value="<?= $currentBarang['harga']; ?>" required>
        </div>

        <div class="form-group">
            <label>Status *</label>
            <select name="status">
                <option value="1" <?= $currentBarang['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= $currentBarang['status'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>

        <div class="button-group">
            <button class="btn btn-submit">💾 Update Data</button>
            <button type="button" class="btn btn-cancel" onclick="window.location.href='list.php'">✖️ Batal</button>
        </div>

    </form>

</div>
</div>

</body>
</html>
