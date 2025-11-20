<?php
/**
 * File: views/penerimaan/add.php
 * FINAL VERSION – dengan validasi min/max jumlah terima
 */

require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

// Cek login user
if (!isset($_SESSION['user']['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$db = new DBConnection();
$conn = $db->getConnection();

$username = $_SESSION['user']['username'];
$iduser   = $_SESSION['user']['iduser'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penerimaan Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">
    <div class="card p-4 shadow-sm">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">📥 Tambah Penerimaan Barang</h2>
            <a href="list.php" class="btn btn-secondary">⮜ Kembali</a>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <form action="store.php" method="POST">

            <!-- PILIH PENGADAAN -->
            <div class="mb-3">
                <label class="form-label fw-bold">📦 Pilih Pengadaan</label>
                <select name="idpengadaan" id="idpengadaan" class="form-select" required>
                    <option value="">-- Pilih Pengadaan --</option>

                    <?php
                    $res = $conn->query("CALL sp_get_pengadaan_dropdown()");
                    if ($res) {
                        while ($row = $res->fetch_assoc()) {
                            echo "<option value='{$row['idpengadaan']}'>{$row['display_text']}</option>";
                        }
                        $res->free_result();
                        $conn->next_result();
                    }
                    ?>
                </select>
            </div>

            <!-- PETUGAS -->
            <div class="mb-3">
                <label class="form-label fw-bold">👤 Petugas</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
                <input type="hidden" name="iduser" value="<?= $iduser ?>">
            </div>

            <hr>
            <h5 class="fw-bold mb-3">📝 Detail Barang pada Pengadaan</h5>

            <!-- Container detail barang -->
            <div id="detailPengadaanContainer">
                <div class="alert alert-info">Silakan pilih pengadaan terlebih dahulu.</div>
            </div>

            <hr class="my-4">

            <button type="submit" class="btn btn-success btn-lg" id="btnSimpan" disabled>
                ✅ Simpan Penerimaan
            </button>

        </form>
    </div>
</div>

<!-- ========================================================= -->
<!-- AJAX + VALIDASI MIN/MAX JUMLAH TERIMA (FINAL) -->
<!-- ========================================================= -->
<script>
document.getElementById("idpengadaan").addEventListener("change", function () {

    const idpengadaan = this.value;

    if (!idpengadaan) {
        document.getElementById("detailPengadaanContainer").innerHTML =
            '<div class="alert alert-info">Silakan pilih pengadaan terlebih dahulu.</div>';
        document.getElementById("btnSimpan").disabled = true;
        return;
    }

    fetch("get_detail_pengadaan.php?idpengadaan=" + idpengadaan)
        .then(res => res.text())
        .then(html => {

            document.getElementById("detailPengadaanContainer").innerHTML = html;

            // Ambil semua input qty
            const qtyInputs = document.querySelectorAll("input[name^='jumlah_terima']");
            const totalBarang = qtyInputs.length;

            // RULE VALIDASI MIN/MAX
            qtyInputs.forEach(input => {
                let maxValue = parseInt(input.getAttribute("max"));

                if (totalBarang === 1) {
                    // jika hanya 1 barang → minimal harus 1
                    input.min = 1;
                    input.value = maxValue > 0 ? 1 : 0;
                } else {
                    // lebih dari 1 barang → boleh 0
                    input.min = 0;
                    input.value = 0;
                }
            });

            document.getElementById("btnSimpan").disabled = (totalBarang === 0);
        })
        .catch(err => {
            alert("Gagal memuat detail pengadaan!");
            console.error(err);
        });

});
</script>

</body>
</html>
