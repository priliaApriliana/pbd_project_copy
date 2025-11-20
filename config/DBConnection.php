<?php
class DBConnection {
    // Konfigurasi koneksi (bisa kamu sesuaikan)
    private string $servername = "localhost";
    private string $username   = "root";
    private string $password   = "";
    private string $dbname     = "db_inventori_baru";

    private mysqli $dbconn;

    public function __construct() {
        // buat koneksi ke MySQL
        $this->dbconn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // cek koneksi
        if ($this->dbconn->connect_error) {
            die(" Koneksi database gagal: " . $this->dbconn->connect_error);
        }

        // pastikan koneksi menggunakan UTF-8 (biar aman untuk teks Indonesia)
        $this->dbconn->set_charset("utf8mb4");
    }

    // method untuk ambil koneksi (digunakan di class lain)
    public function getConnection(): mysqli {
        return $this->dbconn;
    }

    // optional: method untuk menutup koneksi (kalau mau manual)
    public function close(): void {
        if ($this->dbconn) {
            $this->dbconn->close();
        }
    }
}
?>
