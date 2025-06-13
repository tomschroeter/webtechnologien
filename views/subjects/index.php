<?php
require_once dirname(dirname(__DIR__)) . "/components/find_image_ref.php";
?>
<h1 class="mt-3 mb-3">Subjects</h1>
<p class="text-muted">Found: <?php echo count($subjects)?> subjects</p>

<!-- List to display all subjects -->
<ul class="list-group mb-5">
  <?php if (!empty($subjects)): ?>
    <?php foreach ($subjects as $subject): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="/subjects/<?php echo $subject->getSubjectId() ?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <span><?php echo htmlspecialchars($subject->getSubjectName()) ?></span>
          <!-- Checks if subject image exists -->
          <?php $imagePath =  "/assets/images/subjects/square-thumbs/".$subject->getSubjectId().".jpg";
        $placeholderPath = "/assets/placeholder/subjects/square-thumb/placeholder.svg";
        $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
        ?>
          <img src="<?php echo $correctImagePath?>" alt="Subject image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <li class="list-group-item">No subjects found.</li>
  <?php endif; ?>
</ul>
