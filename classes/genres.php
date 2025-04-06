<?php

class Genre {
    private $genreID;
    private $genreName;
    private $era;
    private $description;
    private $link;

    // Constructor using individual parameters
    public function __construct(
        $genreName,
        $era,
        $description = null,
        $link = null,
        $genreID = null
    ) {
        $this->genreName = $genreName;
        $this->era = $era;
        $this->description = $description;
        $this->link = $link;
        $this->genreID = $genreID;
    }
// Getter
public function getGenreID() {
    return $this->genreID;
}

// Setter
public function setGenreID($genreID) {
    $this->genreID = $genreID;
}

// Getter
public function getGenreName() {
    return $this->genreName;
}

// Setter
public function setGenreName($genreName) {
    $this->genreName = $genreName;
}

// Getter
public function getEra() {
    return $this->era;
}

// Setter
public function setEra($era) {
    $this->era = $era;
}

// Getter
public function getDescription() {
    return $this->description;
}

// Setter
public function setDescription($description) {
    $this->description = $description;
}

// Getter
public function getLink() {
    return $this->link;
}

// Setter
public function setLink($link) {
    $this->link = $link;
}


}
