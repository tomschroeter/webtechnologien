<?php

class Review
{
    private $reviewId;
    private $customerId;
    private $artworkId;
    private $rating;
    private $comment;
    private $reviewDate;

    private function __construct(
        $reviewId = null,
        $artworkId,
        $customerId,
        $reviewDate = null,
        $rating,
        $comment
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
            $record['ReviewId'],
            $record['ArtWorkId'],
            $record['CustomerId'],
            $record['ReviewDate'],
            $record['Rating'],
            $record['Comment']
        );
    }

    public function getReviewId()
    {
        return $this->reviewId;
    }

    public function setReviewId($reviewId)
    {
        $this->reviewId = $reviewId;
    }


    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }


    public function getArtworkId()
    {
        return $this->artworkId;
    }

    public function setArtworkId($artworkId)
    {
        $this->artworkId = $artworkId;
    }


    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }


    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }


    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    public function setReviewDate($reviewDate)
    {
        $this->reviewDate = $reviewDate;
    }
}
