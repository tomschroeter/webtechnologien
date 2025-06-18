<?php

class ArtworkGenre
{
    private int $artworkGenreId;
    private int $artworkId;
    private int $genreId;

    public function __construct(int $artworkId, int $genreId, int $artworkGenreId)
    {
        $this->setArtworkId($artworkId);
        $this->setGenreId($genreId);
        $this->setArtworkGenreId($artworkGenreId);
    }

    public function getArtworkGenreId(): int
    {
        return $this->artworkGenreId;
    }

    public function setArtworkGenreId(int $artworkGenreId): void
    {
        $this->artworkGenreId = $artworkGenreId;
    }

    public function getArtworkId(): int
    {
        return $this->artworkId;
    }

    public function setArtworkId(int $artworkId): void
    {
        $this->artworkId = $artworkId;
    }

    public function getGenreId(): int
    {
        return $this->genreId;
    }

    public function setGenreId(int $genreId): void
    {
        $this->genreId = $genreId;
    }
}
