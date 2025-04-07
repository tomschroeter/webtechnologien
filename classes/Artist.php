<?php
class artists
{


    private $artistId;
    private $firstName;
    private $lastName;
    private $nationality;
    private $yearOfBirth;
    private $yearOfDeath;
    private $details;
    private $artistLink;



    public function __construct(
        $firstName,
        $lastName,
        $nationality,
        $yearOfBirth,
        $yearOfDeath = null,
        $details = null,
        $artistLink = null,
        $artistId = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->nationality = $nationality;
        $this->yearOfBirth = $yearOfBirth;
        $this->yearOfDeath = $yearOfDeath;
        $this->details = $details;
        $this->artistLink = $artistLink;
        $this->artistId = $artistId;
    }

    public function getArtistID()
    {
        return $this->artistId;
    }

    public function setArtistID($artistId)
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
