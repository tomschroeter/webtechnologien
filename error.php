<?php
/**
 * Legacy error handling - redirect to MVC error system
 * This file is kept for backward compatibility but now uses the new HttpException system.
 */

require_once __DIR__ . "/controllers/ErrorController.php";
require_once __DIR__ . "/exceptions/HttpException.php";

// Extract error parameters from URL
$error = $_GET['error'] ?? '';
$type = $_GET['type'] ?? '';

// Map legacy error codes to HTTP status codes and messages
$errorMappings = [
    'tooShort' => [400, "Please enter at least 3 characters in the search bar."],
    'invalidParam' => [400, "The passed parameter is invalid."],
    'missingParam' => [400, "A required parameter is missing."],
    'invalidID' => [404, "No " . ($type ?: "entry") . " with the given ID was found."],
    'notLoggedIn' => [401, "You must be logged in to perform this action."],
    'unauthorized' => [403, "You are not authorized to perform this action."],
    'missingReviewData' => [400, "Required review data is missing."],
    'invalidReviewData' => [422, "Invalid review data submitted."],
    'duplicateReview' => [409, "You have already reviewed this artwork."],
    'searchError' => [500, "An error occurred while searching. Please try again."],
    'genreNotFound' => [404, "No genre with the given ID was found."],
    'artworkNotFound' => [404, "No artwork with the given ID was found."],
    'artistNotFound' => [404, "No artist with the given ID was found."],
    'subjectNotFound' => [404, "No subject with the given ID was found."],
    'userNotFound' => [404, "User not found."],
    'databaseError' => [500, "A database error occurred. Please try again later."],
    'reviewError' => [500, "An error occurred while processing your review. Please try again."],
];

// Get error mapping or default to 500
$errorInfo = $errorMappings[$error] ?? [500, "An unknown error occurred."];
[$statusCode, $message] = $errorInfo;

// Create and handle the error using the new system
$errorController = new ErrorController();
$errorController->handleError($statusCode, $message);
?>