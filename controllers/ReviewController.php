<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/classes/Review.php";
require_once dirname(__DIR__) . "/Database.php";

class ReviewController extends BaseController
{
    private $db;
    private $reviewRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->reviewRepository = new ReviewRepository($this->db);
    }
    
    public function addReview()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['customerId'])) {
            throw new HttpException(401, "You must be logged in to add a review.");
        }
        
        $artworkId = $_POST['artworkId'] ?? null;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
        $comment = trim($_POST['comment'] ?? '');
        $customerId = $_SESSION['customerId'];
        
        // Basic input validation
        if (!$artworkId || !$rating || $rating < 1 || $rating > 5 || empty($comment)) {
            throw new HttpException(422, "Invalid review data. Please provide a valid rating (1-5) and comment.");
        }
        
        try {
            // Prevent duplicate review
            if ($this->reviewRepository->hasUserReviewed($customerId, $artworkId)) {
                throw new HttpException(409, "You have already reviewed this artwork.");
            }
            
            // Create Review object
            $review = new Review(
                null, // ReviewId (auto-generated)
                $artworkId,
                $customerId,
                date('Y-m-d H:i:s'),
                $rating,
                $comment
            );
            
            // Save to database and get the review ID
            $reviewId = $this->reviewRepository->addReview($review);

            $redirectUrl = "/artworks/$artworkId";

            $this->redirectWithNotification(
                $redirectUrl,
                'Successfully added your review!',
                'success'
            );
            
        } catch (Exception $e) {
            throw new HttpException(500, "An error occurred while saving your review. Please try again.");
        }
    }
    
    public function deleteReview($reviewId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Only allow admin users
        if (!($_SESSION['isAdmin'] ?? false)) {
            throw new HttpException(403, "You are not authorized to delete reviews.");
        }
        
        // Validate review ID
        if (!$reviewId || !is_numeric($reviewId)) {
            throw new HttpException(400, "Invalid review ID provided.");
        }
        
        try {
            // Delete the review
            $this->reviewRepository->deleteReview($reviewId);
            
            $this->redirectWithNotification(
                $_SERVER['HTTP_REFERER'] ?? '/',
                'Successfully removed review!',
                'success',
            );
            
        } catch (HttpException $e) {
            throw $e; // Re-throw HttpExceptions
        } catch (Exception $e) {
            throw new HttpException(500, "An error occurred while deleting the review. Please try again.");
        }
    }
    
    private function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
