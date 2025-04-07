<?php

class Review
{
    private $reviewId;
    private $customerId;
    private $artworkId;
    private $rating;
    private $comment;
    private $reviewDate;

    public function __construct(
        $customerId,
        $artworkId,
        $rating,
        $comment,
        $reviewDate = null,
        $reviewId = null
    ) {
        $this->customerId = $customerId;
        $this->artworkId = $artworkId;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->reviewDate = $reviewDate;
        $this->reviewId = $reviewId;
    }


    public function getReviewID()
    {
        return $this->reviewId;
    }

    public function setReviewID($reviewId)
    {
        $this->reviewId = $reviewId;
    }


    public function getCustomerID()
    {
        return $this->customerId;
    }

    public function setCustomerID($customerId)
    {
        $this->customerId = $customerId;
    }


    public function getArtworkID()
    {
        return $this->artworkId;
    }

    public function setArtworkID($artworkId)
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
