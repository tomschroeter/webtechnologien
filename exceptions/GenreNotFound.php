<?php

/**
 * Custom exception class for when no genre with the given ID was found.
 */
class GenreNotFoundException extends Exception
{
    private int $genreId;

    public function __construct(int $genreId)
    {
        $this->genreId = $genreId;
 
        $message = "Genre with ID {$genreId} couldn't be found.";
        parent::__construct($message);
    }

    public function getGenreId(): int
    {
        return $this->genreId;
    }
}
