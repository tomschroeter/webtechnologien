<?php

class Gallery
{
    private $galleryId;
    private $galleryName;
    private $galleryNativeName;
    private $galleryCity;
    private $galleryCountry;
    private $latitude;
    private $longitude;
    private $galleryWebsite;


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
        $this->setGalleryName($galleryName);
        $this->setGalleryNativeName($galleryNativeName);
        $this->setGalleryCity($galleryCity);
        $this->setGalleryCountry($galleryCountry);
        $this->setGalleryWebsite($galleryWebsite);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setGalleryId($galleryId);
    }



    public function getGalleryId()
    {
        return $this->galleryId;
    }


    public function setGalleryId($galleryId)
    {
        $this->galleryId = $galleryId;
    }


    public function getGalleryName()
    {
        return $this->galleryName;
    }


    public function setGalleryName($galleryName)
    {
        $this->galleryName = $galleryName;
    }


    public function getGalleryNativeName()
    {
        return $this->galleryNativeName;
    }


    public function setGalleryNativeName($galleryNativeName)
    {
        $this->galleryNativeName = $galleryNativeName;
    }


    public function getGalleryCity()
    {
        return $this->galleryCity;
    }


    public function setGalleryCity($galleryCity)
    {
        $this->galleryCity = $galleryCity;
    }

    public function getGalleryWebSite()
    {
        return $this->galleryWebsite;
    }

    public function setGalleryWebSite($galleryWebsite)
    {
        $this->galleryWebsite = $galleryWebsite;
    }

    public function getGalleryCountry()
    {
        return $this->galleryCountry;
    }


    public function setGalleryCountry($galleryCountry)
    {
        $this->galleryCountry = $galleryCountry;
    }


    public function getLatitude()
    {
        return $this->latitude;
    }


    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }


    public function getLongitude()
    {
        return $this->longitude;
    }


    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
}
