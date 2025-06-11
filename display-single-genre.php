<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/navbar.php";

session_start();

$_SESSION['customerId'] = 1; // TEMP: simulate logged-in user
$_SESSION['isAdmin'] = true; // TEMP: simulate admin privileges

$db = new Database();
$genreRepository = new GenreRepository($db);
$artworkRepository = new ArtworkRepository($db);

// Handle Add Artist Favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['favoriteArtworks'])) {
            $_SESSION['favoriteArtworks'] = [];
        }
        
        $artworkId = (int)$_POST['artworkId'];
        
        if ($_POST['action'] === 'add_to_favorites') {
            if (!in_array($artworkId, $_SESSION['favoriteArtworks'])) {
                $_SESSION['favoriteArtworks'][] = $artworkId;
                $message = "Artwork added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is already in your favorites.";
                $messageType = "info";
            }
		}
	} catch (Exception $e) {
        $message = "Error updating favorites. Please try again.";
        $messageType = "danger";
    }
}

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
      <?php 
        require_once __DIR__ . '/components/artwork-card-list.php';
        renderArtworkCardList($artworks);
      ?>
    </div>
  </div>
  <?php require_once 'bootstrap.php'; ?>
</body>

</html>