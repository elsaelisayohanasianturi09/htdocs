<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'prakpemweb9';


$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


$data_per_halaman = 10;


$halaman_sekarang = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_sekarang - 1) * $data_per_halaman;


$total_data_query = "SELECT COUNT(*) AS total FROM buku";
$total_data_result = $conn->query($total_data_query);
$total_data = $total_data_result->fetch_assoc()['total'];


$total_halaman = ceil($total_data / $data_per_halaman);


$sql = "SELECT * FROM buku LIMIT $offset, $data_per_halaman";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku</title>
    <link rel="stylesheet" href="style.css">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
</style>
</head>
<body>
    <div class="container">
        <h1>Daftar Buku</h1>
        <a href="create.php" class="btn">Tambah Buku</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Tahun Terbit</th>
                    <th>Genre</th>
                    <th>Stok</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                        $judul = htmlspecialchars($row['JudulBuku'], ENT_QUOTES, 'UTF-8');
                        $pengarang = htmlspecialchars($row['Pengarang'], ENT_QUOTES, 'UTF-8');
                        $tahun = htmlspecialchars($row['TahunTerbit'], ENT_QUOTES, 'UTF-8');
                        $genre = htmlspecialchars($row['Genre'], ENT_QUOTES, 'UTF-8');
                        $stok = htmlspecialchars($row['Stok'], ENT_QUOTES, 'UTF-8');
                        $deskripsi = htmlspecialchars($row['Deksripsi'], ENT_QUOTES, 'UTF-8');

                        echo "<tr>
                            <td>$id</td>
                            <td>$judul</td>
                            <td>$pengarang</td>
                            <td>$tahun</td>
                            <td>$genre</td>
                            <td>$stok</td>
                            <td>$deskripsi</td>
                            <td>
                                <a href='update.php?id=$id' class='btn btn-edit'>Edit</a>
                                <a href='delete.php?id=$id' class='btn btn-delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>Hapus</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data buku</td></tr>";
                }
                ?>
            </tbody>
        </table>

       
        <div class="pagination">
            <?php if ($halaman_sekarang > 1): ?>
                <a href="?halaman=<?php echo $halaman_sekarang - 1; ?>" class="btn">Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                <a href="?halaman=<?php echo $i; ?>" class="btn <?php echo ($i == $halaman_sekarang) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($halaman_sekarang < $total_halaman): ?>
                <a href="?halaman=<?php echo $halaman_sekarang + 1; ?>" class="btn">Berikutnya</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>
