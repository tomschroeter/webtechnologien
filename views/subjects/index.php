<?php
// Include helper function for resolving image paths or fallbacks
require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
?>

<!-- Page Heading -->
<h1 class="mt-4 mb-3">Subjects</h1>
<p class="text-muted">Found: <?php echo count($subjects) ?> subjects</p>

<!-- List to display all subjects -->
<ul class="list-group mb-5">
  <?php if (!empty($subjects)): ?>
    <?php foreach ($subjects as $subject): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="/subjects/<?php echo $subject->getSubjectId() ?>"
          class="d-flex justify-content-between align-items-center w-100 link-underline-on-hover text-dark">

          <!-- Display subject name -->
          <span><?php echo htmlspecialchars($subject->getSubjectName()) ?></span>

          <?php
          // Set path to subject image, fallback to placeholder if not found
          $imagePath = "/assets/images/subjects/square-thumbs/" . $subject->getSubjectId() . ".jpg";
          $placeholderPath = "/assets/placeholder/subjects/square-thumb/placeholder.svg";
          $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
          ?>

          <!-- Display subject image thumbnail -->
          <img src="<?php echo $correctImagePath ?>" alt="Subject image"
            style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <!-- Message when no subjects are available -->
    <li class="list-group-item">No subjects found.</li>
  <?php endif; ?>
</ul>