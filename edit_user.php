<?php
include('header.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the user ID from the hidden input field
    $id = $_POST['user_id'];

    // Retrieve other form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $mobile_number = $_POST['mobile_number'];
    $email_id = $_POST['email_id'];

    // Prepare the SQL statement for updating user data
    $sql = "UPDATE users SET first_name=?, last_name=?, gender=?, dob=?, address=?, mobile_number=?, email_id=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $first_name, $last_name, $gender, $dob, $address, $mobile_number, $email_id, $id);

    if ($stmt->execute()) {
        // Redirect back to user list if the update was successful
        header("Location: user_list.php");
        exit;
    } else {
        // Handle the error case
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
