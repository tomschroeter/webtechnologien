<?php

/**
 * Represents a Review object along with the associated customer data and artwork.
 *
 * This class encapsulates a Review instance together with the associated customer data and antwork,
 * providing convenient accessors to retrieve an antwork's review and the author's data.
 *
 */
class ReviewWithCustomerInfoAndArtwork
{
    private Review $review;
    private Artwork $artwork;
    private string $customerFirstName;
    private string $customerLastName;
    private string $customerCity;
    private string $customerCountry;

    public function __construct(
        Review $review,
        Artwork $artwork,
        string $customerFirstName,
        string $customerLastName,
        string $customerCity,
        string $customerCountry
    ) {
        $this->review = $review;
        $this->artwork = $artwork;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->customerCity = $customerCity;
        $this->customerCountry = $customerCountry;
    }

    public function getReview(): Review
    {
        return $this->review;
    }

    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    public function getCustomerFirstName(): string
    {
        return $this->customerFirstName;
    }

    public function getCustomerLastName(): string
    {
        return $this->customerLastName;
    }

    public function getCustomerCity(): string
    {
        return $this->customerCity;
    }

    public function getCustomerCountry(): string
    {
        return $this->customerCountry;
    }

    public function getCustomerLocation(): string
    {
        return $this->customerCity . ' (' . $this->customerCountry . ')';
    }

    /**
     * Returns customer's full name 
     */
    public function getCustomerFullName(): string
    {
        return $this->customerFirstName . ' ' . $this->customerLastName;
    }
}

/**
 * @extends \ArrayObject<ReviewWithCustomerInfoAndArtwork>
 */
class ReviewWithCustomerInfoAndArtworkArray extends \ArrayObject
{
    public function offsetSet(mixed $key, mixed $val): void
    {
        if (!$val instanceof ReviewWithCustomerInfoAndArtwork) {
            throw new \InvalidArgumentException('Value must be an ReviewWithCustomerInfoAndArtwork instance');
        }

        parent::offsetSet($key, $val);
    }
}