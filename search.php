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

$filterBy = isset($_GET['filterBy']) ? $_GET['filterBy'] : '';
// Checks if search query has been submitted
if (isset($_GET['searchQuery'])) {
	$searchQuery = trim($_GET['searchQuery']);
} else {
	if ($filterBy !== '') {
		$searchQuery = "Couldn't find any results that fit the searching criteria";
	} else {
		header("Location: /error.php?error=missingParam");
		exit();
	}
}

// Checks if search query has valid size (>= 3 characters)
if (strlen($searchQuery) < 3 && $filterBy === '') {
	header("Location: /error.php?error=tooShort");
	exit();
}

// Checks if sort parameter for displayed artworks is set
$sortParameter = isset($_GET['sortParameter']) ? $_GET['sortParameter'] : 'Title'; // search by title by default

// Checks if user sets sort order for displayed artists
$sortArtist = isset($_GET['sortArtist']) ? $_GET['sortArtist'] === 'descending' : false; // sort from a-z by default

// Checks if user sets sort order for displayed artworks
$sortArtwork = isset($_GET['sortArtwork']) ? $_GET['sortArtwork'] === 'descending' : false; // sort from lowest to highest by default

// Artist filters

$artistName = isset($_GET['artistName']) ? $_GET['artistName'] : null;

$artistStartDate = isset($_GET['artistStartDate']) ? $_GET['artistStartDate'] : null;

$artistEndDate = isset($_GET['artistEndDate']) ? $_GET['artistEndDate'] : null;

$artistNationality = isset($_GET['artistNationality']) ? $_GET['artistNationality'] : null;

// Artwork filters

$artworkTitle = isset($_GET['artworkTitle']) ? $_GET['artworkTitle'] : null;

$artworkStartDate = isset($_GET['artworkStartDate']) ? $_GET['artworkStartDate'] : null;

$artworkEndDate = isset($_GET['artworkEndDate']) ? $_GET['artworkEndDate'] : null;

$artworkGenre = isset($_GET['artworkGenre']) ? $_GET['artworkGenre'] : null;

if (isset($_GET['filterBy'])) {
	switch ($filterBy) {
		case 'artist':
			$artistSearchResults = $artistRepository->getArtistByAdvancedSearch($artistName, $artistStartDate, $artistEndDate, $artistNationality, $sortArtist);
			$artworkSearchResults = [];
			break;
		case 'artwork':
			$artistSearchResults = [];
			$artworkSearchResults = $artworkRepository->getArtworksByAdvancedSearch($artworkTitle, $artworkStartDate, $artworkEndDate, $artworkGenre, $sortParameter, $sortArtwork);
			break;
	}
} else {
	// Get results for all artists that fit the search query
	$artistSearchResults = $artistRepository->getArtistBySearchQuery($searchQuery, $sortArtist);
	// Get results for all artworks that fit the search query
	$artworkSearchResults = $artworkRepository->getArtworkBySearchQuery($searchQuery, $sortParameter, $sortArtwork);
}

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
		<?php if (sizeof($artistSearchResults) > 0 && ($filterBy === 'artist' || $filterBy === '')): ?>
			<div class="d-flex align-items-center mt-3 mb-3">
				<h3 class="flex-grow-1 mb-0">Artists</h3>
				<!-- Form providing the ability to sort the order of displayed artists -->
				<form method="get">
					<!-- Sets already submitted url params -->
					<?php foreach ($_GET as $key => $value): ?>
						<?php if ($key !== 'sortArtist'): ?>
							<input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>">
						<?php endif; ?>
					<?php endforeach; ?>
					<select name="sortArtist" onchange="this.form.submit()" class="form-select">
						<option value="ascending" <?php echo !$sortArtist ? 'selected' : '' ?>>Name (ascending)</option>
						<option value="descending" <?php echo $sortArtist ? 'selected' : '' ?>>Name (descending)</option>
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
								<?php echo $artist->getFirstName() ?> 			<?= $artist->getLastName() ?>
							</span>
						</a>
						<div class="d-flex align-items-center" style="gap: 0.5rem;">
							<!-- Display add to favorites button if logged in -->
							<?php if (isset($_SESSION['customerId'])): ?>
								<form method="post" action="/favorites-handler.php" class="mr-2 mb-0">
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
							<?php endif; ?>

							<!-- Checks if artists' image exists -->
							<?php $imagePath = "/assets/images/artists/square-thumb/" . $artist->getArtistId() . ".jpg";
							$placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
							$correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
							?>
							<img src="<?php echo $correctImagePath ?>" alt="Künsterbild"
								style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
						</div>
					</li>
				<?php endforeach ?>
			</ul>
		<?php endif; ?>

		<?php if (sizeof($artworkSearchResults) > 0 && ($filterBy === 'artwork' || $filterBy === '')): ?>
			<div class="d-flex align-items-center mt-3 mb-3">
				<h3 class="flex-grow-1 mb-0">Artworks</h3>
				<!-- Form providing the ability to sort the order of displayed artworks by specific parameters -->
				<form method="get" class="d-flex">
					<!-- Sets already submitted url params -->
					<?php foreach ($_GET as $key => $value): ?>
						<?php if ($key !== 'sortParameter' && $key !== 'sortArtwork'): ?>
							<input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>">
						<?php endif; ?>
					<?php endforeach; ?>

					<!-- Form to change the sort parameter -->
					<select name="sortParameter" onchange="this.form.submit()" class="form-select mx-2">
						<option value="Title" <?php echo $sortParameter == "Title" ? 'selected' : '' ?>>Title</option>
						<option value="LastName" <?php echo $sortParameter == "LastName" ? 'selected' : '' ?>>Artist name</option>
						<option value="YearOfWork" <?php echo $sortParameter == "YearOfWork" ? 'selected' : '' ?>>Year</option>
					</select>

					<!-- Form to change the sort order -->
					<select name="sortArtwork" onchange="this.form.submit()" class="form-select">
						<option value="ascending" <?php echo !$sortArtwork ? 'selected' : '' ?>>ascending</option>
						<option value="descending" <?php echo $sortArtwork ? 'selected' : '' ?>>descending</option>
					</select>
				</form>
			</div>

			<!-- List to display all artworks that fit the search query -->
			<ul class="list-group">
				<?php foreach ($artworkSearchResults as $index => $combined): ?>
					<li class="list-group-item d-flex align-items-center justify-content-between">
						<!-- Ref link to display single artwork -->
						<a href="<?php echo route('artworks', ['id' => $combined->getArtwork()->getArtworkId()]) ?>"
							class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
							<!-- Display artwork title, artist name and year of publishment -->
							<span class="text-truncate" style="max-width: 60%; white-space: normal;">
								<?php echo '&quot;' . $combined->getArtwork()->getTitle() . '&quot; ' .
									'by ' . $combined->getArtistFirstName() . ' ' . $combined->getArtistLastName() . ',' .
									' veröffentlicht ' . $combined->getArtwork()->getYearOfWork() ?>
							</span>
						</a>
						<div class="d-flex align-items-center" style="gap: 0.5rem;">
							<!-- Display add to favorites button if logged in -->
							<?php if (isset($_SESSION['customerId'])): ?>
								<form method="post" action="/favorites-handler.php" class="mr-2 mb-0">
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
							<?php endif; ?>

							<!-- Checks if artworks' image exists -->
							<?php $imagePath = "/assets/images/works/square-small/" . $combined->getArtwork()->getImageFileName() . ".jpg";
							$placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg";
							$correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
							?>
							<img src="<?php echo $correctImagePath ?>" alt="Kunstwerk"
								style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
						</div>
					</li>
				<?php endforeach ?>
			</ul>
		<?php endif; ?>

		<!-- Output if search didn't return a result -->
	<?php else: ?>
		<?php echo 'No results were found for the search term' . ' "' . $searchQuery . '"' . '.'; ?>
	<?php endif; ?>
	<?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>

</html>