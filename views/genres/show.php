<br>
<h1><?php echo htmlspecialchars($genre->getGenreName()) ?></h1>

<div class="mt-4 ml-3">
  <div class="row">
    <div class="col-auto">
      <?php
      require_once dirname(dirname(__DIR__)) . "/components/find-image-ref.php";
      $imagePath = "/assets/images/genres/square-medium/" . $genre->getGenreId() . ".jpg";
      $placeholderPath = "/assets/placeholder/genres/square-medium/placeholder.svg";
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
      ?>
      <img src="<?php echo $correctImagePath ?>" alt="<?php echo htmlspecialchars($genre->getGenreName()) ?>"
        style="height: 200px; width: 200px; object-fit: cover;">
    </div>
    <div class="col">
      <p><?php echo $genre->getDescription() ?></p>
      <p>More Infos: <a href=<?php echo $genre->getLink() ?> target="_blank" class="text-decoration-none">Wikipedia</a>
      </p>
    </div>
  </div>
</div>
<div class="mt-4">
  <div class="row">
    <div class="col-md-12">
      <h3>Artworks in this Genre</h3>

      <?php if (!empty($artworks)): ?>
        <div class="row mt-4">
          <?php
          require_once dirname(dirname(__DIR__)) . "/components/artwork-card-list.php";
          renderArtworkCardList($artworks);
          ?>
        </div>
      <?php else: ?>
        <p>No artworks found in this genre.</p>
      <?php endif; ?>
    </div>
  </div>
</div>