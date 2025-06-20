<?php

require_once __DIR__ . "/router/FrontController.php";
require_once __DIR__ . "/controllers/ErrorController.php";
require_once __DIR__ . "/exceptions/HttpException.php";

try {
    $frontController = new FrontController();
    $handled = $frontController->dispatch();
    
    // If FrontController didn't handle the request, it might be a static file
    // or the web server should handle it
    if (!$handled) {
        // This shouldn't normally happen, but just in case
        throw new HttpException(404, "Page not found.");
    }
    
} catch (HttpException $e) {
    // Handle HttpExceptions with proper status codes and messages
    $errorController = new ErrorController();
    $errorController->handleHttpException($e);
    
} catch (Exception $e) {
    // Handle unexpected exceptions
    error_log("Unexpected error in application: " . $e->getMessage());
    
    // Show generic 500 error page
    $errorController = new ErrorController();
    $errorController->handleError(500, "An unexpected error occurred. Please try again later.");
}
