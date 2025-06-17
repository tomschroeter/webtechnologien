<?php

class CustomerLogon
{
    private int $customerId;
    private string $userName;
    private string $pass;
    private ?string $salt;
    private int $state;
    private int $type;
    private string $dateJoined;
    private string $dateLastModified;
    private ?int $isAdmin;

    private function __construct(
        int $customerId,
        string $userName,
        string $pass,
        ?string $salt,
        int $state,
        int $type,
        string $dateJoined,
        string $dateLastModified,
        ?int $isAdmin
    ) {
        $this->setCustomerId($customerId);
        $this->setUserName($userName);
        $this->setPass($pass);
        $this->setSalt($salt);
        $this->setState($state);
        $this->setType($type);
        $this->setDateJoined($dateJoined);
        $this->setDateLastModified($dateLastModified);
        $this->setIsAdmin($isAdmin);
    }

    public static function createCustomerLogonFromRecord(array $record): CustomerLogon
    {
        return new self(
            (int) $record['CustomerID'],
            (string) $record['UserName'],
            (string) $record['Pass'],
            $record['Salt'] ?? null,
            (int) $record['State'],
            (int) $record['Type'],
            (string) $record['DateJoined'],
            (string) $record['DateLastModified'],
            isset($record['isAdmin']) ? (int) $record['isAdmin'] : null
        );
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass): void
    {
        $this->pass = $pass;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getDateJoined(): string
    {
        return $this->dateJoined;
    }

    public function setDateJoined(string $dateJoined): void
    {
        $this->dateJoined = $dateJoined;
    }

    public function getDateLastModified(): string
    {
        return $this->dateLastModified;
    }

    public function setDateLastModified(string $dateLastModified): void
    {
        $this->dateLastModified = $dateLastModified;
    }

    public function getIsAdmin(): ?int
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(?int $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }
}
