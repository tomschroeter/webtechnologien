<?php

class CustomerLogon
{
    private $customerId;
    private $userName;
    private $pass;
    private $salt;
    private $state;
    private $type;
    private $dateJoined;
    private $dateLastModified;
    private $isAdmin;

    private function __construct(
        $customerId = null,
        $userName,
        $pass,
        $salt,
        $state = 1,
        $type = 0,
        $dateJoined = null,
        $dateLastModified = null,
        $isAdmin = false
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
            $record['CustomerID'],
            $record['UserName'],
            $record['Pass'],
            $record['Salt'],
            $record['State'],
            $record['Type'],
            $record['DateJoined'],
            $record['DateLastModified'],
            $record['isAdmin']
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


    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }


    public function getPass()
    {
        return $this->pass;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }


    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }


    public function getDateJoined()
    {
        return $this->dateJoined;
    }

    public function setDateJoined($dateJoined)
    {
        $this->dateJoined = $dateJoined;
    }


    public function getDateLastModified()
    {
        return $this->dateLastModified;
    }

    public function setDateLastModified($dateLastModified)
    {
        $this->dateLastModified = $dateLastModified;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

}
