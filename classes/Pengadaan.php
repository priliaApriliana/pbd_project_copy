<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Pengadaan {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    // =========================
    // 1️⃣ Ambil semua data pengadaan (pakai VIEW)
    // =========================
    public function getAllFromView(string $filter = 'all'): array {
        if ($filter === 'pending') {
            $sql = "SELECT * FROM v_pengadaan_pending ORDER BY kode_pengadaan DESC, tanggal_pengadaan DESC";
        } else {
            $sql = "SELECT * FROM v_pengadaan_all ORDER BY kode_pengadaan DESC, tanggal_pengadaan DESC";
        }

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // =========================
    // 2️⃣ Ambil detail pengadaan (pakai SP)
    // =========================
    public function getDetailById(int $idpengadaan): array {
        $stmt = $this->conn->prepare("CALL sp_get_detail_pengadaan_by_id(?)");
        $stmt->bind_param("i", $idpengadaan);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $stmt->close();
        $this->conn->next_result();
        return $data;
    }

    // =========================
    // 3️⃣ Insert Header Pengadaan (pakai SP_sp_insert_pengadaan)
    // =========================
    public function createHeader(string $idvendor, string $iduser): int|false {

        // SP memiliki OUT parameter @new_id
        $stmt = $this->conn->prepare("CALL sp_insert_pengadaan(?, ?, @new_id)");
        $stmt->bind_param("ss", $iduser, $idvendor);
        $stmt->execute();
        $stmt->close();

        // Ambil output dari stored procedure
        $res = $this->conn->query("SELECT @new_id AS idpengadaan");
        $row = $res->fetch_assoc();

        return !empty($row['idpengadaan']) ? intval($row['idpengadaan']) : false;
    }

    // =========================
    // 4️⃣ Insert Detail Pengadaan (pakai SP_sp_insert_detail_pengadaan)
    // =========================
    public function createDetail(int $idpengadaan, string $idbarang, int $jumlah, int $harga): bool {

        $stmt = $this->conn->prepare("CALL sp_insert_detail_pengadaan(?, ?, ?, ?)");
        $stmt->bind_param(
            "isii",
            $idpengadaan,
            $idbarang,
            $harga,
            $jumlah
        );

        $success = $stmt->execute();
        $stmt->close();
        $this->conn->next_result();
        return $success;
    }

    // =========================
    // 5️⃣ Dropdown Vendor (pakai SP)
    // =========================
    public function getVendorDropdown(): array {
        $res = $this->conn->query("CALL sp_get_vendor_dropdown()");
        $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        $this->conn->next_result();
        return $data;
    }

    // =========================
    // 6️⃣ Dropdown Barang (pakai SP)
    // =========================
    public function getBarangDropdown(): array {
        $res = $this->conn->query("CALL sp_get_barang_dropdown()");
        $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        $this->conn->next_result();
        return $data;
    }

    // =========================
    // 7️⃣ Hapus pengadaan (AMAN)
    // =========================
    public function delete(int $idpengadaan): bool {

        // 1. Cek apakah sudah ada penerimaan
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS jml
            FROM penerimaan
            WHERE idpengadaan = ?
        ");
        $stmt->bind_param("i", $idpengadaan);
        $stmt->execute();
        $cek = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($cek['jml'] > 0) {
            throw new Exception("Pengadaan tidak dapat dihapus karena sudah memiliki penerimaan!");
        }

        // 2. Hapus detail
        $stmtDetail = $this->conn->prepare("DELETE FROM detail_pengadaan WHERE idpengadaan = ?");
        $stmtDetail->bind_param("i", $idpengadaan);
        $stmtDetail->execute();
        $stmtDetail->close();

        // 3. Hapus header
        $stmtHeader = $this->conn->prepare("DELETE FROM pengadaan WHERE idpengadaan = ?");
        $stmtHeader->bind_param("i", $idpengadaan);
        $success = $stmtHeader->execute();
        $stmtHeader->close();

        return $success;
    }
}
?>
