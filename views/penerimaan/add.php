<?php
require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$db = new DBConnection();
$conn = $db->getConnection();

$username = $_SESSION['user']['username'] ?? 'Tidak diketahui';
$iduser   = $_SESSION['user']['iduser'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Penerimaan Barang</title>

<!-- FONT & ICONS -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
/* ============================================
   GLOBAL STYLE (SESUAI TEMA)
   ============================================ */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%);
    margin: 0;
}

.main-content {
    margin-left: 280px;
    padding: 32px;
}

/* ============================================
   HEADER CARD
   ============================================ */
.header-card {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    padding: 22px 30px;
    color: white;
    font-size: 22px;
    font-weight: 700;
    border-radius: 18px 18px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-card a {
    color: white;
    background: rgba(255,255,255,0.25);
    padding: 8px 18px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
}
.header-card a:hover {
    background: rgba(255,255,255,0.45);
}

/* ============================================
   CARD UTAMA
   ============================================ */
.card-box {
    background: white;
    padding: 32px;
    border-radius: 0 0 18px 18px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

/* ============================================
   FORM GRID
   ============================================ */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 25px;
}

label {
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 6px;
}

select, input.form-control {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    background: #fafffa;
    border: 2px solid #c8e6c9;
    font-size: 15px;
}

select:focus, input:focus {
    border-color: #4caf50;
    outline: none;
    box-shadow: 0 0 0 3px rgba(76,175,80,0.25);
}

input[readonly] {
    background: #ebffef;
    font-weight: 600;
    color: #1b5e20;
}

/* ============================================
   TABLE DETAIL (TEMA BARANG/PENGADAAN)
   ============================================ */
.detail-box {
    background: #f1fdf4;
    border: 2px dashed #86efac;
    border-radius: 18px;
    padding: 22px;
    margin-top: 20px;
}

.detail-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.detail-table th {
    background: #2e7d32;
    color: white;
    padding: 14px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.detail-table td {
    padding: 14px;
    background: white;
    border-bottom: 1px solid #e0e0e0;
    font-size: 14px;
}

input.jumlah-input, input.harga-input {
    width: 110px;
    padding: 6px 8px;
    border-radius: 10px;
    border: 2px solid #c8e6c9;
    text-align: center;
}

input.jumlah-input:focus, input.harga-input:focus {
    border-color: #4caf50;
    box-shadow: 0 0 0 3px rgba(76,175,80,0.25);
}

/* ============================================
   EMPTY STATE
   ============================================ */
.empty-state {
    padding: 40px;
    text-align: center;
    font-style: italic;
    color: #748c70;
}

/* ============================================
   BUTTON SIMPAN
   ============================================ */
.btn-simpan {
    background: #2e7d32;
    color: white;
    padding: 12px 40px;
    border: none;
    border-radius: 50px;
    font-size: 18px;
    font-weight: 600;
    margin-top: 28px;
    transition: 0.3s;
}

.btn-simpan:hover {
    background: #1b5e20;
    box-shadow: 0 10px 25px rgba(46,125,50,0.35);
    transform: translateY(-3px);
}

.btn-simpan:disabled {
    background: #a5a5a5;
    box-shadow: none;
    transform: none;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media(max-width: 900px){
    .main-content { margin-left: 0; padding: 20px; }
    .form-grid { grid-template-columns: 1fr; }
}
</style>
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">

    <!-- HEADER -->
    <div class="header-card">
        <span>Tambah Penerimaan Barang</span>
        <a href="list.php"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <!-- CARD -->
    <div class="card-box">

        <!-- FORM -->
        <form action="store.php" method="POST" id="formPenerimaan">

            <!-- GRID: PENGADAAN + PETUGAS -->
            <div class="form-grid">
                <div>
                    <label>Pilih Pengadaan *</label>
                    <select name="idpengadaan" id="idpengadaan" required>
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

                <div>
                    <label>Petugas</label>
                    <input class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
                    <input type="hidden" name="iduser" value="<?= $iduser ?>">
                </div>
            </div>

            <!-- DETAIL -->
            <div id="detailPengadaanContainer">
                <div class="empty-state">Silakan pilih pengadaan terlebih dahulu.</div>
            </div>

            <center>
                <button type="submit" class="btn-simpan" id="btnSimpan" disabled>
                    Simpan Penerimaan
                </button>
            </center>

        </form>
    </div>
</div>

<script>
document.getElementById("idpengadaan").addEventListener("change", function () {
    const idpengadaan = this.value;

    if (!idpengadaan) {
        document.getElementById("detailPengadaanContainer").innerHTML =
            '<div class="empty-state">Silakan pilih pengadaan terlebih dahulu.</div>';
        document.getElementById("btnSimpan").disabled = true;
        return;
    }

    fetch("get_detail_pengadaan.php?idpengadaan=" + idpengadaan)
        .then(res => res.text())
        .then(html => {
            document.getElementById("detailPengadaanContainer").innerHTML = html;
            const qtyInputs = document.querySelectorAll("input[name^='jumlah_terima']");
            document.getElementById("btnSimpan").disabled = qtyInputs.length === 0;
        })
        .catch(err => alert("Gagal memuat detail pengadaan!"));
});
</script>

</body>
</html>
