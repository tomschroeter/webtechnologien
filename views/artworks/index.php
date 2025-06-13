<!-- Form providing the ability to sort artworks -->
<div class="d-flex align-items-center mt-3 mb-3">
  <h1 class="flex-grow-1 mb-0">Artworks</h1>
  <div class="d-flex gap-2">

    <!-- Sort field selector -->
    <form method="get" class="mx-2">
      <!-- Preserve the current sort order when changing field -->
      <input type="hidden" name="order" value="<?php echo $sortOrder ?>">
      <select name="sort" onchange="this.form.submit()" class="form-select">
        <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''?>>Title</option>
        <option value="artist" <?php echo $sortBy == 'artist' ? 'selected' : ''?>>Artist name</option>
        <option value="year" <?php echo $sortBy == 'year' ? 'selected' : ''?>>Year</option>
      </select>
    </form>

    <!-- Sort order selector -->
    <form method="get">
      <!-- Preserve the current sort field when changing order -->
      <input type="hidden" name="sort" value="<?php echo $sortBy ?>">
      <select name="order" onchange="this.form.submit()" class="form-select">
        <option value="asc" <?php echo $sortOrder == 'asc' ? 'selected' : ''?>>ascending</option>
        <option value="desc" <?php echo $sortOrder == 'desc' ? 'selected' : ''?>>descending</option>
      </select>
    </form>

  </div>
</div>

<p class="text-muted">Found: <?php echo count($artworks)?> artworks</p>

<!-- List to display all artworks -->
<ul class="list-group mb-5">
  <?php foreach ($artworks as $artwork): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <a href="<?php echo route('artworks', ['id' => $artwork->getArtworkId()])?>"
         class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
        <div>
          <h5 class="mb-1"><?php echo $artwork->getTitle()?></h5>
          <p class="mb-1">
            <?php
              // Get artist name - need to handle this in controller instead
              echo isset($artwork->artistName) ? $artwork->artistName : 'Unknown Artist';
            ?>
            <?php if ($artwork->getYearOfWork()): ?>
              <span class="text-muted">(<?php echo $artwork->getYearOfWork()?>)</span>
            <?php endif; ?>
          </p>
        </div>
        <!-- Check if artwork image exists -->
        <?php $imagePath = "/assets/images/works/square-small/".$artwork->getImageFileName().".jpg";
      $placeholderPath = "/assets/placeholder/works/square-small/placeholder.svg";
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
      ?>
        <img src="<?php echo $correctImagePath?>" alt="<?php echo $artwork->getTitle()?>" 
             style="max-width: 100px; max-height: 100px; object-fit: cover;">
      </a>
    </li>
  <?php endforeach; ?>
</ul>
