<?php
// Memulai sesi untuk menyimpan data antar halaman
session_start();

// Mengimpor file konfigurasi dan kelas Message
require_once 'config.php';
require_once 'Message.php';

// Membuat objek Database dan menghubungkannya
$database = new Database();
$db = $database->connect();

// Membuat objek Message untuk mengakses data pesan
$message = new Message($db);

// Mendapatkan pesan status dari sesi jika ada
$statusMsg = '';
if(isset($_SESSION['status'])) {
    $statusMsg = $_SESSION['status'];
    unset($_SESSION['status']); // Menghapus status setelah ditampilkan
}

// Mengambil data form yang disimpan di sesi (jika ada) dan menghapusnya setelah digunakan
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V Universe - Support for Kim Taehyung</title>
    <link rel="stylesheet" href="style/index.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Lilita+One&family=Merienda:wght@300..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    </style>
</head>
<body>
    <header>
        <h1>V Universe</h1>
        <p>Send Your Support Message to Kim Taehyung</p>
    </header>

    <div class="container">
        <?php if($statusMsg): ?>
            <!-- Menampilkan pesan status jika ada -->
            <div class="status-message <?php echo strpos($statusMsg, 'error') !== false ? 'status-error' : 'status-success'; ?>">
                <?php echo $statusMsg; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <!-- Form untuk mengirim pesan dukungan -->
            <form action="process.php" method="POST">
                <input type="hidden" name="action" value="create">
                
                <!-- Input untuk nama -->
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($formData['name']) ? htmlspecialchars($formData['name']) : ''; ?>" required>
                    <div class="error" id="nameError"></div>
                </div>

                <!-- Input untuk email -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>" required>
                    <div class="error" id="emailError"></div>
                </div>

                <!-- Input untuk negara -->
                <div class="form-group">
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" value="<?php echo isset($formData['country']) ? htmlspecialchars($formData['country']) : ''; ?>" required>
                    <div class="error" id="countryError"></div>
                </div>

                <!-- Input untuk tanggal lahir -->
                <div class="form-group">
                    <label for="birthdate">Birth Date:</label>
                    <input type="date" id="birthdate" name="birthdate" value="<?php echo isset($formData['birthdate']) ? htmlspecialchars($formData['birthdate']) : ''; ?>" required>
                    <div class="error" id="birthdateError"></div>
                </div>

                <!-- Input untuk pesan -->
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4" required><?php echo isset($formData['message']) ? htmlspecialchars($formData['message']) : ''; ?></textarea>
                    <div class="error" id="messageError"></div>
                </div>

                <!-- Rating dengan bintang -->
                <div class="form-group">
                    <label>Rating:</label>
                    <div class="rating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <span class="star" data-rating="<?php echo $i; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="<?php echo isset($formData['rating']) ? htmlspecialchars($formData['rating']) : ''; ?>">
                    <div class="error" id="ratingError"></div>
                </div>
                
                <button type="submit">Send Message</button>
            </form>
        </div>
        
        <div class="image">
            <img src="image/1.png" alt="vbts">
        </div>

        <div class="image1">
            <img src="image/2.png" alt="vbts">
        </div>
        <!-- Menampilkan daftar pesan yang telah dikirim -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>Message</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $messages = $message->read(); // Membaca semua pesan dari database
                foreach($messages as $msg): ?>
                <tr>
                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                    <td><?php echo htmlspecialchars($msg['country']); ?></td>
                    <td><?php echo htmlspecialchars($msg['message']); ?></td>
                    <td>
                        <div class="rating readonly">
                            <?php
                            for($i = 1; $i <= 5; $i++) {
                                echo '<span class="star ' . ($i <= $msg['rating'] ? 'active' : '') . '">★</span>';
                            }
                            ?>
                        </div>
                    </td>
                    <td class="action-buttons">
                        <!-- Link untuk edit dan hapus pesan -->
                        <a href="edit.php?id=<?php echo $msg['id']; ?>" class="edit-btn">Edit</a>
                        <a href="javascript:void(0);" 
                           onclick="deleteMessage(<?php echo $msg['id']; ?>)" 
                           class="delete-btn">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>



    <script>
        // Script untuk menghandle rating bintang dan validasi form
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const stars = document.querySelectorAll('.rating .star');
            const ratingInput = document.getElementById('rating');

            // Menangani klik pada bintang untuk memilih rating
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    if (!this.parentElement.classList.contains('readonly')) {
                        const rating = this.dataset.rating;
                        ratingInput.value = rating;
                        updateStars(this.parentElement, rating);
                    }
                });

                // Menampilkan rating saat mouse hover pada bintang
                star.addEventListener('mouseover', function() {
                    if (!this.parentElement.classList.contains('readonly')) {
                        const rating = this.dataset.rating;
                        updateStars(this.parentElement, rating);
                    }
                });

                // Mengembalikan rating saat mouse keluar dari bintang
                star.addEventListener('mouseout', function() {
                    if (!this.parentElement.classList.contains('readonly')) {
                        const rating = ratingInput.value;
                        updateStars(this.parentElement, rating);
                    }
                });
            });

            // Validasi form sebelum submit
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Menghapus pesan error sebelumnya
                document.querySelectorAll('.error').forEach(error => error.textContent = '');

                // Memvalidasi field yang wajib diisi
                ['name', 'email', 'country', 'birthdate', 'message'].forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        document.getElementById(`${field}Error`).textContent = 
                            `${field.charAt(0).toUpperCase() + field.slice(1)} is required`;
                        isValid = false;
                    }
                });

                // Validasi format email
                const email = document.getElementById('email');
                if (email.value && !isValidEmail(email.value)) {
                    document.getElementById('emailError').textContent = 'Please enter a valid email address';
                    isValid = false;
                }

                // Memeriksa apakah rating sudah dipilih
                if (!ratingInput.value) {
                    document.getElementById('ratingError').textContent = 'Please select a rating';
                    isValid = false;
                }

                // Jika ada error, batalkan pengiriman form
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });

        // Memperbarui bintang yang dipilih sesuai dengan rating
        function updateStars(container, rating) {
            container.querySelectorAll('.star').forEach(star => {
                star.classList.toggle('active', star.dataset.rating <= rating);
            });
        }

        // Fungsi untuk memvalidasi format email
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Fungsi untuk menghapus pesan
        function deleteMessage(id) {
            if (confirm('Apakah kamu yakin menghapus pesan?')) {
                window.location.href = `process.php?action=delete&id=${id}`;
            }
        }

        // Fungsi untuk menetapkan cookie
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        // Fungsi untuk mendapatkan nilai cookie berdasarkan nama
        function getCookie(name) {
            var nameEq = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i].trim();
                if (c.indexOf(nameEq) === 0) {
                    return c.substring(nameEq.length, c.length);
                }
            }
            return null;
        }

        // Fungsi untuk menghapus cookie
        function deleteCookie(name) {
            document.cookie = name + "=; Max-Age=-99999999; path=/";
        }

        // Fungsi untuk menyimpan data ke localStorage
        function saveToLocalStorage(key, value) {
            localStorage.setItem(key, JSON.stringify(value));
        }

        // Fungsi untuk mengambil data dari localStorage
        function getFromLocalStorage(key) {
            var value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        }

        // Fungsi untuk menghapus data dari localStorage
        function removeFromLocalStorage(key) {
            localStorage.removeItem(key);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Mengambil data dari localStorage atau cookie dan mengisinya ke dalam form
            const name = getFromLocalStorage('name') || getCookie('name');
            const email = getFromLocalStorage('email') || getCookie('email');
            const country = getFromLocalStorage('country') || getCookie('country');
            const birthdate = getFromLocalStorage('birthdate') || getCookie('birthdate');
            const message = getFromLocalStorage('message') || getCookie('message');
            const rating = getFromLocalStorage('rating') || getCookie('rating');

            if (name) document.getElementById('name').value = name;
            if (email) document.getElementById('email').value = email;
            if (country) document.getElementById('country').value = country;
            if (birthdate) document.getElementById('birthdate').value = birthdate;
            if (message) document.getElementById('message').value = message;
            if (rating) document.getElementById('rating').value = rating;

            // Menyimpan input ke localStorage dan cookie saat pengguna mengetik
            document.getElementById('name').addEventListener('input', function() {
                saveToLocalStorage('name', this.value);
                setCookie('name', this.value, 7);
            });
            document.getElementById('email').addEventListener('input', function() {
                saveToLocalStorage('email', this.value);
                setCookie('email', this.value, 7);
            });
            document.getElementById('country').addEventListener('input', function() {
                saveToLocalStorage('country', this.value);
                setCookie('country', this.value, 7);
            });
            document.getElementById('birthdate').addEventListener('input', function() {
                saveToLocalStorage('birthdate', this.value);
                setCookie('birthdate', this.value, 7);
            });
            document.getElementById('message').addEventListener('input', function() {
                saveToLocalStorage('message', this.value);
                setCookie('message', this.value, 7);
            });

            // Menyimpan rating ke localStorage dan cookie saat pengguna memilih rating
            document.querySelectorAll('.rating .star').forEach(star => {
                star.addEventListener('click', function() {
                    const ratingValue = this.dataset.rating;
                    document.getElementById('rating').value = ratingValue;
                    saveToLocalStorage('rating', ratingValue);
                    setCookie('rating', ratingValue, 7);
                });
            });
        });
    </script>
</body>
</html>
