<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/navbar.php";

$db = new Database();
$genreRepository = new GenreRepository($db);
$artworkRepository = new ArtworkRepository($db);

// Checks if id is set correctly in URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $genreId = $_GET['id'];
} else {
  header("Location: /error.php?error=invalidParam");
  exit();
}

// Checks if genre exists in database
try {
  $genre = $genreRepository->getGenreById($genreId);
  $artworks = $artworkRepository->getArtworksByGenre($genreId);
} catch (Exception $e) {
  header("Location: /error.php?error=invalidID&type=genre");
  exit();
}
?>

<body class="container">
  <br>
  <h1><?php echo $genre->getGenreName() ?></h1>
  <div class="container mt-3">
    <div class="row">
      <!-- Displays genre image -->
      <?php $imagePath = "/assets/images/genres/square-medium/" . $genre->getGenreId() . ".jpg";
      $placeholderPath = "/assets/placeholder/genres/square-medium/placeholder.svg";
      if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
        $correctImagePath = $imagePath;
      } else {
        $correctImagePath = $placeholderPath;
      }
      ?>
      <img src="<?php echo $correctImagePath ?>" alt="Bild von <?php echo $genre->getGenreName() ?>">
      <div class="col">
        <?php echo $genre->getDescription(); ?>
        <div class="mt-2">
          <b>Learn more:</b> <a href=<?php echo $genre->getLink() ?>>Wikipedia</a>
        </div>
      </div>
    </div>
    <h2 class="mt-5">Artworks for <?php echo $genre->getGenreName() ?></h2>
    <div class="row mt-4">
      <?php foreach ($artworks as $artwork): ?>
        <!-- Creates new URL to display single artwork --->
      <?php $artworkLink = route('artworks', ['id' => $artwork->getArtworkId()]) ?>
      <!-- List of artworks -->
        <div class="col-md-3 mb-4">
          <!-- Artwork card including image, name and view button --->
        <div class="card h-100">
          <!-- Checks if artworks' image exists -->
            <?php $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
            $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
              $correctImagePath = $imagePath;
            } else {
              $correctImagePath = $placeholderPath;
            }
            ?>
            <a href="<?php echo $artworkLink ?>" target="_blank">
              <img src="<?php echo $correctImagePath ?>" class="card-img-top" alt="<?php echo $artwork->getTitle() ?>">
            </a>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title text-center">
                <a href="<?php echo $artworkLink ?>" target="_blank"
                  class="text-body"><?php echo $artwork->getTitle() ?></a>
              </h5>
              <a href="<?php echo $artworkLink ?>" target="_blank" class="btn btn-primary mt-auto">View</a>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
  <?php require_once 'bootstrap.php'; ?>
</body>

</html>