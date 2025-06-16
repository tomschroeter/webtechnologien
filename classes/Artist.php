<?php

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

    private function __construct(
        $artistId,
        $firstName,
        $lastName,
        $nationality,
        $yearOfBirth,
        $yearOfDeath,
        $details,
        $artistLink
    ) {
        $this->setArtistId($artistId);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setNationality($nationality);
        $this->setYearOfBirth($yearOfBirth);
        $this->setYearOfDeath($yearOfDeath);
        $this->setDetails($details);
        $this->setArtistLink($artistLink);
    }

    public static function createArtistFromRecord(array $record): Artist
    {
        return new self(
            $record['ArtistID'],
            $record['FirstName'],
            $record['LastName'],
            $record['Nationality'],
            $record['YearOfBirth'],
            $record['YearOfDeath'],
            $record['Details'],
            $record['ArtistLink']
        );
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
