<?php

// Bootstrap file for MVC routing
require_once __DIR__ . "/router/FrontController.php";

try {
    // Try MVC routing first
    $frontController = new FrontController();
    $handled = $frontController->dispatch();
    
    // If not handled by MVC, return false so the original file can be processed
    if (!$handled) {
        return false;
    }
    
    // If handled by MVC, we're done
    return true;
} catch (Exception $e) {
    error_log("MVC Bootstrap Error: " . $e->getMessage());
    
    // Show 500 error page
    try {
        require_once __DIR__ . "/controllers/ErrorController.php";
        $errorController = new ErrorController();
        $errorController->serverError();
        return true;
    } catch (Exception $innerException) {
        // If we can't even show the error page, fall back to basic error
        http_response_code(500);
        echo "<h1>500 - Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
        return true;
    }
}
