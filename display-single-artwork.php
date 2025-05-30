<?php
session_start();
$_SESSION['customerId'] = 1; // TEMP: simulate logged-in user
$_SESSION['isAdmin'] = true; // TEMP: simulate admin privileges

require_once "bootstrap.php";
require_once "classes/Artwork.php";
require_once "classes/Artist.php";
require_once "repositories/ArtworkRepository.php";
require_once "repositories/ArtistRepository.php";
require_once "repositories/ReviewRepository.php";
require_once "head.php";
require_once "navbar.php";

// Check if 'id' is set and valid in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: error.php?error=invalidParam");
    exit;
}

$artworkId = (int)$_GET['id'];

$db = new Database();
$artworkRepo = new ArtworkRepository($db);
$artistRepo = new ArtistRepository($db);
$reviewRepo = new ReviewRepository($db);

// Load artwork, artist and reviews
try {
    $artwork = $artworkRepo->findById($artworkId);
    $artist = $artistRepo->getArtistById($artwork->getArtistId());
    $reviews = $reviewRepo->getAllReviewsForArtwork($artworkId);
} catch (Exception $e) {
    header("Location: error.php?error=invalidID");
    exit;
}
?>

<body class="container mt-4">
    <h1><?= htmlspecialchars($artwork->getTitle()) ?></h1>
    <p><strong>By:</strong> <?= htmlspecialchars($artist->getFirstName() . " " . $artist->getLastName()) ?></p>

    <!-- Display artwork image -->
    <?php
    $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
    $placeholder = "/assets/placeholder/works/square-medium/placeholder.svg";
    $finalPath = file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath) ? $imagePath : $placeholder;
    ?>
    <img src="<?= $finalPath ?>" alt="<?= $artwork->getTitle() ?>" class="img-fluid mb-3">

    <!-- Review form (only if user is logged in and hasn't reviewed yet) -->
    <?php if (isset($_SESSION['customerId'])): ?>
        <?php
        $alreadyReviewed = $reviewRepo->hasUserReviewed($_SESSION['customerId'], $artworkId);
        ?>
        <?php if (!$alreadyReviewed): ?>
            <form method="POST" action="add-review.php" class="mb-3">
                <input type="hidden" name="artworkId" value="<?= $artworkId ?>">
                <label for="rating">Rating (1–5):</label>
                <input type="number" name="rating" min="1" max="5" required class="form-control mb-2">
                <label for="comment">Comment:</label>
                <textarea name="comment" required class="form-control mb-2"></textarea>
                <button type="submit" class="btn btn-success">Submit Review</button>
            </form>
        <?php else: ?>
            <p class="text-muted">You have already reviewed this artwork.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-muted">Please log in to leave a review.</p>
    <?php endif; ?>

    <!-- Review list -->
    <h4>Reviews</h4>
    <?php if (count($reviews) > 0): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="border p-2 mb-2 bg-light">
                <strong>Rating:</strong> <?= $review->getRating() ?>/5<br>
                <strong>Comment:</strong> <?= htmlspecialchars($review->getComment()) ?><br>
                <small class="text-muted"><?= $review->getReviewDate() ?></small>
                <!-- Show delete option for admins -->
                <?php if ($_SESSION['isAdmin'] ?? false): ?>
                    <form method="POST" action="delete-review.php" onsubmit="return confirm('Delete this review?')" class="mt-1">
                        <input type="hidden" name="reviewId" value="<?= $review->getReviewId() ?>">
                        <input type="hidden" name="artworkId" value="<?= $review->getArtworkId() ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No reviews yet.</p>
    <?php endif; ?>
</body>
</html>
