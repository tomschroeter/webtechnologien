<?php

// Bootstrap file for MVC routing
require_once dirname(__DIR__) . "/src/router/FrontController.php";

// Try MVC routing first
$frontController = new FrontController();
$handled = $frontController->dispatch();

// If not handled by MVC, return false so the original file can be processed
if (!$handled) {
    return false;
}

// If handled by MVC, we're done
return true;
