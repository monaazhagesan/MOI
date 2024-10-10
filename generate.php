<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id']; 
$query = $conn->prepare("SELECT e.event_name, c.collection_amount, c.created_at 
                         FROM collections c 
                         JOIN events e ON c.event_id = e.id 
                         WHERE c.user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
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
                    <th>Event Name</th>
                    <th>Collection Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['users_name']; ?></td>
                        <td><?php echo $row['users_ph']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="generate_pdf.php" class="btn btn-primary">Download PDF</a>
    </div>
</body>
</html>
