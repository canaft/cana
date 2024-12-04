<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Gunakan password_verify jika password disimpan dalam bentuk hash
        if ($pass === $row['password']) { // Ganti dengan `password_verify($pass, $row['password'])` jika password hash
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Arahkan berdasarkan role
            if ($row['role'] == 'admin') {
                header("Location: admin.php");
            } elseif ($row['role'] == 'owner') {
                header("Location: owner.php");
            } else {
                header("Location: user.php");
            }
            exit();
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5; /* Warna background lembut */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-container {
    width: 100%;
    max-width: 400px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    color: #6f4f28; /* Coklat */
}

form {
    display: flex;
    flex-direction: column;
}

input[type="text"], input[type="password"] {
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

input[type="text"]:focus, input[type="password"]:focus {
    border-color: #8B4513; /* Coklat tua saat fokus */
    outline: none;
    box-shadow: 0 0 4px rgba(139, 69, 19, 0.2); /* Coklat tua */
}

button {
    padding: 12px;
    background-color: #8B4513; /* Coklat tua */
    border: none;
    border-radius: 5px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background-color: #A0522D; /* Coklat lebih terang saat hover */
}

.error-message {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    font-size: 14px;
    text-align: center;
}

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="index.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
