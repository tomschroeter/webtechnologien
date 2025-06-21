<?php

/**
 * Custom exception class for HTTP errors.
 * Contains an HTTP status code and a corresponding status text message,
 * alongside the standard exception message.
 */
class HttpException extends Exception
{
    private int $statusCode;
    private string $statusText;

    public function __construct(int $statusCode, string $message, array $statusTexts = null)
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
}
