<?php
/**
 * File: views/pengadaan/store.php
 * Fungsi: Simpan Pengadaan (Header + Detail)
 * ENGINE: Stored Procedure + Trigger
 */

require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

// Validasi apakah form disubmit
if (!isset($_POST['simpan_pengadaan'])) {
    header("Location: add.php");
    exit();
}

$db = new DBConnection();
$conn = $db->getConnection();

// Ambil input dari form
$idvendor = $_POST['idvendor'];
$iduser   = $_POST['iduser'];

// Array detail barang
$barang_ids     = $_POST['barang_id'];
$harga_satuans  = $_POST['harga_satuan'];
$jumlahs        = $_POST['jumlah'];

// Validasi minimal 1 detail barang
if (empty($barang_ids) || count($barang_ids) < 1) {
    $_SESSION['error_message'] = "Tambahkan minimal 1 barang!";
    header("Location: add.php");
    exit();
}

mysqli_begin_transaction($conn);

try {

    // =====================================================
    // STEP 1 — Insert Header Pengadaan (SP)
    // status awal otomatis 'P' dari SP sp_insert_pengadaan
    // =====================================================
    $stmt = $conn->prepare("CALL sp_insert_pengadaan(?, ?, @new_id)");
    $stmt->bind_param("ss", $iduser, $idvendor);
    $stmt->execute();
    $stmt->close();

    // Ambil output ID pengadaan baru
    $res = $conn->query("SELECT @new_id AS idpengadaan");
    $row = $res->fetch_assoc();
    $idpengadaan = intval($row['idpengadaan']);

    if ($idpengadaan <= 0) {
        throw new Exception("Gagal mengambil ID pengadaan baru!");
    }

    // =====================================================
    // STEP 2 — Insert Detail Pengadaan (SP)
    // subtotal + total dihitung otomatis oleh trigger
    // =====================================================
    for ($i = 0; $i < count($barang_ids); $i++) {

        $idbarang      = $barang_ids[$i];
        $harga_satuan  = intval($harga_satuans[$i]);
        $jumlah        = intval($jumlahs[$i]);

        $stmt2 = $conn->prepare("CALL sp_insert_detail_pengadaan(?, ?, ?, ?)");
        $stmt2->bind_param("isii", 
            $idpengadaan, 
            $idbarang, 
            $harga_satuan, 
            $jumlah
        );

        if (!$stmt2->execute()) {
            throw new Exception("Gagal insert detail pengadaan!");
        }

        $stmt2->close();
        $conn->next_result();
    }

    mysqli_commit($conn);

    // Pesan sukses
    $_SESSION['success_message'] = "Pengadaan berhasil disimpan! Total dihitung otomatis.";
    header("Location: list.php");
    exit();

} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error_message'] = "Gagal menyimpan pengadaan: " . $e->getMessage();
    header("Location: add.php");
    exit();
}

?>
