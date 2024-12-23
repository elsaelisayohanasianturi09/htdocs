<?php
// Menyertakan file konfigurasi database dan kelas Message
require_once 'config.php';  // File untuk konfigurasi database
require_once 'Message.php'; // File untuk kelas Message yang berfungsi untuk mengambil data pesan

// Membuat instance objek Database dan Message
$database = new Database();  // Membuat objek Database
$db = $database->connect();  // Menghubungkan ke database
$message = new Message($db);  // Membuat objek Message dengan koneksi database

// Inisialisasi variabel messageData dengan nilai null
$messageData = null;

// Mengecek apakah ada parameter 'id' di query string untuk mengambil data pesan spesifik
if(isset($_GET['id'])) {
    // Mengambil data pesan berdasarkan ID yang diberikan
    $messageData = $message->readOne($_GET['id']);
}

// Jika data pesan tidak ditemukan, redirect ke halaman utama (index)
if(!$messageData) {
    header('Location: index.php');  // Arahkan ulang ke halaman index
    exit;  // Hentikan eksekusi lebih lanjut
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Message - V Universe</title>
    <link rel="stylesheet" href="style/edit.css"> <!-- Menyertakan file CSS khusus untuk halaman edit -->
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Lilita+One&family=Merienda:wght@300..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    </style>
</head>
<body>
    <header>
        <h1>V Universe</h1>
        <p>Edit Your Support Message</p> <!-- Judul halaman -->
    </header>

    <div class="container">
        <!-- Tombol untuk kembali ke halaman pesan -->
        <div class="back-button">
            <a href="index.php">Back to Messages</a> <!-- Link kembali ke halaman index -->
        </div>

        <div class="form-container">
            <form action="process.php" method="POST">
                <!-- Input tersembunyi untuk menunjukkan aksi 'update' -->
                <input type="hidden" name="action" value="update">
                <!-- Input tersembunyi untuk mengirimkan ID pesan yang akan diupdate -->
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($messageData['id']); ?>">

                <!-- Form group untuk input 'name' -->
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($messageData['name']); ?>" required>
                    <div class="error" id="nameError">Name is required</div> <!-- Pesan error jika field 'name' kosong -->
                </div>

                <!-- Form group untuk input 'email' -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($messageData['email']); ?>" required>
                    <div class="error" id="emailError">Valid email is required</div> <!-- Pesan error jika format email salah -->
                </div>

                <!-- Form group untuk input 'country' -->
                <div class="form-group">
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($messageData['country']); ?>" required>
                    <div class="error" id="countryError">Country is required</div> <!-- Pesan error jika field 'country' kosong -->
                </div>

                <!-- Form group untuk input 'birthdate' -->
                <div class="form-group">
                    <label for="birthdate">Birth Date:</label>
                    <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($messageData['birthdate']); ?>" required>
                    <div class="error" id="birthdateError">Birth date is required</div> <!-- Pesan error jika field 'birthdate' kosong -->
                </div>

                <!-- Form group untuk input 'message' -->
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4" required><?php echo htmlspecialchars($messageData['message']); ?></textarea>
                    <div class="error" id="messageError">Message is required</div> <!-- Pesan error jika field 'message' kosong -->
                </div>

                <!-- Form group untuk rating dengan seleksi bintang -->
                <div class="form-group">
                    <label>Rating:</label>
                    <div class="rating">
                        <?php 
                        // Membuat 5 bintang, sorot yang aktif sesuai dengan rating pesan saat ini
                        for($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo $i <= $messageData['rating'] ? 'active' : ''; ?>" data-rating="<?php echo $i; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <!-- Input tersembunyi untuk menyimpan rating yang dipilih -->
                    <input type="hidden" name="rating" id="rating" value="<?php echo htmlspecialchars($messageData['rating']); ?>">
                    <div class="error" id="ratingError">Rating is required</div> <!-- Pesan error jika rating tidak dipilih -->
                </div>

                <!-- Tombol kirim untuk memperbarui pesan -->
                <button type="submit">Update Message</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('rating');

            // Fungsi LocalStorage untuk menyimpan, mengambil, dan menghapus data
            function setLocalStorage(key, value) {
                localStorage.setItem(key, JSON.stringify(value));
            }

            function getLocalStorage(key) {
                const value = localStorage.getItem(key);
                return value ? JSON.parse(value) : null;
            }

            function deleteLocalStorage(key) {
                localStorage.removeItem(key);
            }

            // Fungsi Cookie untuk menyimpan, mengambil, dan menghapus cookies
            function setCookie(name, value, days) {
                const expires = days ? `; expires=${new Date(Date.now() + days * 864e5).toUTCString()}` : '';
                document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/';
            }

            function getCookie(name) {
                const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? decodeURIComponent(match[2]) : null;
            }

            function deleteCookie(name) {
                setCookie(name, '', -1); // Set cookie dengan tanggal kedaluwarsa masa lalu untuk menghapusnya
            }

            // Mengecek apakah ada data form yang tersimpan di localStorage atau cookies dan mengisi field form
            const formFields = ['name', 'email', 'country', 'birthdate', 'message', 'rating'];
            formFields.forEach(field => {
                const savedValue = getLocalStorage(field) || getCookie(field); // Prioritaskan localStorage
                if (savedValue) {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = savedValue;
                    }

                    // Update tampilan bintang rating untuk 'rating'
                    if (field === 'rating') {
                        updateStars(savedValue);
                    }
                }
            });

            // Simpan data form ke localStorage dan cookies ketika pengguna menginput atau mengubah field
            formFields.forEach(field => {
                const input = document.getElementById(field);
                if (input) {
                    input.addEventListener('input', function () {
                        setLocalStorage(field, input.value); // Simpan ke localStorage
                        setCookie(field, input.value, 7); // Simpan ke cookie selama 7 hari
                    });
                }
            });

            // Hapus data dari localStorage dan cookies saat form disubmit
            form.addEventListener('submit', function () {
                formFields.forEach(field => {
                    deleteLocalStorage(field);
                    deleteCookie(field);
                });
            });

            // Event listener klik bintang untuk memperbarui rating
            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;
                    setLocalStorage('rating', rating); // Simpan rating ke localStorage
                    setCookie('rating', rating, 7); // Simpan rating ke cookie
                    updateStars(rating);
                });

                // Tampilkan pratinjau rating saat mouse hover
                star.addEventListener('mouseover', function () {
                    const rating = this.dataset.rating;
                    updateStars(rating);
                });

                // Kembalikan rating asli setelah hover berakhir
                star.addEventListener('mouseout', function () {
                    const rating = ratingInput.value;
                    updateStars(rating);
                });
            });

            // Memperbarui tampilan bintang sesuai dengan rating yang dipilih
            function updateStars(rating) {
                stars.forEach(star => {
                    star.classList.toggle('active', star.dataset.rating <= rating);
                });
            }
        });
    </script>
</body>
</html>
