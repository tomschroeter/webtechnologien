<?php
require_once dirname(__DIR__) . "/components/find-image-ref.php";

function renderArtworkCardList($artworks)
{
    foreach ($artworks as $artwork) {
        $artworkLink = route('artworks', ['id' => $artwork->getArtworkId()]);
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
                            <form method="post" action="/favorites-handler.php" class="d-flex">
                                <?php
                                $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($artwork->getArtworkId(), $_SESSION['favoriteArtworks']);
                                ?>
                                <?php if ($isInFavorites): ?>
                                    <input type="hidden" name="action" value="remove_artwork_from_favorites">
                                    <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
                                    <button type="submit" class="btn btn-outline-danger">
                                        ♥
                                    </button>
                                <?php else: ?>
                                    <input type="hidden" name="action" value="add_artwork_to_favorites">
                                    <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
                                    <button type="submit" class="btn btn-primary">
                                        ♡
                                    </button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>