<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $festival_id = $_POST['festival_id'];
    $user_id = $_POST['user_id'];

    // Perform the assignment
    $assign_res = mysqli_query($conn, "INSERT INTO user_festival_assignment (user_id, festival_id) VALUES ('$user_id', '$festival_id')");
    
    if ($assign_res) {
        echo "User assigned successfully!";
    } else {
        echo "Assignment failed.";
    }
}
?>
