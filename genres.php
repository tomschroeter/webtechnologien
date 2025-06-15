<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__)."/src/head.php";
require_once dirname(__DIR__)."/src/repositories/GenreRepository.php";
require_once dirname(__DIR__)."/src/navbar.php";
require_once dirname(__DIR__) . "/src/components/find-image-ref.php";

$genreRepository = new GenreRepository(new Database());

$genres = $genreRepository->getAllGenres();
?>

<body class="container">
  <h1 class="mt-3 mb-3">Genres</h1>
  <p class="text-muted">Found: <?php echo count($genres)?> genres</p>

  <!-- List to display all genres -->
  <ul class="list-group mb-5">
    <?php foreach ($genres as $genre): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="<?php echo route('genres', ['id' => $genre->getGenreId()]) ?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <span><?php echo $genre->getGenreName() ?></span>
          <!-- Checks if genre image exists -->
          <?php
$imagePath =  "/assets/images/genres/square-thumbs/".$genre->getGenreId().".jpg";
$placeholderPath = "/assets/placeholder/genres/square-thumbs/placeholder.svg";
$correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
?>
          <img src="<?php echo $correctImagePath?>" alt="Genre image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
