<?php
include ('config.php');
$adminUsername = 'admin';
$adminPassword = password_hash('password123', PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password) VALUES ('$adminUsername', '$adminPassword')";

if ($conn->query($sql) === TRUE) {
    echo "Admin account created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
