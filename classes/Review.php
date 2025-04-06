<?php

class Review {
    private $reviewID;
    private $customerID;
    private $artworkID;
    private $rating;
    private $comment;
    private $reviewDate;

    public function __construct(
        $customerID,
        $artworkID,
        $rating,
        $comment,
        $reviewDate = null,
        $reviewID = null
    ) {
        $this->customerID = $customerID;
        $this->artworkID = $artworkID;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->reviewDate = $reviewDate;
        $this->reviewID = $reviewID;
    }

    // Getter
public function getReviewID() {
    return $this->reviewID;
}
// Setter
public function setReviewID($reviewID) {
    $this->reviewID = $reviewID;
}

// Getter
public function getCustomerID() {
    return $this->customerID;
}
// Setter
public function setCustomerID($customerID) {
    $this->customerID = $customerID;
}

// Getter
public function getArtworkID() {
    return $this->artworkID;
}
// Setter
public function setArtworkID($artworkID) {
    $this->artworkID = $artworkID;
}

// Getter
public function getRating() {
    return $this->rating;
}
// Setter
public function setRating($rating) {
    $this->rating = $rating;
}

// Getter
public function getComment() {
    return $this->comment;
}
// Setter
public function setComment($comment) {
    $this->comment = $comment;
}

// Getter
public function getReviewDate() {
    return $this->reviewDate;
}
// Setter
public function setReviewDate($reviewDate) {
    $this->reviewDate = $reviewDate;
}

}
