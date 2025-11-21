<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Penjualan {
    private $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    // 1️⃣ Ambil semua data penjualan (sesuai struktur DB asli)
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM v_penjualan_all");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 2️⃣ Ambil satu data penjualan berdasarkan ID
    public function getPenjualanById($idpenjualan) {
        $stmt = $this->conn->prepare("
            SELECT 
                p.idpenjualan,
                u.username AS petugas,
                u.username AS kasir,
                m.persen AS margin_persen,
                IFNULL(p.subtotal_nilai, 0) AS subtotal,
                IFNULL(p.ppn, 0) AS ppn,
                IFNULL(p.total_nilai, 0) AS total,
                DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i:%s') AS tanggal
            FROM penjualan p
            LEFT JOIN user u ON p.iduser = u.iduser
            LEFT JOIN margin_penjualan m ON p.idmargin_penjualan = m.idmargin_penjualan
            WHERE p.idpenjualan = ?
        ");
        $stmt->bind_param("s", $idpenjualan);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    // 3️⃣ Ambil detail penjualan (barang yang dijual)
    public function getDetailPenjualan($idpenjualan) {
        $stmt = $this->conn->prepare("
            SELECT 
                dp.iddetail_penjualan,
                dp.idbarang AS barang_idbarang,
                b.nama AS nama_barang,
                dp.harga_satuan AS harga_jual,
                dp.jumlah AS jumlah_jual,
                IFNULL(dp.subtotal, 0) AS sub_total_jual,
                IFNULL(dp.subtotal, 0) AS subtotal,
                sat.nama_satuan
            FROM detail_penjualan dp
            JOIN barang b ON dp.idbarang = b.idbarang
            LEFT JOIN satuan sat ON b.idsatuan = sat.idsatuan
            WHERE dp.penjualan_idpenjualan = ?
        ");
        $stmt->bind_param("s", $idpenjualan);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    // 4️⃣ Ambil barang yang tersedia untuk dijual (ada stok)
    public function getBarangTersedia() {
        $result = $this->conn->query("
            SELECT 
                b.idbarang,
                b.nama AS nama_barang,
                b.harga AS harga_beli,
                IFNULL(fn_cek_stok_barang(b.idbarang), 0) AS stock,
                sat.nama_satuan
            FROM barang b
            LEFT JOIN satuan sat ON b.idsatuan = sat.idsatuan
            WHERE b.status = 1
            AND fn_cek_stok_barang(b.idbarang) > 0
            ORDER BY b.nama
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 4️⃣ B: Alias untuk getBarangTersedia (untuk backward compatibility)
    public function getBarangDropdown() {
        $barangList = $this->getBarangTersedia();
        // Tambahkan display_text untuk dropdown
        foreach ($barangList as &$barang) {
            $barang['display_text'] = $barang['nama_barang'] . ' (' . $barang['nama_satuan'] . ') - Stok: ' . $barang['stock'] . ' - Rp ' . number_format($barang['harga_beli'], 0, ',', '.');
        }
        return $barangList;
    }

    // 5️⃣ Ambil margin penjualan aktif untuk dropdown
    public function getMarginAktif() {
        $result = $this->conn->query("
            SELECT 
                idmargin_penjualan,
                persen,
                CONCAT('Margin ', persen, '%') AS display_text
            FROM margin_penjualan
            WHERE status = 1
            ORDER BY persen ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 6️⃣ Generate ID Penjualan Otomatis
    public function generateIdPenjualan() {
        $result = $this->conn->query("
            SELECT IFNULL(MAX(CAST(SUBSTRING(idpenjualan, 3) AS UNSIGNED)), 0) + 1 AS next_num
            FROM penjualan
        ");
        $row = $result->fetch_assoc();
        return 'PJ' . str_pad($row['next_num'], 3, '0', STR_PAD_LEFT);
    }

    // 7️⃣ Insert penjualan baru (header) - SESUAI STRUKTUR DB ASLI
    public function insertPenjualan($iduser, $idmargin_penjualan) {
        $idpenjualan = $this->generateIdPenjualan();
        
        $stmt = $this->conn->prepare("
            INSERT INTO penjualan (idpenjualan, iduser, idmargin_penjualan, subtotal_nilai, ppn, total_nilai) 
            VALUES (?, ?, ?, 0, 0, 0)
        ");
        $stmt->bind_param("sss", $idpenjualan, $iduser, $idmargin_penjualan);
        
        if ($stmt->execute()) {
            $stmt->close();
            return $idpenjualan;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Gagal insert penjualan: $error");
        }
    }

    // 8️⃣ Insert detail penjualan (memicu trigger stok & update total otomatis)
    public function insertDetailPenjualan($idpenjualan, $idbarang, $jumlah, $harga_jual) {
        $stmt = $this->conn->prepare("
            INSERT INTO detail_penjualan (penjualan_idpenjualan, idbarang, harga_satuan, jumlah, subtotal)
            VALUES (?, ?, ?, ?, 0)
        ");
        $stmt->bind_param("ssii", $idpenjualan, $idbarang, $harga_jual, $jumlah);
        
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Gagal insert detail penjualan: $error");
        }
        $stmt->close();
        return true;
    }

    // 9️⃣ Hapus detail penjualan
    public function deleteDetailPenjualan($iddetail_penjualan) {
        $stmt = $this->conn->prepare("DELETE FROM detail_penjualan WHERE iddetail_penjualan = ?");
        $stmt->bind_param("i", $iddetail_penjualan);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // 🔟 Hapus penjualan (header + detail)
    public function delete($idpenjualan) {
        // Hapus detail terlebih dahulu
        $stmt = $this->conn->prepare("DELETE FROM detail_penjualan WHERE penjualan_idpenjualan = ?");
        $stmt->bind_param("s", $idpenjualan);
        $stmt->execute();
        $stmt->close();
        
        // Hapus header
        $stmt = $this->conn->prepare("DELETE FROM penjualan WHERE idpenjualan = ?");
        $stmt->bind_param("s", $idpenjualan);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // 1️⃣1️⃣ Update total penjualan menggunakan SP yang sudah ada
    public function updateTotalPenjualan($idpenjualan) {
        $stmt = $this->conn->prepare("CALL sp_update_total_penjualan(?)");
        $stmt->bind_param("s", $idpenjualan);
        $result = $stmt->execute();
        $stmt->close();
        $this->conn->next_result(); // Clear result set
        return $result;
    }

    // 1️⃣2️⃣ Hitung total penjualan (untuk summary/dashboard)
    public function getTotalPenjualan($periode = 'all') {
        $where = "";
        if ($periode == 'today') {
            $where = "WHERE DATE(p.created_at) = CURDATE()";
        } elseif ($periode == 'month') {
            $where = "WHERE MONTH(p.created_at) = MONTH(CURDATE()) AND YEAR(p.created_at) = YEAR(CURDATE())";
        }

        $query = "
            SELECT 
                COUNT(*) AS total_transaksi,
                IFNULL(SUM(p.total_nilai), 0) AS total_pendapatan
            FROM penjualan p
            $where
        ";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_assoc() : ['total_transaksi' => 0, 'total_pendapatan' => 0];
    }

    // 1️⃣3️⃣ Alias untuk backward compatibility
    public function createHeader($iduser, $idmargin_penjualan = 'M002') {
        // Jika tidak ada margin, gunakan margin default (10%)
        if (empty($idmargin_penjualan)) {
            $result = $this->conn->query("SELECT idmargin_penjualan FROM margin_penjualan WHERE status = 1 ORDER BY persen ASC LIMIT 1");
            $row = $result->fetch_assoc();
            $idmargin_penjualan = $row['idmargin_penjualan'] ?? 'M002';
        }
        return $this->insertPenjualan($iduser, $idmargin_penjualan);
    }

    public function createDetail($idpenjualan, $idbarang, $jumlah, $harga) {
        return $this->insertDetailPenjualan($idpenjualan, $idbarang, $jumlah, $harga);
    }

    public function updateTotals($idpenjualan) {
        return $this->updateTotalPenjualan($idpenjualan);
    }

    public function getById($idpenjualan) {
        return $this->getPenjualanById($idpenjualan);
    }

    public function getFiltered($kasir, $tanggal)
{
    $sql = "SELECT * FROM v_penjualan_all WHERE 1=1";

    if ($kasir !== 'all') {
        $sql .= " AND kasir = '$kasir'";
    }

    if ($tanggal === 'today') {
        $sql .= " AND DATE(tanggal) = CURDATE()";
    }
    elseif ($tanggal === 'week') {
        $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    }
    elseif ($tanggal === 'month') {
        $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }

    $sql .= " ORDER BY tanggal DESC";

    $result = $this->conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

}
?>