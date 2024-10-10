<?php
require_once __DIR__ . '/vendor/autoload.php';
include "config.php";

// Initialize mPDF
$mpdf = new \Mpdf\Mpdf();

// Fetch all data from your database
$festival_id = $_GET['festival_id'];

$index = [];
$html = '';

// Start generating the PDF content
$res = mysqli_query($conn, "
    SELECT * 
    FROM mrg 
    WHERE festival_id = $festival_id 
    ORDER BY place ASC
");

if (mysqli_num_rows($res) > 0) {
    // Use UTF-8 charset in HTML
    $html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

    // Prepare the main content table
    $mainContent = '<table border="1" style="border-collapse:collapse; width:100%; font-size:12px;">';
    $mainContent .= '<thead><tr>
                <th style="padding: 5px;font-size:16px;font-family: latha;">வ.எண்</th>
                <th style="padding: 5px;font-size:16px;font-family: latha;">உறவினர் விவரம்</th>                
                <th style="padding: 5px;font-size:16px;font-family: latha;">பெற்ற மொய்</th>
                <th style="padding: 5px;font-size:16px;font-family: latha;">செய்த மொய்</th>
                <th style="padding: 5px;font-size:16px;font-family: latha;">மொத்தம் பெற்ற இருப்பு</th>
              </tr></thead><tbody>';

    $grandTotal = 0;
    $lastPrintedPlace = '';
    $counter = 1;

    // Fetching data to create index and the main table
    while ($row = mysqli_fetch_assoc($res)) {
        $place = $row['place'];

        // Create index entry for each unique place
        if ($lastPrintedPlace !== trim($place)) {
            // Store the place and the next page number for the index
            $index[] = ['place' => $place, 'page' => count($index) + 1]; // Store index entry with next page number
            
            // Add to the main table for this place
            $mainContent .= '<tr>
                        <td style="text-align:center; font-size:14px;padding: 10px;font-family: latha;">' . $counter++ . '</td>
                        <td style="font-size:14px;padding: 10px;font-family: latha;">
                            ' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . 
                            (!empty($row['profession1']) ? ' (' . htmlspecialchars($row['profession1'], ENT_QUOTES, 'UTF-8') . ')' : '') . 
                            ' - ' . htmlspecialchars($row['spouse_name'], ENT_QUOTES, 'UTF-8') . '
                        </td>
                        <td style="text-align:right; padding: 10px;font-family: latha; font-size: 30px; font-weight: bold;">' . (int)$row['amount'] . '</td>
                        <td style="font-size:14px;padding: 10px;font-family: latha;"></td>
                        <td style="text-align:right; padding: 10px;font-family: latha;"></td>
                      </tr>';
            $lastPrintedPlace = trim($place);
        }

        // Calculate totals
        $grandTotal += (int)$row['amount'];
    }

    // Close the main content table
    $mainContent .= '</tbody></table>';

    // Prepare the index page with a heading
    $indexHtml = '<h1 style="font-family: latha;">Index Page</h1>';
    $indexHtml .= '<table style="width:100%; font-size:12px;">';
    $indexHtml .= '<thead><tr>
                     <th style="padding: 5px; font-family: latha; font-size:20px;">பெயர்</th>
                     <th style="padding: 5px; font-family: latha; font-size:20px;">பக்கம் எண்</th>
                   </tr></thead><tbody>';

    // Populate the index table
    foreach ($index as $entry) {
        $indexHtml .= '<tr>
                         <td style="padding: 5px; font-family: latha;">' . htmlspecialchars($entry['place'], ENT_QUOTES, 'UTF-8') . '</td>
                         <td style="padding: 5px; font-family: latha;">' . $entry['page'] . '</td>
                       </tr>';
    }

    // Close the index table
    $indexHtml .= '</tbody></table>';

    // Write the index page to PDF first
    $mpdf->WriteHTML($indexHtml);
    $mpdf->AddPage(); // Add a new page for the main content
    $mpdf->WriteHTML($mainContent); // Write the main content

    // Set footer and output the PDF
    $mpdf->SetFooter('{PAGENO} / {nbpg}');
    $mpdf->Output('collection_report.pdf', 'D');
} else {
    echo "No data found";
}
?>
