<?php
require_once(__DIR__ . '/../config/DBConnection.php');

class ReturBarang {
    private $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    /* ============================================================
       1. DROPDOWN PENERIMAAN (status Selesai)
    ============================================================ */
    public function getPenerimaanDropdown() {
        $data = [];

        $res = $this->conn->query("CALL sp_get_penerimaan_dropdown()");
        if ($res) {
            $data = $res->fetch_all(MYSQLI_ASSOC);
            $res->free();
            $this->conn->next_result();
        }
        return $data;
    }


    /* ============================================================
       2. DETAIL PENERIMAAN UNTUK FORM RETUR
    ============================================================ */
    public function getDetailPenerimaan($idpenerimaan) {
        $stmt = $this->conn->prepare("CALL sp_get_detail_penerimaan(?)");
        $stmt->bind_param("i", $idpenerimaan);
        $stmt->execute();

        $res = $stmt->get_result();
        $data = $res->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        $this->conn->next_result();
        return $data;
    }


    /* ============================================================
       3. INSERT HEADER RETUR
    ============================================================ */
    public function createRetur($idpenerimaan, $iduser) {
        $stmt = $this->conn->prepare("
            INSERT INTO retur_barang (idpenerimaan, iduser) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $idpenerimaan, $iduser);

        if ($stmt->execute()) {
            $newId = $this->conn->insert_id;
            $stmt->close();
            return $newId;
        }

        $error = $stmt->error;
        $stmt->close();
        throw new Exception("Gagal membuat retur: $error");
    }


    /* ============================================================
       4. INSERT DETAIL RETUR
    ============================================================ */
    public function createDetailRetur($idretur, $iddetail_penerimaan, $jumlah, $alasan) {

        // hindari duplikasi — generate ID retur yang aman
        $kode = "DR" . str_pad(rand(1, 99999), 5, "0", STR_PAD_LEFT);

        $stmt = $this->conn->prepare("
            INSERT INTO detail_retur
            (iddetail_retur, jumlah, alasan, idretur, iddetail_penerimaan)
            VALUES (?, ?, ?, ?, ?)
        "); 

        $stmt->bind_param("sisii", $kode, $jumlah, $alasan, $idretur, $iddetail_penerimaan);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Gagal menambah detail retur: $error");
        }

        $stmt->close();
        return true;
    }


    /* ============================================================
       5. GET LIST RETUR (untuk list.php)
    ============================================================ */
    public function getAll() {
        $res = $this->conn->query("SELECT * FROM v_retur_all");

        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }


    /* ============================================================
       6. GET HEADER RETUR (untuk detail.php)
    ============================================================ */
    public function getHeader($idretur) {
        $stmt = $this->conn->prepare("
            SELECT * FROM v_retur_all WHERE idretur = ?
        ");
        $stmt->bind_param("i", $idretur);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        $stmt->close();
        return $result;
    }


    /* ============================================================
       7. GET DETAIL RETUR (untuk detail.php)
    ============================================================ */
    public function getDetail($idretur) {
        $stmt = $this->conn->prepare("
            SELECT * FROM v_retur_detail WHERE idretur = ?
        ");
        $stmt->bind_param("i", $idretur);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        return $result;
    }

    /* ============================================================
       8. DELETE DETAIL RETUR (untuk delete.php)
    ============================================================ */
    public function deleteDetailByReturId($idretur) {
        $stmt = $this->conn->prepare("
            DELETE FROM detail_retur WHERE idretur = ?
        ");
        $stmt->bind_param("i", $idretur);
        $ok = $stmt->execute();
        $stmt->close();
    
        if (!$ok) {
            throw new Exception("Gagal menghapus detail retur!");
        }
    }

    /* ============================================================
       9. hapus header retur 
    ============================================================ */
    public function deleteReturHeader($idretur) {
        $stmt = $this->conn->prepare("
            DELETE FROM retur_barang WHERE idretur = ?
        ");
        $stmt->bind_param("i", $idretur);
        $ok = $stmt->execute();
        $stmt->close();
    
        if (!$ok) {
            throw new Exception("Gagal menghapus retur!");
        }
    }
    

}
?>
