<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../exceptions/HttpException.php";

/**
 * Controller handling error messages.
 */
class ErrorController extends BaseController
{
    /**
     * Handles displaying a generic error page with a given HTTP status code and message.
     * 
     * @param int $statusCode HTTP status code to send (e.g., 404, 500).
     * @param string $message Detailed error message to display.
     * @param string $statusText Short text description of the status (e.g., "Not Found", "Internal Server Error").
     * 
     * Sets the HTTP response code, prepares error data, and renders the error view with a layout.
     */
    public function handleError(int $statusCode, string $message, string $statusText): void
    {
        // Set the HTTP response status code header
        http_response_code($statusCode);

        // Ensure session is started (needed for notifications or other session data)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Prepare data array to pass to the error view
        $data = [
            'title' => "{$statusCode} - {$statusText} - Art Gallery",
            'statusCode' => $statusCode,
            'statusText' => $statusText,
            'message' => $message
        ];

        // Render the error view within the main layout and output it
        echo $this->renderWithLayout('errors/error', $data);
    }

    /**
     * Handles an HttpException by extracting details and delegating to handleError.
     * 
     * @param HttpException $exception The exception object that contains status code, message, and status text.
     * 
     * This method is a convenient wrapper to unify exception handling for HTTP errors.
     */
    public function handleHttpException(HttpException $exception): void
    {
        $this->handleError(
            $exception->getStatusCode(),
            $exception->getMessage(),
            $exception->getStatusText(),
        );
    }
}
