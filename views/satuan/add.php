<?php
// views/satuan/add.php
require_once(__DIR__ . "/../../classes/Satuan.php");

$satuanObj = new Satuan();
$error = "";

// Proses Insert
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idsatuan = trim($_POST['idsatuan']);
    $nama_satuan = trim($_POST['nama_satuan']);
    $status = trim($_POST['status']);

    if (empty($idsatuan) || empty($nama_satuan) || $status === "") {
        $error = "Semua field wajib diisi!";
    } else {
        if ($satuanObj->create($idsatuan, $nama_satuan, $status)) {
            header("Location: list.php?msg=success");
            exit();
        } else {
            $error = "Gagal menambahkan satuan. ID mungkin sudah digunakan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Satuan</title>

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
            <h1>Tambah Satuan Baru</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>ID Satuan <span class="required">*</span></label>
                <input type="text" name="idsatuan" placeholder="Contoh: S006" maxlength="10" required>
            </div>

            <div class="form-group">
                <label>Nama Satuan <span class="required">*</span></label>
                <input type="text" name="nama_satuan" placeholder="Contoh: Pack / Unit / Botol" maxlength="45" required>
            </div>

            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <select name="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-submit">💾 Simpan Data</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='list.php'">✖️ Batal</button>
            </div>

        </form>
    </div>
</div>

</body>
</html>
