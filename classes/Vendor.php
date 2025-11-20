<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Vendor {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    /**
     * Ambil vendor (aktif / all)
     * - default: all
     */
    public function getAll(string $filter = 'all'): array {
        switch ($filter) {
            case 'aktif':
                $sql = "SELECT * FROM v_vendor_aktif ORDER BY kode_vendor DESC";
                break;

            default: // all
                $sql = "SELECT * FROM v_vendor_all ORDER BY kode_vendor DESC";
                break;
        }

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Tambah vendor
     */
    public function create(string $id, string $nama, string $badanHukum, string $status): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO vendor (idvendor, nama_vendor, badan_hukum, status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $id, $nama, $badanHukum, $status);
        return $stmt->execute();
    }

    /**
     * Update Vendor
     */
    public function update(string $id, string $nama, string $badanHukum, string $status): bool {
        $stmt = $this->conn->prepare("
            UPDATE vendor
            SET nama_vendor = ?, badan_hukum = ?, status = ?
            WHERE idvendor = ?
        ");
        $stmt->bind_param("ssss", $nama, $badanHukum, $status, $id);
        return $stmt->execute();
    }

    /**
     * Hapus Vendor
     */
    public function delete(string $id): bool {
        $stmt = $this->conn->prepare("
            DELETE FROM vendor WHERE idvendor = ?
        ");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}
?>
