<?php
require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
?>

<h1 class="mb-0 mt-4">Artists</h1>
<div class="d-flex align-items-center justify-content-between mt-3 mb-3">
  <p class="text-muted mb-0">Found: <?= count($artists) ?> artists</p>
  <form method="get" class="w-auto ms-3">
    <select name="sort" onchange="this.form.submit()" class="form-select form-select-sm">
      <option value="ascending" <?= !$sort ? 'selected' : '' ?>>Name (ascending)</option>
      <option value="descending" <?= $sort ? 'selected' : '' ?>>Name (descending)</option>
    </select>
  </form>
</div>

<ul class="list-group mb-5">
  <?php if (!empty($artists)): ?>
    <?php foreach ($artists as $artist): ?>
      <?php
      $imagePath = "/assets/images/artists/square-thumb/" . $artist->getArtistId() . ".jpg";
      $placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
      ?>
      <li class="list-group-item p-2">
        <a href="/artists/<?= $artist->getArtistId() ?>"
          class="d-flex justify-content-between align-items-center text-dark w-100 link-underline-on-hover"
          style="padding-left: 1rem; padding-right: 1rem;">
          <span><?= htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?></span>
          <img src="<?= $correctImagePath ?>" alt="Picture of Artist"
            style="max-width: 100px; max-height: 100px; object-fit: cover; margin-left: 1rem;">
        </a>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <li class="list-group-item">No artists found.</li>
  <?php endif; ?>
</ul>