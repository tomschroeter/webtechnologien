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
$artworksWithRating = $artworkRepository->getTopRatedArtworks();
?>

<div style="max-width: 500px; margin: auto;">
  <div class="row justify-content-center g-3">
    <?php if ($artworksWithRating): ?>
      <?php foreach ($artworksWithRating as $index => $combined):
        // Get relevant data
        $artwork = $combined->getArtwork();
        $artworkTitle = htmlspecialchars($artwork->getTitle());
        $artworkId = $artwork->getArtworkId();
        $artistId = $artwork->getArtistId();
        $artistName = htmlspecialchars($combined->getArtistName());
        $reviewCount = $combined->getReviewCount();

        // Render rating as stars
        $rating = $combined->getRating();
        $stars = renderStars($rating);
        $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
        $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        ?>
        <div class="col-12 col-sm-6 col-md-4 d-flex justify-content-center">
          <div class="card" style="width: 150px; min-height: 260px;">
            <a href="artworks/<?= $artworkId ?>" class="d-block link-no-underline">
              <img src="<?= $correctImagePath ?>" alt="<?= $artworkTitle ?>" class="card-img-top"
                style="height: 150px; width: 150px; object-fit: cover;">
            </a>
            <div class="card-body p-2">
              <!-- Artwork Title Link mit Hover -->
              <h4 class="card-title h6 mb-1">
                <a href="artworks/<?= $artworkId ?>" class="link-underline-on-hover" style="color: inherit;">
                  <?= $artworkTitle ?>
                </a>
              </h4>

              <!-- Artist Name Link mit Hover -->
              <p class="card-text small mb-1">
                <a href="artists/<?= $artistId ?>" class="link-underline-on-hover" style="color: inherit;">
                  <?= $artistName ?>
                </a>
              </p>
              <p class="card-text text-warning" style="font-size: 0.9rem;">
                <?= $stars ?>
                <span class="text-muted" style="font-size: 0.7rem; vertical-align: 1px;">(<?= $reviewCount ?>)</span>
              </p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div>No results found</div>
    <?php endif; ?>
  </div>
</div>