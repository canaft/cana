<?php
include 'db.php';

// Ambil ID buku yang ingin diedit
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM books WHERE id=$id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $penerbit = $_POST['penerbit'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun'];

    // Update cover jika ada file baru
    if ($_FILES['cover']['name']) {
        $cover = $_FILES['cover']['name'];
        $coverPath = "uploads/" . basename($cover);
        move_uploaded_file($_FILES['cover']['tmp_name'], $coverPath);
        $sql = "UPDATE books SET judul='$judul', penerbit='$penerbit', pengarang='$pengarang', tahun='$tahun', cover='$cover' WHERE id=$id";
    } else {
        $sql = "UPDATE books SET judul='$judul', penerbit='$penerbit', pengarang='$pengarang', tahun='$tahun' WHERE id=$id";
    }

    if ($conn->query($sql)) {
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
    <title>Edit Buku</title>
    <style>
        /* Reset CSS untuk memastikan konsistensi */
     /* Reset CSS untuk memastikan konsistensi */
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f5f5; /* Warna latar belakang yang lembut */
}

.form-container {
    width: 60%;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.form-container h2 {
    text-align: center;
    font-size: 2rem;
    color: #6f4f28; /* Coklat */
}

.form-container input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

.form-container input[type="file"] {
    padding: 5px;
}

.form-container img {
    max-width: 200px;
    margin-top: 10px;
    display: block;
    margin: 0 auto;
}

.form-container button {
    width: 100%;
    padding: 10px;
    background-color: #8B4513; /* Coklat tua */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

.form-container button:hover {
    background-color: #A0522D; /* Coklat yang lebih terang saat hover */
}

.form-container .btn-small {
    display: inline-block;
    padding: 8px 16px; /* Sesuaikan ukuran padding */
    background-color: #6f4f28; /* Coklat */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
    font-size: 0.9rem; /* Ukuran font sedikit lebih kecil */
    margin-top: 10px; /* Memberikan jarak atas */
}

.form-container .btn-small:hover {
    background-color: #8B4513; /* Coklat tua saat hover */
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Buku</h2>
        <form action="edit_buku.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <input type="text" name="judul" placeholder="Judul Buku" value="<?= htmlspecialchars($row['judul']) ?>" required><br>
            <input type="text" name="penerbit" placeholder="Penerbit" value="<?= htmlspecialchars($row['penerbit']) ?>" required><br>
            <input type="text" name="pengarang" placeholder="Pengarang" value="<?= htmlspecialchars($row['pengarang']) ?>" required><br>
            <input type="number" name="tahun" placeholder="Tahun Terbit" value="<?= htmlspecialchars($row['tahun']) ?>" required><br>
            
            <!-- Form untuk upload cover -->
            <input type="file" name="cover" accept="image/*"><br>
            
            <!-- Tampilkan gambar cover yang sudah ada -->
            <img src="uploads/<?= htmlspecialchars($row['cover']) ?>" alt="Cover Buku" class="cover-thumbnail"><br><br>

            <!-- Tombol untuk mengupdate buku -->
            <button type="submit" name="update_book" class="btn-small">Update Buku</button>
        </form>
        <a href="admin.php" class="btn-small">Kembali</a>
        </div>
</body>
</html>
