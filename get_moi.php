<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT id, name, spouse_name, relative_name, profession, profession1, contactNumber, place, amount FROM mrg WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(["error" => "Moi not found"]);
    }

    $stmt->close();
}

$conn->close();
