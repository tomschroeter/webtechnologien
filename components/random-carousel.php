<?php
/**
 * @component-type smart
 * Fetches artworks on its own and displays them in a Bootstrap carousel.
 * Images are *not* fetched from the database since that could include missing images
 */

// Load all medium and large artwork image paths
$mediumArtworkDirectory = 'assets/images/works/square-medium';
$largeArtworkDirectory = 'assets/images/works/large';

$allArtworksAsMedium = glob($mediumArtworkDirectory . '/*.jpg');
$allArtworksAsLarge = glob($largeArtworkDirectory . '/*.jpg');

// Select 16 random medium images and one random large image
$randomArtworks = array_rand($allArtworksAsMedium, min(16, count($allArtworksAsMedium)));
$randomArtwork = $allArtworksAsLarge[array_rand($allArtworksAsLarge)];
?>

<div id="homeCarousel" class="carousel slide card mb-5" data-bs-ride="carousel" style="overflow-x: hidden;">
  <div class="carousel-inner">

    <!-- Slide 1: Grid of random artworks -->
    <div class="carousel-item active text-center p-5" style="min-height: 540px; background-color: #ffffff;">
      <h2 class="mb-4">Discover more than 300 Artworks from around the world</h2>
      <div class="d-flex justify-content-center flex-wrap">
        <?php foreach ((array) $randomArtworks as $artworkIndex): ?>
          <?php $imagePath = $allArtworksAsMedium[$artworkIndex]; ?>
          <div class="p-2">
            <img src="<?= $imagePath ?>" class="img-fluid rounded shadow-sm"
              style="width: 150px; height: 150px; object-fit: cover;" alt="Artwork">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Slide 2: Review and favorites -->
    <div class="carousel-item text-center p-5" style="min-height: 540px; background-color: #ffffff;">
      <h2 class="mb-4">Leave Reviews and Pick Your Favourites</h2>
      <div class="d-flex justify-content-center align-items-center flex-wrap">
        <div class="p-3">
          <img src="<?= $randomArtwork ?>" style="max-height: 300px; object-fit: cover;" alt="Highlighted Artwork">
          <h5 class="mt-2"><em>"Absolutely stunning piece!"</em></h5>
        </div>
        <div class="p-3">
          <img src="/assets/carousel-images/favorites.png" style="max-height: 300px;" alt="Favorites Screenshot">
          <h5 class="mt-2">Your personal favorites at a glance.</h5>
        </div>
      </div>
    </div>

    <!-- Slide 3: Learn more about artworks -->
    <div class="carousel-item text-center p-5" style="min-height: 540px; background-color: #ffffff;">
      <h2 class="mb-4">Learn More About Your Favorite Pieces</h2>
      <img src="/assets/carousel-images/artwork_page.png" style="max-height: 300px;" alt="Artwork Description"
        class="mx-4">
      <img src="/assets/carousel-images/artwork_page_2.png" style="max-height: 300px;" alt="Artwork Gallery">
      <h5 class="mt-3">Detailed descriptions, artist background, reviews, and more!</h5>
    </div>
  </div>

  <!-- Carousel navigation buttons -->
  <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev"
    style="left: -70px; filter: invert(100%)">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next"
    style="right: -70px; filter: invert(100%)">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>