<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/navbar.php";
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/router/router.php";
require_once dirname(__DIR__) . "/src/components/find-image-ref.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['customerId'])) {
    header("Location: error.php?error=notLoggedIn");
    exit;
}

$db = new Database();
$artworkRepository = new ArtworkRepository($db);
$artistRepository = new ArtistRepository($db);

// Initialize variables for messages
$message = '';
$messageType = '';

// Get favorites from session
$artworkFavorites = $_SESSION['favoriteArtworks'] ?? [];
$artistFavorites = $_SESSION['favoriteArtists'] ?? [];
$artworks = [];
$artists = [];

// Load favorite artworks
if (!empty($artworkFavorites)) {
    foreach ($artworkFavorites as $artworkId) {
        try {
            $artwork = $artworkRepository->findById($artworkId);
            $artist = $artistRepository->getArtistById($artwork->getArtistId());
            $artworks[] = ['artwork' => $artwork, 'artist' => $artist];
        } catch (Exception $e) {
            // Remove invalid artwork from favorites
            if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                unset($_SESSION['favoriteArtworks'][$key]);
                $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks']);
            }
            $message = "Some artworks were removed from favorites because they are no longer available.";
            $messageType = "warning";
        }
    }
}

// Load favorite artists
if (!empty($artistFavorites)) {
    foreach ($artistFavorites as $artistId) {
        try {
            $artist = $artistRepository->getArtistById($artistId);
            $artists[] = $artist;
        } catch (Exception $e) {
            // Remove invalid artist from favorites
            if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                unset($_SESSION['favoriteArtists'][$key]);
                $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists']);
            }
            $message = "Some artists were removed from favorites because they are no longer available.";
            $messageType = "warning";
        }
    }
}

// Handle remove artwork from favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_favorites') {
    $artworkId = (int)$_POST['artworkId'];
    if (($key = array_search($artworkId, $artworkFavorites)) !== false) {
        unset($_SESSION['favoriteArtworks'][$key]);
        $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks']); // Re-index array
        $message = "Artwork removed from favorites successfully.";
        $messageType = "success";
        
        // Redirect to prevent resubmission
        header("Location: /favorites");
        exit();
    }
}

// Handle remove artist from favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_artist_from_favorites') {
    $artistId = (int)$_POST['artistId'];
    if (($key = array_search($artistId, $artistFavorites)) !== false) {
        unset($_SESSION['favoriteArtists'][$key]);
        $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists']); // Re-index array
        $message = "Artist removed from favorites successfully.";
        $messageType = "success";
        
        // Redirect to prevent resubmission
        header("Location: /favorites");
        exit();
    }
}
?>

<body class="container">
    <br>
    <h1>My Favorites</h1>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="container mt-3">
        <!-- Artists Section -->
        <div class="mb-5">
            <h2>Favorite Artists</h2>
            <?php if (empty($artists)): ?>
                <div class="alert alert-info" role="alert">
                    <p class="mb-0">You haven't added any artists to your favorites yet. Browse our <a href="/artists.php" class="alert-link">artist collection</a> to find artists you love!</p>
                </div>
            <?php else: ?>
                <p class="text-muted mb-4">You have <?php echo count($artists) ?> artist<?php echo count($artists) > 1 ? 's' : '' ?> in your favorites.</p>
                
                <div class="row">
                    <?php foreach ($artists as $artist): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card">
                                <?php 
                                // For artist images
                                $imagePath = "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
                                $placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
                                $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                                ?>
                                <img src="<?php echo $correctImagePath ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>"
                                     style="height: 250px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?php echo route('artists', ['id' => $artist->getArtistId()]) ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>
                                        </a>
                                    </h5>
                                    <p class="card-text">
                                        <?php echo htmlspecialchars($artist->getNationality()) ?>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted"><?php echo htmlspecialchars($artist->getYearOfBirth()) ?><?php if ($artist->getYearOfDeath()) echo " - " . htmlspecialchars($artist->getYearOfDeath()) ?></small>
                                    </p>
                                    
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="remove_artist_from_favorites">
                                        <input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('Remove this artist from your favorites?')">
                                            Remove from Favorites
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Artworks Section -->
        <div class="mb-5">
            <h2>Favorite Artworks</h2>
            <?php if (empty($artworks)): ?>
                <div class="alert alert-info" role="alert">
                    <p class="mb-0">You haven't added any artworks to your favorites yet. Browse our <a href="/artworks.php" class="alert-link">artwork collection</a> to find pieces you love!</p>
                </div>
            <?php else: ?>
                <p class="text-muted mb-4">You have <?php echo count($artworks) ?> artwork<?php echo count($artworks) > 1 ? 's' : '' ?> in your favorites.</p>
                
                <div class="row">
                    <?php foreach ($artworks as $item):
                        $artwork = $item['artwork']; $artist = $item['artist']; ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card">
                                <?php 
                                // For artwork images
                                $imageFileName = $artwork->getImageFileName();
                                $imagePath = "/assets/images/works/medium/" . $imageFileName . ".jpg";
                                $placeholderPath = "/assets/placeholder/works/medium/placeholder.svg";
                                $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                                ?>
                                <img src="<?php echo $correctImagePath ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>"
                                     style="height: 250px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?php echo route('artworks', ['id' => $artwork->getArtworkId()]) ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($artwork->getTitle()) ?>
                                        </a>
                                    </h5>
                                    <p class="card-text">
                                        by <a href="<?php echo route('artists', ['id' => $artist->getArtistId()]) ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>
                                        </a>
                                    </p>
                                    <?php if ($artwork->getYearOfWork()): ?>
                                        <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($artwork->getYearOfWork()) ?></small></p>
                                    <?php endif; ?>
                                    
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="remove_from_favorites">
                                        <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('Remove this artwork from your favorites?')">
                                            Remove from Favorites
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
