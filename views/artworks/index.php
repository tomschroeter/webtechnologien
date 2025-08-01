<?php
// Include helper function to get image path or placeholder if image is missing
require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
?>

<!-- Page title -->
<h1 class="mb-0 mt-4">Artworks</h1>

<div class="d-flex align-items-center justify-content-between mt-3 mb-3">
  <!-- Display total artworks found -->
  <p class="text-muted mb-0">Found: <?= count($artworks) ?> artworks</p>

  <div class="d-flex gap-2">
    <!-- Sort by field selector -->
    <form method="get" class="m-0">
      <!-- Preserve current sort order when changing sort field -->
      <input type="hidden" name="order" value="<?= htmlspecialchars($sortOrder) ?>">
      <select name="sort" onchange="this.form.submit()" class="form-select form-select-sm">
        <option value="title" <?= $sortBy == 'title' ? 'selected' : '' ?>>Title</option>
        <option value="artist" <?= $sortBy == 'artist' ? 'selected' : '' ?>>Artist name</option>
        <option value="year" <?= $sortBy == 'year' ? 'selected' : '' ?>>Year</option>
      </select>
    </form>

    <!-- Sort order selector -->
    <form method="get" class="m-0">
      <!-- Preserve current sort field when changing order -->
      <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
      <select name="order" onchange="this.form.submit()" class="form-select form-select-sm">
        <option value="asc" <?= $sortOrder == 'asc' ? 'selected' : '' ?>>ascending</option>
        <option value="desc" <?= $sortOrder == 'desc' ? 'selected' : '' ?>>descending</option>
      </select>
    </form>
  </div>
</div>

<!-- List of artworks -->
<ul class="list-group mb-5">
  <?php if (!empty($artworks)): ?>
    <?php foreach ($artworks as $artwork): ?>
      <?php
      $imagePath = "/assets/images/works/square-small/" . $artwork->getImageFileName() . ".jpg";
      $placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg";
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
      ?>
      <li class="list-group-item py-3 px-4">
        <a href="/artworks/<?= $artwork->getArtworkID() ?>"
          class="d-flex justify-content-between align-items-center text-dark w-100 link-underline-on-hover px-2">
          <div>
            <!-- Artwork title -->
            <h5 class="mb-1"><?= htmlspecialchars($artwork->getTitle()) ?></h5>

            <!-- Artist name and year of work -->
            <p class="mb-1">
              <?= isset($artwork->artistName) ? htmlspecialchars($artwork->artistName) : 'Unknown Artist' ?>
              <?php if ($artwork->getYearOfWork()): ?>
                <span class="text-muted"> (<?= htmlspecialchars($artwork->getYearOfWork()) ?>)</span>
              <?php endif; ?>
            </p>
          </div>

          <!-- Artwork thumbnail image -->
          <img src="<?= $correctImagePath ?>" alt="<?= htmlspecialchars($artwork->getTitle()) ?>" class="img-fluid rounded"
            style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <!-- Message shown when no artworks are found -->
    <li class="list-group-item">No artworks found.</li>
  <?php endif; ?>
</ul>