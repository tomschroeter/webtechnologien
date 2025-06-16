<?php
// Try MVC routing first
$mvcHandled = require_once __DIR__ . "/mvc_bootstrap.php";

// If MVC handled the request, we're done
if ($mvcHandled) {
    exit(); // Use exit() instead of return to stop execution
}
?>