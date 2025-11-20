<?php
require_once(__DIR__ . '/../config/DBConnection.php');

class BaseModel {
    private $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection();
    }

    /**
     * Ambil semua data dari tabel tertentu
     * @param string $table — nama tabel
     * @param array $columns — daftar kolom (default: semua kolom)
     * @param string|null $orderBy — kolom untuk urutan data
     */
    public function getAll(string $table, array $columns = ['*'], ?string $orderBy = null): array {
        $colList = implode(', ', $columns);
        $sql = "SELECT $colList FROM $table";
        if ($orderBy) $sql .= " ORDER BY $orderBy";

        $result = $this->conn->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    /**
     * Ambil satu data berdasarkan ID
     */
    public function getById(string $table, string $idColumn, $id) {
        $stmt = $this->conn->prepare("SELECT * FROM $table WHERE $idColumn = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Hapus data berdasarkan ID
     */
    public function delete(string $table, string $idColumn, $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM $table WHERE $idColumn = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    /**
     * Tambah data (dinamis)
     */
    public function insert(string $table, array $data): bool {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $types = str_repeat('s', count($data));
        $values = array_values($data);

        $stmt = $this->conn->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    /**
     * Update data berdasarkan ID
     */
    public function update(string $table, string $idColumn, $id, array $data): bool {
        $setClause = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $types = str_repeat('s', count($data)) . 's';
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->conn->prepare("UPDATE $table SET $setClause WHERE $idColumn = ?");
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }
}
?>
