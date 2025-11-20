<?php
// views/role/add.php
require_once(__DIR__ . "/../../classes/Role.php");

$role = new Role();
$error = "";
$success = "";

// Proses Insert
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idrole = trim($_POST['idrole']);
    $nama_role = trim($_POST['nama_role']);
    
    // Validasi input
    if (empty($idrole) || empty($nama_role)) {
        $error = "Semua field harus diisi!";
    } else {
        if ($role->create($idrole, $nama_role)) {
            header("Location: list.php?msg=success");
            exit();
        } else {
            $error = "Gagal menambahkan role. ID mungkin sudah ada.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Role</title>
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
                <h1>Tambah Role Baru</h1>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
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
                        value="<?php echo isset($_POST['idrole']) ? htmlspecialchars($_POST['idrole']) : ''; ?>"
                        required
                        maxlength="10"
                    >
                    <div class="form-hint">Format: R001, R002, dst. Maksimal 10 karakter</div>
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
                        value="<?php echo isset($_POST['nama_role']) ? htmlspecialchars($_POST['nama_role']) : ''; ?>"
                        required
                        maxlength="100"
                    >
                    <div class="form-hint">Nama role yang akan ditampilkan dalam sistem</div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-submit">
                        💾 Simpan Data
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