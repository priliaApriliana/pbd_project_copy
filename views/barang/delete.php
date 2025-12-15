<?php
require_once(__DIR__. "/../../classes/Barang.php");
$barangObj = new Barang();

// PERBAIKAN: Jangan cast ke int, biarkan string
$id = $_GET['id'] ?? '';

// DEBUG (uncomment untuk testing)
// echo "ID: " . $id . "<br>";
// echo "Type: " . gettype($id) . "<br>";

if (!empty($id)) {
    $result = $barangObj->delete($id);
    
    // DEBUG hasil delete
    // echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "<br>";
    // exit;
}

header("Location: list.php");
exit;
?>