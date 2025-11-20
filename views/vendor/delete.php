<?php
// views/vendor/delete.php
require_once(__DIR__ . "/../../classes/Vendor.php");

$vendorObj = new Vendor();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?msg=error");
    exit();
}

$idvendor = $_GET['id'];

if ($vendorObj->delete($idvendor)) {
    header("Location: list.php?msg=deleted");
    exit();
} else {
    header("Location: list.php?msg=error");
    exit();
}
?>
