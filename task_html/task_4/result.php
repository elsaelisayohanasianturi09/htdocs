<?php
session_start();

if (!isset($_SESSION['formData'])) {
    die("Data tidak ditemukan.");
}

$data = $_SESSION['formData'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil </title>
  <style>
    table { width: 80%; margin: 20px auto; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
  </style>
  <link rel="stylesheet" href="main.css">
</head>
<body>
  <h1>Hasil Pendaftaran</h1>
  <table>
    <tr><th>Nama</th><td><?= htmlspecialchars($data['name']) ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($data['email']) ?></td></tr>
    <tr><th>Umur</th><td><?= htmlspecialchars($data['age']) ?></td></tr>
    <tr><th>Jenis Kelamin</th><td><?= htmlspecialchars($data['gender']) ?></td></tr>
    <tr><th>Browser/OS</th><td><?= htmlspecialchars($data['userAgent']) ?></td></tr>
  </table>

  <h2>Isi File</h2>
  <table>
    <tr><th>Baris</th><th>Isi</th></tr>
    <?php foreach ($data['fileRows'] as $index => $line): ?>
      <tr><td><?= $index + 1 ?></td><td><?= htmlspecialchars($line) ?></td></tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
