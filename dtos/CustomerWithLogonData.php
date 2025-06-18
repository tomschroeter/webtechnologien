<?php

class CustomerWithLogonData
{
    private int $customerId;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $userName;
    private int $type;
    private int $state;
    private int $isAdmin;

    public function __construct(
        int $customerId,
        string $firstName,
        string $lastName,
        string $email,
        string $userName,
        int $type,
        int $state,
        int $isAdmin
    ) {
        $this->customerId = $customerId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->userName = $userName;
        $this->type = $type;
        $this->state = $state;
        $this->isAdmin = $isAdmin;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function getIsAdmin(): int
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(int $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
}

/**
 *
 * @extends \ArrayObject<CustomerWithLogonData>
 */
class CustomerWithLogonDataArray extends \ArrayObject
{
    public function offsetSet($key, $val)
    {
        if ($val instanceof CustomerWithLogonData) {
            return parent::offsetSet($key, $val);
        }

        // Backslash means using the Standard PHP Library ArrayObject class (same for \ArrayObject)
        throw new \InvalidArgumentException('Value must be a CustomerWithLogonData instance');
    }
}
