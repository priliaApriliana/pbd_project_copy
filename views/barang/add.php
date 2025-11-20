<?php
// views/barang/add.php
require_once(__DIR__ . "/../../classes/Barang.php");

$barangObj = new Barang();
$error = "";

// Ambil list satuan untuk dropdown
$satuanList = $barangObj->getSatuanOptions();

// Proses tambah barang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idbarang  = trim($_POST['idbarang']);
    $jenis     = trim($_POST['jenis']);
    $nama      = trim($_POST['nama']);
    $idsatuan  = trim($_POST['idsatuan']);
    $status    = trim($_POST['status']);
    $harga     = trim($_POST['harga']);

    if ($idbarang === "" || $jenis === "" || $nama === "" || $idsatuan === "" || $status === "" || $harga === "") {
        $error = "Semua field wajib diisi!";
    } else {
        if ($barangObj->create($idbarang, $jenis, $nama, $idsatuan, (int)$status, (int)$harga)) {
            header("Location: list.php?msg=success");
            exit();
        } else {
            $error = "Gagal menambahkan barang. ID mungkin sudah digunakan.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/add.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">
<div class="container">

    <div class="header">
        <div class="header-icon">➕</div>
        <h1>Tambah Barang Baru</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>ID Barang *</label>
            <input type="text" name="idbarang" placeholder="Contoh: B011" required>
        </div>

        <div class="form-group">
            <label>Jenis *</label>
            <select name="jenis" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="F">Food & Beverage</option>
                <option value="H">Health & Beauty</option>
                <option value="S">Stationary</option>
                <option value="C">Cleaning & Household</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nama Barang *</label>
            <input type="text" name="nama" required>
        </div>

        <div class="form-group">
            <label>Satuan *</label>
            <select name="idsatuan" required>
                <option value="">-- Pilih Satuan --</option>
                <?php foreach ($satuanList as $s): ?>
                    <option value="<?= $s['kode_satuan']; ?>">
                        <?= $s['kode_satuan']; ?> - <?= $s['nama_satuan']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Harga Satuan *</label>
            <input type="number" name="harga" min="0" required>
        </div>

        <div class="form-group">
            <label>Status *</label>
            <select name="status" required>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>

        <div class="button-group">
            <button class="btn btn-submit">💾 Simpan Data</button>
            <button type="button" class="btn btn-cancel" onclick="window.location.href='list.php'">✖️ Batal</button>
        </div>

    </form>

</div>
</div>

</body>
</html>
