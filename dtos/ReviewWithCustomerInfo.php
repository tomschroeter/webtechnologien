<?php

/**
 * Represents a Review object along with the associated customer data.
 *
 * This class encapsulates a Review instance together with the associated customer data,
 * providing convenient accessors to retrieve both the review and the author's data.
 *
 */
class ReviewWithCustomerInfo
{
    private Review $review;
    private string $customerFirstName;
    private string $customerLastName;
    private string $customerCity;
    private string $customerCountry;

    public function __construct(
        Review $review,
        string $customerFirstName,
        string $customerLastName,
        string $customerCity,
        string $customerCountry
    ) {
        $this->review = $review;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->customerCity = $customerCity;
        $this->customerCountry = $customerCountry;
    }

    public function getReview(): Review
    {
        return $this->review;
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
     * Returns the customer's full name 
     */
    public function getCustomerFullName(): string
    {
        return $this->customerFirstName . ' ' . $this->customerLastName;
    }
}

/**
 * @extends \ArrayObject<ReviewWithCustomerInfo>
 */
class ReviewWithCustomerInfoArray extends \ArrayObject
{
    public function offsetSet($key, $val): void
    {
        if (!$val instanceof ReviewWithCustomerInfo) {
            throw new \InvalidArgumentException('Value must be a ReviewWithCustomerInfo instance');
        }

        parent::offsetSet($key, $val);
    }
}
