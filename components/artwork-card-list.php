<?php
function renderArtworkCardList($artworks) {
    foreach ($artworks as $artwork) {
        $artworkLink = route('artworks', ['id' => $artwork->getArtworkId()]);
        $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";
        $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            $correctImagePath = $imagePath;
        } else {
            $correctImagePath = $placeholderPath;
        }
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <a href="<?php echo $artworkLink ?>" target="_blank">
                    <img src="<?php echo $correctImagePath ?>" class="card-img-top" alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>">
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">
                        <a href="<?php echo $artworkLink ?>" target="_blank" class="text-body"><?php echo htmlspecialchars($artwork->getTitle()) ?></a>
                    </h5>
                    <div class="d-flex align-items-center mt-auto">
                        <a href="<?php echo $artworkLink ?>" target="_blank" class="btn btn-primary flex-fill mr-2">View</a>
                        <form method="post" class="d-flex">
                            <?php
                            $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($artwork->getArtworkId(), $_SESSION['favoriteArtworks']);
                            ?>
                            <?php if ($isInFavorites): ?>
                                <input type="hidden" name="action" value="remove_from_favorites">
                                <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
                                <button type="submit" class="btn btn-outline-danger">
                                    ♥
                                </button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="add_to_favorites">
                                <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
                                <button type="submit" class="btn btn-outline-primary">
                                    ♡
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
