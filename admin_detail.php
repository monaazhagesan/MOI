<?php
include 'config.php'; // Include the database connection
include 'header.php'; // Include the database connection

// Check if the form is submitted to update the admin details
if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $company_name = $_POST['company_name'];
    $contact_number = $_POST['contact_number'];

    // Prepare the SQL statement to update the admin details
    $query = $conn->prepare("UPDATE company_details SET company_name = ?, contact_number = ? WHERE id = ?");
    $query->bind_param("ssi", $company_name, $contact_number, $id);

    if ($query->execute()) {
        echo "<script>alert('Admin updated successfully'); window.location.href='admin_detail.php';</script>";
    } else {
        echo "<script>alert('Admin update failed: " . $query->error . "');</script>";
    }

    // Close the statement
    $query->close();
}

// Fetch admin details (assuming there's only one admin record)
$query = $conn->prepare("SELECT * FROM company_details LIMIT 1");
$query->execute();

// Fetch the result and store it in an array
$result = $query->get_result();
$admin = $result->fetch_assoc();

$query->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container-admin {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-admin">
        <h2 class="text-center">Edit Admin Details</h2>
        <form action="admin_detail.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">

            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $admin['company_name']; ?>">
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo $admin['contact_number']; ?>">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Update Admin</button>
        </form>
    </div>
</body>
</html>
<?php
include 'footer.php'; ?>