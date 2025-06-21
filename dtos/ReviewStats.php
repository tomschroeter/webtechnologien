<?php

/**
 * Combines the average rating and the total amount of reviews for a specific artwork.
 */
class ReviewStats
{
    private float $averageRating;
    private int $totalReviews;

    public function __construct(float $averageRating, int $totalReviews)
    {
        $this->averageRating = $averageRating;
        $this->totalReviews = $totalReviews;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function getTotalReviews(): int
    {
        return $this->totalReviews;
    }

    /**
     * Checks if the artwork has associated reviews
     */
    public function hasReviews(): bool
    {
        return $this->totalReviews > 0;
    }

    /**
     * Returns the average rating with 1 decimal digit
     */
    public function getFormattedAverageRating(): string
    {
        // Remove .0 for whole numbers
        $formatted = number_format($this->averageRating, 1);
        return rtrim(rtrim($formatted, '0'), '.');
    }

    /**
     * Returns the formatted average rating
     */
    public function getFormattedAverageRatingOutOf5(): string
    {
        return $this->getFormattedAverageRating() . "/5";
    }

    public function getNumberOfReviewsAsText(): string
    {
        if ($this->totalReviews === 0) {
            return "No reviews yet";
        }

        return $this->totalReviews === 1 ? "1 review" : "{$this->totalReviews} reviews";
    }
}

/**
 * @extends \ArrayObject ReviewStats>
 */
class ReviewStatsArray extends \ArrayObject
{
    public function offsetSet(mixed $key, mixed $val): void
    {
        if (!$val instanceof ReviewStats) {
            throw new \InvalidArgumentException('Value must be an ReviewStats instance');
        }

        parent::offsetSet($key, $val);
    }
}
