<?php

/**
 * Custom exception class for when no artist with the given ID was found.
 */
class ArtistNotFoundException extends Exception
{
    private int $artistId;

    public function __construct(int $artistId)
    {
        $this->artistId = $artistId;
 
        $message = "Artist with ID {$artistId} couldn't be found.";
        parent::__construct($message);
    }

    public function getArtistId(): int
    {
        return $this->artistId;
    }
}
