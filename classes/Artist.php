<?php

class Artist
{
    private int $artistId;
    private string $firstName;
    private string $lastName;
    private ?string $nationality;
    private ?int $yearOfBirth;
    private ?int $yearOfDeath;
    private ?string $details;
    private ?string $artistLink;

    private function __construct(
        int $artistId,
        string $firstName,
        string $lastName,
        ?string $nationality,
        ?int $yearOfBirth,
        ?int $yearOfDeath,
        ?string $details,
        ?string $artistLink
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
            (int) $record['ArtistID'],
            (string) $record['FirstName'],
            (string) $record['LastName'],
            $record['Nationality'] !== null ? (string) $record['Nationality'] : null,
            isset($record['YearOfBirth']) ? (int) $record['YearOfBirth'] : null,
            isset($record['YearOfDeath']) ? (int) $record['YearOfDeath'] : null,
            $record['Details'] !== null ? (string) $record['Details'] : null,
            $record['ArtistLink'] !== null ? (string) $record['ArtistLink'] : null
        );
    }

    public function getArtistId(): int
    {
        return $this->artistId;
    }

    public function setArtistId(int $artistId): void
    {
        $this->artistId = $artistId;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
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

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): void
    {
        $this->nationality = $nationality;
    }

    public function getYearOfBirth(): ?int
    {
        return $this->yearOfBirth;
    }

    public function setYearOfBirth(?int $yearOfBirth): void
    {
        $this->yearOfBirth = $yearOfBirth;
    }

    public function getYearOfDeath(): ?int
    {
        return $this->yearOfDeath;
    }

    public function setYearOfDeath(?int $yearOfDeath): void
    {
        $this->yearOfDeath = $yearOfDeath;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): void
    {
        $this->details = $details;
    }

    public function getArtistLink(): ?string
    {
        return $this->artistLink;
    }

    public function setArtistLink(?string $artistLink): void
    {
        $this->artistLink = $artistLink;
    }

    public function getFullName(): string
    {
        return trim(($this->getFirstName() ?? '') . ' ' . $this->getLastName());
    }
}