<?php
// Menghubungkan ke file database.php untuk koneksi database
require 'database.php';

// Mendapatkan pesan dukungan dari database
try {
    $sql = "SELECT 
                name, email, country, birthdate, message, rating, 
                created_at AS date 
            FROM support_massage 
            ORDER BY created_at DESC";

    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VVerse - Fansite Kim Taehyung</title>
    <link rel="stylesheet" href="styledashboard.css">
</head>
<body>
    <header>
        <h1>VVerse</h1>
        <p>Welcome to the ultimate fansite for Kim Taehyung (V BTS)</p>
    </header>

    <main>
        <section id="form-section">
            <h2>Kirim Pesan Dukungan</h2>
            <form id="support-form" action="proses.php" method="POST" onsubmit="return validateForm()">
                <label for="name">Nama Pengguna:</label>
                <input type="text" id="name" name="name" required onfocus="highlightInput(this)" onblur="validateName()">
                <span id="name-error" class="error-message"></span>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required onfocus="highlightInput(this)" onblur="validateEmail()">
                <span id="email-error" class="error-message"></span>

                <label for="country">Asal Negara:</label>
                <select id="country" name="country" required onfocus="highlightInput(this)" onchange="validateCountry()">
                    <option value="">Pilih Negara</option>
                    <option value="Indonesia">Indonesia</option>
                    <option value="Korea Selatan">Korea Selatan</option>
                    <option value="Jepang">Jepang</option>
                    <option value="Amerika Serikat">Amerika Serikat</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <span id="country-error" class="error-message"></span>

                <!-- Elemen tambahan -->
                <label for="birthdate">Tanggal Lahir:</label>
                <input type="date" id="birthdate" name="birthdate" required onfocus="highlightInput(this)" onblur="validateBirthdate()">
                <span id="birthdate-error" class="error-message"></span>

                <label for="message">Pesan Dukungan:</label>
                <textarea id="message" name="message" rows="4" required onfocus="highlightInput(this)" onblur="validateMessage()"></textarea>
                <span id="message-error" class="error-message"></span>

                <label for="rating">Rating (1-5):</label>
                <select id="rating" name="rating" required onchange="validateRating()">
                    <option value="">Pilih Rating</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
                <span id="rating-error" class="error-message"></span>

                <button type="submit" onmouseover="highlightButton(this)" onmouseout="resetButton(this)">Kirim</button>
                <button type="button" onclick="resetForm()">Reset</button>
            </form>
        </section>

        <section id="table-section">
            <h2>Pesan Dukungan</h2>
            <div class="table-container">
                <table id="support-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Asal Negara</th>
                            <th>Tanggal Lahir</th>
                            <th>Pesan</th>
                            <th>Rating</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Menampilkan data pesan dukungan dari database
                        foreach ($data as $message) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($message['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($message['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($message['country']) . "</td>";
                            echo "<td>" . htmlspecialchars($message['birthdate']) . "</td>";
                            echo "<td>" . htmlspecialchars($message['message']) . "</td>";
                            echo "<td>" . htmlspecialchars($message['rating']) . "</td>";
                            echo "<td>" . htmlspecialchars($message['date']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 VVerse. All rights reserved.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
