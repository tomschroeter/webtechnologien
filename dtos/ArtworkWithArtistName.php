<?php
class ArtworkWithArtistName
{
    private $artwork;
    private $artistFirstName;
    private $artistLastName;

    public function __construct(Artwork $artwork,  string|null $artistFirstName, string|null $artistLastName)
    {
        $this->artwork = $artwork;
        $this->artistFirstName = $artistFirstName;
        $this->artistLastName = $artistLastName;
    }

    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    public function getArtistFirstName(): string|null
    {
        return $this->artistFirstName;
    }

    public function getArtistLastName(): string|null
    {
        return $this->artistLastName;
    }
}


/**
 *
 * @extends \ArrayObject<ArtworkWithArtistName, string, string>
 */
class ArtworkWithArtistNameArray extends \ArrayObject
{
    public function offsetSet($key, $val)
	{
        if ($val instanceof ArtworkWithArtistName)
		{
            return parent::offsetSet($key, $val);
        }

		// Backslash means using the Standard PHP Library ArrayObject class (same for \ArrayObject)
        throw new \InvalidArgumentException('Value must be a ArtworkWithArtistName instance');
    }
}