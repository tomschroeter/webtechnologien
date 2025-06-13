<?php
// Try MVC routing first
$mvcHandled = require_once dirname(__DIR__) . "/src/mvc_bootstrap.php";

// If MVC handled the request, we're done
if ($mvcHandled) {
    return;
}

// Otherwise, fall back to original behavior
?>
<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/navbar.php";
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/router/router.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$artistRepository = new ArtistRepository($db);
$artworkRepository = new ArtworkRepository($db);

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
				require_once dirname(__DIR__) . "/src/components/find_image_ref.php";
				$imagePath = "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
				$placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
				$correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
				?>
				<img src="<?php echo $correctImagePath ?>" alt="Image of <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>">
			</div>
			<div class="col-md-8">
				<p><?php echo htmlspecialchars($artist->getDetails()) ?></p>

				<!-- Add/Remove Artist Favorites Form -->
                <?php 
                $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($artist->getArtistId(), $_SESSION['favoriteArtists']);
                ?>
                <form method="post" action="/favorites-handler.php" class="mb-3">
                    <?php if ($isInFavorites): ?>
                        <input type="hidden" name="action" value="remove_artist_from_favorites">
                        <input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
                        <button type="submit" class="btn btn-outline-danger">
                            ♥ Remove from Favorites
                        </button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="add_artist_to_favorites">
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
            require_once dirname(__DIR__) . "/src/components/artwork-card-list.php";
            renderArtworkCardList($artworks);
            ?>
		</div>
	</div>

	<?php require_once 'bootstrap.php'; ?>
</body>
</html>
