<?php

/**
 * Represents a review entity from the database.
 * 
 * This class encapsulates artwork data and provides
 * getter and setter methods for accessing and modifying
 * review properties.
 * 
 * Instances are created using the constructor or the static method `createReviewFromRecord()`,
 * which accepts an associative array (e.g., a database record).
 */
class Review
{
    private ?int $reviewId;
    private int $customerId;
    private int $artworkId;
    private int $rating;
    private ?string $comment;
    private ?string $reviewDate;

    public function __construct(
        ?int $reviewId,
        int $artworkId,
        int $customerId,
        ?string $reviewDate,
        int $rating,
        ?string $comment
    ) {
        $this->setReviewId($reviewId);
        $this->setArtworkId($artworkId);
        $this->setCustomerId($customerId);
        $this->setReviewDate($reviewDate);
        $this->setRating($rating);
        $this->setComment($comment);
    }

    public static function createReviewFromRecord(array $record): Review
    {
        return new self(
            isset($record['ReviewId']) ? (int) $record['ReviewId'] : null,
            (int) $record['ArtWorkId'],
            (int) $record['CustomerId'],
            $record['ReviewDate'] ?? null,
            (int) $record['Rating'],
            $record['Comment'] ?? null
        );
    }

    public function getReviewId(): ?int
    {
        return $this->reviewId;
    }

    public function setReviewId(?int $reviewId): void
    {
        $this->reviewId = $reviewId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getArtworkId(): int
    {
        return $this->artworkId;
    }

    public function setArtworkId(int $artworkId): void
    {
        $this->artworkId = $artworkId;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getReviewDate(): ?string
    {
        return $this->reviewDate;
    }

    public function setReviewDate(?string $reviewDate): void
    {
        $this->reviewDate = $reviewDate;
    }
}
