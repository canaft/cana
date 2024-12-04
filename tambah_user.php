<?php
session_start();
include 'db.php';

// Cek jika user bukan admin
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Tambah user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $email, $role);
        $stmt->execute();
        $stmt->close();

        // Refresh halaman setelah tambah user
        header("Location: tambah_user.php");
        exit();
    }
}

// Hapus user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $id = $_POST['id'];

    // Hapus user berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Refresh halaman setelah hapus user
    header("Location: tambah_user.php");
    exit();
}

// Ambil data user untuk ditampilkan di tabel
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f6f0; /* Warna latar belakang coklat lembut */
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        input[type="text"], input[type="password"], input[type="email"], select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d7b59e; /* Warna coklat muda untuk border */
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #c29c82; /* Coklat pada tombol */
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #a28064; /* Coklat lebih gelap pada hover */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #c29c82; /* Coklat untuk header tabel */
            color: white;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #e53935;
        }

        td form {
            display: inline-block;
            margin: 0;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah User</h2>

        <!-- Form Tambah User -->
        <form action="tambah_user.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="" disabled selected>Pilih Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="owner">Owner</option>
            </select>
            <button type="submit" name="add_user">Tambah User</button>
        </form>

        <!-- Tabel Data User -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td>
                            <form action="tambah_user.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button class="btn-delete" name="delete_user">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="admin.php" style="display: block; text-align: center; margin-top: 20px;">Kembali ke Dashboard</a>
    </div>
</body>
</html>
