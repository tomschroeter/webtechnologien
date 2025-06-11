<?php
require_once "repositories/ReviewRepository.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only allow admin users
if (!($_SESSION['isAdmin'] ?? false)) {
    header("Location: error.php?error=unauthorized");
    exit;
}

$reviewId = $_POST['reviewId'] ?? null;
$artworkId = $_POST['artworkId'] ?? null;

// Validate form data
if (!$reviewId || !$artworkId) {
    header("Location: error.php?error=missingReviewData");
    exit;
}

$db = new Database();
$repo = new ReviewRepository($db);

// Delete the review
$repo->deleteReview($reviewId);

// Redirect back to previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
