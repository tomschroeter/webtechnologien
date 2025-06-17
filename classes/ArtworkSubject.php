<?php

class ArtworkSubject
{
    private int $artworkSubjectId;
    private int $artworkId;
    private int $subjectId;

    public function __construct($artworkId, $subjectId, $artworkSubjectId)
    {
        $this->setArtworkId($artworkId);
        $this->setSubjectId($subjectId);
        $this->setArtworkSubjectId($artworkSubjectId);
    }

    public function getArtworkSubjectId(): int
    {
        return $this->artworkSubjectId;
    }

    public function setArtworkSubjectId(int $artworkSubjectId): void
    {
        $this->artworkSubjectId = $artworkSubjectId;
    }


    public function getArtworkId(): int
    {
        return $this->artworkId;
    }

    public function setArtworkId(int $artworkId): void
    {
        $this->artworkId = $artworkId;
    }


    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function setSubjectId(int $subjectId): void
    {
        $this->subjectId = $subjectId;
    }
}
