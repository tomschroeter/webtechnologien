<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/navbar.php";
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/router/router.php";

session_start();

$db = new Database();
$artworkRepository = new ArtworkRepository($db);
$artistRepository = new ArtistRepository($db);

// Initialize variables for messages
$message = '';
$messageType = '';

// Get favorites from session
$favorites = $_SESSION['favorites'] ?? [];
$artworks = [];

if (!empty($favorites)) {
    foreach ($favorites as $artworkId) {
        try {
            $artwork = $artworkRepository->findById($artworkId);
            $artist = $artistRepository->getArtistById($artwork->getArtistId());
            $artworks[] = ['artwork' => $artwork, 'artist' => $artist];
        } catch (Exception $e) {
            // Remove invalid artwork from favorites
            if (($key = array_search($artworkId, $_SESSION['favorites'])) !== false) {
                unset($_SESSION['favorites'][$key]);
                $_SESSION['favorites'] = array_values($_SESSION['favorites']);
            }
            $message = "Some artworks were removed from your favorites because they are no longer available.";
            $messageType = "warning";
        }
    }
}

// Handle remove from favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_favorites') {
    $artworkId = (int)$_POST['artworkId'];
    if (($key = array_search($artworkId, $favorites)) !== false) {
        unset($_SESSION['favorites'][$key]);
        $_SESSION['favorites'] = array_values($_SESSION['favorites']); // Re-index array
        $message = "Artwork removed from favorites successfully.";
        $messageType = "success";
        
        // Redirect to prevent resubmission - using a safer redirect method
        header("Location: /favorites.php");
        exit();
    }
}
?>

<body class="container">
    <br>
    <h1>My Favorite Artworks</h1>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="container mt-3">
        <?php if (empty($artworks)): ?>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">No favorites yet!</h4>
                <p>You haven't added any artworks to your favorites list yet.</p>
                <hr>
                <p class="mb-0">Browse our <a href="/artworks.php" class="alert-link">artwork collection</a> to find pieces you love!</p>
            </div>
        <?php else: ?>
            <p class="text-muted mb-4">You have <?php echo count($artworks) ?> artwork<?php echo count($artworks) > 1 ? 's' : '' ?> in your favorites.</p>
            
            <div class="row">
                <?php foreach ($artworks as $item):
                    $artwork = $item['artwork']; $artist = $item['artist']; ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <?php 
                            // Fix image filename padding
                            $imageFileName = $artwork->getImageFileName();
                            if (strlen($imageFileName) < 6) {
                                $imageFileName = '0' . $imageFileName;
                            }
                            
                            $imagePath = "/assets/images/works/medium/" . $imageFileName . ".jpg";
                            $placeholderPath = "/assets/placeholder/works/medium/placeholder.svg";
                            $correctImagePath = file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath) ? $imagePath : $placeholderPath;
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
                                
                                <a href="<?php echo route('artworks', ['id' => $artwork->getArtworkId()]) ?>" 
                                   class="btn btn-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once 'bootstrap.php'; ?>
</body>
</html>
