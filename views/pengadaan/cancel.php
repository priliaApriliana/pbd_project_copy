<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . "/../../classes/Pengadaan.php");

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error_message'] = "ID Pengadaan tidak valid!";
    header("Location: list.php");
    exit();
}

$pengadaanObj = new Pengadaan();

try {
    // Cek apakah pengadaan sudah cancel
    $data = $pengadaanObj->getById($id);
    
    if (!$data) {
        $_SESSION['error_message'] = "Data pengadaan tidak ditemukan!";
        header("Location: list.php");
        exit();
    }
    
    if ($data['status'] === 'C') {
        $_SESSION['error_message'] = "Pengadaan sudah dibatalkan sebelumnya!";
        header("Location: list.php");
        exit();
    }
    
    // Cancel pengadaan (update status jadi 'C')
    if ($pengadaanObj->cancel($id)) {
        $_SESSION['success_message'] = "Pengadaan #{$id} berhasil dibatalkan!";
    } else {
        $_SESSION['error_message'] = "Gagal membatalkan pengadaan!";
    }
    
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
}

header("Location: list.php");
exit();
?>