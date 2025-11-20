<?php
// views/satuan/delete.php
require_once(__DIR__ . "/../../classes/Satuan.php");

$satuanObj = new Satuan();

// Cek ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?msg=error");
    exit();
}

$id = $_GET['id'];

if ($satuanObj->delete($id)) {
    header("Location: list.php?msg=deleted");
    exit();
} else {
    header("Location: list.php?msg=error");
    exit();
}
?>
