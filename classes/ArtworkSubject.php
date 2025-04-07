<?php

class ArtworkSubject
{
    private $artworkSubjectId;
    private $artworkId;
    private $subjectId;

    public function __construct($artworkId, $subjectId, $artworkSubjectId = null)
    {
        $this->artworkId = $artworkId;
        $this->subjectId = $subjectId;
        $this->artworkSubjectId = $artworkSubjectId;
    }


    public function getArtworkSubjectId()
    {
        return $this->artworkSubjectId;
    }

    public function setArtworkSubjectId($artworkSubjectId)
    {
        $this->artworkSubjectId = $artworkSubjectId;
    }


    public function getArtworkId()
    {
        return $this->artworkId;
    }

    public function setArtworkId($artworkId)
    {
        $this->artworkId = $artworkId;
    }


    public function getSubjectId()
    {
        return $this->subjectId;
    }

    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;
    }
}
