<?php

/**
 * Represents an Artwork object along with the associated artist's first and last names
 * and the artwork's average rating and number of reviews.
 *
 * This class encapsulates an Artwork instance together with the artist's name details and it's reviews,
 * providing convenient accessors to retrieve both the artwork and additional relevant information.
 *
 */
class ArtworkWithRatingAndArtistName
{
    private Artwork $artwork;
    private ?string $artistFirstName;
    private string $artistLastName;
    private $rating;
    private $reviewCount;

    public function __construct(Artwork $artwork, ?string $artistFirstName, string $artistLastName, float $rating, int $reviewCount)
    {
        $this->artwork = $artwork;
        $this->artistFirstName = $artistFirstName;
        $this->artistLastName = $artistLastName;
        $this->rating = $rating;
        $this->reviewCount = $reviewCount;
    }

    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    public function getArtistFirstName(): ?string
    {
        return $this->artistFirstName;
    }

    public function getArtistLastName(): string
    {
        return $this->artistLastName;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function getReviewCount(): int
    {
        return $this->reviewCount;
    }

    /**
     * Returns the full name of the artist
     */
    public function getArtistFullName(): string
    {
        return $this->getArtistFirstName() . ' ' . $this->getArtistLastName();
    }
}


/**
 * @extends \ArrayObject<string, float, ArtworkWithRatingAndArtistName>
 */
class ArtworkWithRatingAndArtistNameArray extends \ArrayObject
{
    public function offsetSet(mixed $key, mixed $val): void
    {
        if (!$val instanceof ArtworkWithRatingAndArtistName) {
            throw new \InvalidArgumentException('Value must be an ArtworkWithRatingAndArtistName instance');
        }

        parent::offsetSet($key, $val);
    }
}
