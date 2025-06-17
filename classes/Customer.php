<?php

class Customer
{
    private $customerId;
    private $firstName;
    private $lastName;
    private $address;
    private $city;
    private $region;
    private $country;
    private $postal;
    private $phone;
    private $email;


    private function __construct(
        $customerId = null,
        $firstName,
        $lastName,
        $address,
        $city,
        $region = null,
        $country,
        $postal,
        $phone = null,
        $email
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
            $record['CustomerID'],
            $record['FirstName'],
            $record['LastName'],
            $record['Address'],
            $record['City'],
            $record['Region'],
            $record['Country'],
            $record['Postal'],
            $record['Phone'],
            $record['Email']
        );
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }


    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }


    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }


    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }


    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }


    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }


    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }


    public function getPostal()
    {
        return $this->postal;
    }

    public function setPostal($postal)
    {
        $this->postal = $postal;
    }


    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
}
