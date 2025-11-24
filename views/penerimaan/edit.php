<?php
/**
 * File: views/penerimaan/edit.php
 * Fungsi: Edit penerimaan barang (header + detail)
 * Mekanisme:
 *  - Header penerimaan hanya tampil, tidak bisa diubah
 *  - Detail bisa di-update (jumlah_terima)
 *  - Trigger menghitung ulang subtotal + status
 */

require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

$db = new DBConnection();
$conn = $db->getConnection();

// Validasi login
if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$idpenerimaan = intval($_GET['id']);

// Ambil data header penerimaan
$stmt = $conn->prepare("
    SELECT p.idpenerimaan, p.status, p.created_at, 
           pg.idpengadaan, v.nama_vendor, u.username
    FROM penerimaan p
    JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
    JOIN vendor v ON pg.vendor_idvendor = v.idvendor
    JOIN user u ON p.iduser = u.iduser
    WHERE p.idpenerimaan = ?
");
$stmt->bind_param("i", $idpenerimaan);
$stmt->execute();
$header = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$header) {
    $_SESSION['error_message'] = "Penerimaan tidak ditemukan!";
    header("Location: list.php");
    exit();
}

// Ambil detail penerimaan
$stmt2 = $conn->prepare("
    SELECT dp.iddetail_penerimaan, dp.barang_idbarang,
           b.nama AS nama_barang, s.nama_satuan,
           dp.jumlah_terima, dp.harga_satuan_terima, dp.sub_total_terima
    FROM detail_penerimaan dp
    JOIN barang b ON dp.barang_idbarang = b.idbarang
    JOIN satuan s ON b.idsatuan = s.idsatuan
    WHERE dp.idpenerimaan = ?
");
$stmt2->bind_param("i", $idpenerimaan);
$stmt2->execute();
$detail = $stmt2->get_result();
$stmt2->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penerimaan Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">

      <!-- Fonts + Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/icons/bootstrap-icons.min.css">
</head>

<body class="bg-light">

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4">

    <div class="card p-4 shadow-sm">

        <h2 class="mb-3">✏️ Edit Penerimaan Barang</h2>
        <a href="list.php" class="btn btn-secondary mb-3">⮜ Kembali</a>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <form action="update.php" method="POST">

            <input type="hidden" name="idpenerimaan" value="<?= $idpenerimaan ?>">

            <!-- HEADER -->
            <div class="mb-3">
                <label class="form-label fw-bold">📦 Pengadaan</label>
                <input type="text" class="form-control" 
                       value="Pengadaan #<?= $header['idpengadaan'] ?> - <?= $header['nama_vendor'] ?>" 
                       readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">👤 Petugas</label>
                <input type="text" class="form-control" value="<?= $header['username'] ?>" readonly>
            </div>

            <hr>

            <h5 class="fw-bold">📝 Detail Penerimaan</h5>

            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th>Satuan</th>
                        <th>Harga Terima</th>
                        <th>Jumlah Terima</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $detail->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['nama_barang'] ?></td>
                        <td><?= $row['nama_satuan'] ?></td>

                        <td>Rp <?= number_format($row['harga_satuan_terima'], 0, ',', '.') ?></td>

                        <td style="width: 140px;">
                            <input type="hidden" name="iddetail[]" value="<?= $row['iddetail_penerimaan'] ?>">
                            <input type="number" 
                                   name="jumlah_terima[]" 
                                   class="form-control"
                                   min="1"
                                   value="<?= $row['jumlah_terima'] ?>">
                        </td>

                        <td>
                            Rp <?= number_format($row['sub_total_terima'], 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <hr>

            <button type="submit" class="btn btn-primary btn-lg">
                💾 Simpan Perubahan
            </button>

        </form>

    </div>
</div>

</body>
</html>
