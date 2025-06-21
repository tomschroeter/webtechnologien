<?php

/**
 * Represents a genre entity from the database.
 * 
 * This class encapsulates genre data and provides
 * getter and setter methods for accessing and modifying
 * genre properties.
 * 
 * Instances are created using the static method `createGenreFromRecord()`,
 * which accepts an associative array (e.g., a database record).
 */
class Genre
{
    private int $genreId;
    private string $genreName;
    private ?string $era;
    private ?string $description;
    private ?string $link;

    private function __construct(
        string $genreName,
        ?string $era,
        ?string $description = null,
        ?string $link = null,
        ?int $genreId = null
    ) {
        $this->setGenreName($genreName);
        $this->setEra($era);
        $this->setDescription($description);
        $this->setLink($link);
        if ($genreId !== null) {
            $this->setGenreId($genreId);
        }
    }

    public static function createGenreFromRecord(array $record): Genre
    {
        return new self(
            genreName: (string) $record['GenreName'],
            era: $record['Era'] ?? null,
            description: $record['Description'] ?? null,
            link: $record['Link'] ?? null,
            genreId: $record['GenreID']
        );
    }

    public function getGenreId(): int
    {
        return $this->genreId;
    }

    public function setGenreId(int $genreId): void
    {
        $this->genreId = $genreId;
    }

    public function getGenreName(): string
    {
        return $this->genreName;
    }

    public function setGenreName(string $genreName): void
    {
        $this->genreName = $genreName;
    }

    public function getEra(): ?string
    {
        return $this->era;
    }

    public function setEra(?string $era): void
    {
        $this->era = $era;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }
}
