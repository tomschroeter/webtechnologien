<?php
// Session starten, aber nur wenn noch keine läuft
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// TEST: Temporär gesetzter Nutzer (für Tests, bis Login existiert)
$_SESSION['customerId'] = 1;

// Prüfen, ob User gesetzt ist (sicherheitscheck)
if (!isset($_SESSION['customerId'])) {
    die("Not logged in – customerId missing in session.");
}

require_once "bootstrap.php"; // Initialisiert DB & Klassen
require_once "classes/Review.php";
require_once "repositories/ReviewRepository.php";

// Formulardaten holen
$artworkId = $_POST['artworkId'] ?? null;
$customerId = $_SESSION['customerId'];
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
$comment = trim($_POST['comment'] ?? '');

// Validierung
if (!$artworkId || !$rating || $rating < 1 || $rating > 5 || empty($comment)) {
    die("Invalid review data.");
}

// DB und Repository initialisieren
$db = new Database();
$repo = new ReviewRepository($db);

// Prüfen, ob der User dieses Artwork schon bewertet hat
if ($repo->hasUserReviewed($customerId, $artworkId)) {
    die("You have already reviewed this artwork.");
}

// Review-Objekt erstellen
$review = new Review(
    null,                      // ReviewId (wird von DB generiert)
    $artworkId,
    $customerId,
    date('Y-m-d H:i:s'), 
    $rating,
    $comment
);

// In DB speichern
$repo->addReview($review);

// Zurück zur Artwork-Seite
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;


