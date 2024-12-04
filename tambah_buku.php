<?php
session_start();
include 'db.php';

// Cek jika user bukan admin
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menambahkan buku
    if (isset($_POST['add_book'])) {
        $judul = $_POST['judul'];
        $penerbit = $_POST['penerbit'];
        $pengarang = $_POST['pengarang'];
        $tahun = $_POST['tahun'];
        $cover = $_FILES['cover']['name'];

        move_uploaded_file($_FILES['cover']['tmp_name'], "uploads/$cover");

        // Menyimpan buku ke database
        $stmt = $conn->prepare("INSERT INTO books (judul, penerbit, pengarang, tahun, cover) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $judul, $penerbit, $pengarang, $tahun, $cover);
        $stmt->execute();
        $stmt->close();

        // Redirect ke halaman admin
        header("Location: admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f6f0; /* Warna latar belakang coklat lembut */
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        input[type="text"], input[type="number"], input[type="file"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d7b59e; /* Border coklat muda */
            border-radius: 5px;
        }

        button {
            background-color: #c29c82; /* Coklat untuk tombol */
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #a28064; /* Coklat lebih gelap saat hover */
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Buku</h2>
        <form action="tambah_buku.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="judul" placeholder="Judul Buku" required>
            <input type="text" name="penerbit" placeholder="Penerbit" required>
            <input type="text" name="pengarang" placeholder="Pengarang" required>
            <input type="number" name="tahun" placeholder="Tahun Terbit" required>
            <input type="file" name="cover" accept="image/*" required>
            <button type="submit" name="add_book">Simpan</button>
        </form>
        <a href="admin.php">Kembali</a>
    </div>
</body>
</html>
