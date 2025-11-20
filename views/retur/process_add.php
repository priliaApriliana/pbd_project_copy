<!-- file yang menerima POST dan menyimpan data retur ke database. -->

<?php
session_start();
if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php"); exit();
}

require_once(__DIR__ . '/../../classes/ReturBarang.php');
require_once(__DIR__ . '/../../config/DBConnection.php');

$retur = new ReturBarang();
$conn = (new DBConnection())->getConnection();

$idpenerimaan = $_POST['idpenerimaan'];
$iduser = $_SESSION['user']['iduser'];

$stock = isset($_POST['stock']) && $_POST['stock'] !== '' 
    ? intval($_POST['stock']) 
    : null;

if ($stock === null) {
    die("Error: field stock wajib diisi!");
}


// VALIDASI status penerimaan
$cek = $conn->query("SELECT status FROM penerimaan WHERE idpenerimaan = $idpenerimaan")->fetch_assoc();

if ($cek['status'] !== 'S') {
    die("<script>alert('Penerimaan belum selesai!'); history.back();</script>");
}

// Insert retur header
$idretur = $retur->createRetur($idpenerimaan, $iduser);

// Loop detail retur
foreach ($_POST['retur_jumlah'] as $iddetail => $jumlah) {

    if ($jumlah <= 0) continue;

    // Cek jumlah terima aslinya
    $cekJumlah = $conn->query("
        SELECT jumlah_terima 
        FROM detail_penerimaan 
        WHERE iddetail_penerimaan = $iddetail
    ")->fetch_assoc();

    if ($jumlah > $cekJumlah['jumlah_terima']) {
        die("<script>alert('Jumlah retur melebihi jumlah terima!'); history.back();</script>");
    }

    $alasan = $_POST['retur_alasan'][$iddetail];

    $retur->createDetailRetur($idretur, $iddetail, $jumlah, $alasan);
}

header("Location: list.php?msg=success");
exit();
?>
