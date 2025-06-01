<?php

require_once dirname(__DIR__)."/Database.php";

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

    // constructor is only used when fetching from database
    private function __construct(
        $artworkId,
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
        $artworkLink,
        $googleLink
    ) {
        $this->setArtworkId($artworkId);
        $this->setArtistId($artistId);
        $this->setImageFileName($imageFileName);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setExcerpt($excerpt);
        $this->setArtworkType($artworkType);
        $this->setYearOfWork($yearOfWork);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setMedium($medium);
        $this->setOriginalHome($originalHome);
        $this->setGalleryId($galleryId);
        $this->setArtworkLink($artworkLink);
        $this->setGoogleLink($googleLink);
    }

    public static function createArtworkFromRecord(array $record): Artwork
    {
        return new self(
            $record['ArtWorkID'],
            $record['ArtistID'],
            $record['ImageFileName'],
            $record['Title'],
            $record['Description'],
            $record['Excerpt'],
            $record['ArtWorkType'],
            $record['YearOfWork'],
            $record['Width'],
            $record['Height'],
            $record['Medium'],
            $record['OriginalHome'],
            $record['GalleryID'],
            $record['ArtWorkLink'],
            $record['GoogleLink']
        );
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
