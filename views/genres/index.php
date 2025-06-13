<?php
require_once dirname(dirname(__DIR__)) . "/components/find_image_ref.php";
?>
<h1 class="mt-3 mb-3">Genres</h1>
<p class="text-muted">Found: <?php echo count($genres)?> genres</p>

<!-- List to display all genres -->
<ul class="list-group mb-5">
  <?php if (!empty($genres)): ?>
    <?php foreach ($genres as $genre): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="/genres/<?php echo $genre->getGenreId() ?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <span><?php echo htmlspecialchars($genre->getGenreName()) ?></span>
          <!-- Checks if genre image exists -->
          <?php
  $imagePath =  "/assets/images/genres/square-thumbs/".$genre->getGenreId().".jpg";
  $placeholderPath = "/assets/placeholder/genres/square-thumbs/placeholder.svg";
  $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
  ?>
          <img src="<?php echo $correctImagePath?>" alt="Genre image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <li class="list-group-item">No genres found.</li>
  <?php endif; ?>
</ul>
