<?php
// Returns a web-usable image path if the file exists, otherwise returns the placeholder path
function getImagePathOrPlaceholder(string $webPath, string $placeholderPath): string {
    $absolutePath = $_SERVER['DOCUMENT_ROOT'] . $webPath;
    if (file_exists($absolutePath)) {
        return $webPath;
    } else {
        return $placeholderPath;
    }
}