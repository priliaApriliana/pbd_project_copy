<?php
/**
 * File: views/penerimaan/update.php
 * Fungsi: Proses update penerimaan (detail_penerimaan)
 * Mekanisme:
 *  - Update jumlah_terima per baris detail
 *  - sub_total_terima dihitung ulang pakai fn_hitung_subtotal(harga_satuan_terima, jumlah_terima)
 *  - Trigger AFTER UPDATE detail_penerimaan akan memanggil sp_update_status_penerimaan
 */

require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

// Pastikan form dipanggil via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

// Ambil id penerimaan
if (!isset($_POST['idpenerimaan'])) {
    $_SESSION['error_message'] = "ID penerimaan tidak ditemukan.";
    header("Location: list.php");
    exit();
}

$idpenerimaan = intval($_POST['idpenerimaan']);

// Ambil array detail dari form
$iddetails       = $_POST['iddetail']      ?? [];
$jumlah_terimas  = $_POST['jumlah_terima'] ?? [];

if (empty($iddetails) || empty($jumlah_terimas) || count($iddetails) !== count($jumlah_terimas)) {
    $_SESSION['error_message'] = "Data detail penerimaan tidak lengkap.";
    header("Location: edit.php?id=" . $idpenerimaan);
    exit();
}

$db   = new DBConnection();
$conn = $db->getConnection();

mysqli_begin_transaction($conn);

try {

    // Loop semua detail dan update
    for ($i = 0; $i < count($iddetails); $i++) {

        $iddetail = intval($iddetails[$i]);
        $jumlah   = intval($jumlah_terimas[$i]);

        if ($jumlah <= 0) {
            throw new Exception("Jumlah terima harus lebih dari 0 pada baris ke-" . ($i+1));
        }

        /**
         * Pakai function di database:
         *   sub_total_terima = fn_hitung_subtotal(harga_satuan_terima, jumlah_terima)
         * Harga satuan ambil dari kolom yg sudah ada (tidak perlu dikirim dari form)
         */
        $sql = "
            UPDATE detail_penerimaan
            SET jumlah_terima = ?,
                sub_total_terima = fn_hitung_subtotal(harga_satuan_terima, ?)
            WHERE iddetail_penerimaan = ?
              AND idpenerimaan = ?
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Gagal prepare statement: " . $conn->error);
        }

        $stmt->bind_param("iiii", $jumlah, $jumlah, $iddetail, $idpenerimaan);

        if (!$stmt->execute()) {
            throw new Exception("Gagal update detail penerimaan (ID Detail: $iddetail): " . $stmt->error);
        }

        $stmt->close();
        // Trigger AFTER UPDATE detail_penerimaan akan otomatis:
        //   CALL sp_update_status_penerimaan(NEW.idpenerimaan);
    }

    // Sukses semua → commit
    mysqli_commit($conn);

    $_SESSION['success_message'] = "Penerimaan berhasil diperbarui. Status & subtotal dihitung otomatis.";
    header("Location: list.php");
    exit();

} catch (Exception $e) {

    mysqli_rollback($conn);

    $_SESSION['error_message'] = "Gagal mengupdate penerimaan: " . $e->getMessage();
    header("Location: edit.php?id=" . $idpenerimaan);
    exit();
}
