<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/navbar.php";
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/src/repositories/SubjectRepository.php";
require_once dirname(__DIR__) . "/src/repositories/GalleryRepository.php";
require_once dirname(__DIR__) . "/src/router/router.php";

session_start();

$db = new Database();
$artworkRepository = new ArtworkRepository($db);
$artistRepository = new ArtistRepository($db);
$genreRepository = new GenreRepository($db);
$subjectRepository = new SubjectRepository($db);
$galleryRepository = new GalleryRepository($db);

// Handle Add/Remove Favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
        
        $artworkId = (int)$_POST['artworkId'];
        
        if ($_POST['action'] === 'add_to_favorites') {
            if (!in_array($artworkId, $_SESSION['favorites'])) {
                $_SESSION['favorites'][] = $artworkId;
                $message = "Artwork added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is already in your favorites.";
                $messageType = "info";
            }
        } elseif ($_POST['action'] === 'remove_from_favorites') {
            if (($key = array_search($artworkId, $_SESSION['favorites'])) !== false) {
                unset($_SESSION['favorites'][$key]);
                $_SESSION['favorites'] = array_values($_SESSION['favorites']); // Re-index array
                $message = "Artwork removed from favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is not in your favorites.";
                $messageType = "info";
            }
        }
    } catch (Exception $e) {
        $message = "Error updating favorites. Please try again.";
        $messageType = "danger";
    }
}

// Check if artwork ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /error.php?error=invalidParam");
    exit();
}

$artworkId = (int)$_GET['id'];

// Try to get the artwork out of database
try {
    $artwork = $artworkRepository->findById($artworkId);
    $artist = $artistRepository->getArtistById($artwork->getArtistId());
} catch (Exception $e) {
    header("Location: /error.php?error=invalidID&type=artwork");
    exit();
}

// Fetch additional data
try {
    $genres = $artworkRepository->getGenresByArtwork($artworkId);
    $subjects = $artworkRepository->getSubjectsByArtwork($artworkId);
    $reviews = $artworkRepository->getReviewsByArtwork($artworkId);
    $reviewStats = $artworkRepository->getReviewStats($artworkId);

    $gallery = null;
    if ($artwork->getGalleryId()) {
        $gallery = $galleryRepository->getGalleryById($artwork->getGalleryId());
    }
} catch (Exception $e) {
    // Set default values if there's an error fetching additional data
    $genres = [];
    $subjects = [];
    $reviews = [];
    $reviewStats = ['averageRating' => 0, 'totalReviews' => 0];
    $gallery = null;
}

$imagePath = "/assets/images/works/medium/" . $artwork->getImageFileName() . ".jpg";
$largeImagePath = "/assets/images/works/large/" . $artwork->getImageFileName() . ".jpg";
$placeholderPath = "/assets/placeholder/works/medium/placeholder.svg";

if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
    $correctImagePath = $imagePath;
    $correctLargeImagePath = $largeImagePath;
} else {
    $correctImagePath = $placeholderPath;
    $correctLargeImagePath = $placeholderPath;
}
?>

<body class="container">
    <br>
    <h1><?php echo htmlspecialchars($artwork->getTitle()) ?></h1>
    
    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <a href="#" data-toggle="modal" data-target="#imageModal">
                    <img src="<?php echo $correctImagePath ?>" 
                         alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>" 
                         class="img-fluid" 
                         style="max-width: 100%; cursor: pointer;">
                </a>
                
                <!-- Modal for large image -->
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel"><?php echo htmlspecialchars($artwork->getTitle()) ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="<?php echo $correctLargeImagePath ?>" 
                                     alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>" 
                                     class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <h3>by <a href="<?php echo route('artists', ['id' => $artist->getArtistId()]) ?>" class="text-decoration-none">
                    <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>
                </a></h3>
                
                <div class="mb-3">
                    <?php if ($reviewStats['totalReviews'] > 0): ?>
                        <div class="d-flex align-items-center">
                            <span class="h5 mb-0 mr-2">Rating: <?php echo $reviewStats['averageRating'] ?>/10</span>
                            <small class="text-muted">(based on <?php echo $reviewStats['totalReviews'] ?> review<?php echo $reviewStats['totalReviews'] > 1 ? 's' : '' ?>)</small>
                        </div>
                    <?php else: ?>
                        <span class="text-muted">No reviews yet</span>
                    <?php endif; ?>
                </div>

                <!-- Add/Remove Favorites Form -->
                <?php 
                $isInFavorites = isset($_SESSION['favorites']) && in_array($artwork->getArtworkId(), $_SESSION['favorites']);
                ?>
                <form method="post" class="mb-3">
                    <?php if ($isInFavorites): ?>
                        <input type="hidden" name="action" value="remove_from_favorites">
                        <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-heart-fill"></i> Remove from Favorites
                        </button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="add_to_favorites">
                        <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-heart"></i> Add to Favorites
                        </button>
                    <?php endif; ?>
                </form>

                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th colspan="2">Artwork Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($artwork->getYearOfWork()): ?>
                            <tr>
                                <th>Year:</th>
                                <td><?php echo htmlspecialchars($artwork->getYearOfWork()) ?></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($artwork->getMedium()): ?>
                            <tr>
                                <th>Medium:</th>
                                <td><?php echo htmlspecialchars($artwork->getMedium()) ?></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($artwork->getWidth() && $artwork->getHeight()): ?>
                            <tr>
                                <th>Dimensions:</th>
                                <td><?php echo htmlspecialchars($artwork->getWidth() . ' × ' . $artwork->getHeight()) ?> cm</td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($artwork->getArtworkType()): ?>
                            <tr>
                                <th>Type:</th>
                                <td><?php echo htmlspecialchars($artwork->getArtworkType()) ?></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($genres)): ?>
                            <tr>
                                <th>Genres:</th>
                                <td>
                                    <?php foreach ($genres as $index => $genre): ?>
                                        <a href="<?php echo route('genres', ['id' => $genre->getGenreId()]) ?>" 
                                           class="text-decoration-none"><?php echo htmlspecialchars($genre->getGenreName()) ?></a><?php if ($index < count($genres) - 1): ?>, <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($subjects)): ?>
                            <tr>
                                <th>Subjects:</th>
                                <td>
                                    <?php foreach ($subjects as $index => $subject): ?>
                                        <a href="<?php echo route('subjects', ['id' => $subject->getSubjectId()]) ?>" 
                                           class="text-decoration-none"><?php echo htmlspecialchars($subject->getSubjectName()) ?></a><?php if ($index < count($subjects) - 1): ?>, <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($artwork->getArtworkLink()): ?>
                            <tr>
                                <th>More Info:</th>
                                <td><a href="<?php echo htmlspecialchars($artwork->getArtworkLink()) ?>" target="_blank" class="text-decoration-none">External Link</a></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($artwork->getGoogleLink()): ?>
                            <tr>
                                <th>Google Arts:</th>
                                <td><a href="<?php echo htmlspecialchars($artwork->getGoogleLink()) ?>" target="_blank" class="text-decoration-none">View on Google Arts & Culture</a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($artwork->getDescription()): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h3>Description</h3>
                    <p><?php 
                        $description = $artwork->getDescription();
                        // Decode HTML entities and display properly formatted text
                        $description = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        echo nl2br($description); 
                    ?></p>
                </div>
            </div>
        <?php elseif ($artwork->getExcerpt()): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h3>About this artwork</h3>
                    <p><?php 
                        $excerpt = $artwork->getExcerpt();
                        // Decode HTML entities and display properly formatted text
                        $excerpt = html_entity_decode($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        echo nl2br($excerpt); 
                    ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($gallery): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h3 class="d-flex justify-content-between align-items-center" 
                        style="cursor: pointer;" 
                        data-toggle="collapse" 
                        data-target="#galleryCollapse" 
                        aria-expanded="false" 
                        aria-controls="galleryCollapse">
                        Museum Information
                        <i class="fas fa-chevron-down" id="galleryArrow"></i>
                    </h3>
                    <div class="accordion" id="galleryAccordion">
                        <div class="card border-0">
                            <div id="galleryCollapse" class="collapse" aria-labelledby="galleryHeading" 
                                 data-parent="#galleryAccordion">
                                <div class="card-body p-0">
                                    <table class="table table-striped table-bordered mb-0">
                                        <tr>
                                            <th width="150">Museum:</th>
                                            <td><?php echo htmlspecialchars($gallery->getGalleryName()) ?></td>
                                        </tr>
                                        
                                        <?php if ($gallery->getGalleryNativeName() && $gallery->getGalleryNativeName() != $gallery->getGalleryName()): ?>
                                            <tr>
                                                <th>Native Name:</th>
                                                <td><?php echo htmlspecialchars($gallery->getGalleryNativeName()) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        
                                        <tr>
                                            <th>Location:</th>
                                            <td><?php echo htmlspecialchars($gallery->getGalleryCity() . ', ' . $gallery->getGalleryCountry()) ?></td>
                                        </tr>
                                        
                                        <?php if ($gallery->getGalleryWebSite()): ?>
                                            <tr>
                                                <th>Website:</th>
                                                <td><a href="<?php echo htmlspecialchars($gallery->getGalleryWebSite()) ?>" target="_blank" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($gallery->getGalleryWebSite()) ?>
                                                </a></td>
                                            </tr>
                                        <?php endif; ?>
                                        
                                        <?php if ($gallery->getLatitude() && $gallery->getLongitude()): ?>
                                            <tr>
                                                <th>Coordinates:</th>
                                                <td><?php echo htmlspecialchars($gallery->getLatitude() . ', ' . $gallery->getLongitude()) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row mt-5">
            <div class="col-12">
                <h3>Reviews</h3>
                
                <!-- Add Review Form for Authenticated Users -->
                <!-- TODO: Add authentication check and review form here -->
                
                <?php if (!empty($reviews)): ?>
                    <div class="mt-3">
                        <?php foreach ($reviews as $review): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <?php echo htmlspecialchars($review['customerName']) ?> - 
                                                <?php echo htmlspecialchars($review['customerCity'] . ' (' . $review['customerCountry'] . ')') ?>
                                            </h6>
                                            <div class="mb-2">
                                                <strong>Rating: <?php echo $review['rating'] ?>/10</strong>
                                            </div>
                                            <p class="card-text"><?php 
                                                $comment = $review['comment'];
                                                // Decode HTML entities and strip any remaining HTML tags
                                                $comment = html_entity_decode($comment, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                                $comment = strip_tags($comment); // Remove any HTML tags
                                                echo nl2br(htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')); 
                                            ?></p>
                                            <small class="text-muted">
                                                <?php echo date('F j, Y', strtotime($review['reviewDate'])) ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Admin Delete Button -->
                                        <!-- TODO: Add admin check and delete functionality -->
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No reviews yet. Be the first to review this artwork!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require_once 'bootstrap.php'; ?>
    
    <script>
    // Handle accordion arrow rotation for museum information
    $('#galleryCollapse').on('show.bs.collapse', function () {
        $('#galleryArrow').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    
    $('#galleryCollapse').on('hide.bs.collapse', function () {
        $('#galleryArrow').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    
    // Handle click on heading to toggle collapse
    $('h3[data-toggle="collapse"]').on('click', function() {
        var target = $(this).attr('data-target');
        $(target).collapse('toggle');
    });
    </script>
</body>
</html>
