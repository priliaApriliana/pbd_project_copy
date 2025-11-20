<?php
require_once(__DIR__ . "/../../classes/MarginPenjualan.php");

$marginObj = new MarginPenjualan();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?msg=error");
    exit();
}

$idmargin = $_GET['id'];

if ($marginObj->delete($idmargin)) {
    header("Location: list.php?msg=deleted");
    exit();
} else {
    header("Location: list.php?msg=error");
    exit();
}
?>
