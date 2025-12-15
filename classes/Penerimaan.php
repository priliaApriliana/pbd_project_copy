<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Penerimaan {
    private $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    // Ambil semua data penerimaan
    public function getAllPenerimaan($status = 'all') {
        $stmt = $this->conn->prepare("CALL sp_get_penerimaan(?)");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    // Ambil detail penerimaan (pakai SP)
    public function getDetailPenerimaan($idpenerimaan) {
        $details = [];
        $stmt = $this->conn->prepare("CALL sp_get_detail_penerimaan(?)");
        $stmt->bind_param("i", $idpenerimaan);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            $details = $res->fetch_all(MYSQLI_ASSOC);
            $res->free_result();
        }
        $stmt->close();
        $this->conn->next_result();
        return $details;
    }

    // Ambil daftar pengadaan untuk dropdown (pakai SP)
    public function getPengadaanDropdown() {
        $data = [];
        $result = $this->conn->query("CALL sp_get_pengadaan_dropdown()");
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $this->conn->next_result();
        }
        return $data;
    }

    // Ambil detail barang dari pengadaan (pakai SP)
    public function getDetailPengadaan($idpengadaan) {
        $data = [];
        $stmt = $this->conn->prepare("CALL sp_get_detail_pengadaan(?)");
        $stmt->bind_param("i", $idpengadaan);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            $data = $res->fetch_all(MYSQLI_ASSOC);
            $res->free_result();
        }
        $stmt->close();
        $this->conn->next_result();
        return $data;
    }

    // Insert penerimaan baru (header)
    public function insertPenerimaan($idpengadaan, $iduser) {
        $status = 'A';
        $stmt = $this->conn->prepare("INSERT INTO penerimaan (status, idpengadaan, iduser) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $status, $idpengadaan, $iduser);
        if ($stmt->execute()) {
            $idpenerimaan = $this->conn->insert_id;
            $stmt->close();
            return $idpenerimaan;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Gagal insert penerimaan: $error");
        }
    }

    // Insert detail penerimaan (memicu trigger stok otomatis)
    public function insertDetailPenerimaan($idpenerimaan, $idbarang, $jumlah, $harga) {
        $stmt = $this->conn->prepare("
            INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
            VALUES (?, ?, ?, ?, 
            hitung_subtotal(?, ?))
        ");
        $stmt->bind_param("isiii", $idpenerimaan, $idbarang, $jumlah, $harga, $harga, $jumlah);
        $ok = $stmt->execute();
        if (!$ok) {
            throw new Exception("Gagal insert detail penerimaan: " . $stmt->error);
        }
        $stmt->close();
        return true;
    }

    // Helper: Ambil data satu penerimaan + vendor + user (view)
    
    public function getPenerimaanById($idpenerimaan) {
        $stmt = $this->conn->prepare("SELECT * FROM v_penerimaan_all WHERE idpenerimaan = ?");
        $stmt->bind_param("i", $idpenerimaan);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function delete(int $id): bool {
        // Hapus juga detail_penerimaan terkait agar tidak orphan
        $this->conn->query("DELETE FROM detail_penerimaan WHERE idpenerimaan = $id");
    
        $stmt = $this->conn->prepare("DELETE FROM penerimaan WHERE idpenerimaan = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
}
?>
