<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class MarginPenjualan {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    /**
     * Ambil semua margin (aktif / all)
     */
    public function getAll(string $filter = 'aktif'): array {
        switch ($filter) {
            case 'all':
                $sql = "SELECT * FROM v_margin_penjualan_all ORDER BY tanggal_dibuat DESC";
                break;

            default: // aktif
                $sql = "SELECT * FROM v_margin_penjualan_aktif ORDER BY tanggal_dibuat DESC";
                break;
        }

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Tambah margin menggunakan STORED PROCEDURE
     * SP otomatis nonaktifkan margin lama dan insert margin baru
     */
    public function create(string $id, float $persen, string $iduser): bool
    {
        $sql = "CALL sp_insert_margin(?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sds", $id, $persen, $iduser);
        return $stmt->execute();
    }

    /**
     * Update margin
     */
    public function update(string $id, float $persen, string $iduser): bool {
        // Manual update timestamp
        $stmt = $this->conn->prepare("UPDATE margin_penjualan 
                                      SET persen = ?, iduser = ?, updated_at = CURRENT_TIMESTAMP 
                                      WHERE idmargin_penjualan = ?");
        $stmt->bind_param("dss", $persen, $iduser, $id);
        return $stmt->execute();
    }

    /**
     * Hapus margin
     */
    public function delete(string $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    /**
     * Cek apakah ID sudah ada
     */
    public function isIdExists(string $id): bool {
        $stmt = $this->conn->prepare("SELECT idmargin_penjualan FROM margin_penjualan WHERE idmargin_penjualan = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Ambil user untuk dropdown
     */
    public function getUserOptions(): array {
        $result = $this->conn->query("SELECT iduser, username FROM user ORDER BY username ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>