<?php
session_start();
include "config.php"; // Ensure this file contains the correct database connection details

if (isset($_POST["submit"])) {
    
    $name = $_POST['name'];
    $spouse_name = $_POST['spouse_name'];
    $occupation = $_POST['occupation'];
    $festival_name = $_POST['festival_name'];
    $date = $_POST['date'];
    $place = $_POST['place'];

    // Use prepared statements to prevent SQL injection
    $query = $conn->prepare("INSERT INTO festival (name, spouse_name, occupation, festival_name, date, place) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssss", $name, $spouse_name, $occupation, $festival_name, $date, $place);
    
    if ($query->execute()) {
        // Registration successful
        $_SESSION['name'] = $name;
        $_SESSION['spouse_name'] = $spouse_name;
        $_SESSION['occupation'] = $occupation;
        $_SESSION['festival_name'] = $festival_name;
        $_SESSION['date'] = $date;
        $_SESSION['place'] = $place;

        echo "<script>alert('Registration successful'); window.location.href='form1.php';</script>";
    } else {
        // Registration failed - show the error message
        $error_message = $query->error;
        echo "<script>alert('Registration failed. Error: $error_message'); window.location.href='form1.php';</script>";
    }

    // Close the prepared statement and the database connection
    $query->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form in Tamil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://tse4.mm.bing.net/th?id=OIP.sJfsNEJyzLCgM7SYAxK-vQHaEM&pid=Api&P=0&h=220'); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8); /* White background with transparency */
            border-radius: 10px;
            padding: 20px;
            max-width: 500px; /* Smaller container */
            margin-top: 50px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">விழாக்கள்</h2>
        <form action="mainform.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">பெயர்:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="spouse_name" class="form-label">துணைவி பெயர்:</label>
                <input type="text" class="form-control" id="spouse_name" name="spouse_name" required>
            </div>
            <div class="mb-3">
                <label for="occupation" class="form-label">தொழில்:</label>
                <input type="text" class="form-control" id="occupation" name="occupation" required>
            </div>
            <div class="mb-3">
                <label for="festival_name" class="form-label">விழாவின் பெயர்:</label>
                <input type="text" class="form-control" id="festival_name" name="festival_name" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">தேதி:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="place" class="form-label">இடம்:</label>
                <input type="text" class="form-control" id="place" name="place" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">சமர்ப்பிக்கவும்</button>
        </form>
    </div>
</body>
</html>



