<?php
// Legacy error handling - redirect to MVC error system
require_once __DIR__ . "/controllers/ErrorController.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = $_GET['error'] ?? '';
$type = $_GET['type'] ?? '';

// Handle specific error cases
switch ($error) {
    case 'tooShort':
        http_response_code(400);
        $message = "Please enter at least 3 characters in the search bar.";
        break;
    case 'invalidParam':
        http_response_code(400);
        $message = "The passed parameter is invalid.";
        break;
    case 'missingParam':
        http_response_code(400);
        $message = "A required parameter is missing.";
        break;
    case 'invalidID':
        http_response_code(404);
        if ($type === 'subject') {
            $message = "No subject with the given ID was found.";
        } elseif ($type === 'artist') {
            $message = "No artist with the given ID was found.";
        } elseif ($type === 'genre') {
            $message = "No genre with the given ID was found.";
        } else {
            $message = "No entry with the given ID was found.";
        }
        break;
    case 'notLoggedIn':
        http_response_code(401);
        $message = "You must be logged in to perform this action.";
        break;
    case 'unauthorized':
        http_response_code(403);
        $message = "You are not authorized to perform this action.";
        break;
    case 'missingReviewData':
        http_response_code(400);
        $message = "Required review data is missing.";
        break;
    case 'invalidReviewData':
        http_response_code(400);
        $message = "Invalid review data submitted.";
        break;
    case 'duplicateReview':
        http_response_code(409);
        $message = "You have already reviewed this artwork.";
        break;
    case 'searchError':
        http_response_code(500);
        $message = "An error occurred while searching. Please try again.";
        break;
    case 'genreNotFound':
        http_response_code(404);
        $message = "No genre with the given ID was found.";
        break;
    case 'artworkNotFound':
        http_response_code(404);
        $message = "No artwork with the given ID was found.";
        break;
    case 'artistNotFound':
        http_response_code(404);
        $message = "No artist with the given ID was found.";
        break;
    case 'subjectNotFound':
        http_response_code(404);
        $message = "No subject with the given ID was found.";
        break;
    case 'userNotFound':
        http_response_code(404);
        $message = "User not found.";
        break;
    case 'databaseError':
        http_response_code(500);
        $message = "A database error occurred. Please try again later.";
        break;
    default:
        http_response_code(500);
        $message = "An unknown error occurred.";
}

// Store error message in session for display
$_SESSION['error_message'] = $message;

// Redirect to appropriate error page based on status code
$statusCode = http_response_code();
if ($statusCode >= 400 && $statusCode < 500) {
    // Client errors (4xx) - show 404 page
    $errorController = new ErrorController();
    $errorController->notFound();
} else {
    // Server errors (5xx) - show 500 page
    $errorController = new ErrorController();
    $errorController->serverError();
}
?>