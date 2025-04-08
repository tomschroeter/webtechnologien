<?php

class customerlogon
{ 
    private $customerId;
    private $userName;
    private $pass;
    private $salt;
    private $state;      // Activ (1) / Inactiv (0)
    private $type;       // 0 = User, 1 = Admin
    private $dateJoined;
    private $dateLastModified;

    public function __construct(
        $userName,
        $pass,
        $salt = null,
        $state = 1,
        $type = 0,
        $dateJoined = null,
        $dateLastModified = null,
        $customerId = null
    ) {
        $this->userName = $userName;
        $this->pass = $pass;
        $this->salt = $salt;
        $this->state = $state;
        $this->type = $type;
        $this->dateJoined = $dateJoined;
        $this->dateLastModified = $dateLastModified;
        $this->customerId = $customerId;
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
}
