<?php

/**
 * Represents an Artist object along with the count of reviews related to that artist.
 *
 * This class encapsulates an Artist instance and the number of reviews, providing
 * convenient accessors to retrieve the artist data and review count.
 *
 */
class ArtistWithStats
{
    private Artist $artist;
    private int $reviewCount;

    public function __construct(Artist $artist, int $reviewCount)
    {
        $this->artist = $artist;
        $this->reviewCount = $reviewCount;
    }

    public function getArtist(): Artist
    {
        return $this->artist;
    }

    public function getReviewCount(): int
    {
        return $this->reviewCount;
    }
}


/**
 * https://stackoverflow.com/questions/20763744/type-hinting-specify-an-array-of-objects
 *
 * For type completion:
 * @extends \ArrayObject<int, ArtistWithStats>
 */
class ArtistWithStatsArray extends \ArrayObject
{
    public function offsetSet(mixed $key, mixed $val): void
    {
        if (!$val instanceof ArtistWithStats) {
            throw new \InvalidArgumentException('Value must be an ArtistWithStats instance');
        }

        parent::offsetSet($key, $val);
    }
}