<?php
session_start();

if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/ReturBarang.php");

$retur = new ReturBarang();

// Ambil ID retur
if (!isset($_GET['id'])) {
    header("Location: list.php?error=ID retur tidak ditemukan");
    exit();
}

$idretur = $_GET['id'];

// Data header
$dataHeader = $retur->getHeader($idretur);

// Data detail retur
$dataDetail = $retur->getDetail($idretur);

$message = "";

// Notifikasi sukses/error
if (isset($_GET['success'])) {
    $message = "<div class='alert alert-success alert-dismissible fade show'>
                    <strong>✔ Berhasil!</strong> " . htmlspecialchars($_GET['success']) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
}
if (isset($_GET['error'])) {
    $message = "<div class='alert alert-danger alert-dismissible fade show'>
                    <strong>✖ Gagal!</strong> " . htmlspecialchars($_GET['error']) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
}

// Jika header tidak ditemukan
if (!$dataHeader) {
    header("Location: list.php?error=Data retur tidak ditemukan");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Retur Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">

    <div class="card p-4 shadow-sm">

        <h4 class="fw-bold mb-3">♻ Detail Retur Barang</h4>

        <?= $message ?>

        <!-- INFORMASI HEADER -->
        <div class="mb-4 p-3 border rounded bg-light">
            <h5 class="fw-bold mb-3">🧾 Informasi Retur</h5>

            <div class="row">
                <div class="col-md-4">
                    <label class="fw-bold">ID Retur</label>
                    <input type="text" class="form-control" value="<?= $dataHeader['idretur'] ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold">Tanggal Retur</label>
                    <input type="text" class="form-control" value="<?= $dataHeader['tanggal'] ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold">ID Penerimaan</label>
                    <input type="text" class="form-control" value="<?= $dataHeader['idpenerimaan'] ?>" readonly>
                </div>

                <div class="col-md-4 mt-3">
                    <label class="fw-bold">Petugas</label>
                    <input type="text" class="form-control" value="<?= $dataHeader['petugas'] ?>" readonly>
                </div>
            </div>
        </div>

        <!-- TABEL DETAIL RETUR -->
        <h5 class="fw-bold mt-4 mb-2">📦 Detail Barang Diretur</h5>

        <table class="table table-bordered table-hover">
            <thead class="table-warning text-center">
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dataDetail)): ?>
                    <?php foreach ($dataDetail as $d): ?>
                        <tr class="text-center">
                            <td><?= $d['idbarang'] ?></td>
                            <td><?= $d['nama_barang'] ?></td>
                            <td><?= $d['jumlah'] ?></td>
                            <td><?= $d['alasan'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada barang yang diretur</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="list.php" class="btn btn-secondary mt-3">⬅ Kembali ke Daftar Retur</a>
    </div>
</div>

</body>
</html>
