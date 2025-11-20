<?php
require_once(__DIR__ . '/../../config/DBConnection.php');

$db = new DBConnection();
$conn = $db->getConnection();

// ===== VALIDASI ID PENGADAAN =====
if (!isset($_GET['idpengadaan'])) {
    echo "<div class='alert alert-danger'>ID Pengadaan tidak ditemukan!</div>";
    exit;
}

$idpengadaan = intval($_GET['idpengadaan']);

// =====================================================
// 🔥 GUNAKAN STORED PROCEDURE:
//     sp_get_barang_pengadaan_belum_terima
// Hanya menampilkan barang yg masih punya sisa qty
// =====================================================
$stmt = $conn->prepare("CALL sp_get_barang_pengadaan_belum_terima(?)");
$stmt->bind_param("i", $idpengadaan);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='alert alert-success fw-bold'>
            ✔ Semua barang pada pengadaan ini sudah diterima seluruhnya.
          </div>";
    exit;
}

// Hitung jumlah baris barang
$total_barang = $res->num_rows;
?>

<table class="table table-bordered table-hover mt-3 align-middle">
    <thead class="table-light text-center">
        <tr>
            <th>Barang</th>
            <th>Satuan</th>
            <th>Dipesan</th>
            <th>Sudah Terima</th>
            <th>Sisa</th>
            <th>Jumlah Terima</th>
            <th>Harga Terima</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $res->fetch_assoc()): ?>

        <?php
            // ===== RULE MIN QTY =====
            // jika hanya ada 1 barang → min 1
            // jika > 1 barang → min 0
            $minQty = ($total_barang == 1) ? 1 : 0;

            // jika sisa = 0 → disable input
            $disabled = ($row['sisa_belum_terima'] <= 0) ? "disabled" : "";
        ?>

        <tr class="<?= ($row['sisa_belum_terima'] <= 0) ? 'table-danger' : '' ?>">
            
            <!-- Nama Barang -->
            <td><?= $row['nama_barang'] ?></td>

            <!-- Satuan -->
            <td class="text-center"><?= $row['nama_satuan'] ?></td>

            <!-- Jumlah Pesan -->
            <td class="text-center"><?= $row['jumlah_pesan'] ?></td>

            <!-- Sudah Terima -->
            <td class="text-center fw-bold text-success"><?= $row['total_terima'] ?></td>

            <!-- Sisa -->
            <td class="text-center fw-bold text-primary"><?= $row['sisa_belum_terima'] ?></td>

            <!-- Input Jumlah Terima -->
            <td style="width:140px;">
                <input type="number"
                    name="jumlah_terima[]"
                    class="form-control"
                    min="<?= $minQty ?>"
                    max="<?= $row['sisa_belum_terima'] ?>"
                    value="<?= $minQty ?>" 
                    <?= $disabled ?>
                    required>
            </td>

            <!-- Input Harga Terima -->
            <td style="width:160px;">
                <input type="number"
                    name="harga_satuan_terima[]"
                    class="form-control"
                    min="0"
                    value="<?= $row['harga_satuan'] ?>"
                    required>
            </td>

            <!-- Hidden idbarang -->
            <input type="hidden" name="barang_id[]" value="<?= $row['idbarang'] ?>">

        </tr>

        <?php endwhile; ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->next_result();
?>
