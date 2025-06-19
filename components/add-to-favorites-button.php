<?php
if (!isset($type) || !isset($item)) {
    throw new Exception("Missing required variables: item or type");
}

$showLabel = $showLabel ?? false;

if ($type === 'artist') {
    if (!($item instanceof Artist)) {
        throw new Exception("Expected instance of Artist for type 'artist'");
    }
    $id = $item->getArtistId();
    $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($id, $_SESSION['favoriteArtists']);

} elseif ($type === 'artwork') {
    if (!($item instanceof Artwork)) {
        throw new Exception("Expected instance of Artwork for type 'artwork'");
    }
    $id = $item->getArtworkId();
    $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($id, $_SESSION['favoriteArtworks']);

} else {
    throw new Exception("Unsupported type: $type");
}
?>

<button type="button" class="btn favorite-btn <?php echo $isInFavorites ? 'btn-outline-danger' : 'btn-primary' ?>"
    data-type="<?php echo $type ?>" data-id="<?php echo $id ?>"
    data-is-favorite="<?php echo $isInFavorites ? 'true' : 'false' ?>">
    <span class="heart"><?php echo $isInFavorites ? '♥' : '♡' ?></span>
    <?php if ($showLabel): ?>
        <?php echo $isInFavorites ? ' Remove from Favorites' : ' Add to Favorites' ?>
    <?php endif; ?>
</button>