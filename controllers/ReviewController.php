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
            $this->redirect('/error.php?error=notLoggedIn');
        }
        
        $artworkId = $_POST['artworkId'] ?? null;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
        $comment = trim($_POST['comment'] ?? '');
        $customerId = $_SESSION['customerId'];
        
        // Basic input validation
        if (!$artworkId || !$rating || $rating < 1 || $rating > 5 || empty($comment)) {
            $this->redirect('/error.php?error=invalidReviewData');
        }
        
        try {
            // Prevent duplicate review
            if ($this->reviewRepository->hasUserReviewed($customerId, $artworkId)) {
                $this->redirect('/error.php?error=duplicateReview');
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
            $this->redirect('/error.php?error=reviewError');
        }
    }
    
    public function deleteReview($reviewId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Only allow admin users
        if (!($_SESSION['isAdmin'] ?? false)) {
            $this->redirect('/error.php?error=unauthorized');
            return;
        }
        
        // Validate review ID
        if (!$reviewId || !is_numeric($reviewId)) {
            $this->redirect('/error.php?error=missingReviewData');
            return;
        }
        
        try {
            // Delete the review
            $this->reviewRepository->deleteReview($reviewId);
            
            $this->redirectWithNotification(
                $_SERVER['HTTP_REFERER'] ?? '/',
                'Successfully removed review!',
                'success',
            );
            
        } catch (Exception $e) {
            $this->redirect('/error.php?error=reviewError');
        }
    }
    
    private function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
