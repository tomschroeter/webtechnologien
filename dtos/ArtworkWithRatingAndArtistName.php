<?php


class ArtworkWithRatingAndArtistName
{
    private $artwork;
    private $artistName;
    private $rating;

    public function __construct(Artwork $artwork, string $artistFirstName, string $artistLastName, float $rating)
    {
        $this->artwork = $artwork;
        $this->artistName = $artistFirstName . ' ' . $artistLastName;
        $this->rating = $rating;
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
