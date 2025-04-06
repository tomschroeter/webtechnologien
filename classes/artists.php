<?php
class artists
{


    private $artistID;
    private $firstName;
    private $lastName;
    private $nationality;
    private $yearOfBirth;
    private $yearOfDeath;
    private $details;      
    private $artistLink;


    // Constructor with individual parameters
    public function __construct(
        $firstName,
        $lastName,
        $nationality,
        $yearOfBirth,
        $yearOfDeath = null,
        $details = null,
        $artistLink = null,
        $artistID = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->nationality = $nationality;
        $this->yearOfBirth = $yearOfBirth;
        $this->yearOfDeath = $yearOfDeath;
        $this->details = $details;
        $this->artistLink = $artistLink;
        $this->artistID = $artistID; 
    }
    //Getter
    public function getArtistID() {
        return $this->artistID;
    }
    //Setter
    public function setArtistID($artistID) {
        $this->artistID = $artistID;
    }
    //Getter
    public function getFirstName() {
        return $this->firstName;
    }
    //Setter
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    //Getter
    public function getLastName() {
        return $this->lastName;
    }
    //Setter
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    //Getter
    public function getNationality() {
        return $this->nationality;
    }
    //Setter
    public function setNationality($nationality) {
        $this->nationality = $nationality;
    }
    //Getter
    public function getYearOfBirth() {
        return $this->yearOfBirth;
    }
    //Setter
    public function setYearOfBirth($yearOfBirth) {
        $this->yearOfBirth = $yearOfBirth;
    }
    //Getter
    public function getYearOfDeath() {
        return $this->yearOfDeath;
    }
    //Setter
    public function setYearOfDeath($yearOfDeath) {
        $this->yearOfDeath = $yearOfDeath;
    }
    //Getter
    public function getDetails() {
        return $this->details;
    }
    //Setter
    public function setDetails($details) {
        $this->details = $details;
    }
    //Getter
    public function getArtistLink() {
        return $this->artistLink;
    }
    //Setter
    public function setArtistLink($artistLink) {
        $this->artistLink = $artistLink;
    }
    
}
