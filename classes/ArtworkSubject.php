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


    public function getArtworkSubjectID()
    {
        return $this->artworkSubjectId;
    }

    public function setArtworkSubjectID($artworkSubjectId)
    {
        $this->artworkSubjectId = $artworkSubjectId;
    }


    public function getArtworkID()
    {
        return $this->artworkId;
    }

    public function setArtworkID($artworkId)
    {
        $this->artworkId = $artworkId;
    }


    public function getSubjectID()
    {
        return $this->subjectId;
    }

    public function setSubjectID($subjectId)
    {
        $this->subjectId = $subjectId;
    }
}
