<?php
session_start();
require_once(__DIR__ . '/../classes/User.php');

$userClass = new User();
$username = $_POST['username'];
$password = $_POST['password'];

$user = $userClass->login($username, $password);

if ($user) {
    $_SESSION['user'] = [
        'logged_in' => true,
        'iduser'    => $user['iduser'],   // ✅ ubah dari 'id' jadi 'iduser'
        'nama'      => $user['nama'],
        'username'  => $user['username'],
        'role'      => $user['role']
    ];

    header("Location: ../views/dashboard.php");
    exit();
} else {
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: ../views/login.php");
    exit();
}
?>
