<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in

// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header("Location: login.php");
//     exit;
// }

// Check user role
function checkAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo "Access denied.";
        exit;
    }
}
?>
