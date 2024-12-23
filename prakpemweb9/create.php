<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'prakpemweb9';


$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun'];
    $genre = $_POST['genre']; // VARCHAR
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi']; // TEXT


    $sql = "INSERT INTO buku (JudulBuku, Pengarang, TahunTerbit, Genre, Stok, Deksripsi) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $judul, $pengarang, $tahun, $genre, $stok, $deskripsi);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data!');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku</title>
    <link rel="stylesheet" href="style.css">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
</style>
</head>
<body>
    <div class="container">
        <h1>Tambah Buku</h1>
        <form method="POST" action="">
            <label for="judul">Judul Buku</label>
            <input type="text" id="judul" name="judul" maxlength="255" required>

            <label for="pengarang">Pengarang</label>
            <input type="text" id="pengarang" name="pengarang" maxlength="255" required>

            <label for="tahun">Tahun Terbit</label>
            <input type="number" id="tahun" name="tahun" min="1500" max="<?php echo date('Y'); ?>" required>

            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" maxlength="100" required> <!-- Sesuai tipe VARCHAR -->

            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" min="0" required>

            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="5" maxlength="65535" required></textarea> <!-- Sesuai tipe TEXT -->

            <button type="submit" class="btn">Simpan</button>
            <a href="index.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>
