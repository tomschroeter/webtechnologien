<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__)."/src/head.php";
require_once dirname(__DIR__)."/src/repositories/SubjectRepository.php";
require_once dirname(__DIR__)."/src/navbar.php";
require_once dirname(__DIR__)."/src/components/find_image_ref.php";

$subjectRepository = new SubjectRepository(new Database());

$subjects = $subjectRepository->getAllSubjects();
?>

<body class="container">
  <h1 class="mt-3 mb-3">Subjects</h1>
  <p class="text-muted">Found: <?php echo count($subjects)?> subjects</p>

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
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        ?>
          <img src="<?php echo $correctImagePath?>" alt="Subject image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
