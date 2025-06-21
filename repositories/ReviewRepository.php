<?php

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Review.php";
require_once dirname(__DIR__) . "/dtos/ReviewWithCustomerInfo.php";
require_once dirname(__DIR__) . "/dtos/ReviewWithCustomerInfoAndArtwork.php";
require_once dirname(__DIR__) . "/dtos/ReviewStats.php";

class ReviewRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function addReview(Review $review): int
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "
            INSERT INTO reviews (ArtWorkId, CustomerId, ReviewDate, Rating, Comment)
            VALUES (:artworkId, :customerId, :reviewDate, :rating, :comment)
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $review->getArtworkId(), PDO::PARAM_INT);
        $stmt->bindValue("customerId", $review->getCustomerId(), PDO::PARAM_INT);
        $stmt->bindValue("reviewDate", $review->getReviewDate());
        $stmt->bindValue("rating", $review->getRating(), PDO::PARAM_INT);
        $stmt->bindValue("comment", $review->getComment());

        $stmt->execute();

        // Get the review ID before disconnecting
        $reviewId = $this->db->lastInsertId();

        $this->db->disconnect();

        return (int) $reviewId;
    }

    public function hasUserReviewed(int $customerId, int $artworkId): bool
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "
            SELECT COUNT(*) as review_count
            FROM reviews
            WHERE CustomerId = :customerId AND ArtWorkId = :artworkId
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("customerId", $customerId, PDO::PARAM_INT);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        $this->db->disconnect();

        return $result && $result["review_count"] > 0;
    }

    public function getAllReviewsForArtwork(int $artworkId): array
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "
            SELECT * FROM reviews
            WHERE ArtWorkId = :artworkId
            ORDER BY ReviewDate DESC
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $reviews = [];

        foreach ($stmt as $row) {
            $reviews[] = Review::createReviewFromRecord($row);
        }

        $this->db->disconnect();
        return $reviews;
    }

    public function getAllReviewsWithCustomerInfo(int $artworkId): array
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "
            SELECT r.*, c.FirstName, c.LastName, c.City, c.Country
            FROM reviews r
            JOIN customers c ON r.CustomerId = c.CustomerID
            WHERE r.ArtWorkId = :artworkId
            ORDER BY r.ReviewDate DESC
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $reviews = [];

        foreach ($stmt as $row) {
            $review = Review::createReviewFromRecord($row);
            $reviewWithCustomerInfo = new ReviewWithCustomerInfo(
                $review,
                $row['FirstName'],
                $row['LastName'],
                $row['City'],
                $row['Country']
            );

            $reviews[] = $reviewWithCustomerInfo;
        }

        $this->db->disconnect();
        return $reviews;
    }

    public function deleteReview(int $reviewId): void
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "DELETE FROM reviews WHERE ReviewId = :id";
        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $reviewId, PDO::PARAM_INT);
        $stmt->execute();

        $this->db->disconnect();
    }

    public function getReviewStats(int $artworkId): ReviewStats
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "
            SELECT AVG(Rating) as avgRating, COUNT(*) as totalReviews
            FROM reviews
            WHERE ArtWorkId = :artworkId
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        $this->db->disconnect();

        $averageRating = $result['avgRating'] ? round($result['avgRating'], 1) : 0.0;
        $totalReviews = (int) $result['totalReviews'];

        return new ReviewStats($averageRating, $totalReviews);
    }

    public function getRecentReviews()
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "SELECT r.*, a.*, c.FirstName, c.LastName, c.City, c.Country
        FROM reviews r
        JOIN artworks a ON a.ArtWorkId = r.ArtWorkId
        JOIN customers c ON c.CustomerID = r.CustomerId
        ORDER BY r.ReviewDate DESC
        LIMIT 3
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $reviews = [];

        foreach ($stmt as $row) {
            $review = Review::createReviewFromRecord($row);
            $artwork = Artwork::createArtworkFromRecord($row);
            $reviewerFirstName = $row['FirstName'];
            $reviewerLastName = $row['LastName'];
            $reviewerCity = $row['City'];
            $reviewerCountry = $row['Country'];
            $reviews[] = new ReviewWithCustomerInfoAndArtwork($review, $artwork, $reviewerFirstName, $reviewerLastName, $reviewerCity, $reviewerCountry);
        }

        $this->db->disconnect();

        return $reviews;
    }
}