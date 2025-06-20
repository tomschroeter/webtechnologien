<?php

class HttpException extends Exception
{
    private int $statusCode;
    private string $statusText;
    
    public function __construct(int $statusCode, string $message = '', array $statusTexts = null)
    {
        $this->statusCode = $statusCode;
        
        // Default status texts
        $defaultStatusTexts = [
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
        
        $statusTexts = $statusTexts ?? $defaultStatusTexts;
        $this->statusText = $statusTexts[$statusCode] ?? 'Unknown Error';
        
        // Use provided message or generate a default one
        $message = $message ?: $this->getDefaultMessage($statusCode);
        
        parent::__construct($message, $statusCode);
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    public function getStatusText(): string
    {
        return $this->statusText;
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
