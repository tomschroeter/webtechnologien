<br>
<!-- Artist full name as page heading -->
<h1><?php echo htmlspecialchars($artist->getFullName()) ?></h1>

<div class="mt-4">
    <div class="row">
        <div class="col-auto mb-3 mb-md-0">
            <?php
            // Include helper to find image or placeholder
            require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";

            $imagePath = "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
            $placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
            $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
            ?>
            <!-- Display artist image -->
            <img src="<?php echo $correctImagePath ?>"
                alt="Image of <?php echo htmlspecialchars($artist->getFullName()) ?>" class="img-fluid rounded">
        </div>

        <div class="col-md-8">
            <!-- Artist biography or detailed description -->
            <p><?php echo $artist->getDetails() ?></p>

            <!-- Favorites button: only show if user is logged in -->
            <?php if (isset($_SESSION['customerId'])): ?>
                <div class="favorites-container mb-3">
                    <?php
                    // Set variables for favorites component
                    $type = "artist";
                    $item = $artist;
                    $showLabel = true; // show text label next to button
                    // Include favorites button component (add/remove functionality)
                    require dirname(dirname(__DIR__)) . "/components/add-to-favorites-button.php"
                        ?>
                </div>
            <?php endif ?>

            <!-- Table displaying key artist details -->
            <table class="table table-bordered w-100 mt-4">
                <thead class="table-dark">
                    <tr>
                        <th colspan="2" class="text-center">Artist Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Date:</th>
                        <td>
                            <!-- Show birth year -->
                            <?php echo htmlspecialchars($artist->getYearOfBirth()) ?>
                            <!-- Show death year if available -->
                            <?php if ($artist->getYearOfDeath())
                                echo " - " . htmlspecialchars($artist->getYearOfDeath()) ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Nationality:</th>
                            <td><?php echo htmlspecialchars($artist->getNationality()) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">More Info:</th>
                        <td>
                            <!-- Link to external resource like Wikipedia; opens in new tab -->
                            <a href="<?php echo htmlspecialchars($artist->getArtistLink()) ?>" target="_blank"
                                rel="noopener noreferrer" class="text-decoration-none">
                                Wikipedia
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section to display artworks by this artist -->
    <h2 class="mt-5">Artworks by <?php echo htmlspecialchars($artist->getFullName()) ?></h2>
    <div class="row mt-4">
        <?php
        // Include reusable artwork card list component
        require_once dirname(dirname(__DIR__)) . "/components/artwork-card-list.php";
        // Render artworks passed from controller
        renderArtworkCardList($artworks);
        ?>
    </div>
</div>