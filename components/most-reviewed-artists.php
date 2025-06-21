<?php
/**
 * @component-type smart
 * 
 * This component fetches and renders the top 3 most-reviewed artists as cards.
 * Each card includes:
 * - Artist image
 * - Full name
 * - Total review count
 * - Rank position
 */

// Load required model and repository classes
require_once dirname(__DIR__) . "/classes/Artist.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";

// Instantiate repository with a database connection
$db = new Database();
$artistRepository = new ArtistRepository($db);

// Fetch top 3 artists ranked by number of reviews
$mostReviewedArtists = $artistRepository->getMostReviewed(3);
?>

<div class="container" style="max-width: 500px;">
  <div class="row justify-content-center g-3">

    <?php foreach ($mostReviewedArtists as $index => $combined):
      // Extract individual artist and associated review count
      $artist = $combined->getArtist();
      $artistName = $artist->getFullName();
      $artistId = $artist->getArtistId();
      $reviewCount = $combined->getReviewCount();

      // Position in ranking (1-based index)
      $position = $index + 1;

      // Image path based on artist ID
      $imagePath = "/assets/images/artists/square-medium/" . $artistId . ".jpg";
      ?>

      <div class="col-12 col-sm-6 col-md-4 d-flex justify-content-center">
        <!-- Entire card is clickable and links to artist detail page -->
        <a href="artists/<?= $artistId ?>" class="link-no-underline">
          <div class="card" style="width: 150px; min-height: 260px;">

            <!-- Artist image with fixed height and cover cropping -->
            <img src="<?= $imagePath ?>" class="card-img-top" alt="Artist Image"
              style="height: 150px; object-fit: cover;">

            <!-- Card body with artist name and review count -->
            <div class="card-body p-2">
              <h6 class="card-title mb-1 link-underline-on-hover">
                <?= $position ?>.
                <span><?= htmlspecialchars($artistName) ?></span>
              </h6>
              <p class="card-text small mb-0">
                <?= $reviewCount ?> Reviews
              </p>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>