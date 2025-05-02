<!DOCTYPE html>
<?php
session_start();
$_SESSION['customerId'] = 1; // TEST-Nutzer ID = 1
?>

<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/navbar.php";

$db = new Database();
$artistRepository = new ArtistRepository($db);
$artworkRepository = new ArtworkRepository($db);

// Checks if id is set correctly in URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$artistId = $_GET['id'];
} else {
	header("Location: /error.php?error=invalidParam");
	exit();
}

// Checks if artist exists in database
try {
	$artist = $artistRepository->getArtistById($artistId);
	$artworks = $artworkRepository->getArtworksByArtist($artistId);
} catch (Exception $e) {
	header("Location: /error.php?error=invalidID");
	exit();
}
?>

<body class="container">
	<br>
	<h1> <?php echo $artist->getFirstName() ?> <?php echo $artist->getLastName() ?> </h1>
	<div class="container mt-3">
		<div class="row">
			<div>
				<!-- Displays artist image -->
				<!-- Checks if artists' image exists -->
				<?php $imagePath =  "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
				$placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
					$correctImagePath = $imagePath;
				} else {
					$correctImagePath = $placeholderPath;
				}
				?>
				<img src="<?php echo $correctImagePath ?>" alt="Bild von <?php echo $artist->getFirstName() . ' ' . $artist->getLastName() ?>">
			</div>
			<div class="col-md-8">
				<!-- Displays artist description -->
				<p><?php echo $artist->getDetails() ?></p>

				<!-- Add to favourites button -->
				<form method="post" action="add-favourite.php">
					<input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
					<button type="submit" class="btn btn-primary mt-2">Add to Favourites</button>
				</form>

				<!-- Table with artist details -->
				<table class="table table-bordered w-75 mt-4">
					<thead class="thead-dark">
						<tr>
							<th colspan="2">Artist Details</th>
						</tr>
					</thead>
					<tr>
						<th>Date:</th>
						<td><?php echo $artist->getYearOfBirth() ?><?php if ($artist->getYearOfDeath()) echo " - " . $artist->getYearOfDeath() ?></td>
					</tr>
					<tr>
						<th>Nationality:</th>
						<td><?php echo $artist->getNationality() ?></td>
					</tr>
					<tr>
						<th>More Info:</th>
						<td><a href="<?php echo $artist->getArtistLink() ?>" target="_blank">Wikipedia</a></td>
					</tr>
				</table>
			</div>
		</div>
		<h2 class="mt-5">Artworks by <?php echo $artist->getFirstName() ?> <?php echo $artist->getLastName() ?></h2>
		<div class="row mt-4">
			<?php foreach ($artworks as $artwork): ?>
				<!-- Creates new URL to display single artwork --->
				<?php $artworkLink = route('artworks', ['id' => $artwork->getArtworkId()]) ?>
				<!-- List of artworks -->
				<div class="col-md-3 mb-4">
					<!-- Artwork card including image, name and view button --->
					<div class="card h-100">
						<!-- Checks if artworks' image exists -->
						<?php $imagePath =  "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
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
							<!-- Checks if user is logged in -->
							<?php if (isset($_SESSION['customerId'])): ?>

								<!-- Loads ReviewRepository and checks if user already reviewed this artwork -->
								<?php
								require_once dirname(__DIR__) . "/src/repositories/ReviewRepository.php";
								$reviewRepo = new ReviewRepository($db);
								$alreadyReviewed = $reviewRepo->hasUserReviewed($_SESSION['customerId'], $artwork->getArtworkId());
								?>

								<!-- Displays review form if user has not reviewed yet -->
								<?php if (!$alreadyReviewed): ?>
									<form method="POST" action="add-review.php" class="mt-2">
										<input type="hidden" name="artworkId" value="<?= $artwork->getArtworkId() ?>">
										<label for="rating">Rating (1–5):</label>
										<input type="number" name="rating" min="1" max="5" required class="form-control mb-1">
										<label for="comment">Comment:</label>
										<textarea name="comment" required class="form-control mb-2"></textarea>
										<button type="submit" class="btn btn-sm btn-success">Submit Review</button>
									</form>
									<!-- Message if user has already submitted a review -->
								<?php else: ?>
									<p class="text-muted mt-2">You have already reviewed this artwork.</p>
								<?php endif; ?>

								<!-- Message if user is not logged in -->
							<?php else: ?>
								<p class="text-muted mt-2">Log in to leave a review.</p>
							<?php endif; ?>

							<!-- Loads and displays all reviews for this artwork -->
							<?php
							$reviews = $reviewRepo->getAllReviewsForArtwork($artwork->getArtworkId());
							?>

							<!-- If there are reviews, display them -->
							<?php if (count($reviews) > 0): ?>
								<div class="mt-3">
									<h6>Reviews:</h6>
									<?php foreach ($reviews as $review): ?>
										<div class="border rounded p-2 mb-2 bg-light">
											<strong>Rating:</strong> <?= $review->getRating() ?>/5<br>
											<strong>Comment:</strong> <?= htmlspecialchars($review->getComment()) ?><br>
											<small class="text-muted"><?= $review->getReviewDate() ?></small>
										</div>
									<?php endforeach; ?>
								</div>
								<!-- If no reviews exist -->
							<?php else: ?>
								<p class="text-muted mt-2">No reviews yet.</p>
							<?php endif; ?>
							
							<h5 class="card-title text-center">
								<a href="<?php echo $artworkLink ?>" target="_blank" class="text-body"><?php echo $artwork->getTitle() ?></a>
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