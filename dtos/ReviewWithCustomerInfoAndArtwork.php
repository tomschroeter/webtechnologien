<?php

require_once dirname(__DIR__) . "/classes/Review.php";

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

    public function getCustomerFullName(): string
    {
        return $this->customerFirstName . ' ' . $this->customerLastName;
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
}

/**
 * https://stackoverflow.com/questions/20763744/type-hinting-specify-an-array-of-objects
 *
 * For type completion:
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