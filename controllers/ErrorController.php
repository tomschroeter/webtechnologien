<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../exceptions/HttpException.php";

class ErrorController extends BaseController
{
    public function handleError(int $statusCode, string $message = '', string $statusText = '')
    {
        // Set HTTP status code
        http_response_code($statusCode);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($statusText)) {
            $statusText = $this->getStatusText($statusCode);
        }
        
        if (empty($message)) {
            $message = $this->getDefaultMessage($statusCode);
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
            $exception->getStatusText()
        );
    }
    
    public function serverError()
    {
        $this->handleError(500);
    }
    
    private function getStatusText(int $statusCode): string
    {
        $statusTexts = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            409 => 'Conflict',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable'
        ];
        
        return $statusTexts[$statusCode] ?? 'Unknown Error';
    }
    
    private function getDefaultMessage(int $statusCode): string
    {
        switch ($statusCode) {
            case 400:
                return "The request was invalid or cannot be processed.";
            case 401:
                return "You must be logged in to perform this action.";
            case 403:
                return "You are not authorized to perform this action.";
            case 404:
                return "The requested resource was not found.";
            case 409:
                return "The request conflicts with the current state of the resource.";
            case 422:
                return "The request data is invalid.";
            case 500:
                return "An internal server error occurred. Please try again later.";
            case 502:
                return "Bad gateway error occurred.";
            case 503:
                return "The service is temporarily unavailable.";
            default:
                return "An error occurred while processing your request.";
        }
    }
}
