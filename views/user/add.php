<?php
session_start();

require_once(__DIR__ . "/../../classes/User.php");
require_once(__DIR__ . "/../../classes/Role.php");

$userObj = new User();
$roleObj = new Role();

$error = "";

// Ambil daftar role dari view v_role
$roles = $roleObj->getAll();

// Proses Insert User
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $iduser = trim($_POST['iduser']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $idrole = trim($_POST['idrole']);

    if (empty($iduser) || empty($username) || empty($password) || empty($idrole)) {
        $error = "Semua field wajib diisi!";
    } else {
        if ($userObj->create($iduser, $username, $password, $idrole)) {
            header("Location: list.php?msg=success");
            exit();
        } else {
            $error = "Gagal menambahkan user. ID mungkin sudah ada.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons Global -->
    <link rel="stylesheet" href="../../assets/icons/bootstrap-icons.min.css">

    <!-- CSS ADD USER -->
    <link rel="stylesheet" href="../../assets/style/add.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">

    <div class="form-container">

        <!-- Header -->
        <div class="form-header">
            <div class="header-icon">+</div>
            <div>
                <h1>Tambah User Baru</h1>
                <p>Menambahkan user baru ke dalam sistem Inventori</p>
            </div>
        </div>

        <!-- Error -->
        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST">

            <div class="form-group">
                <label>ID User <span class="required">*</span></label>
                <input type="text" name="iduser" placeholder="Contoh: U003" required maxlength="10">
            </div>

            <div class="form-group">
                <label>Username <span class="required">*</span></label>
                <input type="text" name="username" placeholder="Contoh: admin_gudang" required maxlength="45">
            </div>

            <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <input type="password" name="password" required maxlength="100">
            </div>

            <div class="form-group">
                <label>Role <span class="required">*</span></label>
                <select name="idrole" required>
                    <option value="">-- Pilih Role --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['kode_role']; ?>">
                            <?= $r['kode_role']; ?> - <?= $r['nama_role']; ?>
                        </option>
                    <?php endforeach; ?>
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
