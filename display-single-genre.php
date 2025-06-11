<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/navbar.php";

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$db = new Database();
$genreRepository = new GenreRepository ($db);
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

  <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

  <div class="container mt-3">
    <div class="row">
      <!-- Displays genre image -->
      <?php 
      require_once dirname(__DIR__) . "/src/components/find_image_ref.php";
      $imagePath = "/assets/images/genres/square-medium/" . $genre->getGenreId() . ".jpg";
      $placeholderPath = "/assets/placeholder/genres/square-medium/placeholder.svg";
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
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
      <?php 
        require_once dirname(__DIR__) . "/src/components/artwork-card-list.php";
        renderArtworkCardList($artworks);
      ?>
    </div>
  </div>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>

</html>