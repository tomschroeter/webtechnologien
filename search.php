<!DOCTYPE html>
<html lang="en">

<?php
    require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/navbar.php";
require_once dirname(__DIR__) . "/src/classes/Artist.php";
require_once dirname(__DIR__) . "/src/classes/Artwork.php";
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/dtos/ArtworkWithArtistName.php";

session_start();

$_SESSION['customerId'] = 1; // TEMP: simulate logged-in user
$_SESSION['isAdmin'] = true; // TEMP: simulate admin privileges

$db = new Database();
$artistRepository = new ArtistRepository($db);
$artworkRepository = new ArtworkRepository($db);

// Handle Add Artist Favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['favoriteArtists'])) {
            $_SESSION['favoriteArtists'] = [];
        }
		if (!isset($_SESSION['favoriteArtworks'])) {
            $_SESSION['favoriteArtworks'] = [];
        }
        
		if (isset($_POST['artistId'])){
        	$artistId = (int)$_POST['artistId'];
		}
		if (isset($_POST['artworkId'])){
        	$artworkId = (int)$_POST['artworkId'];
		}

        if ($_POST['action'] === 'add_artist_to_favorites') {
            if (!in_array($artistId, $_SESSION['favoriteArtists'])) {
                $_SESSION['favoriteArtists'][] = $artistId;
                $message = "Artist added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artist is already in your favorites.";
                $messageType = "info";
            }
        } elseif ($_POST['action'] === 'remove_artist_from_favorites') {
            if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                unset($_SESSION['favoriteArtists'][$key]);
                $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists']);
                $message = "Artist removed from favorites!";
                $messageType = "success";
            } else {
                $message = "Artist is not in your favorites.";
                $messageType = "info";
            }
        }

        if ($_POST['action'] === 'add_artwork_to_favorites') {
            if (!in_array($artworkId, $_SESSION['favoriteArtworks'])) {
                $_SESSION['favoriteArtworks'][] = $artworkId;
                $message = "Artwork added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is already in your favorites.";
                $messageType = "info";
            }
        } elseif ($_POST['action'] === 'remove_artwork_from_favorites') {
            if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                unset($_SESSION['favoriteArtworks'][$key]);
                $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks']);
                $message = "Artwork removed from favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is not in your favorites.";
                $messageType = "info";
            }
        }
	} catch (Exception $e) {
        $message = "Error updating favorites. Please try again.";
        $messageType = "danger";
    }
}

// Checks if search query has been submitted
if (isset($_GET['searchQuery'])) {
    $searchQuery = trim($_GET['searchQuery']);
} else {
    header("Location: /error.php?error=missingParam");
    exit();
}

// Checks if search query has valid size (>= 3 characters)
if (strlen($searchQuery) < 3) {
    header("Location: /error.php?error=tooShort");
    exit();
}

// Checks if sort parameter for displayed artworks is set
if (isset($_GET['sortParameter'])) {
    $sortParameter = $_GET['sortParameter'];
} else {
    $sortParameter = "Title"; // search by title by default
}

// Checks if user sets sort order for displayed artists
if (isset($_GET['sortArtist'])) {
    $sortArtist = ($_GET['sortArtist'] === 'descending');
} else {
    $sortArtist = false; // sort from a-z by default
}

// Checks if user sets sort order for displayed artworks
if (isset($_GET['sortArtwork'])) {
    $sortArtwork = ($_GET['sortArtwork'] === 'descending');
} else {
    $sortArtwork = false; // sort from lowest to highest by default
}

// Get results for all artists that fit the search query
$artistSearchResults = $artistRepository->getArtistBySearchQuery($searchQuery, $sortArtist);

// Get results for all artworks that fit the search query
$artworkSearchResults = $artworkRepository->getArtworkBySearchQuery($searchQuery, $sortParameter, $sortArtwork);
?>

<body class="container">
	<h2 class="flex-grow-1 mb-1 mt-3">Suchergebnisse</h2>

	<?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

	<?php if (sizeof($artistSearchResults) > 0 || sizeof($artworkSearchResults) > 0): ?>
		<?php if (sizeof($artistSearchResults) > 0): ?>
			<div class="d-flex align-items-center mt-3 mb-3">
				<h3 class="flex-grow-1 mb-0">Künstler</h3>
				<!-- Form providing the ability to sort the order of displayed artists -->
				<form method="get">
					<!-- Sets already submitted url params -->
					<?php foreach ($_GET as $key => $value): ?>
						<?php if ($key !== 'sortArtist'): ?>
							<input type="hidden" name="<?php echo $key?>" value="<?php echo $value?>">
						<?php endif; ?>
					<?php endforeach; ?>
					<select name="sortArtist" onchange="this.form.submit()" class="form-select">
						<option value="ascending" <?php echo !$sortArtist ? 'selected' : ''?>>Name (aufsteigend)</option>
						<option value="descending" <?php echo $sortArtist ? 'selected' : ''?>>Name (absteigend)</option>
					</select>
				</form>
			</div>

			<!-- List to display all artists that fit the search query -->
			<ul class="list-group">
			<?php foreach ($artistSearchResults as $artist): ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <!-- Ref link to display single artist -->
                    <a href="<?php echo route('artists', ['id' => $artist->getArtistId()]) ?>"
                        class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
                        <!-- Display artist name -->
                        <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                            <?php echo $artist->getFirstName() ?> <?= $artist->getLastName() ?>
                        </span>
                    </a>
                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <!-- Display add to favourites button -->
                        <form method="post" class="mr-2 mb-0">
                        <?php
                            $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($artist->getArtistId(), $_SESSION['favoriteArtists']);
                        ?>
                        <?php if ($isInFavorites): ?>
                            <input type="hidden" name="action" value="remove_artist_from_favorites">
                            <input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
                            <button type="submit" class="btn btn-outline-danger">
                                ♥
                            </button>
                        <?php else: ?>
                            <input type="hidden" name="action" value="add_artist_to_favorites">
                            <input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
                            <button type="submit" class="btn btn-primary">
                                ♡
                            </button>
                        <?php endif; ?>
                        </form>
                        <!-- Checks if artists' image exists -->
                        <?php $imagePath = "/assets/images/artists/square-thumb/".$artist->getArtistId().".jpg";
                            $placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                                $correctImagePath = $imagePath;
                            } else {
                                $correctImagePath = $placeholderPath;
                            }
                        ?>
                        <img src="<?php echo $correctImagePath?>" alt="Künsterbild" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                    </div>
                </li>
            <?php endforeach?>
			</ul>
		<?php endif; ?>

		<?php if (sizeof($artworkSearchResults) > 0): ?>
			<div class="d-flex align-items-center mt-3 mb-3">
				<h3 class="flex-grow-1 mb-0">Kunstwerke</h3>
				<!-- Form providing the ability to sort the order of displayed artworks by specific parameters -->
				<form method="get" class="d-flex">
					<!-- Sets already submitted url params -->
					<?php foreach ($_GET as $key => $value): ?>
						<?php if ($key !== 'sortParameter' && $key !== 'sortArtwork'): ?>
							<input type="hidden" name="<?php echo $key?>" value="<?php echo $value?>">
						<?php endif; ?>
					<?php endforeach; ?>

					<!-- Form to change the sort parameter -->
					<select name="sortParameter" onchange="this.form.submit()" class="form-select mx-2">
						<option value="Title" <?php echo $sortParameter == "Title" ? 'selected' : ''?>>Titel</option>
						<option value="LastName" <?php echo $sortParameter == "LastName" ? 'selected' : ''?>>Künstlername</option>
						<option value="YearOfWork" <?php echo $sortParameter == "YearOfWork" ? 'selected' : ''?>>Jahr</option>
					</select>

					<!-- Form to change the sort order -->
					<select name="sortArtwork" onchange="this.form.submit()" class="form-select">
						<option value="ascending" <?php echo !$sortArtwork ? 'selected' : ''?>>aufsteigend</option>
						<option value="descending" <?php echo $sortArtwork ? 'selected' : ''?>>absteigend</option>
					</select>
				</form>
			</div>

			<!-- List to display all artworks that fit the search query -->	
			<ul class="list-group">
			<?php foreach ($artworkSearchResults as $index => $combined):?>
				<li class="list-group-item d-flex align-items-center justify-content-between">
                    <!-- Ref link to display single artwork -->
                    <a href="<?php echo route('artworks', ['id' => $combined->getArtwork()->getArtworkId()])?>"
                        class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
                        <!-- Display artwork title, artist name and year of publishment -->
                        <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                            <?php echo '&quot;' . $combined->getArtwork()->getTitle() . '&quot; ' .
                                'by ' . $combined->getArtistFirstName() . ' ' . $combined->getArtistLastName() . ',' .
                                ' veröffentlicht ' . $combined->getArtwork()->getYearOfWork()?>
                        </span>
                    </a>
                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <!-- Display add to favourites button -->
                        <form method="post" class="mr-2 mb-0">
                            <?php
                                $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($combined->getArtwork()->getArtworkId(), $_SESSION['favoriteArtworks']);
                            ?>
                            <?php if ($isInFavorites): ?>
                                <input type="hidden" name="action" value="remove_artwork_from_favorites">
                                <input type="hidden" name="artworkId" value="<?php echo $combined->getArtwork()->getArtworkId() ?>">
                                <button type="submit" class="btn btn-outline-danger">
                                    ♥
                                </button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="add_artwork_to_favorites">
                                <input type="hidden" name="artworkId" value="<?php echo $combined->getArtwork()->getArtworkId() ?>">
                                <button type="submit" class="btn btn-primary">
                                    ♡
                                </button>
                            <?php endif; ?>
                        </form>
                        <!-- Checks if artworks' image exists -->
                        <?php $imagePath = "/assets/images/works/square-small/".$combined->getArtwork()->getImageFileName().".jpg";
                            $placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg";
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                                $correctImagePath = $imagePath;
                            } else {
                                $correctImagePath = $placeholderPath;
                            }
                        ?>
                        <img src="<?php echo $correctImagePath?>" alt="Kunstwerk" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                    </div>
                </li>
			<?php endforeach?>
			</ul>
		<?php endif; ?>

		<!-- Output if search didn't return a result -->
		<?php else: ?>
			<?php echo 'Es wurden keinen Ergebnisse für den Suchbegriff' . ' "'  . $searchQuery . '" '  . 'gefunden.'; ?>
	<?php endif; ?>
	<?php require_once 'bootstrap.php'; ?>
</body>
</html>