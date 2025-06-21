<br>
<!-- Display the subject's name as the main heading -->
<h1><?php echo htmlspecialchars($subject->getSubjectName()) ?></h1>

<div class="mt-4 ml-3">
  <div class="row">
    <div class="col-md-12 p-0">
      <?php
      // Dynamically determine the image path for the subject
      require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
      $imagePath = "/assets/images/subjects/square-medium/" . $subject->getSubjectId() . ".jpg";
      $placeholderPath = "/assets/placeholder/subjects/square-medium/placeholder.svg";

      // Fallback to placeholder if subject image doesn't exist
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
      ?>

      <!-- Display subject image -->
      <img src="<?php echo $correctImagePath ?>" alt="<?php echo htmlspecialchars($subject->getSubjectName()) ?>"
        style="height: 200px">
    </div>

    <div class="mt-4 col-md-12 p-0">
      <h3>Artworks with this Subject</h3>

      <?php if (!empty($artworks)): ?>
        <div class="row mt-4">
          <?php
          // Render a grid/list of artworks related to this subject
          require_once dirname(dirname(__DIR__)) . "/components/artwork-card-list.php";
          renderArtworkCardList($artworks);
          ?>
        </div>
      <?php else: ?>
        <!-- Message when no artworks are found -->
        <p>No artworks found with this subject.</p>
      <?php endif; ?>
    </div>
  </div>
</div>