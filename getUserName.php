<?php
// Ensure you have a valid connection to your database
include('db_connection.php'); // Replace with your actual database connection file

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    // Prepare and execute the query to get the user name
    $stmt = $conn->prepare('SELECT user_name FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($userName);
    $stmt->fetch();
    $stmt->close();
    
    echo htmlspecialchars($userName);
} else {
    echo 'User ID not provided';
}
?>
