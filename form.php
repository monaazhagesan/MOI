<?php
// include "config.php";
// include "header.php";

$id = isset($_GET['mrgdisplay_id']) ? intval($_GET['mrgdisplay_id']) : 0;

if ($id > 0) {
    $query = $conn->prepare("SELECT * FROM mrg WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>alert('No record found'); window.location.href='display.php';</script>";
    }
} else {
    echo "<script>alert('Invalid ID'); window.location.href='your_table_page.php';</script>";
}

// Close connection
$query->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://tse4.mm.bing.net/th?id=OIP.sJfsNEJyzLCgM7SYAxK-vQHaEM&pid=Api&P=0&h=220');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 20px;
            max-width: 500px;
            margin-top: 50px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
        }
        .btn-group {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Form Details</h2>
        <form id="formDetails">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($row['name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="spouse_name">Spouse Name</label>
                <input type="text" class="form-control" id="spouse_name" value="<?php echo htmlspecialchars($row['spouse_name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="text" class="form-control" id="contactNumber" value="<?php echo htmlspecialchars($row['contactNumber']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="place">Place</label>
                <input type="text" class="form-control" id="place" value="<?php echo htmlspecialchars($row['place']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" class="form-control" id="amount" value="<?php echo htmlspecialchars($row['amount']); ?>" readonly>
            </div>
        </form>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="printPage()">Print</button>
            <button type="button" class="btn btn-secondary" onclick="printPDF()">Print PDF</button>
        </div>
    </div>
    <script>
        function printPage() {
            window.print();
        }

        function printPDF() {
            // Use jsPDF to generate the PDF
            var doc = new jsPDF();
            doc.fromHTML(document.getElementById('formDetails').innerHTML, 15, 15, {
                'width': 170
            });
            doc.save('form-details.pdf');
        }

        // Load jsPDF library
        var script = document.createElement('script');
        script.src = "https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.6.0/jspdf.umd.min.js";
        script.onload = function() {
            // jsPDF is loaded
            console.log('jsPDF loaded');
        };
        document.head.appendChild(script);
    </script>
</body>
</html>
