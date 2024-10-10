<?php
require_once __DIR__ . '/vendor/autoload.php';
include "config.php";

// Initialize mPDF
$mpdf = new \Mpdf\Mpdf();

// Fetch all data from your database
$festival_id = $_GET['festival_id'];

$index = [];

// Start generating the PDF content
$res = mysqli_query($conn, "
    SELECT * 
    FROM mrg 
    WHERE festival_id = $festival_id 
    ORDER BY place ASC
");

if (mysqli_num_rows($res) > 0) {
    // Use UTF-8 charset in HTML
    $html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

    // Prepare the index page with a heading
    $indexHtml = '<h1 style="font-family: latha;">Index Page</h1>';
    $indexHtml .= '<table style="width:100%; font-size:12px;">';
    $indexHtml .= '<thead><tr>
                     <th style="padding: 5px; font-family: latha; font-size:20px;">பெயர்</th>
                     <th style="padding: 5px; font-family: latha; font-size:20px;">பக்கம் எண்</th>
                   </tr></thead><tbody>';

    $grandTotal = 0;
    $lastPrintedPlace = '';

    // Fetching data to create index and the main table
    while ($row = mysqli_fetch_assoc($res)) {
        $place = $row['place'];
        
        // Create index entry for each place
        if ($lastPrintedPlace !== trim($place)) {
            // Add a new page before each new place
            if ($grandTotal > 0) {
                $mpdf->AddPage(); // Add a new page if it's not the first entry
            }

            // Store the place and the current page number
            $currentPage = $mpdf->page; // Get the current page number
            $index[] = ['place' => $place, 'page' => $currentPage + 1]; // Store next page number for index

            // Add to index HTML
            $indexHtml .= '<tr>
                             <td style="padding: 5px; font-family: latha;">' . htmlspecialchars($place, ENT_QUOTES, 'UTF-8') . '</td>
                             <td style="padding: 5px; font-family: latha;">' . $currentPage . '</td>
                           </tr>';
            $lastPrintedPlace = trim($place);
            $grandTotal++;
        }

        // Add your main content here if needed (e.g., other details related to the place)
    }

    // Close the index table
    $indexHtml .= '</tbody></table>';
    
    // Write the index page
    $mpdf->WriteHTML($indexHtml);
    $mpdf->AddPage(); // Add a new page for the details

    // Now start the main content table
    $html .= '<table border="1" style="border-collapse:collapse; width:100%; font-size:12px;">';
    $html .= '<thead><tr>
                <th style="padding: 5px;font-size:16px;font-family: latha;">வ.எண்</th>
                <th style="padding: 5px;font-size:16px;font-family: latha;">உறவினர் விவரம்</th>                
                <th style="padding: 5px;font-size:16px;font-family: latha;">பெற்ற மொய்</th>
                <th style="padding: 5px;font-size:16px;font-family: latha;">செய்த மொய்</th>
                <th style="padding: 5px;font-size:16px;font-family: latha;">மொத்தம் பெற்ற இருப்பு</th>
              </tr></thead><tbody>';

    // Reset for main data
    $counter = 1;
    $pageTotal = 0;
    $grandTotal = 0;
    $recordCount = 0;

    // Fetch data again for detailed view
    mysqli_data_seek($res, 0); // Reset pointer to the start
    while ($row = mysqli_fetch_assoc($res)) {
        // Add row data for relatives
        $html .= '<tr>
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
        $pageTotal += $row['amount'];
        $grandTotal += $row['amount'];
        $recordCount++;
    }

    // Add the last page's total amount
    $html .= '<tr>
                <td colspan="2" style="text-align:right;padding: 10px;font-family: latha;font-size:16px;"><strong>பக்கத்தின் மொத்தம்</strong></td>
                <td style="text-align:right; padding: 10px;font-family: latha; font-size: 30px; font-weight: bold;"><strong>' . number_format($pageTotal, 0) . '</strong></td>
                <td colspan="2" style="font-size:30px;"></td>
              </tr>'; 

    // Add grand total at the end
    $html .= '<tr>
                <td colspan="2" style="text-align:right;padding: 10px;font-family: latha;"><strong>மொத்த தொகை</strong></td>
                <td style="text-align:right;padding: 10px; font-size: 30px;"><strong>' . number_format($grandTotal, 0) . '</strong></td>
                <td colspan="2"></td>
              </tr></tbody></table>';

    // Write the detailed content
    $mpdf->WriteHTML($html);
    $mpdf->SetFooter('{PAGENO} / {nbpg}');

    // Output the PDF
    $mpdf->Output('collection_report.pdf', 'D');
} else {
    echo "No data found";
}
?>
