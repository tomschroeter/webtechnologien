<?php
require_once dirname(dirname(__DIR__)) . "/components/find_image_ref.php";
require_once dirname(dirname(__DIR__)) . "/repositories/GenreRepository.php";
require_once dirname(dirname(__DIR__)) . "/repositories/SubjectRepository.php";
require_once dirname(dirname(__DIR__)) . "/repositories/GalleryRepository.php";
require_once dirname(dirname(__DIR__)) . "/repositories/ReviewRepository.php";
require_once dirname(dirname(__DIR__)) . "/dtos/ReviewStats.php";

// Fetch additional data that's not passed from controller
$db = new Database();
$genreRepository = new GenreRepository($db);
$subjectRepository = new SubjectRepository($db);
$galleryRepository = new GalleryRepository($db);

try {
    $genres = $genreRepository->getGenresByArtwork($artwork->getArtworkID());
    $subjects = $subjectRepository->getSubjectsByArtwork($artwork->getArtworkID());
    $reviewStats = (new ReviewRepository($db))->getReviewStats($artwork->getArtworkID());

    $gallery = null;
    if ($artwork->getGalleryId()) {
        $gallery = $galleryRepository->getGalleryById($artwork->getGalleryId());
    }
} catch (Exception $e) {
    // Set default values if there's an error fetching additional data
    $genres = [];
    $subjects = [];
    $reviewStats = new ReviewStats(0.0, 0);
    $gallery = null;
}

$imagePath = "/assets/images/works/medium/" . $artwork->getImageFileName() . ".jpg";
$largeImagePath = "/assets/images/works/large/" . $artwork->getImageFileName() . ".jpg";
$placeholderPath = "/assets/placeholder/works/medium/placeholder.svg";
$correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
$correctLargeImagePath = getImagePathOrPlaceholder($largeImagePath, $placeholderPath);
?>

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

<div class="mt-3">
    <div class="row">
        <div class="col-md-6">
            <a href="#" data-toggle="modal" data-target="#imageModal">
                <img src="<?php echo $correctImagePath ?>" 
                     alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>" 
                     class="img-fluid" 
                     style="width: 100%; height: 400px; object-fit: contain; cursor: pointer; border: 1px solid #ddd; background-color: #f8f9fa;">
            </a>
            
            <!-- Modal for large image -->
            <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document" style="height: 95vh; margin: 2.5vh auto;">
                    <div class="modal-content" style="height: 100%;">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel"><?php echo htmlspecialchars($artwork->getTitle()) ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center d-flex align-items-center justify-content-center" style="flex: 1; padding: 20px;">
                            <img src="<?php echo $correctLargeImagePath ?>" 
                                 alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>" 
                                 class="img-fluid"
                                 style="height: 100%; width: auto; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <h3>by <a href="/artists/<?php echo $artist->getArtistId() ?>" class="text-decoration-none">
                <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>
            </a></h3>
            
            <div class="mb-3">
                <?php if ($reviewStats->hasReviews()): ?>
                    <div class="d-flex align-items-center">
                        <span class="h5 mb-0 mr-2">Rating: <?php echo $reviewStats->getFormattedAverageRatingOutOf5() ?></span>
                        <small class="text-muted">(based on <?php echo $reviewStats->getReviewText() ?>)</small>
                    </div>
                <?php else: ?>
                    <span class="text-muted">No reviews yet</span>
                <?php endif; ?>
            </div>

            <!-- Add/Remove Favorites Form -->
            <?php 
            $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($artwork->getArtworkID(), $_SESSION['favoriteArtworks']);
            ?>
            <form method="post" action="/favorites-handler.php" class="mb-3">
                <?php if ($isInFavorites): ?>
                    <input type="hidden" name="action" value="remove_artwork_from_favorites">
                    <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkID() ?>">
                    <button type="submit" class="btn btn-outline-danger">
                        ♥ Remove from Favorites
                    </button>
                <?php else: ?>
                    <input type="hidden" name="action" value="add_artwork_to_favorites">
                    <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkID() ?>">
                    <button type="submit" class="btn btn-primary">
                        ♡ Add to Favorites
                    </button>
                <?php endif; ?>
            </form>

            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Artwork Details</th>
                        <th class="text-right">
                            <?php if ($artwork->getArtworkLink()): ?>
                                <a href="<?php echo htmlspecialchars($artwork->getArtworkLink()) ?>" target="_blank" class="btn btn-light btn-sm text-decoration-none">More Info</a>
                            <?php endif; ?>
                        </th>
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
                    
                    <?php if (!empty($genres)): ?>
                        <tr>
                            <th>Genres:</th>
                            <td>
                                <?php foreach ($genres as $index => $genre): ?>
                                    <a href="/genres/<?php echo $genre->getGenreId() ?>" 
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
                                    <a href="/subjects/<?php echo $subject->getSubjectId() ?>" 
                                       class="text-decoration-none"><?php echo htmlspecialchars($subject->getSubjectName()) ?></a><?php if ($index < count($subjects) - 1): ?>, <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
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
                <?php if ($artwork->getOriginalHome()): ?>
                    <h3 class="mb-4">Gallery</h3>
                <?php endif; ?>
                <div class="card" id="generalAccordion">
                    <div class="accordion">
                        <div class="card-header" id="generalHeading" style="border-bottom: 0;">
                            <h3 class="mb-0">
                                <button class="btn btn-link text-decoration-none text-dark d-flex justify-content-between align-items-center w-100" 
                                        type="button" 
                                        data-toggle="collapse" 
                                        data-target="#generalCollapse" 
                                        aria-expanded="false" 
                                        aria-controls="generalCollapse">
                                    General Information
                                    <span id="generalArrow">▼</span>
                                </button>
                            </h3>
                        </div>
                        <div id="generalCollapse" class="collapse" aria-labelledby="generalHeading" data-parent="#generalAccordion">
                            <div class="card-body">
                                <table class="table table-striped table-bordered mb-0">
                                    <tr>
                                        <th width="150">Name:</th>
                                        <td><?php echo htmlspecialchars($gallery->getGalleryName()) ?></td>
                                    </tr>
                                    
                                    <?php if ($gallery->getGalleryNativeName() && $gallery->getGalleryNativeName() != $gallery->getGalleryName()): ?>
                                        <tr>
                                            <th>Native Name:</th>
                                            <td><?php echo htmlspecialchars($gallery->getGalleryNativeName()) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                    <?php if ($gallery->getGalleryCity() || $gallery->getGalleryCountry()): ?>
                                        <tr>
                                            <th>City:</th>
                                            <td><?php 
                                                $locationParts = array_filter([
                                                    $gallery->getGalleryCity(),
                                                    $gallery->getGalleryCountry()
                                                ]);
                                                echo htmlspecialchars(implode(', ', $locationParts));
                                            ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if ($gallery->getGalleryWebSite()): ?>
                                        <tr>
                                            <th>Website:</th>
                                            <td><a href="<?php echo htmlspecialchars($gallery->getGalleryWebSite()) ?>" target="_blank" class="text-decoration-none">
                                                <?php echo htmlspecialchars($gallery->getGalleryWebSite()) ?>
                                            </a></td>
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

    <?php if ($gallery && $gallery->getLatitude() && $gallery->getLongitude()): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card" id="locationAccordion">
                    <div class="accordion">
                        <div class="card-header" id="locationHeading" style="border-bottom: 0;">
                            <h3 class="mb-0">
                                <button class="btn btn-link text-decoration-none text-dark d-flex justify-content-between align-items-center w-100" 
                                        type="button" 
                                        data-toggle="collapse" 
                                        data-target="#locationCollapse" 
                                        aria-expanded="false" 
                                        aria-controls="locationCollapse">
                                    Location
                                    <span id="locationArrow">▼</span>
                                </button>
                            </h3>
                        </div>
                        <div id="locationCollapse" class="collapse" aria-labelledby="locationHeading" data-parent="#locationAccordion">
                            <div class="card-body">
                                <?php
                                    $latitude = $gallery->getLatitude();
                                    $longitude = $gallery->getLongitude();
                                ?>

                                <div id="map" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 8px;"></div>
                                
                                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                                
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        var lat = <?php echo json_encode($latitude); ?>;
                                        var lon = <?php echo json_encode($longitude); ?>;
                                        
                                        var mapInstance = null; // Contains the Leaflet map instance
                                        var markerInstance = null; // Need to be kept outside to enable opening the popup a second time
                                        var isMapInitialized = false; // Flag to check if map has been initialized

                                        // This workaround is needed to display the map inside the accordion element
                                        // Weird artifacts will occur otherwise
                                        // This works because the map is only initialized when the accordion panel is fully shown
                                        var locationCollapseElement = document.getElementById('locationCollapse');

                                        if (locationCollapseElement) {
                                            // Listen for the 'shown.bs.collapse' event, which fires after the accordion panel is fully visible
                                            $(locationCollapseElement).on('shown.bs.collapse', function () {
                                                var mapDiv = document.getElementById('map');
                                                if (!mapDiv) {
                                                    console.error("Map container #map not found when accordion was shown.");
                                                    return;
                                                }

                                                // Initialize the map for the first time
                                                if (!isMapInitialized) {
                                                    mapInstance = L.map('map').setView([lat, lon], 13);

                                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                        maxZoom: 19,
                                                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                                    }).addTo(mapInstance);

                                                    markerInstance = L.marker([lat, lon]).addTo(mapInstance);
                
                                                    var galleryName = <?php echo json_encode($gallery->getGalleryName()); ?>;
                                                    var popupContent = '<strong>' + galleryName + '</strong><br>Latitude: ' + lat + '<br>Longitude: ' + lon;
                                                    markerInstance.bindPopup(popupContent);
                                                    
                                                    markerInstance.on('click', function () {
                                                        markerInstance.openPopup();
                                                    });

                                                    isMapInitialized = true;
                                                }
                                                else {
                                                    // If map was already initialized before: recalculate its size and set location to pin again
                                                    if (mapInstance) {
                                                        mapInstance.invalidateSize();
                                                        mapInstance.setView([lat, lon], 13);
                                                    }
                                                }
                                            });
                                        } else {
                                            console.error("Accordion collapse element #locationCollapse not found.");
                                        }
                                    });
                                </script>
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
            
            <?php
            // Review form (only if user is logged in and hasn't reviewed yet)
            require_once dirname(dirname(__DIR__)) . "/repositories/ReviewRepository.php";
            $reviewRepo = new ReviewRepository($db);
            
            if (isset($_SESSION['customerId'])):
                $alreadyReviewed = $reviewRepo->hasUserReviewed($_SESSION['customerId'], $artwork->getArtworkID());
                
                if (!$alreadyReviewed): ?>
                    <form method="POST" action="add-review.php" class="mb-3">
                        <input type="hidden" name="artworkId" value="<?= $artwork->getArtworkID() ?>">
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

            
            <?php if (!empty($reviews)): ?>
                <div class="mt-3">
                    <?php foreach ($reviews as $reviewWithCustomerInfo): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <?php echo htmlspecialchars($reviewWithCustomerInfo->getCustomerFullName()) ?> - 
                                            <?php echo htmlspecialchars($reviewWithCustomerInfo->getCustomerLocation()) ?>
                                        </h6>
                                        <div class="mb-2">
                                            <strong>Rating: <?php echo $reviewWithCustomerInfo->getReview()->getRating() ?>/5</strong>
                                        </div>
                                        <p class="card-text"><?php 
                                            $comment = $reviewWithCustomerInfo->getReview()->getComment();
                                            // Decode HTML entities and strip any remaining HTML tags
                                            $comment = html_entity_decode($comment, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                            $comment = strip_tags($comment); // Remove any HTML tags
                                            echo nl2br(htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')); 
                                        ?></p>
                                        <small class="text-muted">
                                            <?php echo date('F j, Y', strtotime($reviewWithCustomerInfo->getReview()->getReviewDate())) ?>
                                        </small>
                                    </div>
                                    
                                    <!-- Show delete option for admins in top right corner -->
                                    <?php if ($_SESSION['isAdmin'] ?? false): ?>
                                        <div>
                                            <form method="POST" action="delete-review.php" onsubmit="return confirm('Delete this review?')">
                                                <input type="hidden" name="reviewId" value="<?php echo $reviewWithCustomerInfo->getReview()->getReviewId() ?>">
                                                <input type="hidden" name="artworkId" value="<?php echo $reviewWithCustomerInfo->getReview()->getArtworkId() ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
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

<script>
// Handle accordion arrow rotation for general museum information
$('#generalCollapse').on('show.bs.collapse', function () {
    $('#generalArrow').text('▲');
});

$('#generalCollapse').on('hide.bs.collapse', function () {
    $('#generalArrow').text('▼');
});

// Handle accordion arrow rotation for location museum information
$('#locationCollapse').on('show.bs.collapse', function () {
    $('#locationArrow').text('▲');
});

$('#locationCollapse').on('hide.bs.collapse', function () {
    $('#locationArrow').text('▼');
});
</script>
      <table class="table table-bordered">
        <thead class="thead-dark">
          <tr><th colspan="2">Artwork Details</th></tr>
        </thead>
        <tr>
          <th>Technique:</th>
          <td><?php echo htmlspecialchars($artwork->getMedium()) ?></td>
        </tr>
        <tr>
          <th>Width:</th>
          <td><?php echo htmlspecialchars($artwork->getWidth()) ?> cm</td>
        </tr>
        <tr>
          <th>Height:</th>
          <td><?php echo htmlspecialchars($artwork->getHeight()) ?> cm</td>
        </tr>
        <?php if ($artwork->getYearOfWork()): ?>
        <tr>
          <th>Year:</th>
          <td><?php echo htmlspecialchars($artwork->getYearOfWork()) ?></td>
        </tr>
        <?php endif; ?>
      </table>
    </div>
  </div>
  
  <!-- Reviews Section -->
  <div class="mt-5">
    <h3>Reviews</h3>
    
    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="mb-4">
        <h4>Add Your Review</h4>
        <form method="post" action="/add-review.php">
          <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
          <div class="form-group">
            <label for="rating">Rating (1-5):</label>
            <select name="rating" id="rating" class="form-control" required>
              <option value="">Select rating</option>
              <option value="1">1 - Poor</option>
              <option value="2">2 - Fair</option>
              <option value="3">3 - Good</option>
              <option value="4">4 - Very Good</option>
              <option value="5">5 - Excellent</option>
            </select>
          </div>
          <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
      </div>
    <?php else: ?>
      <p><a href="/login.php">Login</a> to add a review.</p>
    <?php endif; ?>
    
    <?php if (!empty($reviews)): ?>
      <div class="reviews-list">
        <?php foreach ($reviews as $review): ?>
          <div class="card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <h5 class="card-title">
                  <?php echo str_repeat('★', $review->getRating()) ?>
                  <?php echo str_repeat('☆', 5 - $review->getRating()) ?>
                </h5>
                <small class="text-muted"><?php echo $review->getDateCreated() ?></small>
              </div>
              <p class="card-text"><?php echo htmlspecialchars($review->getComment()) ?></p>
              <small class="text-muted">by <?php echo htmlspecialchars($review->getCustomerUsername ?? 'Anonymous') ?></small>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>No reviews yet. Be the first to review this artwork!</p>
    <?php endif; ?>
  </div>
</div>
