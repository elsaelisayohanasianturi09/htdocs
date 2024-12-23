<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn = null;

    // Konstruktor untuk inisialisasi variabel koneksi
    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'v_universe';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
    }

    // Method untuk mendapatkan koneksi database
    public function connect() {
        try {
            // Mencoba membuat koneksi
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            // Set error mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Set charset ke UTF-8MB4 (untuk mendukung emoji dan karakter lainnya)
            $this->conn->exec("set names utf8mb4");

            return $this->conn;
        } catch(PDOException $e) {
            // Log error ke file log dengan detail error
            error_log("Connection Error: " . $e->getMessage(), 3, '/path/to/your/logfile.log');
            throw new Exception("Database connection failed. Please check the logs for details.");
        }
    }

    // Method untuk menutup koneksi
    public function close() {
        $this->conn = null;
    }

    // Method untuk mengecek status koneksi
    public function isConnected() {
        return $this->conn !== null;
    }

    // Method untuk mendapatkan error terakhir
    public function getLastError() {
        return $this->conn ? $this->conn->errorInfo() : null;
    }
}
?>
