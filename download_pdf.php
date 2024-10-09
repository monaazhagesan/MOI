<?php
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $filepath = __DIR__ . '/' . $file;

    if (file_exists($filepath)) {
        // Set headers to trigger download
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Pragma: public');

        // Clear output buffer
        ob_clean();
        flush();

        // Read the file from the server and output it for download
        readfile($filepath);
        exit;
    } else {
        echo "The file does not exist.";
    }
} else {
    echo "No file specified.";
}
?>
