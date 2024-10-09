<?php
require_once __DIR__ . '/vendor/autoload.php';
include "config.php";

// Initialize mPDF
$mpdf = new \Mpdf\Mpdf();

// Fetch all data from your database

$headerHtml = '<div style="text-align: right; font-family: latha; font-size: 16px;">பக்கம் : {PAGENO}</div>';
$mpdf->SetHTMLHeader($headerHtml);

$festival_id = $_GET['festival_id'];




$res = mysqli_query($conn, "
    SELECT * 
    FROM mrg 
    WHERE festival_id = $festival_id 
    ORDER BY place ASC
");

if (mysqli_num_rows($res) > 0) {
    // Use UTF-8 charset in HTML
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



     $groupedData = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $place = $row['place'];
        if (!isset($groupedData[$place])) {
            $groupedData[$place] = [];
        }
        $groupedData[$place][] = $row;
    }
    

    // First, display only 'தாய்மாமன்' entries
    $res_thaimaman = mysqli_query($conn, "
        SELECT * 
        FROM mrg 
        WHERE festival_id = $festival_id 
        AND relative_name = 'தாய்மாமன்'
        ORDER BY place ASC
    ");

    if (mysqli_num_rows($res_thaimaman) > 0) {
        // Display the title for 'தாய்மாமன்' section
        $html .= '<tr>
                    <td colspan="6" style="padding: 5px;text-align:center;font-size:20px;font-family: latha;"><strong>தாய்மாமன்</strong></td>
                  </tr>';

        while ($row = mysqli_fetch_assoc($res_thaimaman)) {
            if ($recordCount == $recordsPerPage) {
                $html .= '<tr>
                            <td colspan="3" style="text-align:right;padding: 10px;font-family: latha;"><strong>Page Total</strong></td>
                            <td style="text-align:right;padding: 10px;"><strong>' . number_format($pageTotal, 0) . '</strong></td>
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

                         <td style="text-align:right; padding: 10px;font-family: latha;font-size: 30px; font-weight: bold;">' . (int)$row['amount'] . '</td>
                        <td style="font-size:14px;padding: 10px;font-family: latha;"></td>
                        <td style="text-align:right; padding: 10px;font-family: latha;"></td>
                      </tr>';
            $pageTotal += $row['amount'];
            $grandTotal += $row['amount'];
            $recordCount++;
        }
    }

    // Now display the rest of the entries grouped by place
    $res_others = mysqli_query($conn, "
        SELECT * 
        FROM mrg 
        WHERE festival_id = $festival_id 
        AND relative_name != 'தாய்மாமன்'
        ORDER BY place ASC
    ");

    while ($row = mysqli_fetch_assoc($res_others)) {
        if ($recordCount == $recordsPerPage) {
            $html .= '<tr>
                        <td colspan="2" style="text-align:right;padding: 10px;font-family: latha;font-size:16px;"><strong>பக்கத்தின் மொத்தம்</strong></td>
                        <td style="text-align:right;padding: 10px; font-size:30px;"><strong>' . number_format($pageTotal, 0) . '</strong></td>
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

        if ($lastPrintedPlace != trim($row['place'])) {
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


                    <td style="text-align:right; padding: 10px;font-family: latha; font-size: 30px; font-weight: bold;">' . number_format($row['amount'], 0) . '</td>
                    <td style="font-size:14px;padding: 10px;font-family: latha;"></td>
                    <td style="text-align:right; padding: 10px;font-family: latha; font-size: 30px;"></td>
                </tr>';
        $pageTotal += $row['amount'];
        $grandTotal += $row['amount'];
        $lastPrintedPlace = trim($row['place']);
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

    $mpdf->WriteHTML($html);
    $mpdf->SetFooter('{PAGENO} / {nbpg}');
} else {
    $html = '<p>No data available</p>';
    $mpdf->WriteHTML($html);
}

// Save the PDF to a specific location on the server
$pdfFilePath = 'pdfs/festival_details_' . $festival_id . '.pdf';
$mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

// Redirect to the download page or provide a download link
header('Location: download_pdf.php?file=' . urlencode($pdfFilePath));
exit;
