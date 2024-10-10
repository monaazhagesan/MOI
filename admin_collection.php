<?php
// Include the database connection and required libraries
include 'config.php';
require('fpdf/fpdf.php'); // Ensure you include the FPDF library

session_start();

if (!isset($_SESSION['admin_id'])) { // Check if admin_id is set
    echo "You do not have permission to view this report.";
    exit();
}

$admin_id = $_SESSION['admin_id']; // Get the admin ID from the session

// Fetch all collections from the database
$query = $conn->prepare("SELECT u.username, e.event_name, c.collection_amount, c.created_at 
                         FROM collections c 
                         JOIN users u ON c.user_id = u.id 
                         JOIN events e ON c.event_id = e.id");
$query->execute();
$result = $query->get_result();

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Username');
$pdf->Cell(40, 10, 'Event Name');
$pdf->Cell(40, 10, 'Collection Amount');
$pdf->Cell(60, 10, 'Date');
$pdf->Ln();

// Generate PDF if there are collections
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['username']);
        $pdf->Cell(40, 10, $row['event_name']);
        $pdf->Cell(40, 10, $row['collection_amount']);
        $pdf->Cell(60, 10, $row['created_at']);
        $pdf->Ln();
    }

    $pdf->Output('D', 'admin_total_collection_report.pdf'); // Download the PDF
} else {
    echo "No collections found.";
}

$conn->close();
?>
