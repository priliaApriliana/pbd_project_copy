<?php
session_start();

// Cek login
if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/ReturBarang.php");
$retur = new ReturBarang();

// Validasi parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=ID retur tidak valid!");
    exit();
}

$idretur = intval($_GET['id']);

try {

    // ---- HAPUS DETAIL RETUR DULU ----
    $retur->deleteDetailByReturId($idretur);

    // ---- HAPUS RETUR HEADER ----
    $retur->deleteReturHeader($idretur);

    header("Location: list.php?success=Retur berhasil dihapus!");
    exit();

} catch (Exception $e) {
    header("Location: list.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
