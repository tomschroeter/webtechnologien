<h2 class="flex-grow-1 mb-1 mt-4">
    <?php if (isset($isAdvancedSearch) && $isAdvancedSearch): ?>
        Advanced Search Results
    <?php else: ?>
        Search Results
    <?php endif; ?>
</h2>

<?php require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php"; ?>

<?php if (sizeof($artistSearchResults) > 0 || sizeof($artworkSearchResults) > 0): ?>

    <?php if (sizeof($artistSearchResults) > 0): ?>
        <div class="d-flex align-items-center mt-3 mb-3">
            <h3 class="flex-grow-1 mb-0">Artists</h3>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-2">
            <p class="text-muted mb-0">
                Found:
                <?= sizeof($artistSearchResults) . ' ' . (sizeof($artistSearchResults) === 1 ? 'artist' : 'artists'); ?>
            </p>

            <!-- Sorting form for artists -->
            <form method="get" class="d-flex align-items-center">
                <?php foreach ($_GET as $key => $value): ?>
                    <?php if ($key !== 'sortArtist'): ?>
                        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                    <?php endif; ?>
                <?php endforeach; ?>

                <select name="sortArtist" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="ascending" <?= !$sortArtist ? 'selected' : '' ?>>Name (ascending)</option>
                    <option value="descending" <?= $sortArtist ? 'selected' : '' ?>>Name (descending)</option>
                </select>
            </form>
        </div>

        <ul class="list-group">
            <?php foreach ($artistSearchResults as $artist): ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <a href="/artists/<?= $artist->getArtistId() ?>"
                        class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
                        <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                            <?= htmlspecialchars($artist->getFirstName()) ?>             <?= htmlspecialchars($artist->getLastName()) ?>
                        </span>
                    </a>

                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <?php
                            $type = "artist";
                            $item = $artist;
                            require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php";

                            $imagePath = "/assets/images/artists/square-thumb/" . $artist->getArtistId() . ".jpg";
                            $placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
                            $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                        ?>
                        <img
                            src="<?= $correctImagePath ?>"
                            alt="Artist Image"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;"
                        >
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (sizeof($artworkSearchResults) > 0): ?>
        <div class="d-flex align-items-center mt-4 mb-2">
            <h3 class="flex-grow-1 mb-0">Artworks</h3>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <p class="text-muted mb-0">
                Found:
                <?= sizeof($artworkSearchResults) . ' ' . (sizeof($artworkSearchResults) === 1 ? 'artwork' : 'artworks'); ?>
            </p>

            <!-- Sorting form for artworks -->
            <form method="get" class="d-flex align-items-center gap-2">
                <?php foreach ($_GET as $key => $value): ?>
                    <?php if (!in_array($key, ['sortParameter', 'sortArtwork'])): ?>
                        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                    <?php endif; ?>
                <?php endforeach; ?>

                <select name="sortParameter" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="Title" <?= $sortParameter == "Title" ? 'selected' : '' ?>>Title</option>
                    <option value="LastName" <?= $sortParameter == "LastName" ? 'selected' : '' ?>>Artist name</option>
                    <option value="YearOfWork" <?= $sortParameter == "YearOfWork" ? 'selected' : '' ?>>Year</option>
                </select>

                <select name="sortArtwork" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="ascending" <?= !$sortArtwork ? 'selected' : '' ?>>ascending</option>
                    <option value="descending" <?= $sortArtwork ? 'selected' : '' ?>>descending</option>
                </select>
            </form>
        </div>
        <ul class="list-group">
            <?php foreach ($artworkSearchResults as $combined): ?>
                <?php $artwork = $combined->getArtwork(); ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <a href="/artworks/<?= $artwork->getArtworkId() ?>"
                        class="d-flex align-items-center flex-grow-1 text-dark link-underline-on-hover" style="min-width:0;">
                        <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                            &quot;<?= htmlspecialchars($artwork->getTitle()) ?>&quot;
                            by <?= htmlspecialchars($combined->getArtistFirstName()) ?>
                            <?= htmlspecialchars($combined->getArtistLastName()) ?>,
                            published <?= htmlspecialchars($artwork->getYearOfWork()) ?>
                        </span>
                    </a>

                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <?php
                        $type = "artwork";
                        $item = $artwork;
                        require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php";

                        $imagePath = "/assets/images/works/square-small/" . $artwork->getImageFileName() . ".jpg";
                        $placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg";
                        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                        ?>
                        <img
                            src="<?= $correctImagePath ?>" alt="Artwork Image"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-info mt-4">
        <h4>No Results Found</h4>

        <?php if (!empty($isAdvancedSearch)): ?>
            <p>No results were found for your advanced search criteria.</p>

            <?php if (!empty($filterBy)): ?>
                <p><strong>Filter:</strong> <?= ucfirst(htmlspecialchars($filterBy)) ?></p>
            <?php endif; ?>
            <?php if (!empty($artistName)): ?>
                <p><strong>Artist Name:</strong> <?= htmlspecialchars($artistName) ?></p>
            <?php endif; ?>

            <?php
            $today = date("Y");
            if (!empty($artistStartDate) && empty($artistEndDate)) {
                echo "<p><strong>Artist Year Range:</strong> " . htmlspecialchars($artistStartDate) . " - $today</p>";
            } elseif (empty($artistStartDate) && !empty($artistEndDate)) {
                echo "<p><strong>Artist Year Range:</strong> 0 - " . htmlspecialchars($artistEndDate) . "</p>";
            } elseif (!empty($artistStartDate) && !empty($artistEndDate)) {
                echo "<p><strong>Artist Year Range:</strong> " . htmlspecialchars($artistStartDate) . " - " . htmlspecialchars($artistEndDate) . "</p>";
            }

            if (!empty($artworkTitle)) {
                echo "<p><strong>Artwork Title:</strong> " . htmlspecialchars($artworkTitle) . "</p>";
            }
            if (!empty($artistNationality)) {
                echo "<p><strong>Artist Nationality:</strong> " . htmlspecialchars($artistNationality) . "</p>";
            }
            if (!empty($artworkGenre)) {
                echo "<p><strong>Artwork Genre:</strong> " . htmlspecialchars($artworkGenre) . "</p>";
            }

            if (!empty($artworkStartDate) && empty($artworkEndDate)) {
                echo "<p><strong>Artwork Year Range:</strong> " . htmlspecialchars($artworkStartDate) . " - $today</p>";
            } elseif (empty($artworkStartDate) && !empty($artworkEndDate)) {
                echo "<p><strong>Artwork Year Range:</strong> 0 - " . htmlspecialchars($artworkEndDate) . "</p>";
            } elseif (!empty($artworkStartDate) && !empty($artworkEndDate)) {
                echo "<p><strong>Artwork Year Range:</strong> " . htmlspecialchars($artworkStartDate) . " - " . htmlspecialchars($artworkEndDate) . "</p>";
            }
            ?>
        <?php else: ?>
            <p>No results were found for the search term <strong>"<?= htmlspecialchars($searchQuery) ?>"</strong>.</p>
        <?php endif; ?>

        <div class="mt-3">
            <a href="/artists" class="btn btn-primary me-2">Browse All Artists</a>
            <a href="/artworks" class="btn btn-primary">Browse All Artworks</a>
        </div>
    </div>
<?php endif; ?>