<?php
session_start();

// Tentukan header untuk respon JSON jika diperlukan
header('Content-Type: application/json');

// Sertakan file yang dibutuhkan
require_once 'config.php';
require_once 'Message.php';

// Inisialisasi koneksi database
try {
    $database = new Database();
    $db = $database->connect();
    $message = new Message($db);
} catch (Exception $e) {
    handleError('Koneksi database gagal: ' . $e->getMessage());
}

// Ambil aksi dari POST atau GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Fungsi untuk memvalidasi data input
function validateInput($data) {
    $errors = [];
    
    // Validasi field yang diperlukan
    $required_fields = ['name', 'email', 'country', 'birthdate', 'message', 'rating'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[$field] = ucfirst($field) . ' diperlukan';
        }
    }
    
    // Validasi email
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid';
    }
    
    // Validasi rating
    if (isset($data['rating'])) {
        $rating = filter_var($data['rating'], FILTER_VALIDATE_INT);
        if ($rating === false || $rating < 1 || $rating > 5) {
         $errors['rating'] = 'Rating harus berupa angka valid antara 1 dan 5';
        }
    }

    // Validasi tanggal
    if (!empty($data['birthdate'])) {
        $date = DateTime::createFromFormat('Y-m-d', $data['birthdate']);
        if (!$date || $date->format('Y-m-d') !== $data['birthdate']) {
            $errors['birthdate'] = 'Format tanggal tidak valid';
        }
    }

    return $errors;
}

// Fungsi untuk membersihkan data input
function cleanInput($data) {
    $cleaned = [];
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $cleaned[$key] = htmlspecialchars(strip_tags(trim($value)));
        } else {
            $cleaned[$key] = $value;
        }
    }
    return $cleaned;
}

// Fungsi untuk menangani error
function handleError($message, $errors = [], $redirect = true) {
    // Simpan data form ke session untuk pengisian ulang
    $_SESSION['form_data'] = $_POST;

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // Permintaan AJAX
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ]);
    } else {
        // Pengiriman form biasa
        $_SESSION['status'] = "Error: " . $message;
        $_SESSION['form_errors'] = $errors;
        if ($redirect) {
            header('Location: index.php');
        }
    }
    exit;
}

// Fungsi untuk menangani keberhasilan
function handleSuccess($message, $redirect = true) {
    // Bersihkan data form setelah berhasil
    unset($_SESSION['form_data']);

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // Permintaan AJAX
        echo json_encode([
            'status' => 'success',
            'message' => $message
        ]);
    } else {
        // Pengiriman form biasa
        $_SESSION['status'] = "Success: " . $message;
        if ($redirect) {
            header('Location: index.php');
        }
    }
    exit;
}

// Proses permintaan berdasarkan aksi
try {
    switch($action) {
        case 'create':
            // Validasi input
            $errors = validateInput($_POST);
            if (!empty($errors)) {
                handleError('Harap perbaiki kesalahan berikut:', $errors);
            }

            // Bersihkan dan siapkan data
            $data = cleanInput([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'country' => $_POST['country'],
                'birthdate' => $_POST['birthdate'],
                'message' => $_POST['message'],
                'rating' => (int)$_POST['rating'],
                'browser' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
            ]);

            // Buat pesan
            if($message->create($data)) {
                handleSuccess('Pesan berhasil dikirim!');
            } else {
                handleError('Gagal mengirim pesan');
            }
            break;

        case 'read':
            // Baca pesan tunggal
            if (isset($_GET['id'])) {
                $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
                if ($id === false) {
                    handleError('ID pesan tidak valid');
                }

                $result = $message->readOne($id);
                if ($result) {
                    echo json_encode($result);
                } else {
                    handleError('Pesan tidak ditemukan');
                }
            } 
            // Baca semua pesan
            else {
                $results = $message->read();
                echo json_encode($results);
            }
            break;

        case 'update':
            // Validasi ID
            if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
                handleError('ID pesan tidak valid');
            }

            // Periksa apakah pesan ada
            if (!$message->exists($_POST['id'])) {
                handleError('Pesan tidak ditemukan');
            }

            // Validasi input
            $errors = validateInput($_POST);
            if (!empty($errors)) {
                handleError('Harap perbaiki kesalahan berikut:', $errors);
            }

            // Bersihkan dan siapkan data
            $data = cleanInput([
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'country' => $_POST['country'],
                'birthdate' => $_POST['birthdate'],
                'message' => $_POST['message'],
                'rating' => (int)$_POST['rating']
            ]);

            // Perbarui pesan
            if($message->update($data)) {
                handleSuccess('Pesan berhasil diperbarui!');
            } else {
                handleError('Gagal memperbarui pesan');
            }
            break;

        case 'delete':
            // Validasi ID
            $id = filter_var($_REQUEST['id'], FILTER_VALIDATE_INT);
            if ($id === false) {
                handleError('ID pesan tidak valid');
            }

            // Periksa apakah pesan ada
            if (!$message->exists($id)) {
                handleError('Pesan tidak ditemukan');
            }

            // Hapus pesan
            if($message->delete($id)) {
                handleSuccess('Pesan berhasil dihapus!');
            } else {
                handleError('Gagal menghapus pesan');
            }
            break;

        default:
            handleError('Aksi tidak valid');
            break;
    }
} catch (Exception $e) {
    handleError('Terjadi kesalahan: ' . $e->getMessage());
}
?>
