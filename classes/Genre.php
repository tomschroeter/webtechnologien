<?php

class Genre
{
    private $genreId;
    private $genreName;
    private $era;
    private $description;
    private $link;


    public function __construct(
        $genreName,
        $era,
        $description = null,
        $link = null,
        $genreId = null
    ) {
        $this->genreName = $genreName;
        $this->era = $era;
        $this->description = $description;
        $this->link = $link;
        $this->genreId = $genreId;
    }

    public function getGenreId()
    {
        return $this->genreId;
    }


    public function setGenreId($genreId)
    {
        $this->genreId = $genreId;
    }


    public function getGenreName()
    {
        return $this->genreName;
    }


    public function setGenreName($genreName)
    {
        $this->genreName = $genreName;
    }


    public function getEra()
    {
        return $this->era;
    }


    public function setEra($era)
    {
        $this->era = $era;
    }


    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getLink()
    {
        return $this->link;
    }


    public function setLink($link)
    {
        $this->link = $link;
    }
}
