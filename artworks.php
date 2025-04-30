<!DOCTYPE html>
<html lang="en">

<?php
    require_once dirname(__DIR__)."/src/head.php";
    require_once dirname(__DIR__)."/src/repositories/ArtworkRepository.php";
    require_once dirname(__DIR__)."/src/repositories/ArtistRepository.php";
    require_once dirname(__DIR__)."/src/navbar.php";

    $db = new Database();

    $artworkRepository = new ArtworkRepository($db);
    $artistRepository = new ArtistRepository($db);

    // Get sort parameters from URL
    $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'title';
    $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';

    // Validate sort parameters
    $validSortFields = ['title', 'artist', 'year'];
    if (!in_array($sortBy, $validSortFields)) {
    $sortBy = 'title';
    }

    $validSortOrders = ['asc', 'desc'];
    if (!in_array($sortOrder, $validSortOrders)) {
    $sortOrder = 'asc';
    }

    // Get artworks with the sort parameters
    $artworks = $artworkRepository->getAllArtworks($sortBy, $sortOrder);
?>

<body class="container">
  <!-- Form providing the ability to sort artworks -->
  <div class="d-flex align-items-center mt-3 mb-3">
    <h1 class="flex-grow-1 mb-0">Kunstwerke</h1>
    <div class="d-flex gap-2">

      <!-- Sort field selector -->
      <form method="get" class="mx-2">
        <!-- Preserve the current sort order when changing field -->
        <input type="hidden" name="order" value="<?php echo $sortOrder ?>">
        <select name="sort" onchange="this.form.submit()" class="form-select">
          <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''?>>Titel</option>
          <option value="artist" <?php echo $sortBy == 'artist' ? 'selected' : ''?>>Name des Künstlers</option>
          <option value="year" <?php echo $sortBy == 'year' ? 'selected' : ''?>>Jahr</option>
        </select>
      </form>

      <!-- Sort order selector -->
      <form method="get">
        <!-- Preserve the current sort field when changing order -->
        <input type="hidden" name="sort" value="<?php echo $sortBy ?>">
        <select name="order" onchange="this.form.submit()" class="form-select">
          <option value="asc" <?php echo $sortOrder == 'asc' ? 'selected' : ''?>>Aufsteigend</option>
          <option value="desc" <?php echo $sortOrder == 'desc' ? 'selected' : ''?>>Absteigend</option>
        </select>
      </form>

    </div>
  </div>
  
  <p class="text-muted">Gefunden: <?php echo count($artworks)?> Kunstwerke</p>
  
  <!-- List to display all artworks -->
  <ul class="list-group mb-5">
    <?php foreach ($artworks as $artwork): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="<?php echo route('artworks', ['id' => $artwork->getArtworkID()])?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <div>
            <h5 class="mb-1"><?php echo $artwork->getTitle()?></h5>
            <p class="mb-1">
              <?php 
                $artist = $artistRepository->getArtistById($artwork->getArtistID());
                echo $artist ? $artist->getFirstName() . ' ' . $artist->getLastName() : 'Unbekannter Künstler';
              ?>
              <?php if ($artwork->getYearOfWork()): ?>
                <span class="text-muted">(<?php echo $artwork->getYearOfWork()?>)</span>
              <?php endif; ?>
            </p>
          </div>
          <!-- Check if artwork image exists -->
          <?php $imagePath = "/assets/images/works/square-small/".$artwork->getImageFileName().".jpg";
            $placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg"; 

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
              $correctImagePath = $imagePath;
            } else {
              $correctImagePath = $placeholderPath;
            }
          ?>
          <img src="<?php echo $correctImagePath?>" alt="<?php echo $artwork->getTitle()?>" 
               style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php require_once 'bootstrap.php'; ?>
</body>
</html>
