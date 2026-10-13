<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../template/fractional-cc/brief.docx') === true) {
    $xml = $zip->getFromName('word/document.xml');
    $xml = preg_replace('/<w:p[^>]*>/', "\n", $xml);
    $text = strip_tags($xml);
    file_put_contents(__DIR__ . '/brief-text.txt', $text);
    echo "Extracted " . strlen($text) . " bytes\n";
    $zip->close();
} else {
    echo "Cannot open docx\n";
}
