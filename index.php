<?php

require_once __DIR__ . "/router/FrontController.php";
require_once __DIR__ . "/controllers/ErrorController.php";
require_once __DIR__ . "/exceptions/HttpException.php";

/**
 * Application front entry point.
 *
 * Initializes the FrontController to dispatch HTTP requests.
 * Handles exceptions by delegating to the ErrorController.
 *
 * Routing is handled via FrontController. If no matching route is found
 * or a static resource is requested, an appropriate HTTP error is triggered.
 */
try {
    $frontController = new FrontController();

    // Attempt to handle the current request
    $handled = $frontController->dispatch();

    // If request was not handled (e.g., static file), throw a 404 error
    if (!$handled) {
        throw new HttpException(404, "Page not found.");
    }

} catch (HttpException $e) {
    // Handle known HTTP exceptions (404, 403, etc.)
    $errorController = new ErrorController();
    $errorController->handleHttpException($e);

} catch (Exception $e) {
    // Log unexpected exceptions and show a generic error page
    error_log("Unexpected error in application: " . $e->getMessage());

    $errorController = new ErrorController();
    $errorController->handleError(
        500,
        "An unexpected error occurred. Please try again later.",
        'Internal Server Error'
    );
}
