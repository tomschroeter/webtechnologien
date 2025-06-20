<?php
require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
require_once dirname(dirname(__DIR__)) . "/components/render-stars.php";
require_once dirname(dirname(__DIR__)) . "/Database.php";
require_once dirname(dirname(__DIR__)) . "/repositories/GenreRepository.php";
require_once dirname(dirname(__DIR__)) . "/repositories/SubjectRepository.php";
require_once dirname(dirname(__DIR__)) . "/repositories/GalleryRepository.php";
require_once dirname(dirname(__DIR__)) . "/repositories/ReviewRepository.php";
require_once dirname(dirname(__DIR__)) . "/dtos/ReviewWithStats.php";

// Fetch additional data that's not passed from controller
$db = new Database();
$genreRepository = new GenreRepository($db);
$subjectRepository = new SubjectRepository($db);
$galleryRepository = new GalleryRepository($db);
$reviewRepo = new ReviewRepository($db);

try {
    $genres = $genreRepository->getGenresByArtwork($artwork->getArtworkId());
    $subjects = $subjectRepository->getSubjectsByArtwork($artwork->getArtworkId());
    $reviews = $reviewRepo->getAllReviewsWithCustomerInfo($artwork->getArtworkId());
    $reviewStats = $reviewRepo->getReviewStats($artwork->getArtworkId());

    $gallery = null;
    if ($artwork->getGalleryId()) {
        $gallery = $galleryRepository->getGalleryById($artwork->getGalleryId());
    }
} catch (Exception $e) {
    // Set default values if there's an error fetching additional data
    $genres = [];
    $subjects = [];
    $reviews = [];
    $reviewStats = new ReviewWithStats(0.0, 0);
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

<div class="mt-4">
    <div class="row">
        <div class="col-auto">
            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal">
                <img src="<?= $correctImagePath ?>" alt="<?= htmlspecialchars($artwork->getTitle()) ?>"
                    class="img-fluid border"
                    style="object-fit: contain; cursor: pointer; background-color: #f8f9fa; max-width: 800px">
            </a>

            <!-- Modal for large image -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 80vw; max-height: auto">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">
                                <?= htmlspecialchars($artwork->getTitle()) ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Remove max-height and overflow-y here -->
                        <div class="modal-body p-3">
                            <img src="<?= $correctLargeImagePath ?>" alt="<?= htmlspecialchars($artwork->getTitle()) ?>"
                                class="img-fluid" style="object-fit: contain; width: 100%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <h3>by <a href="/artists/<?= $artist->getArtistId() ?>" class="text-decoration-none">
                    <?= htmlspecialchars($artist->getFullName()) ?>
                </a></h3>

            <div class="mb-3">
                <?php if ($reviewStats->hasReviews()): ?>
                    <div class="d-flex align-items-center gap-2">
                        <span class="h5 mb-0">Rating: <?= $reviewStats->getFormattedAverageRatingOutOf5() ?>
                            <?= renderStars($reviewStats->getFormattedAverageRating()) ?></span>
                        <small class="text-muted">(based on <?= $reviewStats->getReviewText() ?>)</small>
                    </div>
                <?php else: ?>
                    <span class="text-muted">No reviews yet</span>
                <?php endif; ?>
            </div>

            <!-- Add/Remove Favorites -->
            <?php if (isset($_SESSION['customerId'])): ?>
                <div class="favorites-container mb-3">
                    <?php
                    $type = "artwork";
                    $item = $artwork;
                    $showLabel = true;
                    require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php";
                    ?>
                </div>
            <?php endif; ?>

            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Artwork Details</th>
                        <th class="text-end">
                            <?php if ($artwork->getArtworkLink()): ?>
                                <a href="<?= htmlspecialchars($artwork->getArtworkLink()) ?>" target="_blank"
                                    class="btn btn-light btn-sm text-decoration-none">More Info</a>
                            <?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($artwork->getYearOfWork()): ?>
                        <tr>
                            <th scope="row">Year:</th>
                            <td><?= htmlspecialchars($artwork->getYearOfWork()) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($artwork->getMedium()): ?>
                        <tr>
                            <th scope="row">Medium:</th>
                            <td><?= htmlspecialchars($artwork->getMedium()) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($artwork->getWidth() && $artwork->getHeight()): ?>
                        <tr>
                            <th scope="row">Dimensions:</th>
                            <td><?= htmlspecialchars($artwork->getWidth() . ' × ' . $artwork->getHeight()) ?> cm</td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($genres)): ?>
                        <tr>
                            <th scope="row">Genres:</th>
                            <td>
                                <?php foreach ($genres as $index => $genre): ?>
                                    <a href="/genres/<?= $genre->getGenreId() ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($genre->getGenreName()) ?>
                                    </a><?= $index < count($genres) - 1 ? ', ' : '' ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($subjects)): ?>
                        <tr>
                            <th scope="row">Subjects:</th>
                            <td>
                                <?php foreach ($subjects as $index => $subject): ?>
                                    <a href="/subjects/<?= $subject->getSubjectId() ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($subject->getSubjectName()) ?>
                                    </a><?= $index < count($subjects) - 1 ? ', ' : '' ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($artwork->getGoogleLink()): ?>
                        <tr>
                            <th scope="row">Google Arts:</th>
                            <td>
                                <a href="<?= htmlspecialchars($artwork->getGoogleLink()) ?>" target="_blank"
                                    class="text-decoration-none">
                                    View on Google Arts &amp; Culture
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($artwork->getDescription()): ?>
    <div class="row mt-4">
        <div class="col-12">
            <h3>Description</h3>
            <p><?= $artwork->getDescription() ?></p>
        </div>
    </div>
<?php elseif ($artwork->getExcerpt()): ?>
    <div class="row mt-4">
        <div class="col-12">
            <h3>About this artwork</h3>
            <p><?= $artwork->getExcerpt() ?></p>
        </div>
    </div>
<?php endif; ?>

<?php if ($gallery): ?>
    <div class="row mt-4">
        <div class="col-12">
            <?php if ($artwork->getOriginalHome()): ?>
                <h3 class="mb-4">Gallery</h3>
            <?php endif; ?>

            <div class="accordion" id="generalAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="generalHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#generalCollapse" aria-expanded="false" aria-controls="generalCollapse">
                            General Information
                        </button>
                    </h2>
                    <div id="generalCollapse" class="accordion-collapse collapse" aria-labelledby="generalHeading"
                        data-bs-parent="#generalAccordion">
                        <div class="accordion-body p-0">
                            <table class="table table-striped table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 150px;">Name:</th>
                                        <td><?= htmlspecialchars($gallery->getGalleryName()) ?></td>
                                    </tr>
                                    <?php if ($gallery->getGalleryNativeName() && $gallery->getGalleryNativeName() != $gallery->getGalleryName()): ?>
                                        <tr>
                                            <th>Native Name:</th>
                                            <td><?= htmlspecialchars($gallery->getGalleryNativeName()) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($gallery->getGalleryCity() || $gallery->getGalleryCountry()): ?>
                                        <tr>
                                            <th>City:</th>
                                            <td>
                                                <?php
                                                $locationParts = array_filter([
                                                    $gallery->getGalleryCity(),
                                                    $gallery->getGalleryCountry()
                                                ]);
                                                echo htmlspecialchars(implode(', ', $locationParts));
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($gallery->getWebsite()): ?>
                                        <tr>
                                            <th>Website:</th>
                                            <td>
                                                <a href="<?= htmlspecialchars($gallery->getWebsite()) ?>" target="_blank"
                                                    class="text-decoration-none">
                                                    <?= htmlspecialchars($gallery->getWebsite()) ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
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
            <div class="accordion" id="locationAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="locationHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#locationCollapse" aria-expanded="false" aria-controls="locationCollapse">
                            Location
                        </button>
                    </h2>
                    <div id="locationCollapse" class="accordion-collapse collapse" aria-labelledby="locationHeading"
                        data-bs-parent="#locationAccordion">
                        <div class="accordion-body">
                            <?php
                            $latitude = $gallery->getLatitude();
                            $longitude = $gallery->getLongitude();
                            ?>
                            <div id="map" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 8px;">
                            </div>

                            <!-- Leaflet CSS & JS -->
                            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    var lat = <?= json_encode($latitude) ?>;
                                    var lon = <?= json_encode($longitude) ?>;

                                    var mapInstance = null;
                                    var markerInstance = null;
                                    var isMapInitialized = false;

                                    var locationCollapseElement = document.getElementById('locationCollapse');

                                    if (locationCollapseElement) {
                                        locationCollapseElement.addEventListener('shown.bs.collapse', function () {
                                            var mapDiv = document.getElementById('map');
                                            if (!mapDiv) {
                                                console.error("Map container #map not found.");
                                                return;
                                            }

                                            if (!isMapInitialized) {
                                                mapInstance = L.map('map').setView([lat, lon], 13);

                                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                    maxZoom: 19,
                                                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                                }).addTo(mapInstance);

                                                markerInstance = L.marker([lat, lon]).addTo(mapInstance);

                                                var galleryName = <?= json_encode($gallery->getGalleryName()) ?>;
                                                var popupContent = '<strong>' + galleryName + '</strong><br>Latitude: ' + lat + '<br>Longitude: ' + lon;
                                                markerInstance.bindPopup(popupContent);

                                                markerInstance.on('click', function () {
                                                    markerInstance.openPopup();
                                                });

                                                isMapInitialized = true;
                                            } else {
                                                mapInstance.invalidateSize();
                                                mapInstance.setView([lat, lon], 13);
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

        <?php if (isset($_SESSION['customerId'])): ?>

            <?php
            $alreadyReviewed = $reviewRepo->hasUserReviewed($_SESSION['customerId'], $artwork->getArtworkId());
            if (!$alreadyReviewed): ?>
                <form id="add-review-form" method="POST" action="/reviews/add" class="mb-3">
                    <input type="hidden" name="artworkId" value="<?= $artwork->getArtworkId() ?>">
                    <label for="rating">Rating (1-5):</label>
                    <div class="star-rating text-start">
                        <input type="radio" name="rating" id="star5" value="5" required>
                        <label for="star5" title="5 stars">★</label>

                        <input type="radio" name="rating" id="star4" value="4">
                        <label for="star4" title="4 stars">★</label>

                        <input type="radio" name="rating" id="star3" value="3" checked>
                        <label for="star3" title="3 stars">★</label>

                        <input type="radio" name="rating" id="star2" value="2">
                        <label for="star2" title="2 stars">★</label>

                        <input type="radio" name="rating" id="star1" value="1">
                        <label for="star1" title="1 star">★</label>
                    </div>

                    <label for="comment">Comment:</label>
                    <textarea name="comment" required class="form-control mb-4"></textarea>

                    <button type="submit" class="btn btn-success">Submit Review</button>
                </form>
            <?php else: ?>
                <p class="text-muted">You have already reviewed this artwork.</p>
            <?php endif; ?>

        <?php else: ?>
            <p class="text-muted">Please log in to leave a review.</p>
        <?php endif; ?>

        <?php if (!empty($reviews)): ?>
            <div id="reviews-container" class="mt-4">
                <?php foreach ($reviews as $reviewWithCustomerInfo): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <?= htmlspecialchars($reviewWithCustomerInfo->getCustomerFullName()) ?> -
                                        <?= htmlspecialchars($reviewWithCustomerInfo->getCustomerLocation()) ?>
                                    </h6>
                                    <div class="mb-2">
                                        <strong>Rating: <?= $reviewWithCustomerInfo->getReview()->getRating() ?>/5</strong>
                                    </div>
                                    <p class="card-text">
                                        <?php
                                        $comment = $reviewWithCustomerInfo->getReview()->getComment();
                                        $comment = html_entity_decode($comment, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                        $comment = strip_tags($comment);
                                        echo nl2br(htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'));
                                        ?>
                                    </p>
                                    <small class="text-muted">
                                        <?= date('F j, Y', strtotime($reviewWithCustomerInfo->getReview()->getReviewDate())) ?>
                                    </small>
                                </div>

                                <?php if ($_SESSION['isAdmin'] ?? false): ?>
                                    <div>
                                        <form class="delete-review-form" method="POST"
                                            action="/reviews/<?= $reviewWithCustomerInfo->getReview()->getReviewId() ?>/delete">
                                            <input type="hidden" name="reviewId"
                                                value="<?= $reviewWithCustomerInfo->getReview()->getReviewId() ?>">
                                            <input type="hidden" name="artworkId"
                                                value="<?= $reviewWithCustomerInfo->getReview()->getArtworkId() ?>">
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
            <div id="reviews-container" class="mt-3">
                <p class="text-muted">No reviews yet. Be the first to review this artwork!</p>
            </div>
        <?php endif; ?>
    </div>
</div>