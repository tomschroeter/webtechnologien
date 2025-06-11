<!DOCTYPE html>
<html lang="en">

<?php
  require_once dirname(__DIR__)."/src/head.php";
require_once dirname(__DIR__)."/src/repositories/SubjectRepository.php";
require_once dirname(__DIR__)."/src/navbar.php";

$subjectRepository = new SubjectRepository(new Database());

$subjects = $subjectRepository->getAllSubjects();
?>

<body class="container">
  <?php
session_start();
// Handle Add/Remove Favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['favoriteArtworks'])) {
            $_SESSION['favoriteArtworks'] = [];
        }
        $artworkId = (int)$_POST['artworkId'];
        if ($_POST['action'] === 'add_to_favorites') {
            if (!in_array($artworkId, $_SESSION['favoriteArtworks'])) {
                $_SESSION['favoriteArtworks'][] = $artworkId;
            }
        } elseif ($_POST['action'] === 'remove_from_favorites') {
            if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                unset($_SESSION['favoriteArtworks'][$key]);
                $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks']);
            }
        }
    } catch (Exception $e) {
      $message = "Error updating favorites. Please try again.";
      $messageType = "danger";
    }
}
?>

  <h1 class="mt-3 mb-3">Themen</h1>
  <p class="text-muted">Gefunden: <?php echo count($subjects)?> Themen</p>

  <!-- List to display all subjects -->
  <ul class="list-group mb-5">
    <?php foreach ($subjects as $subject): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="<?php echo route('subjects', ['id' => $subject->getSubjectId()]) ?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <span><?php echo $subject->getSubjectName() ?></span>
          <!-- Checks if subject image exists -->
          <?php $imagePath =  "/assets/images/subjects/square-thumbs/".$subject->getSubjectId().".jpg";
        $placeholderPath = "/assets/placeholder/subjects/square-thumb/placeholder.svg";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            $correctImagePath = $imagePath;
        } else {
            $correctImagePath = $placeholderPath;
        }
        ?>
          <img src="<?php echo $correctImagePath?>" alt="Themenbild" style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php require_once 'bootstrap.php'; ?>
</body>
</html>
