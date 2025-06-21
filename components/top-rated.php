<?php
/**
 * @component-type smart
 * Fetches its own data and renders top rated artworks
 */

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/dtos/ArtworkWithRatingAndArtistName.php";
require_once __DIR__ . "/fix-file-path.php";
require_once __DIR__ . "/find-image-ref.php";
require_once __DIR__ . "/render-stars.php";

$db = new Database();
$artworkRepository = new ArtworkRepository($db);

// Fetch the top rated artworks along with their ratings and artist names
$artworksWithRating = $artworkRepository->getTopRatedArtworks();
?>

<div style="max-width: 500px; margin: auto;">
  <div class="row justify-content-center g-3">
    <?php if ($artworksWithRating): ?>
      <?php foreach ($artworksWithRating as $index => $combined):
        // Extract relevant data
        $artwork = $combined->getArtwork();
        $artworkTitle = htmlspecialchars($artwork->getTitle());
        $artworkId = $artwork->getArtworkId();
        $artistId = $artwork->getArtistId();
        $artistName = htmlspecialchars($combined->getArtistFullName());
        $reviewCount = $combined->getReviewCount();

        // Get the rating and render it as stars
        $rating = $combined->getRating();
        $stars = renderStars($rating);

        // Determine image path and fallback to placeholder if image missing
        $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
        $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        ?>

        <div class="col-12 col-sm-6 col-md-4 d-flex justify-content-center">
          <div class="card" style="width: 150px; min-height: 260px;">
            <!-- Link to artwork page wrapping the image -->
            <a href="artworks/<?= $artworkId ?>" class="d-block link-no-underline">
              <img src="<?= $correctImagePath ?>" alt="<?= $artworkTitle ?>" class="card-img-top"
                style="height: 150px; width: 150px; object-fit: cover;">
            </a>

            <div class="card-body p-2">
              <!-- Artwork title with link to artwork page -->
              <h4 class="card-title h6 mb-1">
                <a href="artworks/<?= $artworkId ?>" class="link-underline-on-hover" style="color: inherit;">
                  <?= $artworkTitle ?>
                </a>
              </h4>

              <!-- Artist name with link to artist page -->
              <p class="card-text small mb-1">
                <a href="artists/<?= $artistId ?>" class="link-underline-on-hover" style="color: inherit;">
                  <?= $artistName ?>
                </a>
              </p>

              <!-- Star rating with number of reviews -->
              <p class="card-text text-warning" style="font-size: 0.9rem;">
                <?= $stars ?>
                <span class="text-muted" style="font-size: 0.7rem; vertical-align: 1px;">(<?= $reviewCount ?>)</span>
              </p>
            </div>
          </div>
        </div>

      <?php endforeach; ?>
    <?php else: ?>
      <!-- Display message if no artworks found -->
      <div>No results found</div>
    <?php endif; ?>
  </div>
</div>