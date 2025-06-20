<?php
/**
 * Ensures that an image file name is at least 6 characters long by prepending a '0' if it's shorter.
 * This function is used to fix inconsistencies in file naming,
 * ensuring uniform length required for file lookup or path resolution.
 *
 * @param string $imageFileName The original image file name (e.g., "12345" or "001234")
 *
 * @return string The adjusted file name with a leading zero if it was shorter than 6 characters
 */
function fixFilePath(string $imageFileName): string
{
    // If the image file name has fewer than 6 characters, prepend a '0' to it
    if (strlen($imageFileName) < 6) {
        $imageFileName = '0' . $imageFileName;
    }

    // Return the potentially updated file name
    return $imageFileName;
}
?>