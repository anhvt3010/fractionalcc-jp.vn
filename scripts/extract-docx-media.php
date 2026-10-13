<?php
$docx = __DIR__ . '/../template/fractional-cc/brief.docx';
$zip = new ZipArchive();
if ($zip->open($docx) === true) {
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (strpos($name, 'word/media/') === 0) {
            $stat = $zip->statIndex($i);
            echo $name . ' (' . $stat['size'] . " bytes)\n";
        }
    }
    $zip->close();
} else {
    echo "Cannot open docx\n";
}
