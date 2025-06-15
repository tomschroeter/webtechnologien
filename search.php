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
require_once dirname(__DIR__) . "/src/components/find_image_ref.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$artistRepository = new ArtistRepository($db);
$artworkRepository = new ArtworkRepository($db);

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
	<h2 class="flex-grow-1 mb-1 mt-3">Search Results</h2>

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
				<h3 class="flex-grow-1 mb-0">Artists</h3>
				<!-- Form providing the ability to sort the order of displayed artists -->
				<form method="get">
					<!-- Sets already submitted url params -->
					<?php foreach ($_GET as $key => $value): ?>
						<?php if ($key !== 'sortArtist'): ?>
							<input type="hidden" name="<?php echo $key?>" value="<?php echo $value?>">
						<?php endif; ?>
					<?php endforeach; ?>
					<select name="sortArtist" onchange="this.form.submit()" class="form-select">
						<option value="ascending" <?php echo !$sortArtist ? 'selected' : ''?>>Name (ascending)</option>
						<option value="descending" <?php echo $sortArtist ? 'selected' : ''?>>Name (descending)</option>
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
                        <!-- Display add to favorites button -->
                        <?php
                        $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($artist->getArtistId(), $_SESSION['favoriteArtists']);
                        ?>
                        <button type="button" 
                                class="btn favorite-btn <?php echo $isInFavorites ? 'btn-outline-danger' : 'btn-primary' ?>"
                                data-type="artist"
                                data-id="<?php echo $artist->getArtistId() ?>"
                                data-is-favorite="<?php echo $isInFavorites ? 'true' : 'false' ?>"
                                title="<?php echo $isInFavorites ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                            <?php echo $isInFavorites ? '♥' : '♡' ?>
                        </button>
                        
                        <!-- Fallback form for non-JS users -->
                        <form method="post" action="/favorites-handler.php" class="mr-2 mb-0 d-none fallback-form">
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
                            $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                        ?>
                        <img src="<?php echo $correctImagePath?>" alt="Künsterbild" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                    </div>
                </li>
            <?php endforeach?>
			</ul>
		<?php endif; ?>

		<?php if (sizeof($artworkSearchResults) > 0): ?>
			<div class="d-flex align-items-center mt-3 mb-3">
				<h3 class="flex-grow-1 mb-0">Artworks</h3>
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
						<option value="Title" <?php echo $sortParameter == "Title" ? 'selected' : ''?>>Title</option>
						<option value="LastName" <?php echo $sortParameter == "LastName" ? 'selected' : ''?>>Artist name</option>
						<option value="YearOfWork" <?php echo $sortParameter == "YearOfWork" ? 'selected' : ''?>>Year</option>
					</select>

					<!-- Form to change the sort order -->
					<select name="sortArtwork" onchange="this.form.submit()" class="form-select">
						<option value="ascending" <?php echo !$sortArtwork ? 'selected' : ''?>>ascending</option>
						<option value="descending" <?php echo $sortArtwork ? 'selected' : ''?>>descending</option>
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
                        <!-- Display add to favorites button -->
                        <?php
                        $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($combined->getArtwork()->getArtworkId(), $_SESSION['favoriteArtworks']);
                        ?>
                        <button type="button" 
                                class="btn favorite-btn <?php echo $isInFavorites ? 'btn-outline-danger' : 'btn-primary' ?>"
                                data-type="artwork"
                                data-id="<?php echo $combined->getArtwork()->getArtworkId() ?>"
                                data-is-favorite="<?php echo $isInFavorites ? 'true' : 'false' ?>"
                                title="<?php echo $isInFavorites ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                            <?php echo $isInFavorites ? '♥' : '♡' ?>
                        </button>
                        
                        <!-- Fallback form for non-JS users -->
                        <form method="post" action="/favorites-handler.php" class="mr-2 mb-0 d-none fallback-form">
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
                            $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                        ?>
                        <img src="<?php echo $correctImagePath?>" alt="Kunstwerk" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                    </div>
                </li>
			<?php endforeach?>
			</ul>
		<?php endif; ?>

		<!-- Output if search didn't return a result -->
		<?php else: ?>
			<?php echo 'No results were found for the search term' . ' "'  . $searchQuery . '"' . '.'; ?>
	<?php endif; ?>
	<?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>