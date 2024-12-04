<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $penerbit = $_POST['penerbit'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun'];

    // Handle upload cover
    $cover = $_FILES['cover'];
    $coverName = $cover['name'];
    $coverTmpName = $cover['tmp_name'];
    $coverDestination = 'uploads/' . $coverName;

    // Pindahkan file yang diupload ke folder uploads
    if (move_uploaded_file($coverTmpName, $coverDestination)) {
        // Koneksi ke database
        $conn = new mysqli('localhost', 'root', '', 'user_db');
        if ($conn->connect_error) {
            die("Koneksi gagal: " . $conn->connect_error);
        }

        // Insert data buku ke database
        $sql = "INSERT INTO books (judul, penerbit, pengarang, tahun, cover) 
                VALUES ('$judul', '$penerbit', '$pengarang', '$tahun', '$coverName')";

        if ($conn->query($sql) === TRUE) {
            echo "Buku berhasil ditambahkan!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    } else {
        echo "Gagal mengupload file!";
    }
}
?>
