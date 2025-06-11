<?php
function fixFilePath(array $array) { 
    if (strlen($array['ImageFileName']) < 6) {
            $array['ImageFileName'] = '0' . $array['ImageFileName'];
        }
    return $array;
}
?>