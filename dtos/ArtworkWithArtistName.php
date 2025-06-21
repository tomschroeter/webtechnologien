<?php

/**
 * Represents an Artwork object along with the associated artist's first and last names.
 *
 * This class encapsulates an Artwork instance together with the artist's name details,
 * providing convenient accessors to retrieve both the artwork and the artist's name components.
 *
 */
class ArtworkWithArtistName
{
    private Artwork $artwork;
    private ?string $artistFirstName;
    private string $artistLastName;

    public function __construct(Artwork $artwork, ?string $artistFirstName, string $artistLastName)
    {
        $this->artwork = $artwork;
        $this->artistFirstName = $artistFirstName;
        $this->artistLastName = $artistLastName;
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

    public function getArtistFullName(): string|null
    {
        return $this->getArtistFirstName() . ' ' . $this->getArtistLastName();
    }
}


/**
 * @extends \ArrayObject<ArtworkWithArtistName, string, string>
 */
class ArtworkWithArtistNameArray extends \ArrayObject
{
    public function offsetSet($key, $val): void
    {
        if (!$val instanceof ArtworkWithArtistName) {
            throw new \InvalidArgumentException('Value must be a ArtworkWithArtistName instance');
        }

        parent::offsetSet($key, $val);
    }
}
