<?php
// views/user/edit.php
require_once(__DIR__ . "/../../classes/User.php");
require_once(__DIR__ . "/../../classes/Role.php");

$userObj = new User();
$roleObj = new Role();

$error = "";

// Ambil ID User
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$kode_user = $_GET['id'];

// Ambil semua user (VIEW)
$users = $userObj->getAll();
$currentUser = null;

foreach ($users as $u) {
    if ($u['kode_user'] == $kode_user) {
        $currentUser = $u;
        break;
    }
}

if (!$currentUser) {
    header("Location: list.php?msg=notfound");
    exit();
}

// Ambil daftar role
$roles = $roleObj->getAll();

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $idrole = trim($_POST['idrole']);

    if (empty($username) || empty($idrole)) {
        $error = "Username dan Role wajib diisi!";
    } else {
        $pwd = empty($password) ? $currentUser['password'] : $password;

        if ($userObj->update($kode_user, $username, $pwd, $idrole)) {
            header("Location: list.php?msg=updated");
            exit();
        } else {
            $error = "Gagal mengupdate user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            <h1>Edit User</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>ID User</label>
                <input type="text" value="<?= $currentUser['kode_user']; ?>" disabled>
            </div>

            <div class="form-group">
                <label>Username <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="username" 
                    value="<?= htmlspecialchars($currentUser['username']); ?>" 
                    required
                >
            </div>

            <div class="form-group">
                <label>Password (biarkan kosong jika tidak diganti)</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label>Role <span class="required">*</span></label>
                <select name="idrole" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['kode_role']; ?>"
                            <?= $currentUser['idrole'] == $r['kode_role'] ? 'selected' : ''; ?>>
                            <?= $r['kode_role']; ?> - <?= $r['nama_role']; ?>
                        </option>
                    <?php endforeach; ?>
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
