<?php

class ArtworkGenre {
    private $artworkGenreID;
    private $artworkID;
    private $genreID;

    public function __construct($artworkID, $genreID, $artworkGenreID = null) {
        $this->artworkID = $artworkID;
        $this->genreID = $genreID;
        $this->artworkGenreID = $artworkGenreID;
    }

    // Getter
public function getArtworkGenreID() {
    return $this->artworkGenreID;
}
// Setter
public function setArtworkGenreID($artworkGenreID) {
    $this->artworkGenreID = $artworkGenreID;
}

// Getter
public function getArtworkID() {
    return $this->artworkID;
}
// Setter
public function setArtworkID($artworkID) {
    $this->artworkID = $artworkID;
}

// Getter
public function getGenreID() {
    return $this->genreID;
}
// Setter
public function setGenreID($genreID) {
    $this->genreID = $genreID;
}

}
