<?php
require_once __DIR__ . '/vendor/autoload.php';
include "config.php";

// Initialize mPDF
$mpdf = new \Mpdf\Mpdf();

// Fetch all data from your database

$headerHtml = '<div style="text-align: right; font-family: latha; font-size: 16px;">பக்கம் : {PAGENO}</div>';
$mpdf->SetHTMLHeader($headerHtml);

$festival_id = $_GET['festival_id'];

// Fetch all entries for the given festival ID
$res = mysqli_query($conn, "
    SELECT * 
    FROM mrg 
    WHERE festival_id = $festival_id 
    ORDER BY place ASC
");

// Store the result rows in an array
$allRows = [];
if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $allRows[] = $row; // Collect rows in an array
    }
} else {
    // If no data is available, handle accordingly
    $html = '<p>No data available</p>';
    $mpdf->WriteHTML($html);
    $pdfFilePath = 'pdfs/festival_details_' . $festival_id . '.pdf';
    $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);
    header('Location: download_pdf.php?file=' . urlencode($pdfFilePath));
    exit;
}

// Now you can process the data in $allRows
$taiMamanRows = array_filter($allRows, function ($row) {
    return $row['relative_name'] === 'தாய்மாமன்';
});
$groupedData1 = [];
foreach ($taiMamanRows as $row) {
    $groupedData1['தாய்மாமன்'][] = $row;
}
$otherRows = array_filter($allRows, function ($row) {
    return $row['relative_name'] !== 'தாய்மாமன்';
});
foreach ($otherRows as $row) {
    $place = $row['place'];
    if (!isset($groupedData2[$place]) && !isset($groupedData2[trim($place)])) {
        $groupedData2[$place] = [];
    }
    $groupedData2[$place][] = $row;
}
$groupedData = array_merge($groupedData1, $groupedData2);
$indexPageRows = [];
$count = count(array_keys($groupedData)) / 10;
if ($count <= 1) {
    $startPage = 2;
}
if ($count > 1) {
    $startPage = ceil($count);
}

// $startPage = count(array_keys($groupedData)) / 10 ; // Start from page 2 as index is on page 1
$curentRecords = 0;
$currentPage = $startPage;
// var_dump(json_encode($groupedData));
// exit;
foreach ($groupedData as $place => $records) {
    $curentRecords += count($records);
    $count = $curentRecords / 10;
     $count = $count - ($currentPage - $startPage);
    // var_dump($place);
    // var_dump($curentRecords);
    // var_dump($count);
    // var_dump($currentPage);
    // var_dump($startPage);
    // var_dump(array_search($groupedData['சின்னக்குரவகுடி'],$groupedData));
    // exit;
    // if ('சின்னக்குரவகுடி' == 'சின்னக்குரவகுடி') {
    //     var_dump($curentRecords);
    //     var_dump($count);
    //     var_dump($currentPage);
    //     var_dump($startPage);
    //     exit;
    // }
    if ($count <= 1) {
        $pages = $currentPage;
    }
    if ($count > 1) {
        $pages = $currentPage . ',' . implode(', ', range($currentPage + 1, ceil((count($records) / 10) + $currentPage)));
    }
    // $pages = ceil(count($records) / 10); // Assuming 10 records per page
    $indexPageRows[] = [
        'place' => $place,
        'pages' => $pages, // implode(', ', range($startPage, $startPage + $pages - 1))
    ];
    if ($curentRecords >= 10) {
        // if ($count > 1) {
        $currentPage += 1;
        $curentRecords = $curentRecords % 10;
        // }
    }
    // $startPage += $pages; // Increment start page for the next place
}
// exit;
// Prepare the index page with a heading
$indexHtml = '<h1 style="font-family: latha; text-align: center;">Index Page</h1>'; // Centered heading
$indexHtml .= '<table border="1" style="width:100%; font-size:12px; text-align: center;">'; // Center text for the entire table
$indexHtml .= '<thead><tr>
                    <th style="padding: 5px; font-family: latha; font-size:20px;">வரிசை எண்</th>
                    <th style="padding: 5px; font-family: latha; font-size:20px;">ஊர் பெயர்</th> 
                    <th style="padding: 5px; font-family: latha; font-size:20px;">பக்கம் எண்</th>
                  </tr></thead><tbody>';

// Populate the index table
$varisai_en = 1;
foreach ($indexPageRows as $entry) {
    $indexHtml .= '<tr>
                        <td style="padding: 5px; font-family: latha; text-align: center;">' . $varisai_en++ . '</td> <!-- Sequence number -->
                        <td style="padding: 5px; font-family: latha; text-align: center;">' . $entry['place'] . '</td>
                        <td style="padding: 5px; font-family: latha; text-align: center;">' . $entry['pages'] . '</td> <!-- Page number -->
                     </tr>';
}
$indexHtml .= '</tbody></table>';

// Write and create a new page for the index
$mpdf->WriteHTML($indexHtml);
$mpdf->SetFooter('{PAGENO} / {nbpg}');
$mpdf->AddPage(); // Add a new page for future content if needed


// First, display only 'தாய்மாமன்' entries
$html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
$html .= '<table border="1" style="border-collapse:collapse; width:100%; font-size:12px;">';
$html .= '<thead><tr>
            <th style="padding: 5px;font-size:16px;font-family: latha;">வ.எண்</th>
            <th style="padding: 5px;font-size:16px;font-family: latha;">உறவினர் விவரம்</th>                
            <th style="padding: 5px;font-size:16px;font-family: latha;">பெற்ற மொய்</th>
            <th style="padding: 5px;font-size:16px;font-family: latha;">செய்த மொய்</th>
            <th style="padding: 5px;font-size:16px;font-family: latha;">மொத்தம் பெற்ற இருப்பு</th>
          </tr></thead><tbody>';

$counter = 1;
$pageTotal = 0;
$grandTotal = 0;
$recordsPerPage = 10; // Set the number of records per page to 20
$recordCount = 0;

// Display 'தாய்மாமன்' section
$res_thaimaman = array_filter($allRows, function ($row) {
    return $row['relative_name'] === 'தாய்மாமன்';
});

if (!empty($res_thaimaman)) {
    // Display the title for 'தாய்மாமன்' section
    $html .= '<tr>
                <td colspan="6" style="padding: 5px;text-align:center;font-size:20px;font-family: latha;"><strong>தாய்மாமன்</strong></td>
              </tr>';

    foreach ($res_thaimaman as $row) {
        if ($recordCount == $recordsPerPage) {
            $html .= '<tr>
                        <td colspan="3" style="text-align:right;padding: 10px;font-family: latha;"><strong>Page Total</strong></td>
                        <td style="text-align:right;padding: 10px;"><strong>' . number_format($pageTotal, 2) . '</strong></td>
                        <td colspan="2"></td>
                      </tr></tbody></table>';

            $mpdf->WriteHTML($html);
            $mpdf->SetFooter('{PAGENO} / {nbpg}');
            $mpdf->AddPage();

            // Reset the page total and record count for the new page
            $pageTotal = 0;
            $recordCount = 0;

            $html = '<table border="1" style="border-collapse:collapse; width:100%; font-size:12px;">';
            $html .= '<thead><tr>
                        <th style="padding: 5px;font-size:16px;font-family: latha;">வ.எண்</th>
                        <th style="padding: 5px;font-size:16px;font-family: latha;">உறவினர் விவரம்</th>                
                        <th style="padding: 5px;font-size:16px;font-family: latha;">பெற்ற மொய்</th>
                        <th style="padding: 5px;font-size:16px;font-family: latha;">செய்த மொய்</th>
                        <th style="padding: 5px;font-size:16px;font-family: latha;">மொத்தம் பெற்ற இருப்பு</th>
                      </tr></thead><tbody>';
        }

        // Add row data for 'தாய்மாமன்'
        $html .= '<tr>
                    <td style="text-align:center; font-size:14px;padding: 10px;font-family: latha;">' . $counter++ . '</td>
                    <td style="font-size:14px;padding: 10px;font-family: latha;">
                        ' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') .
            (!empty($row['profession1']) ? ' (' . htmlspecialchars($row['profession1'], ENT_QUOTES, 'UTF-8') . ')' : '') .
            ' - ' . htmlspecialchars($row['spouse_name'], ENT_QUOTES, 'UTF-8') . '
                    </td>
                    <td style="text-align:right; padding: 10px;font-family: latha;font-size: 30px; font-weight: bold;">' . number_format($row['amount'], 2) . '</td>
                    <td style="font-size:14px;padding: 10px;font-family: latha;"></td>
                    <td style="text-align:right; padding: 10px;font-family: latha;"></td>
                  </tr>';
        $pageTotal += $row['amount'];
        $grandTotal += $row['amount'];
        $recordCount++;
    }
}

// Now display the rest of the entries grouped by place
$res_others = array_filter($allRows, function ($row) {
    return $row['relative_name'] !== 'தாய்மாமன்';
});

foreach ($res_others as $row) {
    if ($recordCount == $recordsPerPage) {

        $html .= '<tr>
        <td colspan="2" style="text-align:right;padding: 10px;font-family: latha;font-size:16px;"><strong>பக்கத்தின் மொத்தம்</strong></td>
        <td style="text-align:right;padding: 10px; font-size:25px;"><strong>' . number_format($pageTotal, 0) . '</strong></td>
        <td colspan="2"></td>
    </tr></tbody></table>';


        $mpdf->WriteHTML($html);
        $mpdf->SetFooter('{PAGENO} / {nbpg}');
        $mpdf->AddPage();

        // Reset the page total and record count for the new page
        $pageTotal = 0;
        $recordCount = 0;

        $html = '<table border="1" style="border-collapse:collapse; width:100%; font-size:12px;">';
        $html .= '<thead><tr>
                    <th style="padding: 5px;font-size:16px;font-family: latha;">வ.எண்</th>
                    <th style="padding: 5px;font-size:16px;font-family: latha;">உறவினர் விவரம்</th>                
                    <th style="padding: 5px;font-size:16px;font-family: latha;">பெற்ற மொய்</th>
                    <th style="padding: 5px;font-size:16px;font-family: latha;">செய்த மொய்</th>
                    <th style="padding: 5px;font-size:16px;font-family: latha;">மொத்தம் பெற்ற இருப்பு</th>
                  </tr></thead><tbody>';
    }

    if (!isset($lastPrintedPlace) || $lastPrintedPlace !== trim($row['place'])) {
        $html .= '<tr>
            <td colspan="6" style="padding: 5px;text-align:center;font-size:20px;font-family: latha;"><strong>' . $row['place'] . '</strong></td>
        </tr>';
    }

    // Add row data for other relatives
    $html .= '<tr>
                <td style="text-align:center; font-size:14px;padding: 10px;font-family: latha;">' . $counter++ . '</td>
                <td style="font-size:14px;padding: 10px;font-family: latha;">
                    ' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') .
        (!empty($row['profession1']) ? ' (' . htmlspecialchars($row['profession1'], ENT_QUOTES, 'UTF-8') . ')' : '') .
        ' - ' . htmlspecialchars($row['spouse_name'], ENT_QUOTES, 'UTF-8') . '
                </td>
                <td style="text-align:right; padding: 10px;font-family: latha;font-size: 30px; font-weight: bold;">' . number_format($row['amount'], 0) . '</td>
                <td style="font-size:14px;padding: 10px;font-family: latha;"></td>
                <td style="text-align:right; padding: 10px;font-family: latha;"></td>
              </tr>';

    $lastPrintedPlace = trim($row['place']);
    $pageTotal += $row['amount'];
    $grandTotal += $row['amount'];
    $recordCount++;
}


 // Add the last page's total amount
 $html .= '<tr>
 <td colspan="2" style="text-align:right;padding: 10px;font-family: latha;font-size:16px;"><strong>பக்கத்தின் மொத்தம்</strong></td>
 <td style="text-align:right;padding: 10px;font-size:25px;"><strong>' . number_format($pageTotal, 0) . '</strong></td>
 <td colspan="2"></td>
</tr>';

// Add grand total at the end
$html .= '<tr>
 <td colspan="2" style="text-align:right;padding: 10px;font-family: latha;font-size:16px;"><strong>மொத்த தொகை</strong></td>
 <td style="text-align:right;padding: 10px;font-size:25px;"><strong>' . number_format($grandTotal, 0) . '</strong></td>
 <td colspan="2"></td>
</tr></tbody></table>';


// Write the final HTML to the PDF
$mpdf->WriteHTML($html);
$mpdf->SetFooter('{PAGENO} / {nbpg}');

// Save the PDF file
$pdfFilePath = 'pdfs/festival_details_' . $festival_id . '.pdf';
$mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

// Redirect to download
header('Location: download_pdf.php?file=' . urlencode($pdfFilePath));
exit;
