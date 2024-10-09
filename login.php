<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Check if the user is an admin
    $sql = "SELECT id, username, password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashedPassword);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && password_verify($inputPassword, $hashedPassword)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $id;
        $_SESSION['role'] = 'admin';
        header("Location: index.php");
        exit;
    } else {
        // Check if the user is a regular user
        $stmt->close();
        $sql = "SELECT id, username, password FROM users WHERE username = ? and status=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $inputUsername);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $username, $hashedPassword);
        $stmt->fetch();

        if ($stmt->num_rows == 1 && password_verify($inputPassword, $hashedPassword)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['id'] = $id;
            $_SESSION['role'] = 'user';
            header("Location: index.php");
            exit;
        } else {
            echo "Invalid username or password.";
        }
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <form class="login-form" action="login.php" method="post">
            <h2 class="text-center">Login</h2>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
