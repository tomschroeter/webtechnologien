<?php
// Usage: require 'components/artwork-card-list.php'; renderArtworkCardList($artworks);
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
                    <a href="<?php echo $artworkLink ?>" target="_blank" class="btn btn-primary mt-auto">View</a>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
