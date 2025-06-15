<?php

class ReviewWithStats
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

    public function hasReviews(): bool
    {
        return $this->totalReviews > 0;
    }

    public function getFormattedAverageRating(): string
    {
        // Remove .0 for whole numbers
        $formatted = number_format($this->averageRating, 1);
        return rtrim(rtrim($formatted, '0'), '.');
    }

    public function getFormattedAverageRatingOutOf5(): string
    {
        return $this->getFormattedAverageRating() . "/5";
    }

    public function getReviewText(): string
    {
        if ($this->totalReviews === 0) {
            return "No reviews yet";
        }

        return $this->totalReviews === 1 ? "1 review" : "{$this->totalReviews} reviews";
    }
}

/**
 * https://stackoverflow.com/questions/20763744/type-hinting-specify-an-array-of-objects
 *
 * For type completion:
 * @extends \ArrayObject<ReviewWithStats>
 */
class ReviewWithStatsArray extends \ArrayObject
{
    public function offsetSet(mixed $key, mixed $val): void
    {
        if (!$val instanceof ReviewWithStats) {
            throw new \InvalidArgumentException('Value must be an ReviewWithStats instance');
        }

        parent::offsetSet($key, $val);
    }
}
