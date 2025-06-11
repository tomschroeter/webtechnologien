<?php
function fixFilePath(array $artwork) { 
    if (strlen($artwork['ImageFileName']) < 6) {
            $artwork['ImageFileName'] = '0' . $artwork['ImageFileName'];
        }
    return $artwork;
}
?>