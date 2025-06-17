<?php

class Gallery
{
    private int $galleryId;
    private string $galleryName;
    private ?string $galleryNativeName;
    private ?string $galleryCity;
    private ?string $galleryCountry;
    private ?float $latitude;
    private ?float $longitude;
    private ?string $galleryWebsite;

    private function __construct(
        int $galleryId,
        string $galleryName,
        ?string $galleryNativeName,
        ?string $galleryCity,
        ?string $galleryCountry,
        ?float $latitude,
        ?float $longitude,
        ?string $galleryWebsite
    ) {
        $this->setGalleryId($galleryId);
        $this->setGalleryName($galleryName);
        $this->setGalleryNativeName($galleryNativeName);
        $this->setGalleryCity($galleryCity);
        $this->setGalleryCountry($galleryCountry);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setGalleryWebSite($galleryWebsite);
    }

    public static function createGalleryFromRecord(array $record): Gallery
    {
        return new self(
            (int) $record['GalleryID'],
            (string) $record['GalleryName'],
            $record['GalleryNativeName'] ?? null,
            $record['GalleryCity'] ?? null,
            $record['GalleryCountry'] ?? null,
            isset($record['Latitude']) ? (float) $record['Latitude'] : null,
            isset($record['Longitude']) ? (float) $record['Longitude'] : null,
            $record['GalleryWebSite'] ?? null
        );
    }

    public function getGalleryId(): int
    {
        return $this->galleryId;
    }

    public function setGalleryId(int $galleryId): void
    {
        $this->galleryId = $galleryId;
    }

    public function getGalleryName(): string
    {
        return $this->galleryName;
    }

    public function setGalleryName(string $galleryName): void
    {
        $this->galleryName = $galleryName;
    }

    public function getGalleryNativeName(): ?string
    {
        return $this->galleryNativeName;
    }

    public function setGalleryNativeName(?string $galleryNativeName): void
    {
        $this->galleryNativeName = $galleryNativeName;
    }

    public function getGalleryCity(): ?string
    {
        return $this->galleryCity;
    }

    public function setGalleryCity(?string $galleryCity): void
    {
        $this->galleryCity = $galleryCity;
    }

    public function getGalleryCountry(): ?string
    {
        return $this->galleryCountry;
    }

    public function setGalleryCountry(?string $galleryCountry): void
    {
        $this->galleryCountry = $galleryCountry;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getGalleryWebSite(): ?string
    {
        return $this->galleryWebsite;
    }

    public function setGalleryWebSite(?string $galleryWebsite): void
    {
        $this->galleryWebsite = $galleryWebsite;
    }
}
