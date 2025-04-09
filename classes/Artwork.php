<?php

class Artwork
{
    private $artworkId;
    private $artistId;
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
    private $galleryId;
    private $artworkLink;
    private $googleLink;

    public function __construct(
        $artistId,
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
        $galleryId,
        $artworkLink = null,
        $googleLink = null,
        $artworkId = null
    ) {
        $this->artistId = $artistId;
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
        $this->galleryId = $galleryId;
        $this->artworkLink = $artworkLink;
        $this->googleLink = $googleLink;
        $this->artworkId = $artworkId;
    }

    public function getArtworkId()
    {
        return $this->artworkId;
    }

    public function setArtworkId($artworkId)
    {
        $this->artworkId = $artworkId;
    }


    public function getArtistId()
    {
        return $this->artistId;
    }

    public function setArtistId($artistId)
    {
        $this->artistId = $artistId;
    }


    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    public function setImageFileName($imageFileName)
    {
        $this->imageFileName = $imageFileName;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }


    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getExcerpt()
    {
        return $this->excerpt;
    }

    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
    }


    public function getArtworkType()
    {
        return $this->artworkType;
    }

    public function setArtworkType($artworkType)
    {
        $this->artworkType = $artworkType;
    }


    public function getYearOfWork()
    {
        return $this->yearOfWork;
    }

    public function setYearOfWork($yearOfWork)
    {
        $this->yearOfWork = $yearOfWork;
    }


    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }


    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }


    public function getMedium()
    {
        return $this->medium;
    }

    public function setMedium($medium)
    {
        $this->medium = $medium;
    }


    public function getOriginalHome()
    {
        return $this->originalHome;
    }

    public function setOriginalHome($originalHome)
    {
        $this->originalHome = $originalHome;
    }


    public function getGalleryId()
    {
        return $this->galleryId;
    }

    public function setGalleryId($galleryId)
    {
        $this->galleryId = $galleryId;
    }


    public function getArtworkLink()
    {
        return $this->artworkLink;
    }

    public function setArtworkLink($artworkLink)
    {
        $this->artworkLink = $artworkLink;
    }


    public function getGoogleLink()
    {
        return $this->googleLink;
    }

    public function setGoogleLink($googleLink)
    {
        $this->googleLink = $googleLink;
    }
}
