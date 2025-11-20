<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Role {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    // 🔹 Ambil semua role (VIEW)
    public function getAll(): array {
        $sql = "SELECT * FROM v_role ORDER BY kode_role DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // 🔹 Create role (INSERT via VIEW)
    public function create(string $kode, string $nama): bool {
        $stmt = $this->conn->prepare("INSERT INTO v_role (kode_role, nama_role) VALUES (?, ?)");
        $stmt->bind_param("ss", $kode, $nama);
        return $stmt->execute();
    }

    // 🔹 Update role (UPDATE via VIEW)
    public function update(string $kode, string $nama): bool {
        $stmt = $this->conn->prepare("UPDATE v_role SET nama_role = ? WHERE kode_role = ?");
        $stmt->bind_param("ss", $nama, $kode);
        return $stmt->execute();
    }

    // 🔹 Delete role (DELETE via VIEW)
    public function delete(string $kode): bool {
        $stmt = $this->conn->prepare("DELETE FROM v_role WHERE kode_role = ?");
        $stmt->bind_param("s", $kode);
        return $stmt->execute();
    }
}
?>
