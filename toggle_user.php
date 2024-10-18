<?php
include('header.php');
include "config.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT status FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($status == 1) ? 0 : 1;

    $sql = "UPDATE users SET status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_status, $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

header("Location: user_list.php");
exit;
?>
