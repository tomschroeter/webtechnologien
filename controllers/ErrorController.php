<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../exceptions/HttpException.php";

class ErrorController extends BaseController
{
    public function handleError(int $statusCode, string $message, string $statusText)
    {
        // Set HTTP status code
        http_response_code($statusCode);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $data = [
            'title' => "{$statusCode} - {$statusText} - Art Gallery",
            'statusCode' => $statusCode,
            'statusText' => $statusText,
            'message' => $message
        ];
        
        echo $this->renderWithLayout('errors/error', $data);
    }
    
    public function handleHttpException(HttpException $exception)
    {
        $this->handleError(
            $exception->getStatusCode(),
            $exception->getMessage(),
            $exception->getStatusText(),
        );
    }
}
