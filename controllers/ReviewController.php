<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/classes/Review.php";
require_once dirname(__DIR__) . "/Database.php";

/**
 * Handles display of reviews.
 */
class ReviewController extends BaseController
{
    private Database $db;
    private ReviewRepository $reviewRepository;

    /**
     * Initializes the database connection and review repository.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->reviewRepository = new ReviewRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }

    /**
     * Add a new review submitted by a logged-in user.
     *
     * @throws HttpException if user is not logged in, if input is invalid,
     * if duplicate review exists, or if saving fails.
     */
    public function addReview(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['customerId'])) {
            throw new HttpException(401, "You must be logged in to add a review.");
        }

        $artworkId = $_POST['artworkId'] ?? null;
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : null;
        $comment = trim($_POST['comment'] ?? '');
        $customerId = $_SESSION['customerId'];

        // Validate the ID types
        if (!$artworkId || !is_numeric($artworkId)) {
            throw new HttpException(400, "The artwork ID parameter is invalid or missing.");
        }

        // Basic input validation
        if (!$artworkId || !$rating || $rating < 1 || $rating > 5 || empty($comment)) {
            throw new HttpException(400, "Invalid review data. Please provide a valid rating (1-5) and comment.");
        }

        try {
            $this->artworkRepository->getArtworkById($artworkId);
        }
        catch (ArtworkNotFoundException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        // Prevent duplicate review from same user for the same artwork
        if ($this->reviewRepository->hasUserReviewed($customerId, $artworkId)) {
            throw new HttpException(409, "You have already reviewed this artwork.");
        }

        // Create Review object with current timestamp
        $review = new Review(
            null, // ReviewId (auto-generated)
            $artworkId,
            $customerId,
            date('Y-m-d H:i:s'),
            $rating,
            $comment
        );

        // Save review to database and get its ID
        $reviewId = $this->reviewRepository->addReview($review);

        $redirectUrl = "/artworks/$artworkId";

        // Redirect with success notification
        $this->redirectWithNotification(
            $redirectUrl,
            'Successfully added your review!',
            'success'
        );
    }

    /**
     * Deletes a review by its ID. Only accessible to admin users.
     * This method will not throw an exception, when there is no review with the given ID.
     *
     * @param int $reviewId The ID of the review to delete.
     *
     * @throws HttpException if user is unauthorized, ID is invalid, or if deletion fails.
     */
    public function deleteReview($reviewId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Only allow admin users to delete reviews
        if (!($_SESSION['isAdmin'] ?? false)) {
            throw new HttpException(403, "You are not authorized to delete reviews.");
        }

        // Validate review ID parameter
        if (!$reviewId || !is_numeric($reviewId)) {
            throw new HttpException(400, "Invalid review ID provided.");
        }

        // Delete review from database
        $this->reviewRepository->deleteReview($reviewId);

        // Redirect back with success notification
        $this->redirectWithNotification(
            $_SERVER['HTTP_REFERER'] ?? '/',
            'Successfully removed review!',
            'success',
        );
    }
}
