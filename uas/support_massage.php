<?php
class SupportMessage {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Simpan data ke database
    public function save($data) {
        $sql = "INSERT INTO support_messages (name, email, country, birthdate, message, rating, ip_address, user_agent) 
                VALUES (:name, :email, :country, :birthdate, :message, :rating, :ip_address, :user_agent)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    // Validasi data
    public function validate($data) {
        if (empty($data['name']) || strlen($data['name']) < 3) {
            return "Nama tidak valid.";
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return "Email tidak valid.";
        }
        if (empty($data['country'])) {
            return "Negara asal harus diisi.";
        }
        if (empty($data['birthdate'])) {
            return "Tanggal lahir harus diisi.";
        }
        if (strlen($data['message']) < 10) {
            return "Pesan harus memiliki minimal 10 karakter.";
        }
        if (!in_array($data['rating'], ['1', '2', '3', '4', '5'])) {
            return "Rating tidak valid.";
        }
        return true;
    }

    // Ambil semua pesan
    public function getAllMessages() {
        $sql = "SELECT * FROM support_messages ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hapus pesan berdasarkan ID
    public function delete($id) {
        $sql = "DELETE FROM support_messages WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>
