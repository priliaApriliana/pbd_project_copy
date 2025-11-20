<?php
require_once(__DIR__ . "/../config/DBConnection.php");

class Dashboard {
    private mysqli $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    public function getCount(string $table): int {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM $table");
        $data = $result->fetch_assoc();
        return $data['total'] ?? 0;
    }

    public function getTotalPenjualan(): int {
        $result = $this->conn->query("SELECT SUM(total_nilai) AS total FROM penjualan");
        $data = $result->fetch_assoc();
        return $data['total'] ?? 0;
    }

    public function getTotalPengadaan(): int {
        $result = $this->conn->query("SELECT SUM(total_nilai) AS total FROM pengadaan");
        $data = $result->fetch_assoc();
        return $data['total'] ?? 0;
    }

    public function getTopBarang(): array {
        $sql = "
            SELECT b.nama, SUM(dp.jumlah) AS total_terjual
            FROM detail_penjualan dp
            JOIN barang b ON dp.idbarang = b.idbarang
            GROUP BY b.nama
            ORDER BY total_terjual DESC
            LIMIT 5";
        $res = $this->conn->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
?>