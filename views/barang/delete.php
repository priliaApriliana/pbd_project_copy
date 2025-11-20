<?php
require_once(__DIR__. "/../classes/Barang.php");
$barangObj = new Barang();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $barangObj->delete($id);
}

header("Location: barang_list.php");
exit;
?>