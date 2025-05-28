<?php

// This can be also extended by more additional fields that
// could be returned by future queries.
// If so, a renaming would be appropriate.
class ArtistWithStats
{
    private $artist;
    private $reviewCount;

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
    public function offsetSet($key, $val)
    {
        if ($val instanceof ArtistWithStats) {
            return parent::offsetSet($key, $val);
        }

        // Backslash means using the Standard PHP Library ArrayObject class (same for \ArrayObject)
        throw new \InvalidArgumentException('Value must be a ArtistWithStats instance');
    }
}
