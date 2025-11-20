<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . '/../../classes/ReturBarang.php');

$returObj = new ReturBarang();
$message = '';
$dataPenerimaan = $returObj->getPenerimaanDropdown();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Retur Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">

    <style>
        .ajax-box {
            border: 1px solid #e5e5e5;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
            background: #fafafa;
        }
    </style>
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">

    <div class="card p-4 shadow-sm">

        <div class="d-flex align-items-center mb-4">
            <img src="https://cdn-icons-png.flaticon.com/512/2331/2331943.png" width="40" class="me-3">
            <h4 class="fw-bold mb-0">↩️ Buat Retur Barang</h4>
        </div>

        <?= $message ?>

        <form action="process_add.php" method="POST">

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-bold">Petugas</label>
                    <input type="text" class="form-control"
                        value="<?= htmlspecialchars($_SESSION['user']['username']) ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Tanggal</label>
                    <input type="text" class="form-control" value="<?= date('d/m/Y H:i:s') ?>" readonly>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-bold">Pilih Penerimaan (Status Selesai)</label>
                    <select name="idpenerimaan" id="selectPenerimaan" class="form-select" required>
                        <option value="">-- Pilih Penerimaan --</option>

                        <?php foreach ($dataPenerimaan as $row): ?>
                            <option value="<?= $row['idpenerimaan'] ?>">
                                <?= htmlspecialchars($row['display_text']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

            </div>

            <!-- AJAX DETAIL AREA -->
            <div id="detailArea" class="ajax-box d-none"></div>

            <div class="alert alert-info mt-3">
                <strong>ℹ️ Info penting:</strong> Setelah memilih penerimaan selesai,
                Anda dapat memilih barang mana yang ingin diretur.
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" id="btnSubmit" class="btn btn-success px-4 d-none">
                    Simpan Retur Barang
                </button>
                <a href="list.php" class="btn btn-secondary px-4">← Kembali</a>
            </div>

        </form>

    </div>
</div>

<script>
document.getElementById('selectPenerimaan').addEventListener('change', function () {
    let id = this.value;

    if (id === '') {
        document.getElementById('detailArea').classList.add('d-none');
        document.getElementById('btnSubmit').classList.add('d-none');
        return;
    }

    fetch("ajax_detail_penerimaan.php?id=" + id)
        .then(res => res.text())
        .then(html => {
            const area = document.getElementById('detailArea');
            area.innerHTML = html;
            area.classList.remove('d-none');
            document.getElementById('btnSubmit').classList.remove('d-none');
        });
});
</script>

</body>
</html>
