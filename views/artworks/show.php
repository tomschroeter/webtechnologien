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
        <div class="col-md-6">
            <a href="#" data-toggle="modal" data-target="#imageModal">
                <img src="<?php echo $correctImagePath ?>" alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>"
                    class="img-fluid"
                    style="max-width: auto; max-height: auto; object-fit: contain; cursor: pointer; border: 1px solid #ddd; background-color: #f8f9fa;"></a>

            <!-- Modal for large image -->
            <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document" style="height: 95vh; margin: 2.5vh auto;">
                    <div class="modal-content" style="height: auto;">
                        <div class="modal-header">
                            <h3 class="modal-title" id="imageModalLabel">
                                <?php echo htmlspecialchars($artwork->getTitle()) ?>
                            </h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center d-flex align-items-center justify-content-center"
                            style="flex: 1; padding: 20px;">
                            <img src="<?php echo $correctLargeImagePath ?>"
                                alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>" class="img-fluid"
                                style="height: 100%; width: auto; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h3>by <a href="/artists/<?php echo $artist->getArtistId() ?>" class="text-decoration-none">
                    <?php echo htmlspecialchars($artist->getFullName()) ?>
                </a></h3>

            <div class="mb-3">
                <?php if ($reviewStats->hasReviews()): ?>
                    <div class="d-flex align-items-center">
                        <span class="h5 mb-0 mr-2">Rating:
                            <?php echo $reviewStats->getFormattedAverageRatingOutOf5() . ' ' . renderStars($reviewStats->getFormattedAverageRating()) ?></span>
                        <small class="text-muted" style="transform: translateY(1px);">(based on
                            <?php echo $reviewStats->getReviewText() ?>)</small>
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
                        require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php"
                    ?>
                </div>

            <?php endif; ?>

            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Artwork Details</th>
                        <th class="text-right">
                            <?php if ($artwork->getArtworkLink()): ?>
                                <a href="<?php echo htmlspecialchars($artwork->getArtworkLink()) ?>" target="_blank"
                                    class="btn btn-light btn-sm text-decoration-none">More Info</a>
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
                                        class="text-decoration-none"><?php echo htmlspecialchars($genre->getGenreName()) ?></a><?php if ($index < count($genres) - 1): ?>,
                                    <?php endif; ?>
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
                                        class="text-decoration-none"><?php echo htmlspecialchars($subject->getSubjectName()) ?></a><?php if ($index < count($subjects) - 1): ?>,
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($artwork->getGoogleLink()): ?>
                        <tr>
                            <th>Google Arts:</th>
                            <td><a href="<?php echo htmlspecialchars($artwork->getGoogleLink()) ?>" target="_blank"
                                    class="text-decoration-none">View on Google Arts & Culture</a></td>
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
                <p><?php echo $artwork->getDescription() ?></p>
            </div>
        </div>
    <?php elseif ($artwork->getExcerpt()): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h3>About this artwork</h3>
                <p><?php echo $artwork->getExcerpt() ?></p>
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
                                <button
                                    class="btn btn-link text-decoration-none text-dark d-flex justify-content-between align-items-center w-100"
                                    type="button" data-toggle="collapse" data-target="#generalCollapse"
                                    aria-expanded="false" aria-controls="generalCollapse">
                                    General Information
                                    <span id="generalArrow">▼</span>
                                </button>
                            </h3>
                        </div>
                        <div id="generalCollapse" class="collapse" aria-labelledby="generalHeading"
                            data-parent="#generalAccordion">
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

                                    <?php if ($gallery->getWebsite()): ?>
                                        <tr>
                                            <th>Website:</th>
                                            <td><a href="<?php echo htmlspecialchars($gallery->getWebsite()) ?>" target="_blank"
                                                    class="text-decoration-none">
                                                    <?php echo htmlspecialchars($gallery->getWebsite()) ?>
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

    <?php if ($gallery->getLatitude() && $gallery->getLongitude()): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card" id="locationAccordion">
                    <div class="accordion">
                        <div class="card-header" id="locationHeading" style="border-bottom: 0;">
                            <h3 class="mb-0">
                                <button
                                    class="btn btn-link text-decoration-none text-dark d-flex justify-content-between align-items-center w-100"
                                    type="button" data-toggle="collapse" data-target="#locationCollapse"
                                    aria-expanded="false" aria-controls="locationCollapse">
                                    Location
                                    <span id="locationArrow">▼</span>
                                </button>
                            </h3>
                        </div>
                        <div id="locationCollapse" class="collapse" aria-labelledby="locationHeading"
                            data-parent="#locationAccordion">
                            <div class="card-body">
                                <?php
                                $latitude = $gallery->getLatitude();
                                $longitude = $gallery->getLongitude();
                                ?>

                                <div id="map"
                                    style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 8px;"></div>

                                <!-- Include Leaflet CSS and JS, allowed for this project -->
                                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                                <!-- JavaScript to display map, allowed for this project -->
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
            if (isset($_SESSION['customerId'])):

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
                <div id="reviews-container" class="mt-3">
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
                                            <strong>Rating:
                                                <?php echo $reviewWithCustomerInfo->getReview()->getRating() ?>/5</strong>
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
                                            <form class="delete-review-form" method="POST"
                                                action="/reviews/<?php echo $reviewWithCustomerInfo->getReview()->getReviewId() ?>/delete">
                                                <input type="hidden" name="reviewId"
                                                    value="<?php echo $reviewWithCustomerInfo->getReview()->getReviewId() ?>">
                                                <input type="hidden" name="artworkId"
                                                    value="<?php echo $reviewWithCustomerInfo->getReview()->getArtworkId() ?>">
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
</div>

<script>
    // Notification function
    function showNotification(message, type = 'info') {
        // Create or get notification container
        let container = document.getElementById('notification-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; width: 350px; z-index: 9999;';
            document.body.appendChild(container);
        }

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.style.cssText = 'margin-bottom: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);';
        alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
        container.appendChild(alert);

        // Auto-dismiss after 4 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 4000);
    }

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Document ready, setting up review handlers');

        // Function to handle add review form submission
        function handleAddReviewSubmit(e) {
            console.log('Add review form submitted');
            e.preventDefault();

            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;

            console.log('Form action:', form.action);
            console.log('Form data:', new FormData(form));

            // Disable button during request
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            // Prepare form data
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If not JSON, get text to see what's wrong
                        return response.text().then(text => {
                            console.log('Non-JSON response:', text);
                            throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                        });
                    }
                })
                .then(data => {
                    console.log('Success response:', data);

                    showNotification(data.message, 'success');

                    // Clear form and hide it on success
                    form.reset();
                    form.style.display = 'none';

                    // Show message that they already reviewed
                    const alreadyReviewedMsg = document.createElement('p');
                    alreadyReviewedMsg.className = 'text-muted';
                    alreadyReviewedMsg.textContent = 'You have already reviewed this artwork.';
                    form.parentNode.appendChild(alreadyReviewedMsg);

                    // Add the new review to the DOM
                    if (data.review) {
                        console.log('Adding new review to DOM');

                        // Create delete button HTML for admin users
                        let deleteButtonHtml = '';
                        if (data.isAdmin) {
                            deleteButtonHtml = `
                        <div>
                            <form class="delete-review-form" method="POST" action="/reviews/${data.review.reviewId}/delete">
                                <input type="hidden" name="reviewId" value="${data.review.reviewId}">
                                <input type="hidden" name="artworkId" value="${data.review.artworkId}">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    `;
                        }

                        const reviewHtml = `
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        ${data.review.customerName} - ${data.review.customerLocation}
                                    </h6>
                                    <div class="mb-2">
                                        <strong>Rating: ${data.review.rating}/5</strong>
                                    </div>
                                    <p class="card-text">${data.review.comment.replace(/\n/g, '<br>')}</p>
                                    <small class="text-muted">
                                        ${new Date(data.review.reviewDate).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
                                    </small>
                                </div>
                                ${deleteButtonHtml}
                            </div>
                        </div>
                    </div>
                `;

                        // Check if there are existing reviews
                        const reviewsContainer = document.getElementById('reviews-container');
                        const noReviewsMsg = reviewsContainer.querySelector('p.text-muted');

                        if (noReviewsMsg && noReviewsMsg.textContent.includes('No reviews yet')) {
                            // Replace "no reviews" message with the new review
                            noReviewsMsg.remove();
                            reviewsContainer.insertAdjacentHTML('afterbegin', reviewHtml);
                        } else {
                            // Add to the beginning of existing reviews
                            reviewsContainer.insertAdjacentHTML('afterbegin', reviewHtml);
                        }
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                    showNotification('An error occurred while adding your review.', 'danger');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                });
        }

        // AJAX handling for add review form
        const addReviewForm = document.getElementById('add-review-form');
        if (addReviewForm) {
            addReviewForm.addEventListener('submit', handleAddReviewSubmit);
        }

        // AJAX handling for delete review forms
        document.addEventListener('submit', function (e) {
            if (e.target.classList.contains('delete-review-form')) {
                if (!confirm('Are you sure you want to delete this review?')) {
                    e.preventDefault();
                    return;
                }

                console.log('Delete review form submitted');
                e.preventDefault();

                const form = e.target;
                const reviewCard = form.closest('.card');
                const deleteBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = deleteBtn.textContent;

                console.log('Delete form action:', form.action);

                // Disable button during request
                deleteBtn.disabled = true;
                deleteBtn.textContent = 'Deleting...';

                // Prepare form data
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        console.log('Delete response status:', response.status);

                        // Check if response is JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            // If not JSON, get text to see what's wrong
                            return response.text().then(text => {
                                console.log('Delete non-JSON response:', text);
                                throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                            });
                        }
                    })
                    .then(data => {
                        console.log('Delete success response:', data);

                        showNotification(data.message, 'success');

                        // Fade out and remove the review card
                        reviewCard.style.transition = 'opacity 0.3s';
                        reviewCard.style.opacity = '0';

                        setTimeout(() => {
                            reviewCard.remove();

                            // Check if there are no more reviews
                            const remainingCards = document.querySelectorAll('#reviews-container .card');
                            const reviewsContainer = document.getElementById('reviews-container');
                            if (remainingCards.length === 0) {
                                reviewsContainer.innerHTML = '<p class="text-muted">No reviews yet. Be the first to review this artwork!</p>';
                            }

                            // Show the add review form again (if user is logged in and deleted their own review)
                            const textElements = document.querySelectorAll('p.text-muted');
                            textElements.forEach(element => {
                                if (element.textContent.includes('You have already reviewed this artwork')) {
                                    element.remove();
                                }
                            });

                            // Show the add review form if it was hidden
                            const addReviewForm = document.getElementById('add-review-form');
                            if (addReviewForm && addReviewForm.style.display === 'none') {
                                addReviewForm.style.display = 'block';
                            }

                            // If there's no form (because user had already reviewed), create one
                            if (!addReviewForm) {
                                // Check if user is logged in (we can tell by looking for login message)
                                const loginMessage = document.querySelector('p.text-muted');
                                const isLoggedIn = !loginMessage || !loginMessage.textContent.includes('Please log in to leave a review');

                                if (isLoggedIn) {
                                    // Create the review form
                                    const formHtml = `
                                    <form id="add-review-form" method="POST" action="/reviews/add" class="mb-3">
                                        <input type="hidden" name="artworkId" value="<?= $artwork->getArtworkId() ?>">

                                        <label for="rating">Rating (1-5):</label>
                                        <div class="star-rating text-start">
                                            <input type="radio" name="rating" id="star5" value="5" required>
                                            <label for="star5" title="5 stars">★</label>
                                            <input type="radio" name="rating" id="star4" value="4">
                                            <label for="star4" title="4 stars">★</label>
                                            <input type="radio" name="rating" id="star3" value="3">
                                            <label for="star3" title="3 stars">★</label>
                                            <input type="radio" name="rating" id="star2" value="2">
                                            <label for="star2" title="2 stars">★</label>
                                            <input type="radio" name="rating" id="star1" value="1">
                                            <label for="star1" title="1 star">★</label>
                                        </div>
                                        <label for="comment">Comment:</label>
                                        <textarea name="comment" required class="form-control mb-2"></textarea>

                                        <button type="submit" class="btn btn-success">Submit Review</button>
                                    </form>
                            `      ;

                                    // Find where to insert the form (before the reviews section)
                                    const reviewsContainer = document.getElementById('reviews-container');
                                    if (reviewsContainer) {
                                        reviewsContainer.insertAdjacentHTML('beforebegin', formHtml);

                                        // Re-attach event listener to the new form
                                        const newForm = document.getElementById('add-review-form');
                                        if (newForm) {
                                            newForm.addEventListener('submit', handleAddReviewSubmit);
                                        }
                                    }
                                }
                            }
                        }, 300);
                    })
                    .catch(error => {
                        console.log('Delete error:', error);
                        showNotification('An error occurred while deleting the review.', 'danger');
                    })
                    .finally(() => {
                        deleteBtn.disabled = false;
                        deleteBtn.textContent = originalBtnText;
                    });
            }
        });

        // Handle accordion arrow rotation for general museum information
        const generalCollapse = document.getElementById('generalCollapse');
        const generalArrow = document.getElementById('generalArrow');
        if (generalCollapse && generalArrow) {
            generalCollapse.addEventListener('show.bs.collapse', function () {
                generalArrow.textContent = '▲';
            });

            generalCollapse.addEventListener('hide.bs.collapse', function () {
                generalArrow.textContent = '▼';
            });
        }

        // Handle accordion arrow rotation for location museum information
        const locationCollapse = document.getElementById('locationCollapse');
        const locationArrow = document.getElementById('locationArrow');
        if (locationCollapse && locationArrow) {
            locationCollapse.addEventListener('show.bs.collapse', function () {
                locationArrow.textContent = '▲';
            });

            locationCollapse.addEventListener('hide.bs.collapse', function () {
                locationArrow.textContent = '▼';
            });
        }
    });
</script>
</script>