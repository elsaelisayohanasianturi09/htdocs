<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Pendaftaran</title>
  <link rel="stylesheet" href="main.css">
  <style>
@import url('https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
</style>
</head>
<body>
    <div class="box">
        <form action="process.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <h1>Data</h1>
            <div class="box1">
                <label for="name">Nama:</label>
                <input type="text" id="name" name="name" required minlength="3">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="age">Usia</label>
                <input type="number" id="age" name="age" required min="18" max="100">
                <label for="gender">Jenis Kelamin:</label>
                <select id="gender" name="gender" required>
                    <option value="">Pilih</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <label for="file">Upload File (teks):</label>
                <input type="file" id="file" name="file" accept=".txt" required>
                <button   button type="submit">Kirim</button>
            </div>
        </form>

    </div>
  <script>
    function validateForm() {
      const fileInput = document.getElementById('file');
      const file = fileInput.files[0];
      const allowedExtensions = ['txt'];

      if (file) {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const fileSize = file.size / 1024; // in KB

        if (!allowedExtensions.includes(fileExtension)) {
          alert("Hanya file teks (.txt) yang diperbolehkan.");
          return false;
        }
        if (fileSize > 1024) { // 1 MB max
          alert("Ukuran file tidak boleh lebih dari 1 MB.");
          return false;
        }
      }
      return true;
    }
  </script>
</body>
</html>
