<?php
session_start();

// Only allow admins
if (!($_SESSION['isAdmin'] ?? false)) {
    die("Unauthorized access");
}

require_once "bootstrap.php";
require_once "repositories/ReviewRepository.php";

$reviewId = $_POST['reviewId'] ?? null;
$artworkId = $_POST['artworkId'] ?? null;

if (!$reviewId || !$artworkId) {
    die("Missing data");
}

$db = new Database();
$repo = new ReviewRepository($db);
$repo->deleteReview($reviewId);

// Redirect back to artist page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
