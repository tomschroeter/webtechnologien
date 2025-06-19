<?php
/**
 * Returns a valid web path to an image if the file exists on the server,
 * otherwise returns a path to a placeholder image.
 * This helps avoid broken image links in the UI when an expected image is missing.
 *
 * @param string $webPath          Relative web path to the target image
 * @param string $placeholderPath  Fallback image path to use if the target image does not exist
 *
 * @return string The original image path if it exists on the filesystem; otherwise, the placeholder path
 */
function getImagePathOrPlaceholder(string $webPath, string $placeholderPath): string
{
    // Construct the absolute server file path based on the document root and web path
    $absolutePath = $_SERVER['DOCUMENT_ROOT'] . $webPath;

    // Check if the image file physically exists on the server
    if (file_exists($absolutePath)) {
        // If it exists, return the original web path for rendering in HTML
        return $webPath;
    } else {
        // If the file is missing, fall back to the provided placeholder image
        return $placeholderPath;
    }
}