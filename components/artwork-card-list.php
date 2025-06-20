<?php
// Include utility function to determine if an image file exists or fallback to a placeholder
require_once __DIR__ . "/find-image-ref.php";

/**
 * Renders a responsive grid of artwork cards.
 * Each card includes:
 * - An image (real or placeholder if not found)
 * - Title of the artwork with a link to its detail page
 * - A "View" button
 * - A "Favorite" button if the user is logged in
 *
 * @param Artwork[] $artworks Array of Artwork objects to display
 *
 * @return void Outputs HTML directly
 */
function renderArtworkCardList($artworks): void
{
    // Loop through each artwork to generate a card
    foreach ($artworks as $artwork) {
        // Generate the URL to the artwork's detail page
        $artworkLink = "/artworks/" . $artwork->getArtworkId();

        // Path to the artwork's image file
        $imagePath = "/assets/images/works/square-medium/" . $artwork->getImageFileName() . ".jpg";

        // Fallback placeholder image if artwork image is missing
        $placeholderPath = "/assets/placeholder/works/square-medium/placeholder.svg";

        // Determine correct image path (either actual image or placeholder)
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        ?>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <!-- Clickable image that links to artwork detail page -->
                <a href="<?= $artworkLink ?>" class="text-decoration-none">
                    <img src="<?= $correctImagePath ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($artwork->getTitle()) ?>">
                </a>

                <div class="card-body d-flex flex-column">
                    <!-- Artwork title with link -->
                    <h5 class="card-title text-center mb-3">
                        <a href="<?= $artworkLink ?>" class="text-body link-underline-on-hover">
                            <?= htmlspecialchars($artwork->getTitle()) ?>
                        </a>
                    </h5>

                    <!-- Button group aligned at bottom of card -->
                    <div class="d-flex align-items-center mt-auto gap-2">
                        <!-- View button -->
                        <a href="<?= $artworkLink ?>" class="btn btn-primary flex-fill">
                            View
                        </a>

                        <?php if (isset($_SESSION['customerId'])): ?>
                            <?php
                            // If user is logged in, show add-to-favorites button
                            $type = "artwork";      // Type passed to favorite button logic
                            $item = $artwork;       // Current artwork object
                
                            // Include reusable button logic
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