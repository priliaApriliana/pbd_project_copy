<?php
session_start();

require_once(__DIR__ . '/../../classes/Pengadaan.php');
$pengadaan = new Pengadaan();
$id = $_GET['id'];

try {
    $pengadaan->delete($id);
    $_SESSION['success_message'] = "Data pengadaan berhasil dihapus!";
    header("Location: list.php");
    exit();

} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: list.php");
    exit();
}
