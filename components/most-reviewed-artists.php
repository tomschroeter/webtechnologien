<?php
/** 
 * @component-type smart  
 * Fetches its own data and renders artist cards 
 */
require_once dirname(__DIR__) . "/classes/Artist.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";

$artistRepository = new ArtistRepository(new Database());
$mostReviewedArtists = $artistRepository->findMostReviewed(3);
?>

<div class="container" style="max-width: 500px;">
  <div class="row justify-content-center g-3">
    <?php foreach ($mostReviewedArtists as $index => $combined):
      $artist = $combined->getArtist();
      $artistName = $artist->getFullName();
      $reviewCount = $combined->getReviewCount();
      $artistId = $artist->getArtistId();
      $position = $index + 1;
      $imagePath = "/assets/images/artists/square-medium/" . $artistId . ".jpg";
    ?>
      <div class="col-12 col-sm-6 col-md-4 d-flex justify-content-center">
        <a href="artists/<?= $artistId ?>" class="link-no-underline">
          <div class="card" style="width: 150px; min-height: 260px;">
            <img src="<?= $imagePath ?>" class="card-img-top" alt="Artist Image" 
                 style="height: 150px; object-fit: cover;">
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
