<?php

/**
 * Custom exception class for when no artwork with the given ID was found.
 */
class ArtworkNotFoundException extends Exception
{
    private int $artworkId;

    public function __construct(int $artworkId)
    {
        $this->artworkId = $artworkId;
 
        $message = "Artwork with ID {$artworkId} couldn't be found.";
        parent::__construct($message);
    }

    public function getArtworkId(): int
    {
        return $this->artworkId;
    }
}
