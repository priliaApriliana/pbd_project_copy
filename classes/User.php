<?php
require_once(__DIR__ . '/../config/DBConnection.php');

class User {
    private $conn;

    public function __construct() {
        $db = new DBConnection();
        $this->conn = $db->getConnection(); // ✅ ambil koneksi lewat getter
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM user WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // support password_hash dan plain text
            if (password_verify($password, $user['password']) || $user['password'] === $password) {
                return $user;
            }
        }
        return false;
    }

    // 🔹 Menampilkan semua user dan rolenya (pakai view)
    public function getAll(): array {
        $data = [];
        $sql = "SELECT * FROM v_user ORDER BY kode_user DESC";
    
        if ($result = $this->conn->query($sql)) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
        }
        return $data;
    }
    

    // 🔹 Menambahkan user
    public function create(string $id, string $username, string $password, string $idrole): bool {
        $stmt = $this->conn->prepare("INSERT INTO user (iduser, username, password, idrole) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $id, $username, $password, $idrole);
        return $stmt->execute();
    }

    // 🔹 Update user
    public function update(string $id, string $username, string $password, string $idrole): bool {
        $stmt = $this->conn->prepare("UPDATE user SET username=?, password=?, idrole=? WHERE iduser=?");
        $stmt->bind_param("ssss", $username, $password, $idrole, $id);
        return $stmt->execute();
    }

    // 🔹 Hapus user
    public function delete(string $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM user WHERE iduser=?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    // 🔹 Ambil list role aktif (dropdown)
    public function getRoleOptions(): array {
        $result = $this->conn->query("SELECT idrole, nama_role FROM role ORDER BY nama_role ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}

?>

