<?php
// views/role/edit.php
require_once(__DIR__ . "/../../classes/Role.php");

$roleObj = new Role();
$error = "";
$success = "";

// Ambil ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$kode_role = $_GET['id'];

// Ambil data role berdasarkan ID
$roles = $roleObj->getAll();
$currentRole = null;

foreach ($roles as $r) {
    if ($r['kode_role'] == $kode_role) {
        $currentRole = $r;
        break;
    }
}

// Jika role tidak ditemukan
if (!$currentRole) {
    header("Location: list.php?msg=notfound");
    exit();
}

// Proses Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_role = trim($_POST['nama_role']);
    
    // Validasi input
    if (empty($nama_role)) {
        $error = "Nama role harus diisi!";
    } else {
        if ($roleObj->update($kode_role, $nama_role)) {
            header("Location: list.php?msg=updated");
            exit();
        } else {
            $error = "Gagal mengupdate role.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role</title>
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
                <h1>Edit Role</h1>
            </div>

            <div class="info-box">
                <p>💡 <strong>Info:</strong> ID Role tidak dapat diubah untuk menjaga integritas data</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="kode_role">
                        ID Role
                    </label>
                    <input 
                        type="text" 
                        id="kode_role" 
                        name="kode_role" 
                        value="<?php echo htmlspecialchars($currentRole['kode_role']); ?>"
                        disabled
                    >
                    <div class="form-hint">ID Role tidak dapat diubah</div>
                </div>

                <div class="form-group">
                    <label for="nama_role">
                        Nama Role <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama_role" 
                        name="nama_role" 
                        placeholder="Contoh: Admin Gudang" 
                        value="<?php echo isset($_POST['nama_role']) ? htmlspecialchars($_POST['nama_role']) : htmlspecialchars($currentRole['nama_role']); ?>"
                        required
                        maxlength="100"
                        autofocus
                    >
                    <div class="form-hint">Nama role yang akan ditampilkan dalam sistem</div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-submit">
                        💾 Update Data
                    </button>
                    <button type="button" class="btn btn-cancel" onclick="window.location.href='list.php'">
                        ✖️ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>