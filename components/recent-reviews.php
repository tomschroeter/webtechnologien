<?php
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/dtos/ReviewWithCustomerInfoAndArtwork.php";
require_once dirname(__DIR__) . "/components/render-stars.php";

$db = new Database();
$reviewRepository = new ReviewRepository($db);
$reviews = $reviewRepository->getRecentReviews();

if ($reviews):
?>
  <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px;">
    <?php foreach ($reviews as $review): ?>
      <?php
        $title = htmlspecialchars($review->getArtwork()->getTitle());
        $artworkId = $review->getArtwork()->getArtworkId();
        $name = htmlspecialchars($review->getCustomerFirstName() . ' ' . $review->getCustomerLastName());
        $location = htmlspecialchars($review->getCustomerCity() . ', ' . $review->getCustomerCountry());
        $comment = $review->getReview()->getComment();
        $shortComment = mb_substr($comment, 0, 200);
        $rating = $review->getReview()->getRating();
        $stars = renderStars($rating);
        $date = date("F j, Y", strtotime($review->getReview()->getReviewDate()));
      ?>
      <div style="flex: 1 1 30%; min-width: 300px; background: #f9f9f9; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        <h5 style="margin-bottom: 0.5rem;">
          <a href="/artworks/<?= $artworkId ?>" style="color: black;"><?= $title ?></a>
        </h5>
        <p style="margin: 0; font-style: italic; color: #666;"><?= $name ?> – <?= $location ?></p>
        <div style="color: #f5b301; font-size: 1.2rem; margin: 10px 0 2px;"><?= $stars ?></div>
        <p style="font-size: 0.85rem; color: #888; margin-bottom: 10px;"><?= $date ?></p>
        <p style="margin: 0;"><?=$shortComment?></p>
<div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 140px; background: linear-gradient(to top, #f9f9f9, transparent); pointer-events: none;"></div>      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p>No reviews found</p>
<?php endif; ?>
