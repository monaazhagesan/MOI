<?php
session_start();

include "config.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newPassword = $_POST['password'];
    $mobileNumber = $_POST['mobile_number'];
    $status = isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 0; // Default to 0 if status is not set or inactive

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, mobile_number, status)
            VALUES ('$newUsername', '$hashedPassword', '$mobileNumber', '$status')";

    if ($conn->query($sql) === TRUE) {
        header("Location: user_list.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Collection Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center">My Collection Report</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>  User Id </th>
                    <th> Name</th>
                    <th>Mobile number</th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['mobile_number']; ?></td>
                    </tr>
            </tbody>
        </table>
        <a href="generate_pdf.php" class="btn btn-primary">Download PDF</a>
    </div>
</body>
</html>
