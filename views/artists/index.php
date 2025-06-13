<?php
require_once dirname(dirname(__DIR__)) . "/components/find_image_ref.php";
?>
<!-- Form providing the ability to sort the order of displayed artists -->
<div class="d-flex align-items-center mt-3 mb-3">
  <h1 class="flex-grow-1 mb-0">Artists</h1>
  <form method="get">
    <select name="sort" onchange="this.form.submit()" class="form-select">
      <option value="ascending" <?php echo !$sort ? 'selected' : ''?>>Name (ascending)</option>
      <option value="descending" <?php echo $sort ? 'selected' : ''?>>Name (descending)</option>
    </select>
  </form>
</div>
<p class="text-muted">Found: <?php echo count($artists)?> artists</p>

<!-- List to display all artists -->
<ul class="list-group mb-5">
  <?php if (!empty($artists)): ?>
    <?php foreach ($artists as $artist): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="/artists/<?php echo $artist->getArtistId()?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <span><?php echo htmlspecialchars($artist->getFirstName())?> <?php echo htmlspecialchars($artist->getLastName())?></span>
          <!-- Checks if artists' image exists -->
          <?php
  $imagePath =  "/assets/images/artists/square-thumb/".$artist->getArtistId().".jpg";
  $placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
  $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
  ?>
          <img src="<?php echo $correctImagePath?>" alt="Artist Image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <li class="list-group-item">No artists found.</li>
  <?php endif; ?>
</ul>
