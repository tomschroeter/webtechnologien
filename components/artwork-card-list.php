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
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <a href="<?= $artworkLink ?>" class="text-decoration-none">
                    <img src="<?= $correctImagePath ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($artwork->getTitle()) ?>">
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center mb-3">
                        <a href="<?= $artworkLink ?>" class="text-body link-underline-on-hover">
                            <?= htmlspecialchars($artwork->getTitle()) ?>
                        </a>
                    </h5>
                    <div class="d-flex align-items-center mt-auto gap-2">
                        <a href="<?= $artworkLink ?>" class="btn btn-primary flex-fill">
                            View
                        </a>
                        <?php if (isset($_SESSION['customerId'])): ?>
                            <?php
                            $type = "artwork";
                            $item = $artwork;
                            require __DIR__ . "/add-to-favorites-button.php";
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>