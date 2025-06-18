<?php
require_once __DIR__ . "/find-image-ref.php";

function renderArtworkCardList($artworks)
{
    foreach ($artworks as $artwork) {
        $artworkLink = "/artworks/" . $artwork->getArtworkId();
        $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
        $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <a href="<?php echo $artworkLink ?>">
                    <img src="<?php echo $correctImagePath ?>" class="card-img-top"
                        alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>">
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">
                        <a href="<?php echo $artworkLink ?>"
                            class="text-body"><?php echo htmlspecialchars($artwork->getTitle()) ?></a>
                    </h5>
                    <div class="d-flex align-items-center mt-auto">
                        <a href="<?php echo $artworkLink ?>" class="btn btn-primary flex-fill mr-2">View</a>
                        <?php if (isset($_SESSION['customerId'])): ?>
                            <?php
                            $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($artwork->getArtworkId(), $_SESSION['favoriteArtworks']);
                            ?>
                            <button type="button" 
                                    class="btn favorite-btn <?php echo $isInFavorites ? 'btn-outline-danger' : 'btn-primary' ?>"
                                    data-type="artwork"
                                    data-id="<?php echo $artwork->getArtworkId() ?>"
                                    data-is-favorite="<?php echo $isInFavorites ? 'true' : 'false' ?>"
                                    title="<?php echo $isInFavorites ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                                    <?php echo $isInFavorites ? '<span class="heart">♥</span>' : '<span class="heart">♡</span>' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>