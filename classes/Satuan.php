<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Satuan {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    /**
     * Ambil satuan (aktif / all)
     * - default: all
     */
    public function getAll(string $filter = 'all'): array {
        switch ($filter) {

            case 'aktif':
                $sql = "SELECT * FROM v_satuan_aktif ORDER BY kode_satuan DESC";
                break;

            default: // all
                $sql = "SELECT * FROM v_satuan_all ORDER BY kode_satuan DESC";
                break;
        }

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Tambah satuan baru
     */
    public function create(string $id, string $nama, int $status): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO satuan (idsatuan, nama_satuan, status)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ssi", $id, $nama, $status);
        return $stmt->execute();
    }

    /**
     * Update data satuan
     */
    public function update(string $id, string $nama, int $status): bool {
        $stmt = $this->conn->prepare("
            UPDATE satuan
            SET nama_satuan = ?, status = ?
            WHERE idsatuan = ?
        ");
        $stmt->bind_param("sis", $nama, $status, $id);
        return $stmt->execute();
    }

    /**
     * Hapus satuan berdasarkan ID
     */
    public function delete(string $id): bool {
        $stmt = $this->conn->prepare("
            DELETE FROM satuan
            WHERE idsatuan = ?
        ");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}
?>
