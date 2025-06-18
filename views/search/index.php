<h2 class="flex-grow-1 mb-1 mt-4">
    <?php if (isset($isAdvancedSearch) && $isAdvancedSearch): ?>
        Advanced Search Results
    <?php else: ?>
        Search Results
    <?php endif; ?>
</h2>

<?php
require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
?>

<?php if (sizeof($artistSearchResults) > 0 || sizeof($artworkSearchResults) > 0): ?>
    <?php if (sizeof($artistSearchResults) > 0): ?>
        <div class="d-flex align-items-center mt-3 mb-3">
            <h3 class="flex-grow-1 mb-0">Artists</h3>
            <!-- Form providing the ability to sort the order of displayed artists -->
            <form method="get">
                <!-- Sets already submitted url params -->
                <?php foreach ($_GET as $key => $value): ?>
                    <?php if ($key !== 'sortArtist'): ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key) ?>" value="<?php echo htmlspecialchars($value) ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
                <select name="sortArtist" onchange="this.form.submit()" class="form-select">
                    <option value="ascending" <?php echo !$sortArtist ? 'selected' : '' ?>>Name (ascending)</option>
                    <option value="descending" <?php echo $sortArtist ? 'selected' : '' ?>>Name (descending)</option>
                </select>
            </form>
        </div>
        <div>
            <p class="text-muted">
                Found:
                <?php echo sizeof($artistSearchResults) . ' ' . (sizeof($artistSearchResults) === 1 ? 'artist' : 'artists'); ?>
            </p>
        </div>

        <!-- List to display all artists that fit the search query -->
        <ul class="list-group">
            <?php foreach ($artistSearchResults as $artist): ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <!-- Ref link to display single artist -->
                    <a href="/artists/<?php echo $artist->getArtistId() ?>"
                        class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
                        <!-- Display artist name -->
                        <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                            <?php echo htmlspecialchars($artist->getFirstName()) ?>             <?= htmlspecialchars($artist->getLastName()) ?>
                        </span>
                    </a>
                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <!-- Display add to favorites button -->
                        <?php
                        $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($artist->getArtistId(), $_SESSION['favoriteArtists']);
                        ?>
                        <button type="button"
                            class="btn favorite-btn <?php echo $isInFavorites ? 'btn-outline-danger' : 'btn-primary' ?>"
                            data-type="artist" data-id="<?php echo $artist->getArtistId() ?>"
                            data-is-favorite="<?php echo $isInFavorites ? 'true' : 'false' ?>"
                            title="<?php echo $isInFavorites ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                            <?php echo $isInFavorites ? '♥' : '♡' ?>
                        </button>

                        <!-- Fallback form for non-JS users -->
                        <form method="post" action="/favorites/artists/<?php echo $artist->getArtistId() ?>/toggle"
                            class="mr-2 mb-0 d-none fallback-form">
                            <?php if ($isInFavorites): ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-danger">
                                    ♥
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary">
                                    ♡
                                </button>
                            <?php endif; ?>
                        </form>
                        <!-- Artist image -->
                        <?php $imagePath = "/assets/images/artists/square-thumb/" . $artist->getArtistId() . ".jpg";
                        $placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
                        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                        ?>
                        <img src="<?php echo $correctImagePath ?>" alt="Artist Image"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                    </div>
                </li>
            <?php endforeach ?>
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
                        <input type="hidden" name="<?php echo htmlspecialchars($key) ?>" value="<?php echo htmlspecialchars($value) ?>">
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
        <div>
            <p class="text-muted">
                Found:
                <?php echo sizeof($artworkSearchResults) . ' ' . (sizeof($artworkSearchResults) === 1 ? 'artwork' : 'artworks'); ?>
            </p>
        </div>

        <!-- List to display all artworks that fit the search query -->
        <ul class="list-group">
            <?php foreach ($artworkSearchResults as $index => $combined): ?>
                <?php $artwork = $combined->getArtwork() ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <!-- Ref link to display single artwork -->
                    <a href="/artworks/<?php echo $artwork->getArtworkId() ?>"
                        class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
                        <!-- Display artwork title, artist name and year of publishment -->
                        <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                            <?php echo '&quot;' . htmlspecialchars($artwork->getTitle()) . '&quot; ' .
                                'by ' . htmlspecialchars($combined->getArtistFirstName()) . ' ' . htmlspecialchars($combined->getArtistLastName()) . ',' .
                                ' published ' . htmlspecialchars($artwork->getYearOfWork()) ?>
                        </span>
                    </a>
                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <!-- Display add to favorites button -->
                        <?php
                        $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($artwork->getArtworkId(), $_SESSION['favoriteArtworks']);
                        ?>
                        <button type="button"
                            class="btn favorite-btn <?php echo $isInFavorites ? 'btn-outline-danger' : 'btn-primary' ?>"
                            data-type="artwork" data-id="<?php echo $artwork->getArtworkId() ?>"
                            data-is-favorite="<?php echo $isInFavorites ? 'true' : 'false' ?>"
                            title="<?php echo $isInFavorites ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                            <?php echo $isInFavorites ? '♥' : '♡' ?>
                        </button>

                        <!-- Fallback form for non-JS users -->
                        <form method="post"
                            action="/favorites/artworks/<?php echo $artwork->getArtworkId() ?>/toggle"
                            class="mr-2 mb-0 d-none fallback-form">
                            <?php if ($isInFavorites): ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-danger">
                                    ♥
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary">
                                    ♡
                                </button>
                            <?php endif; ?>
                        </form>
                        <!-- Artwork image -->
                        <?php $imagePath = "/assets/images/works/square-small/" . $artwork->getImageFileName() . ".jpg";
                        $placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg";
                        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                        ?>
                        <img src="<?php echo $correctImagePath ?>" alt="Artwork Image"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif; ?>

    <!-- Output if search didn't return a result -->
<?php else: ?>
    <div class="alert alert-info mt-4">
        <h4>No Results Found</h4>
        <?php if (isset($isAdvancedSearch) && $isAdvancedSearch): ?>
            <p>No results were found for your advanced search criteria.</p>
            <?php if (!empty($filterBy)): ?>
                <p><strong>Filter:</strong> <?php echo ucfirst(htmlspecialchars($filterBy)) ?></p>
            <?php endif; ?>
            <?php if (!empty($artistName)): ?>
                <p><strong>Artist Name:</strong> <?php echo htmlspecialchars($artistName) ?></p>
            <?php endif; ?>

            <!-- Artist Year Range -->
            <?php
            $today = date("Y");
            if (!empty($artistStartDate) && empty($artistEndDate)) {
                echo "<p><strong>Artist Year Range:</strong> " . htmlspecialchars($artistStartDate) . " - $today</p>";
            } elseif (empty($artistStartDate) && !empty($artistEndDate)) {
                echo "<p><strong>Artist Year Range:</strong> 0 - " . htmlspecialchars($artistEndDate) . "</p>";
            } elseif (!empty($artistStartDate) && !empty($artistEndDate)) {
                echo "<p><strong>Artist Year Range:</strong> " . htmlspecialchars($artistStartDate) . " - " . htmlspecialchars($artistEndDate) . "</p>";
            }
            ?>

            <?php if (!empty($artworkTitle)): ?>
                <p><strong>Artwork Title:</strong> <?php echo htmlspecialchars($artworkTitle) ?></p>
            <?php endif; ?>

            <?php if (!empty($artistNationality)): ?>
                <p><strong>Artist Nationality:</strong> <?php echo htmlspecialchars($artistNationality) ?></p>
            <?php endif; ?>
            <?php if (!empty($artworkGenre)): ?>
                <p><strong>Artwork Genre:</strong> <?php echo htmlspecialchars($artworkGenre) ?></p>
            <?php endif; ?>

            <!-- Artwork Year Range -->
            <?php
            if (!empty($artworkStartDate) && empty($artworkEndDate)) {
                echo "<p><strong>Artwork Year Range:</strong> " . htmlspecialchars($artworkStartDate) . " - $today</p>";
            } elseif (empty($artworkStartDate) && !empty($artworkEndDate)) {
                echo "<p><strong>Artwork Year Range:</strong> 0 - " . htmlspecialchars($artworkEndDate) . "</p>";
            } elseif (!empty($artworkStartDate) && !empty($artworkEndDate)) {
                echo "<p><strong>Artwork Year Range:</strong> " . htmlspecialchars($artworkStartDate) . " - " . htmlspecialchars($artworkEndDate) . "</p>";
            }
            ?>
        <?php else: ?>
            <p>No results were found for the search term <strong>"<?php echo htmlspecialchars($searchQuery) ?>"</strong>.</p>
        <?php endif; ?>
        <div class="mt-3">
            <a href="/artists" class="btn btn-primary mr-2">Browse All Artists</a>
            <a href="/artworks" class="btn btn-primary">Browse All Artworks</a>
        </div>
    </div>
<?php endif; ?>