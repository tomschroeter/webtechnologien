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

    /**
     * Adds a new review to the database.
     *
     * @param Review $review The review to be added.
     * @return int The ID of the newly inserted review.
     */
    public function addReview(Review $review): int
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "INSERT INTO reviews (ArtWorkId, CustomerId, ReviewDate, Rating, Comment)
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

    /**
     * Deletes a review by its ID.
     *
     * @param int $reviewId The ID of the review to delete.
     * @return void
     */
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

    /**
     * Checks whether a customer has already reviewed a specific artwork.
     *
     * @param int $customerId The ID of the customer.
     * @param int $artworkId The ID of the artwork.
     * @return bool True if the customer has reviewed the artwork, otherwise false.
     */
    public function hasUserReviewed(int $customerId, int $artworkId): bool
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "SELECT COUNT(*) AS ReviewCount
        FROM reviews
        WHERE CustomerId = :customerId AND ArtWorkId = :artworkId
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("customerId", $customerId, PDO::PARAM_INT);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        $this->db->disconnect();

        $hasUserReviewed = $result && $result["ReviewCount"] > 0;

        return $hasUserReviewed;
    }

    /**
     * Retrieves all reviews for a specific artwork.
     *
     * @param int $artworkId The ID of the artwork.
     * @return Review[] An array of Review objects sorted by review date descending.
     */
    public function getAllReviewsForArtwork(int $artworkId): array
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "SELECT * FROM reviews WHERE ArtWorkId = :artworkId ORDER BY ReviewDate DESC";

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

    /**
     * Retrieves all reviews for a specific artwork, including customer information.
     *
     * @param int $artworkId The ID of the artwork.
     * @return ReviewWithCustomerInfoArray An array of ReviewWithCustomerInfo objects.
     */
    public function getAllReviewsWithCustomerInfo(int $artworkId): ReviewWithCustomerInfoArray
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "SELECT r.*, c.FirstName, c.LastName, c.City, c.Country
        FROM reviews r
        JOIN customers c ON r.CustomerId = c.CustomerID
        WHERE r.ArtWorkId = :artworkId
        ORDER BY r.ReviewDate DESC
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $reviews = new ReviewWithCustomerInfoArray();

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

    /**
     * Retrieves average rating and total review count for a given artwork.
     *
     * @param int $artworkId The ID of the artwork.
     * @return ReviewStats A ReviewStats object containing average rating and total reviews.
     */
    public function getReviewStats(int $artworkId): ReviewStats
    {
        if (!$this->db->isConnected())
            $this->db->connect();

        $sql = "SELECT AVG(Rating) as AvgRating, COUNT(*) as TotalReviews
        FROM reviews
        WHERE ArtWorkId = :artworkId
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        $this->db->disconnect();

        $averageRating = $result['AvgRating'] ? round($result['AvgRating'], 1) : 0.0;
        $totalReviews = (int) $result['TotalReviews'];

        $reviewStats = new ReviewStats($averageRating, $totalReviews);

        return $reviewStats;
    }

    /**
     * Retrieves the most recent reviews including customer and artwork information.
     *
     * @return ReviewWithCustomerInfoAndArtworkArray An array of the latest 3 ReviewWithCustomerInfoAndArtwork objects.
     */
    public function getRecentReviews(): ReviewWithCustomerInfoAndArtworkArray
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

        $reviews = new ReviewWithCustomerInfoAndArtworkArray();

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