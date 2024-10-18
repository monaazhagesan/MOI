<?php
include('header.php'); // Ensure header.php is properly included
include "config.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the user ID from the hidden input field
    $id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

    // Retrieve other form data, allowing empty values
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $relative_name = isset($_POST['relative_name']) ? $_POST['relative_name'] : '';
    $spouse_name = isset($_POST['spouse_name']) ? $_POST['spouse_name'] : '';
    $profession = isset($_POST['profession']) ? $_POST['profession'] : '';
    $profession1 = isset($_POST['profession1']) ? $_POST['profession1'] : '';
    $contactNumber = isset($_POST['contactNumber']) && !empty($_POST['contactNumber']) ? $_POST['contactNumber'] : null; // Allow null if empty
    $place = isset($_POST['place']) ? $_POST['place'] : '';
    $amount = isset($_POST['amount']) && !empty($_POST['amount']) ? $_POST['amount'] : null; // Allow null if empty

    $festival_id = isset($_POST['festival_id']) ? intval($_POST['festival_id']) : null;

    // Check if all required fields are set (only if fields should be required)
    if ($id && $name && $place && $festival_id) {
        // Prepare the SQL statement for updating user data
        $sql = "UPDATE mrg SET name=?, relative_name=?, spouse_name=?, profession=?, profession1=?, contactNumber=?, place=?, amount=? WHERE id=?";
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters, set `NULL` for empty values
            $stmt->bind_param(
                "ssssssssi",
                $name,
                $relative_name,
                $spouse_name,
                $profession,
                $profession1,
                $contactNumber,
                $place,
                $amount,
                $id
            );

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect back to user list if the update was successful
                header("Location: moidisplay.php?festival_id=" . $festival_id);
                exit;
            } else {
                // Handle the error case
                echo "Error updating record: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Invalid input data. Please ensure all required fields are filled.";
    }
}

$conn->close();
