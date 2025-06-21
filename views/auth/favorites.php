<h1 class="mt-4">My Favorites</h1>

<?php
// Include helper to get correct image path or placeholder if missing
require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
?>

<?php if (empty($favoriteArtists) && empty($favoriteArtworks)): ?>
    <!-- Show info message when no favorites exist -->
    <div class="alert alert-info">
        <h4>No Favorites Yet</h4>
        <p>You haven't added any artists or artworks to your favorites yet. Browse our collection and click the heart (♡)
            button to add items to your favorites!</p>
        <div class="mt-3">
            <!-- Buttons to encourage browsing -->
            <a href="/artists" class="btn btn-primary me-2">Browse Artists</a>
            <a href="/artworks" class="btn btn-primary">Browse Artworks</a>
        </div>
    </div>
<?php else: ?>

    <?php if (!empty($favoriteArtists)): ?>
        <!-- Favorite Artists Section -->
        <h4 class="mt-4 mb-3">Favorite Artists (<?= count($favoriteArtists) ?>)</h4>
        <div class="row">
            <?php foreach ($favoriteArtists as $artist): ?>
                <?php
                // Prepare artist details and image paths
                $artistLink = "/artists/" . $artist->getArtistId();
                $imagePath = "/assets/images/artists/square-medium/" . $artist->getArtistId() . ".jpg";
                $placeholderPath = "/assets/placeholder/artists/square-medium/placeholder.svg";
                $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);

                $artistName = $artist->getFirstName() . " " . $artist->getLastName();

                // Check if this artist is still marked as favorite in session
                $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($artist->getArtistId(), $_SESSION['favoriteArtists']);
                ?>
                <div class="col-md-4 col-lg-3 mb-5">
                    <div class="card h-100">
                        <!-- Link and image -->
                        <a href="<?= $artistLink ?>">
                            <img src="<?= $correctImagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($artistName) ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <!-- Artist name linked to profile -->
                            <h5 class="card-title text-center">
                                <a href="<?= $artistLink ?>" class="text-body"><?= htmlspecialchars($artistName) ?></a>
                            </h5>
                            <!-- Optional nationality -->
                            <?php if ($artist->getNationality()): ?>
                                <p class="card-text text-center text-muted">
                                    <?= htmlspecialchars($artist->getNationality()) ?>
                                </p>
                            <?php endif; ?>
                            <!-- Optional birth and death years -->
                            <?php if ($artist->getYearOfBirth()): ?>
                                <p class="card-text text-center">
                                    <small class="text-muted">
                                        <?= htmlspecialchars($artist->getYearOfBirth()) ?>
                                        <?php if ($artist->getYearOfDeath()): ?>
                                            - <?= htmlspecialchars($artist->getYearOfDeath()) ?>
                                        <?php endif; ?>
                                    </small>
                                </p>
                            <?php endif; ?>
                            <!-- Buttons: view profile and toggle favorite -->
                            <div class="d-flex align-items-center mt-auto">
                                <a href="<?= $artistLink ?>" class="btn btn-primary flex-fill me-2">View Profile</a>
                                <?php
                                $type = "artist";
                                $item = $artist;
                                // Include favorite button component
                                require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php"
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($favoriteArtworks)): ?>
        <!-- Favorite Artworks Section -->
        <h4 class="mt-4 mb-3">Favorite Artworks (<?= count($favoriteArtworks) ?>)</h4>
        <div class="row">
            <?php foreach ($favoriteArtworks as $item): ?>
                <?php
                // Extract artwork and artist objects
                $artwork = $item['artwork'];
                $artist = $item['artist'];

                // Prepare links and image paths
                $artworkLink = "/artworks/" . $artwork->getArtworkId();
                $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
                $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
                $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);

                // Check if artwork is still favorited in session
                $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($artwork->getArtworkId(), $_SESSION['favoriteArtworks']);
                ?>
                <div class="col-md-4 col-lg-3 mb-5">
                    <div class="card h-100">
                        <!-- Link and image -->
                        <a href="<?= $artworkLink ?>">
                            <img src="<?= $correctImagePath ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($artwork->getTitle()) ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <!-- Artwork title linked to detail page -->
                            <h5 class="card-title text-center">
                                <a href="<?= $artworkLink ?>" class="text-body"><?= htmlspecialchars($artwork->getTitle()) ?></a>
                            </h5>
                            <!-- Optional artist info -->
                            <?php if ($artist): ?>
                                <p class="card-text text-center text-muted">
                                    by <a href="/artists/<?= $artist->getArtistId() ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($artist->getFullName()) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <!-- Optional year of artwork -->
                            <?php if ($artwork->getYearOfWork()): ?>
                                <p class="card-text text-center">
                                    <small class="text-muted"><?= htmlspecialchars($artwork->getYearOfWork()) ?></small>
                                </p>
                            <?php endif; ?>
                            <!-- Buttons: view artwork and toggle favorite -->
                            <div class="d-flex align-items-center mt-auto">
                                <a href="<?= $artworkLink ?>" class="btn btn-primary flex-fill me-2">View Artwork</a>
                                <?php
                                $type = "artwork";
                                $item = $artwork;
                                // Include favorite button component
                                require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php"
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>