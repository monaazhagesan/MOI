<?php
// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=admin_db', 'root', '');

// Static value for testing
$user_id = 1; // Replace with a valid user ID

// Prepare and execute the SQL query
$sql = "
SELECT
    f.name AS festival_name,
    f.spouse_name,
    f.date,
    f.place AS festival_place,
    u.username,
    m.name AS mrg_name,
    m.spouse_name AS mrg_spouse_name,
    m.place AS mrg_place
FROM
    user_festival_assignment ufa
JOIN
    festival f ON ufa.festival_id = f.id
JOIN
    users u ON ufa.user_id = u.id
JOIN
    mrg m ON m.place = f.place
WHERE
    ufa.user_id = :user_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Display and Print Data</title>
    <link rel="stylesheet" href="styles.css">  <!-- Add your CSS file -->
</head>
<body>
    <h1>Festival Information</h1>
    <table border="1">
        <tr>
            <th>Festival Name</th>
            <th>Spouse Name</th>
            <th>Date</th>
            <th>Festival Place</th>
            <th>Username</th>
            <th>MRG Name</th>
            <th>MRG Spouse Name</th>
            <th>MRG Place</th>
        </tr>
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['festival_name']); ?></td>
                <td><?php echo htmlspecialchars($row['spouse_name']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['festival_place']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['mrg_name']); ?></td>
                <td><?php echo htmlspecialchars($row['mrg_spouse_name']); ?></td>
                <td><?php echo htmlspecialchars($row['mrg_place']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No data found</td>
            </tr>
        <?php endif; ?>
    </table>
    <button onclick="printPage()">Print</button>

    <script>
    function printPage() {
        window.print();
    }
    </script>
</body>
</html>
