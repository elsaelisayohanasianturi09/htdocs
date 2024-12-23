<?php
class Message {
    // Properti database
    private $conn;
    private $table = 'messages';

    // Konstruktor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Membuat Pesan
    public function create($data) {
        try {
            // Membuat query
            $query = "INSERT INTO " . $this->table . "
                    (name, email, country, birthdate, message, rating, browser, ip_address)
                    VALUES
                    (:name, :email, :country, :birthdate, :message, :rating, :browser, :ip)";

            // Menyiapkan pernyataan
            $stmt = $this->conn->prepare($query);

            // Membersihkan data
            $data = $this->cleanData($data);

            // Mengikat data
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':country', $data['country']);
            $stmt->bindParam(':birthdate', $data['birthdate']);
            $stmt->bindParam(':message', $data['message']);
            $stmt->bindParam(':rating', $data['rating']);
            $stmt->bindParam(':browser', $data['browser']);
            $stmt->bindParam(':ip', $data['ip']);

            // Menjalankan query
            if($stmt->execute()) {
                // Secara opsional menyimpan data dalam sesi untuk menjaga status antara reload halaman
                $_SESSION['last_message'] = $data;
                return true;
            }

            return false;
        } catch(PDOException $e) {
            error_log("Error Create: " . $e->getMessage());
            return false;
        }
    }

    // Membaca Semua Pesan
    public function read() {
        try {
            // Membuat query
            $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";

            // Menyiapkan pernyataan
            $stmt = $this->conn->prepare($query);

            // Menjalankan query
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error Read: " . $e->getMessage());
            return [];
        }
    }

    // Membaca Pesan Tunggal
    public function readOne($id) {
        try {
            // Membuat query
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";

            // Menyiapkan pernyataan
            $stmt = $this->conn->prepare($query);

            // Mengikat ID
            $stmt->bindParam(':id', $id);

            // Menjalankan query
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error ReadOne: " . $e->getMessage());
            return false;
        }
    }

    // Memperbarui Pesan
    public function update($data) {
        try {
            // Membuat query
            $query = "UPDATE " . $this->table . "
                    SET name = :name,
                        email = :email,
                        country = :country,
                        birthdate = :birthdate,
                        message = :message,
                        rating = :rating
                    WHERE id = :id";

            // Menyiapkan pernyataan
            $stmt = $this->conn->prepare($query);

            // Membersihkan data
            $data = $this->cleanData($data);

            // Mengikat data
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':country', $data['country']);
            $stmt->bindParam(':birthdate', $data['birthdate']);
            $stmt->bindParam(':message', $data['message']);
            $stmt->bindParam(':rating', $data['rating']);

            // Menjalankan query
            if($stmt->execute()) {
                return true;
            }

            return false;
        } catch(PDOException $e) {
            error_log("Error Update: " . $e->getMessage());
            return false;
        }
    }

    // Menghapus Pesan
    public function delete($id) {
        try {
            // Membuat query
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";

            // Menyiapkan pernyataan
            $stmt = $this->conn->prepare($query);

            // Mengikat id
            $stmt->bindParam(':id', $id);

            // Menjalankan query
            if($stmt->execute()) {
                return true;
            }

            return false;
        } catch(PDOException $e) {
            error_log("Error Delete: " . $e->getMessage());
            return false;
        }
    }

    // Metode Membersihkan Data
    private function cleanData($data) {
        foreach($data as $key => $value) {
            if(is_string($value)) {
                $data[$key] = htmlspecialchars(strip_tags($value));
            }
        }
        return $data;
    }

    // Memverifikasi Pesan Ada
    public function exists($id) {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error Exists: " . $e->getMessage());
            return false;
        }
    }
}
?>
