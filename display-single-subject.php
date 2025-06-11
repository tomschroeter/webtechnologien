<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__)."/src/head.php";
require_once dirname(__DIR__)."/src/repositories/SubjectRepository.php";
require_once dirname(__DIR__)."/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__)."/src/navbar.php";

session_start();

// TEMP: simulate logged-in user (remove in production)
$_SESSION['customerId'] = 1;
$_SESSION['isAdmin'] = true;

// Handle Add/Remove Artwork Favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['favoriteArtworks'])) {
            $_SESSION['favoriteArtworks'] = [];
        }
        $artworkId = (int)$_POST['artworkId'];
        if ($_POST['action'] === 'add_to_favorites') {
            if (!in_array($artworkId, $_SESSION['favoriteArtworks'])) {
                $_SESSION['favoriteArtworks'][] = $artworkId;
                $message = "Artwork added to favorites!";
                $messageType = "success";
            } else {
                $message = "Artwork is already in your favorites.";
                $messageType = "info";
            }
        } elseif ($_POST['action'] === 'remove_from_favorites') {
            if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                unset($_SESSION['favoriteArtworks'][$key]);
                $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks']); // Re-index array
                $message = "Artwork removed from your favorites!";
                $messageType = "success";
            }
        }
    } catch (Exception $e) {
        $message = "Error updating favorites. Please try again.";
        $messageType = "danger";
    }
}

$db = new Database();
$subjectRepository = new SubjectRepository($db);
$artworkRepository = new ArtworkRepository($db);

// Checks if id is set correctly in URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $subjectId = $_GET['id'];
} else {
    header("Location: /error.php?error=invalidParam");
    exit();
}

// Checks if subject exists in database
try {
    $subject = $subjectRepository->getSubjectById($subjectId);
    $artworks = $artworkRepository->getArtworksBySubject($subjectId);
} catch (Exception $e) {
    header("Location: /error.php?error=invalidID&type=subject");
    exit();
}
?>

<body class="container">
	<br>
	<h1> <?php echo $subject->getSubjectName()?></h1>
    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
	<div class="container mt-3">
		<div class="row">
            <!-- Displays subject image -->
            <?php $imagePath =  "/assets/images/subjects/square-medium/".$subject->getSubjectId().".jpg";
$placeholderPath = "/assets/placeholder/subjects/square-medium/placeholder.svg";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
    $correctImagePath = $imagePath;
} else {
    $correctImagePath = $placeholderPath;
}
?>
            <img src="<?php echo $correctImagePath?>" alt="Bild von <?php echo $subject->getSubjectName()?>">
		</div>
        <h2 class="mt-5">Artworks for <?php echo $subject->getSubjectName()?> </h2>
        <div class="row mt-4">
            <?php 
                require_once __DIR__ . '/components/artwork-card-list.php';
                renderArtworkCardList($artworks);
            ?>
        </div>
	</div>
	<?php require_once 'bootstrap.php'; ?>
</body>
</html>