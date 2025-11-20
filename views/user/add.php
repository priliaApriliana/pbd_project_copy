<?php
// views/user/add.php
require_once(__DIR__ . "/../../classes/User.php");
require_once(__DIR__ . "/../../classes/Role.php");

$userObj = new User();
$roleObj = new Role();

$error = "";
$success = "";

// Ambil daftar role (dari view v_role)
$roles = $roleObj->getAll();

// Proses Insert User
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $iduser = trim($_POST['iduser']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $idrole = trim($_POST['idrole']);

    // Validasi
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
            <h1>Tambah User Baru</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ⚠️ <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

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
                <label for="idrole">Role <span class="required">*</span></label>
                <select name="idrole" required>
                    <option value="">-- Pilih Role --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['kode_role']; ?>">
                            <?= $r['kode_role']; ?> - <?= $r['nama_role']; ?>
                        </option>
                    <?php endforeach; ?>
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
