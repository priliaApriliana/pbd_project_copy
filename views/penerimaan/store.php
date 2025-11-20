<?php
/**
 * File: views/penerimaan/store.php
 * Fungsi:
 *  - Insert header penerimaan
 *  - Insert detail penerimaan
 *  - Validasi jumlah_terima (rule baru)
 *  - Trigger update status pengadaan/penerimaan
 */

require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

if (!isset($_POST['idpengadaan']) || !isset($_POST['iduser'])) {
    $_SESSION['error_message'] = "Form tidak lengkap!";
    header("Location: add.php");
    exit();
}

$db = new DBConnection();
$conn = $db->getConnection();

$idpengadaan  = intval($_POST['idpengadaan']);
$iduser       = $_POST['iduser'];

$barang_ids       = $_POST['barang_id'];
$jumlahs          = $_POST['jumlah_terima'];
$harga_satuans    = $_POST['harga_satuan_terima'];


// ====================================================
// 0️⃣ VALIDASI AWAL
// ====================================================
if (empty($barang_ids) || count($barang_ids) < 1) {
    $_SESSION['error_message'] = "Detail barang tidak boleh kosong!";
    header("Location: add.php");
    exit();
}

$jumlah_barang = count($jumlahs);
$nonzero_count = 0;

foreach ($jumlahs as $qty) {
    if ($qty > 0) $nonzero_count++;
}

// RULE: jika hanya 1 barang → minimal 1
if ($jumlah_barang == 1) {
    if ($jumlahs[0] < 1) {
        $_SESSION['error_message'] = "Jumlah penerimaan minimal 1 karena hanya ada satu barang.";
        header("Location: add.php?idpengadaan=".$idpengadaan);
        exit();
    }
} 
// RULE: jika lebih dari 1 barang → minimal ada 1 qty > 0
else {
    if ($nonzero_count < 1) {
        $_SESSION['error_message'] = "Minimal 1 barang harus memiliki jumlah penerimaan.";
        header("Location: add.php?idpengadaan=".$idpengadaan);
        exit();
    }
}

mysqli_begin_transaction($conn);

try {

    // ====================================================
    // 1️⃣ INSERT HEADER PENERIMAAN
    // ====================================================
    $stmtHeader = $conn->prepare("
        INSERT INTO penerimaan (status, idpengadaan, iduser)
        VALUES ('P', ?, ?)
    ");

    $stmtHeader->bind_param("is", $idpengadaan, $iduser);

    if (!$stmtHeader->execute()) {
        throw new Exception("Gagal menyimpan header penerimaan: " . $stmtHeader->error);
    }
    $stmtHeader->close();

    $idpenerimaan = $conn->insert_id;


    // ====================================================
    // 2️⃣ INSERT DETAIL PENERIMAAN
    // ====================================================
    $stmtDetail = $conn->prepare("
        INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima)
        VALUES (?, ?, ?, ?)
    ");

    for ($i = 0; $i < $jumlah_barang; $i++) {

        $idbarang = $barang_ids[$i];
        $jumlah   = intval($jumlahs[$i]);
        $harga    = intval($harga_satuans[$i]);

        // RULE: Jika qty = 0 → SKIP insert agar tidak tersimpan ke DB
        if ($jumlah == 0) {
            continue;
        }

        if ($jumlah < 0) {      // tetap tidak boleh minus
            throw new Exception("Jumlah penerimaan barang tidak valid.");
        }

        $stmtDetail->bind_param("isii",
            $idpenerimaan,
            $idbarang,
            $jumlah,
            $harga
        );

        if (!$stmtDetail->execute()) {
            throw new Exception("Gagal insert detail penerimaan: " . $stmtDetail->error);
        }

        $conn->next_result();
    }

    $stmtDetail->close();

    // COMMIT
    mysqli_commit($conn);

    $_SESSION['success_message'] = "Penerimaan berhasil disimpan!";
    header("Location: list.php");
    exit();

} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error_message'] = "Gagal menyimpan penerimaan: " . $e->getMessage();
    header("Location: add.php?idpengadaan=".$idpengadaan);
    exit();
}
?>
