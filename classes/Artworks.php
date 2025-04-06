<?php

class Artwork {
    private $artworkID;
    private $artistID;
    private $imageFileName;
    private $title;
    private $description;
    private $excerpt;
    private $artworkType;
    private $yearOfWork;
    private $width;
    private $height;
    private $medium;
    private $originalHome;
    private $galleryID;
    private $artworkLink;
    private $googleLink;

    public function __construct(
        $artistID,
        $imageFileName,
        $title,
        $description,
        $excerpt,
        $artworkType,
        $yearOfWork,
        $width,
        $height,
        $medium,
        $originalHome,
        $galleryID,
        $artworkLink = null,
        $googleLink = null,
        $artworkID = null
    ) {
        $this->artistID = $artistID;
        $this->imageFileName = $imageFileName;
        $this->title = $title;
        $this->description = $description;
        $this->excerpt = $excerpt;
        $this->artworkType = $artworkType;
        $this->yearOfWork = $yearOfWork;
        $this->width = $width;
        $this->height = $height;
        $this->medium = $medium;
        $this->originalHome = $originalHome;
        $this->galleryID = $galleryID;
        $this->artworkLink = $artworkLink;
        $this->googleLink = $googleLink;
        $this->artworkID = $artworkID;
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
public function getArtistID() {
    return $this->artistID;
}
// Setter
public function setArtistID($artistID) {
    $this->artistID = $artistID;
}

// Getter
public function getImageFileName() {
    return $this->imageFileName;
}
// Setter
public function setImageFileName($imageFileName) {
    $this->imageFileName = $imageFileName;
}

// Getter
public function getTitle() {
    return $this->title;
}
// Setter
public function setTitle($title) {
    $this->title = $title;
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
public function getExcerpt() {
    return $this->excerpt;
}
// Setter
public function setExcerpt($excerpt) {
    $this->excerpt = $excerpt;
}

// Getter
public function getArtworkType() {
    return $this->artworkType;
}
// Setter
public function setArtworkType($artworkType) {
    $this->artworkType = $artworkType;
}

// Getter
public function getYearOfWork() {
    return $this->yearOfWork;
}
// Setter
public function setYearOfWork($yearOfWork) {
    $this->yearOfWork = $yearOfWork;
}

// Getter
public function getWidth() {
    return $this->width;
}
// Setter
public function setWidth($width) {
    $this->width = $width;
}

// Getter
public function getHeight() {
    return $this->height;
}
// Setter
public function setHeight($height) {
    $this->height = $height;
}

// Getter
public function getMedium() {
    return $this->medium;
}
// Setter
public function setMedium($medium) {
    $this->medium = $medium;
}

// Getter
public function getOriginalHome() {
    return $this->originalHome;
}
// Setter
public function setOriginalHome($originalHome) {
    $this->originalHome = $originalHome;
}

// Getter
public function getGalleryID() {
    return $this->galleryID;
}
// Setter
public function setGalleryID($galleryID) {
    $this->galleryID = $galleryID;
}

// Getter
public function getArtworkLink() {
    return $this->artworkLink;
}
// Setter
public function setArtworkLink($artworkLink) {
    $this->artworkLink = $artworkLink;
}

// Getter
public function getGoogleLink() {
    return $this->googleLink;
}
// Setter
public function setGoogleLink($googleLink) {
    $this->googleLink = $googleLink;
}

}