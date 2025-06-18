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
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'You must be logged in to add a review']);
                return;
            } else {
                $this->redirect('/error.php?error=notLoggedIn');
                return;
            }
        }
        
        $artworkId = $_POST['artworkId'] ?? null;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
        $comment = trim($_POST['comment'] ?? '');
        $customerId = $_SESSION['customerId'];
        
        // Basic input validation
        if (!$artworkId || !$rating || $rating < 1 || $rating > 5 || empty($comment)) {
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid review data provided']);
                return;
            } else {
                $this->redirect('/error.php?error=invalidReviewData');
                return;
            }
        }
        
        try {
            // Prevent duplicate review
            if ($this->reviewRepository->hasUserReviewed($customerId, $artworkId)) {
                if ($this->isAjaxRequest()) {
                    http_response_code(409);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'You have already reviewed this artwork']);
                    return;
                } else {
                    $this->redirect('/error.php?error=duplicateReview');
                    return;
                }
            }
            
            // Create Review object
            $review = new Review(
                null,               // ReviewId (auto-generated)
                $artworkId,
                $customerId,
                date('Y-m-d H:i:s'),
                $rating,
                $comment
            );
            
            // Save to database and get the review ID
            $reviewId = $this->reviewRepository->addReview($review);
            
            if ($this->isAjaxRequest()) {
                // Get user info for the response
                require_once dirname(__DIR__) . "/repositories/CustomerLogonRepository.php";
                $customerRepo = new CustomerLogonRepository($this->db);
                $customer = $customerRepo->getCustomerById($customerId);
                
                $responseData = [
                    'success' => true, 
                    'message' => 'Review added successfully',
                    'review' => [
                        'reviewId' => $reviewId,
                        'rating' => $rating,
                        'comment' => $comment,
                        'reviewDate' => date('Y-m-d H:i:s'),
                        'customerName' => $customer ? ($customer->getFullName()) : 'Anonymous',
                        'customerLocation' => $customer ? $customer->getCity() . ', ' . $customer->getCountry() : 'Unknown',
                        'artworkId' => $artworkId
                    ],
                    'isAdmin' => $_SESSION['isAdmin'] ?? false
                ];
                
                header('Content-Type: application/json');
                echo json_encode($responseData);
            } else {
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/artworks/' . $artworkId);
            }
            
        } catch (Exception $e) {
            if ($this->isAjaxRequest()) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to add review']);
            } else {
                $this->redirect('/error.php?error=reviewError');
            }
        }
    }
    
    public function deleteReview($reviewId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Only allow admin users
        if (!($_SESSION['isAdmin'] ?? false)) {
            if ($this->isAjaxRequest()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Only administrators can delete reviews']);
                return;
            } else {
                $this->redirect('/error.php?error=unauthorized');
                return;
            }
        }
        
        // Validate review ID
        if (!$reviewId || !is_numeric($reviewId)) {
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid review ID']);
                return;
            } else {
                $this->redirect('/error.php?error=missingReviewData');
                return;
            }
        }
        
        try {
            // Delete the review
            $this->reviewRepository->deleteReview($reviewId);
            
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
            } else {
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            
        } catch (Exception $e) {
            if ($this->isAjaxRequest()) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to delete review']);
            } else {
                $this->redirect('/error.php?error=reviewError');
            }
        }
    }
    
    private function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
