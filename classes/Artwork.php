<?php

class Artwork
{
    private int $artworkId;
    private int $artistId;
    private string $imageFileName;
    private string $title;
    private ?string $description;
    private ?string $excerpt;
    private ?int $artworkType;
    private ?int $yearOfWork;
    private ?int $width;
    private ?int $height;
    private ?string $medium;
    private ?string $originalHome;
    private ?int $galleryId;
    private ?string $artworkLink;
    private ?string $googleLink;

    private function __construct(
        int $artworkId,
        int $artistId,
        string $imageFileName,
        string $title,
        ?string $description,
        ?string $excerpt,
        ?int $artworkType,
        ?int $yearOfWork,
        ?int $width,
        ?int $height,
        ?string $medium,
        ?string $originalHome,
        ?int $galleryId,
        ?string $artworkLink,
        ?string $googleLink
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
            (int) $record['ArtWorkID'],
            (int) $record['ArtistID'],
            (string) $record['ImageFileName'],
            (string) $record['Title'],
            $record['Description'] ?? null,
            $record['Excerpt'] ?? null,
            isset($record['ArtWorkType']) ?? null,
            isset($record['YearOfWork']) ?? null,
            isset($record['Width']) ?? null,
            isset($record['Height']) ?? null,
            $record['Medium'] ?? null,
            $record['OriginalHome'] ?? null,
            isset($record['GalleryID']) ?? null,
            $record['ArtWorkLink'] ?? null,
            $record['GoogleLink'] ?? null
        );
    }

    public function getArtworkId(): int
    {
        return $this->artworkId;
    }

    public function setArtworkId(int $artworkId): void
    {
        $this->artworkId = $artworkId;
    }

    public function getArtistId(): int
    {
        return $this->artistId;
    }

    public function setArtistId(int $artistId): void
    {
        $this->artistId = $artistId;
    }

    public function getImageFileName(): string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(string $imageFileName): void
    {
        $this->imageFileName = $imageFileName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    public function getArtworkType(): ?int
    {
        return $this->artworkType;
    }

    public function setArtworkType(?int $artworkType): void
    {
        $this->artworkType = $artworkType;
    }

    public function getYearOfWork(): ?int
    {
        return $this->yearOfWork;
    }

    public function setYearOfWork(?int $yearOfWork): void
    {
        $this->yearOfWork = $yearOfWork;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): void
    {
        $this->height = $height;
    }

    public function getMedium(): ?string
    {
        return $this->medium;
    }

    public function setMedium(?string $medium): void
    {
        $this->medium = $medium;
    }

    public function getOriginalHome(): ?string
    {
        return $this->originalHome;
    }

    public function setOriginalHome(?string $originalHome): void
    {
        $this->originalHome = $originalHome;
    }

    public function getGalleryId(): ?int
    {
        return $this->galleryId;
    }

    public function setGalleryId(?int $galleryId): void
    {
        $this->galleryId = $galleryId;
    }

    public function getArtworkLink(): ?string
    {
        return $this->artworkLink;
    }

    public function setArtworkLink(?string $artworkLink): void
    {
        $this->artworkLink = $artworkLink;
    }

    public function getGoogleLink(): ?string
    {
        return $this->googleLink;
    }

    public function setGoogleLink(?string $googleLink): void
    {
        $this->googleLink = $googleLink;
    }
}