<?php
session_start();
include 'db.php';

// Redirect jika bukan user
if ($_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit();
}

// Cek apakah ada input pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$genre = isset($_POST['genre']) ? $_POST['genre'] : '';

// Modifikasi query untuk pencarian
$sql = "SELECT * FROM books WHERE 1=1"; // 1=1 untuk mempermudah menambahkan kondisi tambahan

if ($search) {
    $sql .= " AND (judul LIKE '%$search%' OR pengarang LIKE '%$search%' OR penerbit LIKE '%$search%' OR tahun LIKE '%$search%')";
}

if ($genre) {
    $sql .= " AND genre LIKE '%$genre%'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
   /* Reset CSS untuk memastikan konsistensi */
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f5f5; /* Warna latar belakang yang lembut */
}

.user-container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.page-title {
    text-align: center;
    font-size: 2rem;
    color: #6f4f28; /* Coklat */
}

.search-container {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 500px;
    background-color: #f9f9f9;
    border-radius: 20px;
    padding: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.search-input, .search-select {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 15px;
    font-size: 1rem;
    outline: none;
}

.search-btn {
    padding: 10px 20px;
    background-color: #8B4513; /* Coklat tua */
    color: white;
    border: none;
    border-radius: 15px;
    cursor: pointer;
}

.search-btn:hover {
    background-color: #A0522D; /* Coklat yang lebih terang saat hover */
}

.book-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.book-card {
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.book-card:hover {
    transform: translateY(-5px);
}

.book-cover {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-bottom: 2px solid #eee;
}

.book-info {
    padding: 15px;
}

.book-title {
    font-size: 1.2rem;
    font-weight: bold;
    color: #6f4f28; /* Coklat */
}

.book-info p {
    font-size: 1rem;
    color: #555;
}

.no-results {
    text-align: center;
    color: #888;
    font-size: 1.2rem;
}

.button-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-logout {
    padding: 10px 20px;
    background-color: #ff4d4d;
    color: white;
    border: none;
    border-radius: 15px;
    cursor: pointer;
}

.btn-logout:hover {
    background-color: #e04343;
}

.cancel-btn {
    padding: 10px 20px;
    background-color: #6f4f28; /* Coklat */
    color: white;
    border: none;
    border-radius: 15px;
    cursor: pointer;
}

.cancel-btn:hover {
    background-color: #8B4513; /* Coklat tua saat hover */
}

    </style>
</head>
<body>
    <div class="user-container">
        <h1 class="page-title">Daftar Buku Perpustakaan</h1>

        <?php if (isset($_SESSION['message'])): ?>
    <div style="color: green; text-align: center; margin-bottom: 20px;">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

        <!-- Form Pencarian -->
        <div class="search-container">
            <form method="POST" action="" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Cari buku..." 
                    value="<?= htmlspecialchars($search) ?>" 
                    class="search-input"
                >
                
                <!-- Dropdown Genre -->
                <select name="genre" class="search-select">
                    <option value="">Pilih Genre</option>
                    <option value="Fiksi" <?= $genre == 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                    <option value="Non-Fiksi" <?= $genre == 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi</option>
                    <option value="Sains" <?= $genre == 'Sains' ? 'selected' : '' ?>>Sains</option>
                    <option value="Sejarah" <?= $genre == 'Sejarah' ? 'selected' : '' ?>>Sejarah</option>
                    <!-- Tambahkan genre lainnya sesuai kebutuhan -->
                </select>
                
                <button type="submit" class="search-btn">Cari</button>
                <?php if ($search || $genre): ?>
                    <a href="user.php" class="cancel-btn" style="margin-left: 10px;">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="book-grid">
            <?php 
            // Menampilkan hasil pencarian buku
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <div class="book-card">
                        <img src="uploads/<?= $row['cover'] ?>" alt="Cover" class="book-cover">
                        <div class="book-info">
                            <h3 class="book-title"><?= $row['judul'] ?></h3>
                            <p><strong>Penerbit:</strong> <?= $row['penerbit'] ?></p>
                            <p><strong>Pengarang:</strong> <?= $row['pengarang'] ?></p>
                            <p><strong>Tahun:</strong> <?= $row['tahun'] ?></p>
                            <p><strong>Genre:</strong> <?= $row['genre'] ?></p>
                            <p><strong>Stok:</strong> <?= $row['stok'] > 0 ? $row['stok'] . ' tersedia' : 'Habis' ?></p>

                            <!-- Pinjam Buku Button -->
                            <?php if ($row['stok'] > 0): ?>
                                <form method="POST" action="borrow.php" style="margin-top: 10px;">
                                    <input type="hidden" name="id_book" value="<?= $row['id'] ?>">
                                    <button type="submit" class="search-btn">Pinjam Buku</button>
                                </form>
                            <?php else: ?>
                                <button disabled class="cancel-btn" style="margin-top: 10px;">Habis</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-results">Tidak ada buku yang ditemukan.</p>
            <?php endif; ?>
        </div>

        <div class="button-container">
            <form action="logout.php" method="POST">
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
