<?php

class Notification
{
    private string $message;
    private string $type;

    public function __construct(string $message, string $type = 'info')
    {
        $this->setMessage($message);
        $this->setType($type);
    }

    public static function success(string $message): self
    {
        return new self($message, 'success');
    }

    public static function error(string $message): self
    {
        return new self($message, 'error');
    }

    public static function primary(string $message): self
    {
        return new self($message, 'primary');
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = trim($message);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type
        ];
    }
}
