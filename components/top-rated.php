<?php
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/dtos/ArtworkWithRatingAndArtistName.php";
require_once dirname(__DIR__) . "/components/fix-file-path.php";
require_once dirname(__DIR__) . "/components/find-image-ref.php";
require_once dirname(__DIR__) . "/components/render-stars.php";
?>

<div style="max-width: 500px; margin: auto;">
  <div style="display: flex; gap: 10px; justify-content: center;">
    <?php
    $db = new Database();
    $artworkRepository = new ArtworkRepository($db);
    $artworksWithRating = $artworkRepository->getTopRatedArtworks();
    ?>

    <?php if ($artworksWithRating): ?>
      <?php foreach ($artworksWithRating as $index => $combined): 
        $artworkTitle = $combined->getArtwork()->getTitle();
        $artworkId = $combined->getArtwork()->getArtworkId();
        $artistId = $combined->getArtwork()->getArtistId();
        $artistName = $combined->getArtistName();
        $rating = $combined->getRating();
        $imagePath = "/assets/images/works/square-medium/" . $combined->getArtwork()->getImageFileName() . ".jpg";
        $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        $stars = renderStars($rating);
      ?>
        <div class="col-md-4 mb-4">
          <div class="card" style="width: 150px; min-height: 260px;">
            <img class="card-img-top" style="height:150px; width:150px; object-fit:cover;" src="<?= $correctImagePath ?>" alt="">
            <div class="card-body p-2">
              <h4 class="card-title h6 mb-1">
                <a href="artworks/<?= $artworkId ?>" style="color: black;"><?= htmlspecialchars($artworkTitle) ?></a>
              </h4>
              <p class="card-text small mb-1">
                <a href="artists/<?= $artistId ?>" style="color: black;"><?= htmlspecialchars($artistName) ?></a>
              </p>
              <p class="card-text text-warning" style="font-size: 0.9rem;"><?= $stars ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div>No results found</div>
    <?php endif; ?>

  </div>
</div>
