<h2 class="flex-grow-1 mb-1 mt-3">Search Results</h2>

<?php 
require_once dirname(dirname(__DIR__)) . "/components/find_image_ref.php";
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
                        <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($value)?>">
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
                <a href="/artists/<?php echo $artist->getArtistId() ?>"
                    class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
                    <!-- Display artist name -->
                    <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                        <?php echo htmlspecialchars($artist->getFirstName()) ?> <?= htmlspecialchars($artist->getLastName()) ?>
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
                    <form method="post" action="/api/favorites/artist/<?php echo $artist->getArtistId() ?>" class="mr-2 mb-0 d-none fallback-form">
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
                    <?php $imagePath = "/assets/images/artists/square-thumb/".$artist->getArtistId().".jpg";
                        $placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
                        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                    ?>
                    <img src="<?php echo $correctImagePath?>" alt="Artist Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
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
                        <input type="hidden" name="<?php echo htmlspecialchars($key)?>" value="<?php echo htmlspecialchars($value)?>">
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
                <a href="/artworks/<?php echo $combined->getArtwork()->getArtworkId()?>"
                    class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark" style="min-width:0;">
                    <!-- Display artwork title, artist name and year of publishment -->
                    <span class="text-truncate" style="max-width: 60%; white-space: normal;">
                        <?php echo '&quot;' . htmlspecialchars($combined->getArtwork()->getTitle()) . '&quot; ' .
                            'by ' . htmlspecialchars($combined->getArtistFirstName()) . ' ' . htmlspecialchars($combined->getArtistLastName()) . ',' .
                            ' published ' . htmlspecialchars($combined->getArtwork()->getYearOfWork())?>
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
                    <form method="post" action="/api/favorites/artwork/<?php echo $combined->getArtwork()->getArtworkId() ?>" class="mr-2 mb-0 d-none fallback-form">
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
                    <?php $imagePath = "/assets/images/works/square-small/".$combined->getArtwork()->getImageFileName().".jpg";
                        $placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg";
                        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
                    ?>
                    <img src="<?php echo $correctImagePath?>" alt="Artwork Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem;">
                </div>
            </li>
        <?php endforeach?>
        </ul>
    <?php endif; ?>

    <!-- Output if search didn't return a result -->
    <?php else: ?>
        <div class="alert alert-info mt-4">
            <h4>No Results Found</h4>
            <p>No results were found for the search term <strong>"<?php echo htmlspecialchars($searchQuery) ?>"</strong>.</p>
            <div class="mt-3">
                <a href="/artists" class="btn btn-primary mr-2">Browse All Artists</a>
                <a href="/artworks" class="btn btn-primary">Browse All Artworks</a>
            </div>
        </div>
<?php endif; ?>
