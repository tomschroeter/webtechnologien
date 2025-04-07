<?php

class ArtworkGenre
{
    private $artworkGenreID;
    private $artworkID;
    private $genreID;

    public function __construct($artworkID, $genreID, $artworkGenreID = null)
    {
        $this->artworkID = $artworkID;
        $this->genreID = $genreID;
        $this->artworkGenreID = $artworkGenreID;
    }


    public function getArtworkGenreID()
    {
        return $this->artworkGenreID;
    }

    public function setArtworkGenreID($artworkGenreID)
    {
        $this->artworkGenreID = $artworkGenreID;
    }


    public function getArtworkID()
    {
        return $this->artworkID;
    }

    public function setArtworkID($artworkID)
    {
        $this->artworkID = $artworkID;
    }


    public function getGenreID()
    {
        return $this->genreID;
    }

    public function setGenreID($genreID)
    {
        $this->genreID = $genreID;
    }
}
