<?php

/**
 * Represents a customer / user entity from the database.
 * 
 * This class encapsulates customer data and provides
 * getter and setter methods for accessing and modifying
 * customer properties.
 * 
 * Instances are created using the constructor or the static method `createCustomerFromRecord()`,
 * which accepts an associative array (e.g., a database record).
 */
class Customer
{
    private ?int $customerId;
    private string $firstName;
    private string $lastName;
    private ?string $address;
    private ?string $city;
    private ?string $region;
    private ?string $country;
    private ?string $postal;
    private ?string $phone;
    private ?string $email;

    public function __construct(
        ?int $customerId,
        string $firstName,
        string $lastName,
        ?string $address,
        ?string $city,
        ?string $region,
        ?string $country,
        ?string $postal,
        ?string $phone,
        ?string $email
    ) {
        $this->setCustomerId($customerId);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setAddress($address);
        $this->setCity($city);
        $this->setRegion($region);
        $this->setCountry($country);
        $this->setPostal($postal);
        $this->setPhone($phone);
        $this->setEmail($email);
    }

    public static function createCustomerFromRecord(array $record): Customer
    {
        return new self(
            (int) ($record['CustomerID']),
            (string) $record['FirstName'],
            (string) $record['LastName'],
            $record['Address'] ?? null,
            $record['City'] ?? null,
            $record['Region'] ?? null,
            $record['Country'] ?? null,
            $record['Postal'] ?? null,
            $record['Phone'] ?? null,
            $record['Email'] ?? null
        );
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): void
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(?string $postal): void
    {
        $this->postal = $postal;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Returns the full customer name 
     */
    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
}
