<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['customerId'])) {
    header("Location: error.php?error=unauthorized");
    exit();
}

// Handle Add Artist Favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['favoriteArtists'])) {
            $_SESSION['favoriteArtists'] = [];
        }
        if (!isset($_SESSION['favoriteArtworks'])) {
            $_SESSION['favoriteArtworks'] = [];
        }

        if (isset($_POST['artistId'])) {
            $artistId = (int) $_POST['artistId'];
        }
        if (isset($_POST['artworkId'])) {
            $artworkId = (int) $_POST['artworkId'];
        }

        if ($_POST['action'] === 'add_artist_to_favorites') {
            if (!in_array($artistId, $_SESSION['favoriteArtists'])) {
                $_SESSION['favoriteArtists'][] = $artistId;
                $message = "Artist added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artist is already in your favorites.";
                $messageType = "info";
            }
        } elseif ($_POST['action'] === 'remove_artist_from_favorites') {
            if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                unset($_SESSION['favoriteArtists'][$key]);
                $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists']);
                $message = "Artist removed from favorites!";
                $messageType = "success";
            } else {
                $message = "Artist is not in your favorites.";
                $messageType = "info";
            }
        }

        if ($_POST['action'] === 'add_artwork_to_favorites') {
            if (!in_array($artworkId, $_SESSION['favoriteArtworks'])) {
                $_SESSION['favoriteArtworks'][] = $artworkId;
                $message = "Artwork added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is already in your favorites.";
                $messageType = "info";
            }
        } elseif ($_POST['action'] === 'remove_artwork_from_favorites') {
            if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                unset($_SESSION['favoriteArtworks'][$key]);
                $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks']);
                $message = "Artwork removed from favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is not in your favorites.";
                $messageType = "info";
            }
        }
    } catch (Exception $e) {
        $message = "Error updating favorites. Please try again.";
        $messageType = "danger";
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();