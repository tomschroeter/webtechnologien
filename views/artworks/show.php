<br>
<h1><?php echo htmlspecialchars($artwork->getTitle()) ?></h1>
<h4 class="text-muted">
  by 
  <a href="<?php echo route('artists', ['id' => $artist->getArtistId()]) ?>" class="text-decoration-none">
    <?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>
  </a>
  <?php if ($artwork->getYearOfWork()): ?>
    <span class="text-muted">(<?php echo $artwork->getYearOfWork() ?>)</span>
  <?php endif; ?>
</h4>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-6">
      <!-- Artwork image -->
      <?php
      require_once dirname(dirname(__DIR__)) . "/components/find_image_ref.php";
      $imagePath = "/assets/images/works/medium/" . $artwork->getImageFileName() . ".jpg";
      $placeholderPath = "/assets/placeholder/works/medium/placeholder.svg";
      $correctImagePath = getImagePathOrPlaceholder($imagePath, $placeholderPath);
      ?>
      <img src="<?php echo $correctImagePath ?>" 
           alt="<?php echo htmlspecialchars($artwork->getTitle()) ?>" 
           class="img-fluid mb-3">
    </div>
    
    <div class="col-md-6">
      <p><?php echo htmlspecialchars($artwork->getDescription()) ?></p>
      
      <!-- Add/Remove Artwork Favorites Form -->
      <?php 
      $isInFavorites = isset($_SESSION['favoriteArtworks']) && in_array($artwork->getArtworkID(), $_SESSION['favoriteArtworks']);
      ?>
      <form method="post" action="/favorites-handler.php" class="mb-3">
          <?php if ($isInFavorites): ?>
              <input type="hidden" name="action" value="remove_artwork_from_favorites">
              <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
              <button type="submit" class="btn btn-outline-danger">
                  ♥ Remove from Favorites
              </button>
          <?php else: ?>
              <input type="hidden" name="action" value="add_artwork_to_favorites">
              <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
              <button type="submit" class="btn btn-primary">
                  ♡ Add to Favorites
              </button>
          <?php endif; ?>
      </form>
      
      <!-- Artwork details table -->
      <table class="table table-bordered">
        <thead class="thead-dark">
          <tr><th colspan="2">Artwork Details</th></tr>
        </thead>
        <tr>
          <th>Technique:</th>
          <td><?php echo htmlspecialchars($artwork->getMedium()) ?></td>
        </tr>
        <tr>
          <th>Width:</th>
          <td><?php echo htmlspecialchars($artwork->getWidth()) ?> cm</td>
        </tr>
        <tr>
          <th>Height:</th>
          <td><?php echo htmlspecialchars($artwork->getHeight()) ?> cm</td>
        </tr>
        <?php if ($artwork->getYearOfWork()): ?>
        <tr>
          <th>Year:</th>
          <td><?php echo htmlspecialchars($artwork->getYearOfWork()) ?></td>
        </tr>
        <?php endif; ?>
      </table>
    </div>
  </div>
  
  <!-- Reviews Section -->
  <div class="mt-5">
    <h3>Reviews</h3>
    
    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="mb-4">
        <h4>Add Your Review</h4>
        <form method="post" action="/add-review.php">
          <input type="hidden" name="artworkId" value="<?php echo $artwork->getArtworkId() ?>">
          <div class="form-group">
            <label for="rating">Rating (1-5):</label>
            <select name="rating" id="rating" class="form-control" required>
              <option value="">Select rating</option>
              <option value="1">1 - Poor</option>
              <option value="2">2 - Fair</option>
              <option value="3">3 - Good</option>
              <option value="4">4 - Very Good</option>
              <option value="5">5 - Excellent</option>
            </select>
          </div>
          <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
      </div>
    <?php else: ?>
      <p><a href="/login.php">Login</a> to add a review.</p>
    <?php endif; ?>
    
    <?php if (!empty($reviews)): ?>
      <div class="reviews-list">
        <?php foreach ($reviews as $review): ?>
          <div class="card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <h5 class="card-title">
                  <?php echo str_repeat('★', $review->getRating()) ?>
                  <?php echo str_repeat('☆', 5 - $review->getRating()) ?>
                </h5>
                <small class="text-muted"><?php echo $review->getDateCreated() ?></small>
              </div>
              <p class="card-text"><?php echo htmlspecialchars($review->getComment()) ?></p>
              <small class="text-muted">by <?php echo htmlspecialchars($review->getCustomerUsername ?? 'Anonymous') ?></small>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>No reviews yet. Be the first to review this artwork!</p>
    <?php endif; ?>
  </div>
</div>
