<?php
// views/user/delete.php
require_once(__DIR__ . "/../../classes/User.php");

$userObj = new User();

// Cek ID user
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?msg=error");
    exit();
}

$iduser = $_GET['id'];

if ($userObj->delete($iduser)) {
    header("Location: list.php?msg=deleted");
    exit();
} else {
    header("Location: list.php?msg=error");
    exit();
}
?>
