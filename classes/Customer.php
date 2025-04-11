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


    public function __construct(
        $firstName,
        $lastName,
        $address,
        $city,
        $country,
        $postal,
        $email,
        $region = null,
        $phone = null,
        $customerId = null
    ) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setAddress($address);
        $this->setCity($city);
        $this->setRegion($region);
        $this->setCountry($country);
        $this->setPostal($postal);
        $this->setPhone($phone);
        $this->setEmail($email);
        $this->setCustomerId($customerId);
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
