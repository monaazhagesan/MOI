<?php
include "config.php";
include "header.php";

$moi_id = isset($_GET['moi_id']) ? intval($_GET['moi_id']) : null;

if (isset($_POST["submit"])) {
    $name = $_POST['name'];
    $wife_name = $_POST['wife_name'];
    $spouse_name = $_POST['spouse_name'];
    $contactNumber = $_POST['contactNumber'];
    $place = $_POST['place'];
    $profession = $_POST['profession'];
    $amount = $_POST['amount'];
    $festival_id = $_POST['festival_id'];

    $user_id = $_SESSION['id'];

    $query = $conn->prepare("INSERT INTO mrg(name, wife_name, spouse_name, contactNumber, place, profession, amount, user_id, festival_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssssiii", $name, wife_name, spouse_name, contactNumber, place, profession, amount, user_id, festival_id);

    if ($query->execute()) {
        echo "<script>alert('Registration successful'); window.location.href='moi.php?festival_id=" . $festival_id . "';</script>";
    } else {
        $error_message = $query->error;
        echo "<script>alert('Registration failed. Error: $error_message'); window.location.href='moi.php';</script>";
    }

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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Default screen styles */
        .container-moi {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input[type="number"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
        .divider {
            margin: 20px 0;
            border-top: 2px solid #007bff;
        }
        .result {
            font-size: 18px;
            color: #007bff;
            font-weight: bold;
            text-align: right;
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .print-button {
            margin-top: 20px;
        }

        /* Styles for print view (thermal printer) */
        @media print {
            body {
                width: 80mm; /* Thermal paper width */
                margin: 0; /* Remove margin */
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            .container-moi {
                width: 80mm; /* Ensure the container matches the paper width */
                padding: 0;
                margin: 0;
                border: none;
                background-color: white;
            }
            .form-group, .result {
                padding: 5px;
                margin: 0;
                border-bottom: 1px dashed #000;
            }
            .result {
                text-align: left; /* Align text to left */
                font-size: 16px;
                font-weight: bold;
            }
            h2 {
                text-align: center;
                margin-bottom: 10px;
                font-size: 14px;
            }
            .print-button {
                display: none; /* Hide the print button */
            }
        }
    </style>
</head>
<body>
    <div class="container-moi">
        <h2>தகவலுக்கு விண்ணப்பம்</h2>
        <form action="moi.php" method="POST">
            <div class="form-group">
                <label for="name">பெயர்</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <!-- Add other form fields here -->
            <div class="form-group result">
                மொத்தம்: ரூபாய் <span id="total">0</span>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">சமர்ப்பிக்க</button>
            <button type="button" class="btn btn-secondary print-button" onclick="printForm()">Print</button>
        </form>
    </div>

    <script>
        function calculateTotal() {
            const fivehundred = document.getElementById('fivehundred').value || 0;
            const twohundred = document.getElementById('twohundred').value || 0;
            const hundred = document.getElementById('hundred').value || 0;
            const fiftyrupees = document.getElementById('fiftyrupees').value || 0;
            const twentyrupees = document.getElementById('twentyrupees').value || 0;
            const tenrupee = document.getElementById('tenrupee').value || 0;
            const onerupee = document.getElementById('onerupee').value || 0;

            const totalAmount = (fivehundred * 500) + (twohundred * 200) + (hundred * 100) + (fiftyrupees * 50) + (twentyrupees * 20) + (tenrupee * 10) + (onerupee * 1);

            document.getElementById('amount').value = totalAmount;
            document.getElementById('total').textContent = totalAmount.toFixed(2);
        }

        function printForm() {
            window.print();
        }
    </script>
</body>
</html>
