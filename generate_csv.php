<?php
include "config.php";

$festival_id = $_GET['festival_id'];

// Fetch data from your database
$res = mysqli_query($conn, "SELECT * FROM mrg WHERE festival_id = $festival_id ORDER BY relative_name, place ASC");

// Set headers to force download as CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="festival_details_' . $festival_id . '.csv"');

// Open output stream for writing CSV
$output = fopen('php://output', 'w');

// Output CSV header
fputcsv($output, ['ID', 'NAME', 'SPOUSE_NAME', 'RELATIONSHIP', 'CONTACT NUMBER', 'PLACE', 'AMOUNT']);

// Output CSV rows
$counter = 1;
while ($row = mysqli_fetch_assoc($res)) {
    fputcsv($output, [
        $counter++,
        $row['name'],
        $row['spouse_name'],
        $row['relative_name'],
        $row['contactNumber'],
        $row['place'],
        $row['amount']
    ]);
}

// Close output stream
fclose($output);
exit;
?>
