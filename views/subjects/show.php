<br>
<h1><?php echo htmlspecialchars($subject->getSubjectName()) ?></h1>

<div class="mt-4 ml-3">
  <div class="row">
    <div>
      <?php
      require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
      $imagePath = "/assets/images/subjects/square-medium/" . $subject->getSubjectId() . ".jpg";
      $placeholderPath = "/assets/placeholder/subjects/square-medium/placeholder.svg";
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
      ?>
      <img src="<?php echo $correctImagePath ?>" alt="<?php echo htmlspecialchars($subject->getSubjectName()) ?>"
        style="height: 200px">
    </div>
  <div class="row">
    <div class="col-md-12 mt-4">
      <h3>Artworks with this Subject</h3>

      <?php if (!empty($artworks)): ?>
        <div class="row mt-4">
          <?php
          require_once dirname(dirname(__DIR__)) . "/components/artwork-card-list.php";
          renderArtworkCardList($artworks);
          ?>
        </div>
      <?php else: ?>
        <p>No artworks found with this subject.</p>
      <?php endif; ?>
    </div>
  </div>
</div>