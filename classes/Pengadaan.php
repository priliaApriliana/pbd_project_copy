<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Pengadaan {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    // ==========================================
    // READ OPERATIONS (Menggunakan VIEW)
    // ==========================================
    
    public function getAll(): array {
        //  PBD: Gunakan VIEW
        $result = $this->conn->query("SELECT * FROM v_pengadaan_all ORDER BY tanggal_pengadaan DESC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getFiltered(string $status = 'all'): array {
        //  PBD: Query ke VIEW dengan filter
        $sql = "SELECT * FROM v_pengadaan_all WHERE 1=1";
        
        switch ($status) {
            case 'pending':
                $sql .= " AND status = 'P'";
                break;
            case 'selesai':
                $sql .= " AND status = 'S'";
                break;
            case 'revisi':
                $sql .= " AND status = 'R'";
                break;
            case 'cancel':
                $sql .= " AND status = 'C'";
                break;
        }
        
        $sql .= " ORDER BY tanggal_pengadaan DESC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $idpengadaan): ?array {
        // PBD: Bisa query ke VIEW atau table
        $stmt = $this->conn->prepare("SELECT * FROM v_pengadaan_all WHERE kode_pengadaan = ?");
        $stmt->bind_param("i", $idpengadaan);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    // ==========================================
    // DETAIL OPERATIONS (Menggunakan SP)
    // ==========================================
    
    public function getDetailById(int $idpengadaan): array {
        //  PBD: Gunakan Stored Procedure
        $stmt = $this->conn->prepare("CALL sp_get_detail_pengadaan_by_id(?)");
        $stmt->bind_param("i", $idpengadaan);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->conn->next_result(); // Clear result set
        return $data;
    }

    // ==========================================
    // CREATE OPERATIONS (Menggunakan SP)
    // ==========================================
    
    public function createHeader(string $iduser, string $idvendor): int|false {
        //  PBD: Gunakan Stored Procedure dengan OUT parameter
        $stmt = $this->conn->prepare("CALL sp_insert_pengadaan(?, ?, @new_id)");
        $stmt->bind_param("ss", $iduser, $idvendor);
        $stmt->execute();
        $stmt->close();
        
        // Ambil OUT parameter
        $result = $this->conn->query("SELECT @new_id AS idpengadaan");
        $row = $result->fetch_assoc();
        
        return !empty($row['idpengadaan']) ? intval($row['idpengadaan']) : false;
    }

    public function createDetail(int $idpengadaan, string $idbarang, int $harga, int $jumlah): bool {
        // PBD: Gunakan Stored Procedure
        // SP akan otomatis:
        // 1. Hitung subtotal dengan Function
        // 2. Update total pengadaan dengan Trigger
        $stmt = $this->conn->prepare("CALL sp_insert_detail_pengadaan(?, ?, ?, ?)");
        $stmt->bind_param("isii", $idpengadaan, $idbarang, $harga, $jumlah);
        $success = $stmt->execute();
        $stmt->close();
        $this->conn->next_result();
        return $success;
    }

    // ==========================================
    // UPDATE OPERATIONS
    // ==========================================
    
    public function updateTotal(int $idpengadaan): bool {
        // 🔥 PBD: Gunakan Stored Procedure
        $stmt = $this->conn->prepare("CALL sp_update_total_pengadaan(?)");
        $stmt->bind_param("i", $idpengadaan);
        $result = $stmt->execute();
        $stmt->close();
        $this->conn->next_result();
        return $result;
    }

    // ==========================================
    // SOFT DELETE (Cancel) - BEST PRACTICE PBD
    // ==========================================
    
    public function cancel(int $idpengadaan): bool {
        // 🔥 PBD: Validasi business logic
        // Cek apakah ada penerimaan selesai
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM penerimaan 
            WHERE idpengadaan = ? AND status = 'S'
        ");
        $stmt->bind_param("i", $idpengadaan);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row['total'] > 0) {
            throw new Exception("Tidak bisa membatalkan pengadaan yang sudah ada penerimaan selesai!");
        }
        
        // 🔥 Soft delete: Update status = 'C'
        $stmt = $this->conn->prepare("UPDATE pengadaan SET status = 'C' WHERE idpengadaan = ?");
        $stmt->bind_param("i", $idpengadaan);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    // ==========================================
    // DROPDOWN OPERATIONS (Menggunakan SP)
    // ==========================================
    
    public function getVendorDropdown(): array {
        // PBD: Gunakan Stored Procedure
        $result = $this->conn->query("CALL sp_get_vendor_dropdown()");
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $this->conn->next_result();
        return $data;
    }

    public function getBarangDropdown(): array {
        // 🔥 PBD: Gunakan Stored Procedure
        $result = $this->conn->query("CALL sp_get_barang_dropdown()");
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $this->conn->next_result();
        return $data;
    }

    // ==========================================
    // HARD DELETE (Jarang dipakai - untuk testing)
    // ==========================================
    
    public function delete(int $idpengadaan): bool {
        // ⚠️ Hard delete - hanya untuk development/testing
        // Production seharusnya pakai cancel()
        
        // Validasi: cek penerimaan
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS jml FROM penerimaan WHERE idpengadaan = ?");
        $stmt->bind_param("i", $idpengadaan);
        $stmt->execute();
        $cek = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($cek['jml'] > 0) {
            throw new Exception("Tidak dapat menghapus pengadaan yang sudah memiliki penerimaan!");
        }

        // Hapus detail
        $stmt = $this->conn->prepare("DELETE FROM detail_pengadaan WHERE idpengadaan = ?");
        $stmt->bind_param("i", $idpengadaan);
        $stmt->execute();
        $stmt->close();

        // Hapus header
        $stmt = $this->conn->prepare("DELETE FROM pengadaan WHERE idpengadaan = ?");
        $stmt->bind_param("i", $idpengadaan);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
?>