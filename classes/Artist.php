<?php
require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/dtos/ArtistWithStats.php";

class Artist
{
    private $artistId;
    private $firstName;
    private $lastName;
    private $nationality;
    private $yearOfBirth;
    private $yearOfDeath;
    private $details;
    private $artistLink;

    public function __construct($record)
    {
        $this->artistId    = $record['ArtistID'];
        $this->firstName   = $record['FirstName'];
        $this->lastName    = $record['LastName'];
        $this->nationality = $record['Nationality'];
        $this->yearOfBirth = $record['YearOfBirth'];
        $this->yearOfDeath = $record['YearOfDeath'];
        $this->details     = $record['Details'];
        $this->artistLink  = $record['ArtistLink'];
    }
    
    public function getArtistId()
    {
        return $this->artistId;
    }

    public function setArtistId($artistId)
    {
        $this->artistId = $artistId;
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

    public function getNationality()
    {
        return $this->nationality;
    }

    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    public function getYearOfBirth()
    {
        return $this->yearOfBirth;
    }

    public function setYearOfBirth($yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;
    }

    public function getYearOfDeath()
    {
        return $this->yearOfDeath;
    }

    public function setYearOfDeath($yearOfDeath)
    {
        $this->yearOfDeath = $yearOfDeath;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }

    public function getArtistLink()
    {
        return $this->artistLink;
    }

    public function setArtistLink($artistLink)
    {
        $this->artistLink = $artistLink;
    }
}
