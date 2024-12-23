<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'prakpemweb9';


$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


$id = $_GET['id'] ?? '';

if ($id) {
 
    $sql = "DELETE FROM buku WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='index.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='index.php';</script>";
}


$conn->close();
?>
