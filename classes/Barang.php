<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Barang {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    // Ambil dari view barang aktif / all
    public function getAll(string $filter = 'all'): array {
        switch ($filter) {
            case 'all':
                $sql = "SELECT * FROM v_barang_all ORDER BY kode_barang DESC";
                break;
            default:
                $sql = "SELECT * FROM v_barang_aktif ORDER BY kode_barang DESC";
                break;
        }
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } 

    // INSERT langsung ke tabel barang
    public function create(string $id, string $jenis, string $nama, string $idsatuan, int $status, int $harga): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO barang (idbarang, jenis, nama, idsatuan, status, harga)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssii", $id, $jenis, $nama, $idsatuan, $status, $harga);
        return $stmt->execute();
    }

    // UPDATE barang
    public function update(string $id, string $jenis, string $nama, string $idsatuan, int $status, int $harga): bool {
        $stmt = $this->conn->prepare("
            UPDATE barang
            SET jenis = ?, nama = ?, idsatuan = ?, status = ?, harga = ?
            WHERE idbarang = ?
        ");
        $stmt->bind_param("sssiss", $jenis, $nama, $idsatuan, $status, $harga, $id);
        return $stmt->execute();
    }

    // DELETE barang
    public function delete(string $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM barang WHERE idbarang = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function getSatuanOptions(): array {
        $result = $this->conn->query("SELECT kode_satuan, nama_satuan FROM v_satuan_aktif ORDER BY nama_satuan ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(string $id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM barang WHERE idbarang = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    // === BARU: Ambil ID barang terakhir untuk auto generate ===
    public function getLastIdBarang(): ?string
    {
        $sql = "SELECT idbarang FROM barang ORDER BY idbarang DESC LIMIT 1";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['idbarang'];               // misal: B012
        }
        return null;                               // tabel masih kosong
    }
    
}
?>