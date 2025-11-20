<?php
// views/role/delete.php
require_once(__DIR__ . "/../../classes/Role.php");

$roleObj = new Role();

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?msg=error");
    exit();
}

$idrole = $_GET['id'];

// Proses Delete
if ($roleObj->delete($idrole)) {
    header("Location: list.php?msg=deleted");
    exit();
} else {
    header("Location: list.php?msg=error");
    exit();
}
?>