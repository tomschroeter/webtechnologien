<?php

class ArtworkGenre
{
    private $artworkGenreId;
    private $artworkId;
    private $genreId;

    public function __construct($artworkId, $genreId, $artworkGenreId = null)
    {
        $this->setArtworkId($artworkId);
        $this->setGenreId($genreId);
        $this->setArtworkGenreId($artworkGenreId);
    }

    public function getArtworkGenreId()
    {
        return $this->artworkGenreId;
    }

    public function setArtworkGenreId($artworkGenreId)
    {
        $this->artworkGenreId = $artworkGenreId;
    }

    public function getArtworkId()
    {
        return $this->artworkId;
    }

    public function setArtworkId($artworkId)
    {
        $this->artworkId = $artworkId;
    }

    public function getGenreId()
    {
        return $this->genreId;
    }

    public function setGenreId($genreId)
    {
        $this->genreId = $genreId;
    }
}
