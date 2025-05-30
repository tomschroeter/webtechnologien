<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate logged-in user (TEMP)
$_SESSION['customerId'] = 1;

require_once "bootstrap.php";
require_once "classes/Review.php";
require_once "repositories/ReviewRepository.php";

// Validate session
if (!isset($_SESSION['customerId'])) {
    header("Location: error.php?error=notLoggedIn");
    exit;
}

$artworkId = $_POST['artworkId'] ?? null;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
$comment = trim($_POST['comment'] ?? '');
$customerId = $_SESSION['customerId'];

// Basic input validation
if (!$artworkId || !$rating || $rating < 1 || $rating > 5 || empty($comment)) {
    header("Location: error.php?error=invalidReviewData");
    exit;
}

$db = new Database();
$repo = new ReviewRepository($db);

// Prevent duplicate review
if ($repo->hasUserReviewed($customerId, $artworkId)) {
    header("Location: error.php?error=duplicateReview");
    exit;
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

// Save to database
$repo->addReview($review);

// Redirect back
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
