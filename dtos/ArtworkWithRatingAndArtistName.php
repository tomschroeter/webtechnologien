<?php


class ArtworkWithRatingAndArtistName
{
    private $artwork;
    private $artistName;
    private $rating;
    private $reviewCount;

    public function __construct(Artwork $artwork, string $artistFirstName, string $artistLastName, float $rating, int $reviewCount)
    {
        $this->artwork = $artwork;
        $this->artistName = $artistFirstName . ' ' . $artistLastName;
        $this->rating = $rating;
        $this->reviewCount = $reviewCount;
    }

    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    public function getArtistName(): string
    {
        return $this->artistName;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function getReviewCount(): int
    {
        return $this->reviewCount;
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
