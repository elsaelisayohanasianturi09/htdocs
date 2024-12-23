<?php
$conn = new mysqli('localhost', 'root', '', 'prakpemweb');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];

    $sql = "INSERT INTO mahasiswa (nim, nama, prodi) VALUES ('$nim', '$nama', '$prodi')";
    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error;
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM mahasiswa");
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

$query = "SELECT * FROM mahasiswa LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 90%;
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    h1 {
        font-size: 24px;
        text-align: center;
        margin-bottom: 20px;
        color: #444;
    }
    .form-container {
        margin-bottom: 20px;
    }
    input, select, button {
        padding: 10px;
        margin: 5px 0;
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    button {
        background-color: #007BFF;
        color: #fff;
        border: none;
        cursor: pointer;
    }
    button:hover {
        background-color: #0056b3;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f0f0f0;
    }
    tr:hover {
        background-color: #f9f9f9;
    }
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
    .pagination a {
        padding: 8px 12px;
        margin: 0 5px;
        text-decoration: none;
        color: #007BFF;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .pagination a:hover {
        background-color: #f0f0f0;
    }
</style>

</head>
<body>
    <div class="container">
        <h1>Data Mahasiswa</h1>
        <div class="form-container">
            <form method="POST">
                <input type="text" name="nim" placeholder="NIM" required>
                <input type="text" name="nama" placeholder="Nama" required>
                <input type="text" name="prodi" placeholder="Prodi" required>
                <button type="submit">Tambahkan Data</button>
            </form>
        </div>


        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nim'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['prodi'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <?php for ($i = 0; $i < $limit; $i++): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Data Kosong</td>
                        </tr>
                    <?php endfor; ?>
                <?php endif; ?>
            </tbody>
        </table>


        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">Previous</a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
