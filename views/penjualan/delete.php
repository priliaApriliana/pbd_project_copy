<?php
session_start();

if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualan = new Penjualan();

// Cek apakah ID penjualan dikirim
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$idpenjualan = $_GET['id'];   // jangan diubah ke integer!

try {
    $delete = $penjualan->delete($idpenjualan);

    if ($delete) {
        // Jika transaksi yang sedang dibuat dihapus, hapus session
        if (isset($_SESSION['penjualan_baru']) && $_SESSION['penjualan_baru'] == $idpenjualan) {
            unset($_SESSION['penjualan_baru']);
        }

        header("Location: list.php?msg=deleted");
        exit();
    } else {
        header("Location: list.php?msg=error");
        exit();
    }   

} catch (Exception $e) {

    // Redirect dengan error message
    header("Location: list.php?msg=error&info=" . urlencode($e->getMessage()));
    exit();
}

?>
