<?php

class CustomerLogon {
    private $customerID;
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
        $customerID = null
    ) {
        $this->userName = $userName;
        $this->pass = $pass;
        $this->salt = $salt;
        $this->state = $state;
        $this->type = $type;
        $this->dateJoined = $dateJoined;
        $this->dateLastModified = $dateLastModified;
        $this->customerID = $customerID;
    }

    // Getter
public function getCustomerID() {
    return $this->customerID;
}
// Setter
public function setCustomerID($customerID) {
    $this->customerID = $customerID;
}

// Getter
public function getUserName() {
    return $this->userName;
}
// Setter
public function setUserName($userName) {
    $this->userName = $userName;
}

// Getter
public function getPass() {
    return $this->pass;
}
// Setter
public function setPass($pass) {
    $this->pass = $pass;
}

// Getter
public function getSalt() {
    return $this->salt;
}
// Setter
public function setSalt($salt) {
    $this->salt = $salt;
}

// Getter
public function getState() {
    return $this->state;
}
// Setter
public function setState($state) {
    $this->state = $state;
}

// Getter
public function getType() {
    return $this->type;
}
// Setter
public function setType($type) {
    $this->type = $type;
}

// Getter
public function getDateJoined() {
    return $this->dateJoined;
}
// Setter
public function setDateJoined($dateJoined) {
    $this->dateJoined = $dateJoined;
}

// Getter
public function getDateLastModified() {
    return $this->dateLastModified;
}
// Setter
public function setDateLastModified($dateLastModified) {
    $this->dateLastModified = $dateLastModified;
}

}
