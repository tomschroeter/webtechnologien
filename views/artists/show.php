<br>
<h1><?php echo htmlspecialchars($artist->getFullName()) ?></h1>

<div class="mt-4">
    <div class="row">
        <div class="col-md-4 mb-3 mb-md-0"> <!-- Artist image -->
            <?php
            require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
            $imagePath = "/assets/images/artists/medium/" . $artist->getArtistId() . ".jpg";
            $placeholderPath = "/assets/placeholder/artists/medium/placeholder.svg";
            $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
            ?>
            <img src="<?php echo $correctImagePath ?>"
                alt="Image of <?php echo htmlspecialchars($artist->getFullName()) ?>" class="img-fluid rounded">
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
                </div>
            <?php endif ?>

            <!-- Artist details -->
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
                            <?php echo htmlspecialchars($artist->getYearOfBirth()) ?>
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

    <h2 class="mt-5">Artworks by <?php echo htmlspecialchars($artist->getFullName()) ?></h2>
    <div class="row mt-4">
        <?php
        require_once dirname(dirname(__DIR__)) . "/components/artwork-card-list.php";
        renderArtworkCardList($artworks);
        ?>
    </div>
</div>