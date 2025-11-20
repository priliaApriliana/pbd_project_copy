<?php
require_once(__DIR__ . '/../../classes/Penerimaan.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $penerimaanObj = new Penerimaan();

    if ($penerimaanObj->delete($id)) {
        header("Location: list.php?msg=deleted");
        exit();
    } else {
        echo "<script>alert('Gagal menghapus data penerimaan.'); window.location='list.php';</script>";
    }
} else {
    header("Location: list.php");
    exit();
}
?>
