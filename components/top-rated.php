<?php
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/dtos/ArtworkWithRatingAndArtistName.php";
require_once dirname(__DIR__) . "/components/fix-file-path.php";
require_once dirname(__DIR__) . "/components/find-image-ref.php";
require_once dirname(__DIR__) . "/components/render-stars.php";

$db = new Database();
$artworkRepository = new ArtworkRepository($db);
$artworksWithRating = $artworkRepository->getTopRatedArtworks();
?>

<div style="max-width: 500px; margin: auto;">
  <div style="display: flex; gap: 10px; justify-content: center;">
    <?php if ($artworksWithRating): ?>
      <?php foreach ($artworksWithRating as $index => $combined):
        // Get relevant data
        $artworkTitle = $combined->getArtwork()->getTitle();
        $artworkId = $combined->getArtwork()->getArtworkId();
        $artistId = $combined->getArtwork()->getArtistId();
        $artistName = $combined->getArtistName();
        $reviewCount = $combined->getReviewCount();

        // Render rating as stars
        $rating = $combined->getRating();
        $stars = renderStars($rating);

        // Get correct image file path
        $imagePath = "/assets/images/works/square-medium/" . $combined->getArtwork()->getImageFileName() . ".jpg";
        $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        ?>
        <!-- Display Artworks -->
        <div class="col-md-4 mb-4">
          <div class="card" style="width: 150px; min-height: 260px;">
            <a href="artworks/<?php echo $artworkId ?>">
              <img class="card-img-top" style="height:150px; width:150px; object-fit:cover;"
                src="<?php echo $correctImagePath ?>" alt="">
            </a>
            <div class="card-body p-2">
              <h4 class="card-title h6 mb-1">
                <a href="artworks/<?php echo $artworkId ?>"
                  style="color: black;"><?php echo htmlspecialchars($artworkTitle) ?></a>
              </h4>
              <p class="card-text small mb-1">
                <a href="artists/<?php echo $artistId ?>"
                  style="color: black;"><?php echo htmlspecialchars($artistName) ?></a>
              </p>
              <p class="card-text text-warning" style="font-size: 0.9rem;"><?php echo $stars?>
                <span class="text-dark" style="font-size: 0.7rem; vertical-align: 1px;">(<?= $reviewCount ?>)</span>
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