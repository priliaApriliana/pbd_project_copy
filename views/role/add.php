<?php
session_start();
require_once(__DIR__ . "/../../classes/Role.php");
$role = new Role();
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idrole = trim($_POST['idrole']);
    $nama_role = trim($_POST['nama_role']);

    if (empty($idrole) || empty($nama_role)) {
        $error = "Semua field wajib diisi!";
    } elseif ($role->create($idrole, $nama_role)) {
        header("Location: list.php?msg=success");
        exit();
    } else {
        $error = "Gagal menambahkan role. ID Role mungkin sudah digunakan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Role Baru | Sistem Inventori</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS terpisah -->
    <link rel="stylesheet" href="../../assets/style/add.css">
</head>

<body>

    <?php include(__DIR__ . '/../layout/sidebar.php'); ?>

    <div class="main-content">
        <div class="form-container">

            <div class="form-header">
                <div class="header-icon">+</div>
                <div>
                    <h1>Tambah Role Baru</h1>
                    <p>Menambahkan role baru ke dalam sistem farmasi</p>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="idrole">
                        ID Role <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="idrole" 
                        name="idrole" 
                        placeholder="Contoh: R003" 
                        value="<?= isset($_POST['idrole']) ? htmlspecialchars($_POST['idrole']) : '' ?>"
                        required 
                        maxlength="10"
                    >
                    <div class="form-hint">Format: R001, R002, dst.</div>
                </div>

                <div class="form-group">
                    <label for="nama_role">Nama Role <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="nama_role" 
                        name="nama_role" 
                        placeholder="Contoh: Admin Gudang" 
                        value="<?= isset($_POST['nama_role']) ? htmlspecialchars($_POST['nama_role']) : '' ?>"
                        required 
                        maxlength="100"
                    >
                    <div class="form-hint">Nama role yang akan ditampilkan dalam sistem</div>
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
