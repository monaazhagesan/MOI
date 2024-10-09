<?php
require_once __DIR__ . '/vendor/autoload.php';

$fontDirs = [__DIR__ . '/assets/font'];

$fontData = [
    'latha' => [
        'R' => 'Latha.ttf',
        'B' => 'lathab.ttf',
    ]
];

$mpdf = new \Mpdf\Mpdf([
    'default_font' => 'latha',
    'fontDir' => $fontDirs,
    'fontdata' => $fontData,
    'default_font_size' => 12,
    'mode' => 'utf-8',
]);

// Ensure UTF-8 encoding
$mpdf->WriteHTML('<meta charset="UTF-8">');
$mpdf->WriteHTML('<p>வணக்கம், இது தமிழ் உரை மாதிரி.</p>');

$mpdf->Output();
?>
