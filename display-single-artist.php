<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/navbar.php";
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/router/router.php";

session_start();

$_SESSION['customerId'] = 1; // TEMP: simulate logged-in user
$_SESSION['isAdmin'] = true; // TEMP: simulate admin privileges

$db = new Database();
$artistRepository = new ArtistRepository($db);
$artworkRepository = new ArtworkRepository($db);

// Handle Add/Remove Artist Favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['favoriteArtists'])) {
            $_SESSION['favoriteArtists'] = [];
        }
        
        $artistId = (int)$_POST['artistId'];
        
        if ($_POST['action'] === 'add_to_favorites') {
            if (!in_array($artistId, $_SESSION['favoriteArtists'])) {
                $_SESSION['favoriteArtists'][] = $artistId;
                $message = "Artist added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artist is already in your favorites.";
                $messageType = "info";
            }
        } elseif ($_POST['action'] === 'remove_from_favorites') {
            if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                unset($_SESSION['favoriteArtists'][$key]);
                $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists']); // Re-index array
                $message = "Artist removed from favorites!";
                $messageType = "success";
            } else {
                $message = "Artist is not in your favorites.";
                $messageType = "info";
            }
        }
    } catch (Exception $e) {
        $message = "Error updating favorites. Please try again.";
        $messageType = "danger";
    }
}

// Check if artist ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	header("Location: /error.php?error=invalidParam");
	exit();
}

$artistId = (int)$_GET['id'];

// Load artist and artworks
try {
	$artist = $artistRepository->getArtistById($artistId);
	$artworks = $artworkRepository->getArtworksByArtist($artistId);
} catch (Exception $e) {
	header("Location: /error.php?error=invalidID&type=artist");
	exit();
}
?>

<body class="container">
	<br>
	<h1><?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?></h1>
	
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
			<div>
				<!-- Artist image -->
				<?php
				$imagePath = "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
				$placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
				$correctImagePath = file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath) ? $imagePath : $placeholderPath;
				?>
				<img src="<?php echo $correctImagePath ?>" alt="Image of <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>">
			</div>
			<div class="col-md-8">
				<p><?php echo htmlspecialchars($artist->getDetails()) ?></p>

				<!-- Add/Remove Artist Favorites Form -->
                <?php 
                $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($artist->getArtistId(), $_SESSION['favoriteArtists']);
                ?>
                <form method="post" class="mb-3">
                    <?php if ($isInFavorites): ?>
                        <input type="hidden" name="action" value="remove_from_favorites">
                        <input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
                        <button type="submit" class="btn btn-outline-danger">
                            ♥ Remove from Favorites
                        </button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="add_to_favorites">
                        <input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
                        <button type="submit" class="btn btn-primary">
                            ♡ Add to Favorites
                        </button>
                    <?php endif; ?>
                </form>

				<!-- Artist details -->
				<table class="table table-bordered w-75 mt-4">
					<thead class="thead-dark">
						<tr><th colspan="2">Artist Details</th></tr>
					</thead>
					<tr>
						<th>Date:</th>
						<td><?php echo htmlspecialchars($artist->getYearOfBirth()) ?><?php if ($artist->getYearOfDeath()) echo " - " . htmlspecialchars($artist->getYearOfDeath()) ?></td>
					</tr>
					<tr>
						<th>Nationality:</th>
						<td><?php echo htmlspecialchars($artist->getNationality()) ?></td>
					</tr>
					<tr>
						<th>More Info:</th>
						<td><a href="<?php echo htmlspecialchars($artist->getArtistLink()) ?>" target="_blank" class="text-decoration-none">Wikipedia</a></td>
					</tr>
				</table>
			</div>
		</div>

		<h2 class="mt-5">Artworks by <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?></h2>
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
