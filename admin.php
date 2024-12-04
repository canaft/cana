<?php
session_start();
include 'db.php';

// Cek jika user bukan admin
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Pencarian buku
$search = '';
$genre = '';

// Ambil data pencarian dari URL
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
if (isset($_GET['genre'])) {
    $genre = $_GET['genre'];
}

// Modifikasi query untuk pencarian berdasarkan kriteria
$query = "SELECT * FROM books WHERE 1=1"; // 1=1 untuk mempermudah menambahkan kondisi tambahan

if ($search) {
    $query .= " AND (judul LIKE '%$search%' OR pengarang LIKE '%$search%' OR penerbit LIKE '%$search%' OR tahun LIKE '%$search%')";
}

if ($genre) {
    $query .= " AND genre LIKE '%$genre%'";
}

$result = $conn->query($query);

// Hapus buku
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $conn->query("DELETE FROM books WHERE id=$id");
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Reset CSS untuk memastikan konsistensi */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f6f0; /* Warna latar belakang soft coklat */
        }

        /* Sidebar Panel */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #d7b59e; /* Warna coklat muda */
            padding-top: 20px;
        }

        .sidebar a {
            color: #fff;
            padding: 15px;
            text-decoration: none;
            display: block;
            font-size: 18px;
        }

        .sidebar a:hover {
            background-color: #c29c82; /* Warna coklat lebih gelap saat hover */
        }

        .sidebar .logo {
            text-align: center;
            color: white;
            font-size: 24px;
            margin-bottom: 30px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        .admin-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #c29c82; /* Warna coklat untuk header */
            color: white;
            padding: 10px 20px;
        }

        .header h1 {
            margin: 0;
        }

        .search-form input, .search-form select, .search-form button {
            padding: 8px 12px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #d7b59e; /* Warna coklat muda untuk border */
        }

        .search-form button {
            background-color: #c29c82; /* Warna coklat pada tombol */
            color: white;
            border: none;
        }

        .search-form button:hover {
            background-color: #a28064; /* Coklat lebih gelap pada hover */
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input, .search-form select {
            width: 20%;
        }

        .search-form button {
            width: auto;
            cursor: pointer;
        }

        .search-form .cancel-btn {
            margin-left: 1px;
            background-color: #a28064; /* Coklat pada tombol batal */
            color: white;
        }

        .btn-small {
            padding: 8px 15px;
            font-size: 14px;
            background-color: #d7b59e; /* Coklat muda pada tombol */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
        }

        .btn-small:hover {
            background-color: #c29c82; /* Hover coklat lebih gelap */
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .styled-table th, .styled-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .styled-table th {
            background-color: #c29c82; /* Coklat pada header tabel */
            color: white;
            text-align: left;
        }

        .styled-table tr:nth-child(even) {
            background-color: #f9f6f0; /* Warna latar belakang baris genap */
        }

        .styled-table img {
            width: 50px;
            height: auto;
        }

        .actions button {
            padding: 8px 12px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .actions .btn-edit {
            background-color: #c29c82; /* Coklat untuk tombol edit */
            color: white;
        }

        .actions .btn-delete {
            background-color: #f44336; /* Merah untuk tombol hapus */
            color: white;
        }

        .logout-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #d32f2f;
        }

        .cancel-btn {
            padding: 7px 11px;
            background-color: #a28064;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel-btn:hover {
            background-color: #8c6a4d; /* Coklat gelap pada hover */
        }
    </style>
</head>
<body>

    <!-- Sidebar Panel -->
    <div class="sidebar">
        <div class="logo">Admin Panel</div>
        <a href="admin.php">Dashboard</a>
        <a href="tambah_buku.php">Tambah Buku</a>
        <a href="tambah_user.php">Tambah User</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="admin-container">
            <!-- Search Form -->
            <form action="admin.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari Buku (Judul, Penerbit, Pengarang, Tahun)" value="<?= htmlspecialchars($search) ?>">

                <!-- Dropdown Genre -->
                <select name="genre">
                    <option value="">Pilih Genre</option>
                    <option value="Fiksi" <?= $genre == 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                    <option value="Non-Fiksi" <?= $genre == 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi</option>
                    <option value="Sains" <?= $genre == 'Sains' ? 'selected' : '' ?>>Sains</option>
                    <option value="Sejarah" <?= $genre == 'Sejarah' ? 'selected' : '' ?>>Sejarah</option>
                    <!-- Tambahkan genre lainnya sesuai kebutuhan -->
                </select>

                <button type="submit">Cari</button>

                <?php if ($search || $genre): ?>
                    <a href="admin.php" class="cancel-btn">Batal</a>
                <?php endif; ?>
            </form>

            <!-- Buttons and Table -->
            <div class="button-container">
                <a href="tambah_buku.php" class="btn-small">Tambah Buku</a>
                <a href="tambah_user.php" class="btn-small">Tambah User</a>
            </div>

            <table class="styled-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Penerbit</th>
                        <th>Pengarang</th>
                        <th>Tahun</th>
                        <th>Cover</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['penerbit']) ?></td>
                        <td><?= htmlspecialchars($row['pengarang']) ?></td>
                        <td><?= htmlspecialchars($row['tahun']) ?></td>
                        <td>
                            <img src="uploads/<?= htmlspecialchars($row['cover']) ?>" alt="Cover">
                        </td>
                        <td class="actions">
                            <form action="edit_buku.php" method="GET" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button class="btn-edit">Edit</button>
                            </form>
                            <form action="admin.php" method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button class="btn-delete" name="delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
