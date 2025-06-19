<br>
<h1><?php echo htmlspecialchars($artist->getFullName()) ?></h1>

<div class="mt-4">
    <div class="row">
        <div> <!-- Artist image -->
            <?php
            require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
            $imagePath = "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
            $placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
            $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
            ?>
            <img src="<?php echo $correctImagePath ?>"
                alt="Image of <?php echo htmlspecialchars($artist->getFullName()) ?>">
        </div>
        <div class="col-md-8">
            <p><?php echo $artist->getDetails() ?></p>

            <!-- Add/Remove Artist Favorites -->
            <?php if (isset($_SESSION['customerId'])): ?>
                <div class="favorites-container mb-3">
                    <?php
                    $type = "artist";
                    $item = $artist;
                    $showLabel = true;
                    require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php"
                        ?>

                    <!-- Fallback form for non-JS users -->
                    <form method="post" action="/favorites-handler.php" class="d-none fallback-form">
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
                </div>
            <?php endif ?>
            <!-- Artist details -->
            <table class="table table-bordered w-75 mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="2">Artist Details</th>
                    </tr>
                </thead>
                <tr>
                    <th>Date:</th>
                    <td><?php echo htmlspecialchars($artist->getYearOfBirth()) ?><?php if ($artist->getYearOfDeath())
                           echo " - " . htmlspecialchars($artist->getYearOfDeath()) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Nationality:</th>
                        <td><?php echo htmlspecialchars($artist->getNationality()) ?></td>
                </tr>
                <tr>
                    <th>More Info:</th>
                    <td><a href="<?php echo htmlspecialchars($artist->getArtistLink()) ?>" target="_blank"
                            class="text-decoration-none">Wikipedia</a></td>
                </tr>
            </table>
        </div>
    </div>

    <h2 class="mt-5">Artworks by <?php echo htmlspecialchars($artist->getFullName()) ?></h2>
    <div class="row mt-4">
        <?php
        require_once dirname(dirname(__DIR__)) . "/components/artwork-card-list.php";
        renderArtworkCardList($artworks);
        ?>
    </div>