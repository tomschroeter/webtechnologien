<?php
require_once dirname(__DIR__)."/classes/Artist.php";
require_once dirname(__DIR__)."/repositories/ArtistRepository.php";
?>

<div class="card-deck">
	<?php

	$artistRepository = new ArtistRepository(new Database());
	$mostReviewedArtists = $artistRepository->findMostReviewed(3);

	foreach ($mostReviewedArtists as $index => $combined) {

	    $artistName = $combined->getArtist()->getFirstName() . " " . $combined->getArtist()->getLastName();
	    $reviewCount = $combined->getReviewCount();
	    $position = $index + 1;

	    echo "
			<div class=\"card\">
				<div class=\"card-body\">
				<h4 class=\"card-title\">$position. $artistName</h4>
				<div class=\"w-100\">
					<p class=\"card-text\">
					$reviewCount Reviews
					</p>
				</div>
				</div>
			</div>
		";
	}
	?>
</div>