<?php
require_once dirname(__DIR__)."/classes/Artist.php";
require_once dirname(__DIR__)."/repositories/ArtistRepository.php";
?>

<div style="max-width: 500px; margin: auto;">
  <div style="display: flex; gap: 10px; justify-content: center;">
    <?php
    $artistRepository = new ArtistRepository(new Database());
    $mostReviewedArtists = $artistRepository->findMostReviewed(3);
    ?>

    <?php foreach ($mostReviewedArtists as $index => $combined):
      $artist = $combined->getArtist();
      $artistName = $artist->getFirstName() . ' ' . $artist->getLastName();
      $reviewCount = $combined->getReviewCount();
      $artistId = $artist->getArtistId();
      $position = $index + 1;
      $imagePath = "/assets/images/artists/square-medium/" . $artistId . ".jpg";
    ?>
      <div class="col-md-4 mb-4">
        <div class="card" style="width: 150px; min-height: 240px;">
          <img class="card-img-top" style="height: 150px; width: 150px; object-fit: cover;" src="<?= $imagePath ?>" alt="">
          <div class="card-body p-2">
            <h4 class="card-title h6 mb-1">
              <?= $position ?>.
              <a href="artists/<?= $artistId ?>" style="color: black;">
                <?= htmlspecialchars($artistName) ?>
              </a>
            </h4>
            <p class="card-text small mb-0">
              <?= $reviewCount ?> Reviews
            </p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>