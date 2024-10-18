<?php
include "config.php";
session_start();



// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['festival_id'])) {
    $id = $_GET['id'];
    $festival_id = $_GET['festival_id'];

    // Prepare and execute the delete query with a join to check if festival_id matches
    $sql = "DELETE mrg FROM mrg
            INNER JOIN festival ON mrg.festival_id = festival.id
            WHERE mrg.id = ? AND mrg.festival_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $festival_id);

    if ($stmt->execute()) {
        header("Location: moidisplay.php?festival_id=" . $festival_id);
        exit;
    } else {
        echo "Error deleting record.";
    }

    $stmt->close();
}

$conn->close();
