<?php

/**
 * Represents a Customer object along with the associated logon data.
 *
 * This class encapsulates a Customer instance together with their logon data,
 * providing convenient accessors to retrieve both customer data and login information.
 *
 */
class CustomerWithLogonData
{
    private int $customerId;
    private ?string $firstName;
    private string $lastName;
    private string $email;
    private string $userName;
    private string $address;
    private string $city;
    private ?string $region;
    private string $country;
    private ?string $postal;
    private ?string $phone;
    private int $type;
    private int $state;
    private int $isAdmin;

    public function __construct(
        int $customerId,
        ?string $firstName,
        string $lastName,
        string $email,
        string $userName,
        string $address,
        string $city,
        ?string $region,
        string $country,
        ?string $postal,
        ?string $phone,
        int $type,
        int $state,
        int $isAdmin
    ) {
        $this->customerId = $customerId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->userName = $userName;
        $this->address = $address;
        $this->city = $city;
        $this->region = $region;
        $this->country = $country;
        $this->postal = $postal;
        $this->phone = $phone;
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

    public function getFirstName(): ?string
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(string $postal): void
    {
        $this->postal = $postal;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
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
    public function offsetSet($key, $val): void
    {
        if (!$val instanceof CustomerWithLogonData) {
            throw new \InvalidArgumentException('Value must be a CustomerWithLogonData instance');
        }

        parent::offsetSet($key, $val);
    }
}
