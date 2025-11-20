<?php
// views/vendor/add.php
require_once(__DIR__ . "/../../classes/Vendor.php");

$vendorObj = new Vendor();
$error = "";

// Proses Insert Vendor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idvendor = trim($_POST['idvendor']);
    $nama_vendor = trim($_POST['nama_vendor']);
    $badan_hukum = trim($_POST['badan_hukum']);
    $status = trim($_POST['status']);

    if (empty($idvendor) || empty($nama_vendor) || empty($badan_hukum) || empty($status)) {
        $error = "Semua field wajib diisi!";
    } else {
        if ($vendorObj->create($idvendor, $nama_vendor, $badan_hukum, $status)) {
            header("Location: list.php?msg=success");
            exit();
        } else {
            $error = "Gagal menambahkan vendor. ID mungkin sudah ada.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Vendor</title>
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
            <h1>Tambah Vendor Baru</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>ID Vendor <span class="required">*</span></label>
                <input type="text" name="idvendor" placeholder="Contoh: V006" required maxlength="10">
            </div>

            <div class="form-group">
                <label>Nama Vendor <span class="required">*</span></label>
                <input type="text" name="nama_vendor" placeholder="Contoh: PT Maju Makmur" required maxlength="100">
            </div>

            <div class="form-group">
                <label>Badan Hukum <span class="required">*</span></label>
                <select name="badan_hukum" required>
                    <option value="">-- Pilih --</option>
                    <option value="Y">Ya (Berbadan Hukum)</option>
                    <option value="N">Tidak (Non Badan Hukum)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <select name="status" required>
                    <option value="">-- Pilih --</option>
                    <option value="A">Aktif</option>
                    <option value="N">Nonaktif</option>
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
