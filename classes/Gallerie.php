<?php

class Gallery {
    private $galleryId;
    private $galleryName;
    private $galleryNativeName;
    private $galleryCity;
    private $galleryCountry;
    private $latitude;
    private $longitude;
    private $galleryWebsite;

    // Constructor using individual parameters
    public function __construct(
        $galleryName,
        $galleryNativeName,
        $galleryCity,
        $galleryCountry,
        $galleryWebsite,
        $latitude = null,
        $longitude = null,
        $galleryId = null

    ) {
        $this->galleryName = $galleryName;
        $this->galleryNativeName = $galleryNativeName;
        $this->galleryCity = $galleryCity;
        $this->galleryCountry = $galleryCountry;
        $this->galleryWebsite = $galleryWebsite;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->galleryId = $galleryId;

    }

    // Getter
public function getGalleryID() {
    return $this->galleryId;
}

// Setter
public function setGalleryID($galleryId) {
    $this->galleryId = $galleryId;
}

// Getter
public function getGalleryName() {
    return $this->galleryName;
}

// Setter
public function setGalleryName($galleryName) {
    $this->galleryName = $galleryName;
}

// Getter
public function getGalleryNativeName() {
    return $this->galleryNativeName;
}

// Setter
public function setGalleryNativeName($galleryNativeName) {
    $this->galleryNativeName = $galleryNativeName;
}

// Getter
public function getGalleryCity() {
    return $this->galleryCity;
}

// Setter
public function setGalleryCity($galleryCity) {
    $this->galleryCity = $galleryCity;
}

//Getter
public function getGalleryWebSite(){
    return $this->galleryWebsite;
}

//Setter
public function setGalleryWebSite($galleryWebsite){
    $this->galleryWebsite = $galleryWebsite;
}
// Getter
public function getGalleryCountry() {
    return $this->galleryCountry;
}

// Setter
public function setGalleryCountry($galleryCountry) {
    $this->galleryCountry = $galleryCountry;
}

// Getter
public function getLatitude() {
    return $this->latitude;
}

// Setter
public function setLatitude($latitude) {
    $this->latitude = $latitude;
}

// Getter
public function getLongitude() {
    return $this->longitude;
}

// Setter
public function setLongitude($longitude) {
    $this->longitude = $longitude;
}

}
