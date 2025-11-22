<?php
require_once(__DIR__ . "/../../classes/Barang.php");

$barangObj = new Barang();
$error = "";

// AUTO GENERATE ID BARANG
function generateNextId($barangObj): string
{
    $last = $barangObj->getLastIdBarang();
    if (!$last) return "B001";
    preg_match('/B(\d+)/', $last, $matches);
    $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
    return 'B' . str_pad($number, 3, '0', STR_PAD_LEFT);
}

$nextId = generateNextId($barangObj);
$satuanList = $barangObj->getSatuanOptions();

// PROSES SIMPAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis    = trim($_POST['jenis']);
    $nama     = trim($_POST['nama']);
    $idsatuan = trim($_POST['idsatuan']);
    $status   = trim($_POST['status']);
    $harga = str_replace(['.', ','], '', $_POST['harga']);  // remove formatting

    if ($jenis === "" || $nama === "" || $idsatuan === "" || $harga === "") {
        $error = "Semua field wajib diisi!";
    } else {
        if ($barangObj->create($nextId, $jenis, $nama, $idsatuan, (int)$status, (int)$harga)) {
            header("Location: list.php?msg=success");
            exit();
        } else {
            $error = "Gagal menambah barang.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/icons/bootstrap-icons.min.css">

    <!-- FORM STYLE GLOBAL -->
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
                <h1>Tambah Barang Baru</h1>
                <p>Menambahkan barang baru ke dalam sistem Inventori</p>
            </div>
        </div>

        <!-- ERROR -->
        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <!-- ID AUTO -->
            <div class="form-group">
                <label>ID Barang <span class="required">*</span></label>
                <input 
                    type="text" 
                    value="<?= htmlspecialchars($nextId); ?>" 
                    readonly
                    class="readonly-field"
                >
                <div class="form-hint">ID otomatis – tidak dapat diubah</div>
            </div>

            <!-- Jenis + Satuan -->
            <div class="form-group">
                <label>Jenis Barang <span class="required">*</span></label>
                <select name="jenis" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="F">Food & Beverage</option>
                    <option value="H">Health & Beauty</option>
                    <option value="S">Stationary</option>
                    <option value="C">Cleaning & Household</option>
                </select>
            </div>

            <div class="form-group">
                <label>Satuan <span class="required">*</span></label>
                <select name="idsatuan" required>
                    <option value="">-- Pilih Satuan --</option>
                    <?php foreach ($satuanList as $s): ?>
                        <option value="<?= $s['kode_satuan']; ?>">
                            <?= $s['kode_satuan']; ?> - <?= $s['nama_satuan']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Nama Barang -->
            <div class="form-group">
                <label>Nama Barang <span class="required">*</span></label>
                <input type="text" name="nama" placeholder="Masukkan nama barang" required>
            </div>

            <!-- Harga -->
            <div class="form-group">
                <label>Harga Satuan <span class="required">*</span></label>
                <div class="input-rupiah-wrapper">
                    <span class="prefix">Rp</span>
                    <input 
                        type="text" 
                        id="harga"
                        name="harga"
                        class="input-rupiah"
                        placeholder="0"
                        required
                    >
                </div>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <select name="status" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Simpan Data
                </button>

                <button 
                    type="button" 
                    class="btn btn-secondary"
                    onclick="window.location.href='list.php'">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
            </div>

        </form>

    </div>

</div>

<script>
document.getElementById("harga").addEventListener("input", function () {
    let angka = this.value.replace(/[^0-9]/g, "");
    if (angka === "") {
        this.value = "";
        return;
    }

    this.value = Number(angka).toLocaleString("id-ID");
});
</script>

</body>
</html>
