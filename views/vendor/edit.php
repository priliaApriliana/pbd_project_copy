<?php
// views/vendor/edit.php
require_once(__DIR__ . "/../../classes/Vendor.php");

$vendorObj = new Vendor();
$error = "";

// Ambil ID vendor
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$kode_vendor = $_GET['id'];

// Ambil semua vendor dari view
$vendors = $vendorObj->getAll();
$currentVendor = null;

foreach ($vendors as $v) {
    if ($v['kode_vendor'] == $kode_vendor) {
        $currentVendor = $v;
        break;
    }
}

if (!$currentVendor) {
    header("Location: list.php?msg=notfound");
    exit();
}

// Proses update vendor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_vendor = trim($_POST['nama_vendor']);
    $badan_hukum = trim($_POST['badan_hukum']);
    $status = trim($_POST['status']);

    if (empty($nama_vendor) || empty($badan_hukum) || empty($status)) {
        $error = "Semua field wajib diisi!";
    } else {
        if ($vendorObj->update($kode_vendor, $nama_vendor, $badan_hukum, $status)) {
            header("Location: list.php?msg=updated");
            exit();
        } else {
            $error = "Gagal mengupdate vendor.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vendor</title>
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
            <h1>Edit Vendor</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>ID Vendor</label>
                <input type="text" value="<?= $currentVendor['kode_vendor']; ?>" disabled>
            </div>

            <div class="form-group">
                <label>Nama Vendor <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="nama_vendor"
                    value="<?= htmlspecialchars($currentVendor['nama_vendor']); ?>" 
                    required
                >
            </div>

            <div class="form-group">
                <label>Badan Hukum <span class="required">*</span></label>
                <select name="badan_hukum" required>
                    <option value="Y" <?= $currentVendor['badan_hukum'] == 'Y' ? 'selected' : '' ?>>Ya</option>
                    <option value="N" <?= $currentVendor['badan_hukum'] == 'N' ? 'selected' : '' ?>>Tidak</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <select name="status" required>
                    <option value="A" <?= $currentVendor['status'] == 'A' ? 'selected' : '' ?>>Aktif</option>
                    <option value="N" <?= $currentVendor['status'] != 'A' ? 'selected' : '' ?>>Nonaktif</option>
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
