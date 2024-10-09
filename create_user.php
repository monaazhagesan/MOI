<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newPassword = $_POST['password'];
    $mobileNumber = $_POST['mobile_number'];
    $status = isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 0; // Default to 0 if status is not set or inactive

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, mobile_number, status)
            VALUES ('$newUsername', '$hashedPassword', '$mobileNumber', '$status')";

    if ($conn->query($sql) === TRUE) {
        header("Location: user_list.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
