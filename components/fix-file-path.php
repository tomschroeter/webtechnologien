<?php
function fixFilePath(string $imageFileName): string
{
    if (strlen($imageFileName) < 6) {
        $imageFileName = '0' . $imageFileName;
    }
    return $imageFileName;
}
?>