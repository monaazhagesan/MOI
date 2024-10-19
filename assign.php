<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['festival_id']) && !empty($_POST['user_ids'])) {
    $festival_id = $_POST['festival_id'];
    $user_ids = json_decode($_POST['user_ids'], true); // Decode the JSON string

    foreach ($user_ids as $user_id) {
        $query = "INSERT INTO user_festival_assignment (user_id, festival_id) VALUES ('$user_id', '$festival_id')";
        mysqli_query($conn, $query);
    }

    echo "Users assigned successfully!";
} else {
    echo "No users selected or festival ID missing.";
}
