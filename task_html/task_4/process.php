<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi data tidak boleh kosong
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $age = intval($_POST['age']);
    $gender = trim($_POST['gender']);
    $file = $_FILES['file'];

    if (empty($name) || empty($email) || empty($age) || empty($gender) || !$file) {
        die("Semua data harus diisi.");
    }

    // Validasi file
    $allowedExtensions = ['txt'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileSize = $file['size'] / 1024; // in KB

    if (!in_array($fileExtension, $allowedExtensions)) {
        die("Hanya file .txt yang diperbolehkan.");
    }
    if ($fileSize > 1024) { // 1 MB max
        die("Ukuran file terlalu besar (maksimal 1 MB).");
    }

    // Baca isi file
    $fileContent = file_get_contents($file['tmp_name']);
    $fileRows = explode(PHP_EOL, $fileContent);

    // Data sistem
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    // Redirect ke result.php dengan data
    session_start();
    $_SESSION['formData'] = compact('name', 'email', 'age', 'gender', 'fileRows', 'userAgent');
    header('Location: result.php');
    exit;
}
?>
