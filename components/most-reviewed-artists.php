<?php
require_once dirname(__DIR__) . "/classes/Artist.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";

$artistRepository = new ArtistRepository(new Database());
$mostReviewedArtists = $artistRepository->findMostReviewed(3);
?>

<div style="max-width: 500px; margin: auto;">
  <div style="display: flex; gap: 10px; justify-content: center;">
    <?php foreach ($mostReviewedArtists as $index => $combined):
      // Get relevant data
      $artist = $combined->getArtist();
      $artistName = $artist->getFullName();
      $reviewCount = $combined->getReviewCount();
      $artistId = $artist->getArtistId();
      $position = $index + 1;
      $imagePath = "/assets/images/artists/square-medium/" . $artistId . ".jpg";
      ?>
      <!-- Display Artists -->
      <div class="col-md-4 mb-4">
        <a href="artists/<?= $artistId ?>" style="color: black;">
          <div class="card" style="width: 150px; min-height: 260px;">
            <img class="card-img-top" style="height: 150px; width: 150px; object-fit: cover;" src="<?= $imagePath ?>"
              alt="Artist Image">
            <div class="card-body p-2">
              <h4 class="card-title h6 mb-1">
                <?php echo $position ?>.
                <?php echo htmlspecialchars($artistName) ?>
              </h4>
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