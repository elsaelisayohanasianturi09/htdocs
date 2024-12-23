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
$judul = $pengarang = $tahun = $genre = $stok = $deskripsi = '';

if ($id) {
    $sql = "SELECT * FROM buku WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $judul = $data['JudulBuku'];
        $pengarang = $data['Pengarang'];
        $tahun = $data['TahunTerbit'];
        $genre = $data['Genre'];
        $stok = $data['Stok'];
        $deskripsi = $data['Deksripsi'];
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun'];
    $genre = $_POST['genre'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $sql = "UPDATE buku SET JudulBuku = ?, Pengarang = ?, TahunTerbit = ?, Genre = ?, Stok = ?, Deksripsi = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $judul, $pengarang, $tahun, $genre, $stok, $deskripsi, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diupdate!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data!');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Buku</title>
    <link rel="stylesheet" href="style.css">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
</style>
</head>
<body>
    <div class="container">
        <h1>Update Buku</h1>
        <form method="POST" action="">
            <label for="judul">Judul Buku</label>
            <input type="text" id="judul" name="judul" value="<?php echo $judul; ?>" required>

            <label for="pengarang">Pengarang</label>
            <input type="text" id="pengarang" name="pengarang" value="<?php echo $pengarang; ?>" required>

            <label for="tahun">Tahun Terbit</label>
            <input type="number" id="tahun" name="tahun" value="<?php echo $tahun; ?>" required>

            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?php echo $genre; ?>" required>

            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" value="<?php echo $stok; ?>" required>

            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="5" required><?php echo $deskripsi; ?></textarea>

            <button type="submit" class="btn">Simpan</button>
            <a href="index.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>
