<!DOCTYPE html>
<html lang="en">

<?php
    require_once dirname(__DIR__)."/src/head.php";
require_once dirname(__DIR__)."/src/repositories/SubjectRepository.php";
require_once dirname(__DIR__)."/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__)."/src/navbar.php";

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