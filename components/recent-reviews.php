<?php
/**
 * @component-type smart
 * Fetches its own data and renders recent reviews
 */

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/dtos/ReviewWithCustomerInfoAndArtwork.php";
require_once __DIR__ . "/render-stars.php";

$db = new Database();
$reviewRepository = new ReviewRepository($db);

// Fetch recent reviews
$reviews = $reviewRepository->getRecentReviews();

// Check if valid reviews were returned
if ($reviews && count($reviews) > 0):
  ?>
  <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px;">
    <?php foreach ($reviews as $review):
      // Extract review data
      $artwork = $review->getArtwork();
      $title = htmlspecialchars($artwork->getTitle());
      $artworkId = $artwork->getArtworkId();

      // Customer name and location
      $name = htmlspecialchars($review->getCustomerFullName());
      $location = htmlspecialchars($review->getCustomerCity() . ', ' . $review->getCustomerCountry());

      // Review content
      $comment = $review->getReview()->getComment();
      // Show only the first 200 characters, add "..." if truncated
      $shortComment = mb_strlen($comment) > 200 ? mb_substr($comment, 0, 200) . '...' : $comment;

      // Star rendering
      $rating = $review->getReview()->getRating();
      $stars = renderStars($rating);

      // Format date
      $date = date("F j, Y", strtotime($review->getReview()->getReviewDate()));
      ?>

      <!-- Review Card -->
      <a href="/artworks/<?= $artworkId ?>" class="link-no-underline review-card" aria-label="Review for <?= $title ?>"
        style="
            flex: 1 1 30%;
            min-width: 300px;
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            display: block;
            color: inherit;
            text-decoration: none;">

        <!-- Artwork Title -->
        <h5 class="link-underline-on-hover" style="margin-bottom: 0.5rem; color: inherit;">
          <?= $title ?>
        </h5>

        <!-- Reviewer Info -->
        <p style="margin: 0; font-style: italic; color: #666;">
          <?= $name ?> - <?= $location ?>
        </p>

        <!-- Rating -->
        <div style="color: #f5b301; font-size: 1.2rem; margin: 10px 0 2px;">
          <?= $stars ?>
        </div>

        <!-- Review Date -->
        <p style="font-size: 0.85rem; color: #888; margin-bottom: 10px;">
          <?= $date ?>
        </p>

        <!-- Short Comment -->
        <p style="margin: 0;">
          <?= $shortComment ?>
        </p>

        <!-- Gradient Overlay for visual fade at bottom -->
        <div style="
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 140px;
            background: linear-gradient(to top, #f9f9f9, transparent);
            pointer-events: none;">
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <!-- Fallback when no reviews exist -->
  <p>No reviews found</p>
<?php endif; ?>