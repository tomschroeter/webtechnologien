<?php
/**
 * Checks required variables and sets up favorite button state based on type and item.
 *
 * @throws Exception If required variables are missing or type/item do not match expected classes.
 */

// Verify that $type and $item variables are set, otherwise throw an exception.
if (!isset($type) || !isset($item)) {
    throw new Exception("Missing required variables: item or type");
}

// Optional flag to determine if button label should be shown; defaults to false if not set.
$showLabel = $showLabel ?? false;

// Handle logic based on the type of the item (either 'artist' or 'artwork').
if ($type === 'artist') {
    // Ensure $item is an instance of the Artist class, otherwise throw an exception.
    if (!($item instanceof Artist)) {
        throw new Exception("Expected instance of Artist for type 'artist'");
    }

    $id = $item->getArtistId();

    // Check if this artist ID is in the user's favorite artists stored in session.
    $isInFavorites = isset($_SESSION['favoriteArtists']) && in_array($id, $_SESSION['favoriteArtists']);

} elseif ($type === 'artwork') {
    // Ensure $item is an instance of the Artwork class, otherwise throw an exception.
    if (!($item instanceof Artwork)) {
        throw new Exception("Expected instance of Artwork for type 'artwork'");
    }

    $id = $item->getArtworkId();

    // Check if this artwork ID is in the user's favorite artworks stored in session.
    $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($id, $_SESSION['favoriteArtworks']);

} else {
    // Throw an exception if the provided type is not supported.
    throw new Exception("Unsupported type: $type");
}
?>

<!--
    Render a button to toggle favorite status.
    - Button classes switch based on whether the item is in favorites
    - Data attributes:
        - data-type: identifies whether the item is 'artist' or 'artwork'.
        - data-id: unique identifier for the item.
        - data-is-favorite: string 'true' or 'false' indicating favorite status.
    - Displays a heart symbol filled (♥) if favorite, outline (♡) if not.
    - Optionally shows text label to add/remove favorites if $showLabel is true.
-->
<button type="button" class="btn favorite-btn <?php echo $isInFavorites ? 'btn-outline-danger' : 'btn-primary' ?>"
    data-type="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
    data-id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
    data-is-favorite="<?php echo $isInFavorites ? 'true' : 'false' ?>">
    <span class="heart"><?php echo $isInFavorites ? '♥' : '♡' ?></span>
    <?php if ($showLabel): ?>
        <?php echo $isInFavorites ? ' Remove from Favorites' : ' Add to Favorites' ?>
    <?php endif; ?>
</button>