<!DOCTYPE html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Check if artist ID is valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$artistId = $_GET['id'];
} else {
	header("Location: /error.php?error=invalidParam");
	exit();
}

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
	<h1><?php echo $artist->getFirstName() ?> <?php echo $artist->getLastName() ?></h1>
	<div class="container mt-3">
		<div class="row">
			<div>
				<!-- Artist image -->
				<?php
				$imagePath = "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
				$placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
				$correctImagePath = file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath) ? $imagePath : $placeholderPath;
				?>
				<img src="<?php echo $correctImagePath ?>" alt="Image of <?php echo $artist->getFirstName() . ' ' . $artist->getLastName() ?>">
			</div>
			<div class="col-md-8">
				<p><?php echo $artist->getDetails() ?></p>

				<!-- Add to favourites button -->
				<form method="post" action="add-favourite.php">
					<input type="hidden" name="artistId" value="<?php echo $artist->getArtistId() ?>">
					<button type="submit" class="btn btn-primary mt-2">Add to Favourites</button>
				</form>

				<!-- Artist details -->
				<table class="table table-bordered w-75 mt-4">
					<thead class="thead-dark">
						<tr><th colspan="2">Artist Details</th></tr>
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
				<?php $artworkLink = "/display-single-artwork.php?id=" . $artwork->getArtworkId(); ?>
				<!-- List of artworks -->
				<div class="col-md-3 mb-4">
					<div class="card h-100">
						<!-- Artwork image -->
						<?php
						$imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
						$placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
						$correctImagePath = file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath) ? $imagePath : $placeholderPath;
						?>
						<a href="<?php echo $artworkLink ?>" target="_blank">
							<img src="<?php echo $correctImagePath ?>" class="card-img-top" alt="<?php echo $artwork->getTitle() ?>">
						</a>

						<div class="card-body d-flex flex-column">
							<h5 class="card-title text-center">
								<a href="<?php echo $artworkLink ?>" target="_blank" class="text-body">
									<?php echo $artwork->getTitle() ?>
								</a>
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
