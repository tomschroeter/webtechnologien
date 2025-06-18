<?php
/**
 * @component-type smart
 * Fetches artworks on it's own
 */


// Load all images
$mediumArtworkDirectory = 'assets/images/works/square-medium';
$largeArtworkDirectory = 'assets/images/works/large';
$allArtworksAsMedium = glob($mediumArtworkDirectory . '/*.jpg', GLOB_BRACE);
$allArtworksAsLarge = glob($largeArtworkDirectory . '/*.jpg', GLOB_BRACE);

// Pick random images for display
$randomArtworks = array_rand($allArtworksAsMedium, 16);
$randomArtwork = $allArtworksAsLarge[array_rand($allArtworksAsLarge)];
?>

<div id="homeCarousel" class="carousel slide mb-5" data-ride="carousel" style="overflow-x: hidden;">
  <div class="carousel-inner">

    <!-- Slide 1 -->
    <div class="carousel-item active text-center p-5" style="min-height: 540px; background-color: #f8f9fa;">
      <h2 class="mb-4">Discover more than 300 Artworks from around the world</h2>
      <div class="d-flex justify-content-center flex-wrap">
        <?php foreach ($randomArtworks as $artwork):
          $imagePath = $allArtworksAsMedium[$artwork];
          ?>
          <div class="p-2">
            <img src="<?= $imagePath ?>" class="img-fluid rounded shadow-sm"
              style="width: 150px; height: 150px; object-fit: cover;" alt="Artwork">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="carousel-item text-center p-5" style="min-height: 540px; background-color: #f8f9fa;">
      <h2 class="mb-4">Leave Reviews and Pick Your Favourites</h2>
      <div class="d-flex justify-content-center align-items-center flex-wrap">
        <div class="p-3">
          <img src="<?= $randomArtwork ?>" style="max-height: 300px; object-fit: cover;" alt="Random Artwork">
          <h5 class="mt-2"><em>"Absolutely stunning piece!"</em></h5>
        </div>
        <div class="p-3">
          <img src="/assets/carousel-images/favorites.png" style="max-height: 300px;" alt="Favorites Screenshot">
          <h5 class="mt-2">Your personal favorites at a glance.</h5>
        </div>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="carousel-item text-center p-5" style="min-height: 540px; background-color: #f8f9fa;">
      <h2 class="mb-4">Learn More About Your Favorite Pieces</h2>
      <img src="/assets/carousel-images/artwork_page.png" style="max-height: 300px;" alt="Artwork Description"
        class="mx-4">
      <img src="/assets/carousel-images/artwork_page_2.png" style="max-height: 300px;" alt="Artwork Gallery">
      <h5 class="mt-3">Detailed descriptions, artist background, reviews, and more!</h5>
    </div>
  </div>

  <!-- Control buttons -->
  <a class="carousel-control-prev" style="left: -70px; filter: invert(100%)" href="#homeCarousel" role="button"
    data-slide="prev" data-interval="500">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" style="right: -70px; filter: invert(100%)" href="#homeCarousel" role="button"
    data-slide="next" data-interval="500">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>