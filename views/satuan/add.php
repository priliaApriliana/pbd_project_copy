<?php
session_start();

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
    <title>Tambah Satuan Baru</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons Global -->
    <link rel="stylesheet" href="../../assets/icons/bootstrap-icons.min.css">

    <!-- ADD FORM CSS -->
    <link rel="stylesheet" href="../../assets/style/add.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">

    <div class="form-container">

        <!-- HEADER -->
        <div class="form-header">
            <div class="header-icon">+</div>
            <div>
                <h1>Tambah Satuan Baru</h1>
                <p>Menambahkan satuan baru ke dalam sistem Inventori</p>
            </div>
        </div>

        <!-- ERROR -->
        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
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

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Simpan Data
                </button>

                <button type="button" class="btn btn-secondary"
                        onclick="window.location.href='list.php'">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>
