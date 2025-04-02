<!DOCTYPE html>
<html lang="en">

<?php include 'head.php'; ?>

<body>
  <?php
  include 'db.php';
  include 'navbar.php';
  ?>

  <!-- TODO: Change homepage style at a later time -->
  </br>
  <?php include 'components/random-carousel.php'; ?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Top Rated Artworks</u></h2>
  <?php include 'components/top-rated.php'; ?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Most Reviewed Artists</u></h2>
  <?php include 'components/most-reviewed-artists.php' ?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Recent Reviews</u></h2>
  <?php include 'components/recent-reviews.php' ?>

  <?php include 'bootstrap.php'; ?>
</body>

</html>