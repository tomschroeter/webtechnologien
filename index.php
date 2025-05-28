<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(__DIR__)."/src/head.php"; ?>

<body class="container">
  <?php require_once dirname(__DIR__)."/src/navbar.php"; ?>

  <!-- TODO: Change homepage style at a later time -->
  <!-- TODO: Rewrite compontents to use new query logic -->
  </br>
  <?php // require_once 'components/random-carousel.php';?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Top Rated Artworks</u></h2>
  <?php // require_once 'components/top-rated.php';?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Most Reviewed Artists</u></h2>
  <?php require_once dirname(__DIR__)."/src/components/most-reviewed-artists.php"; ?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Recent Reviews</u></h2>
  <?php // require_once 'components/recent-reviews.php'?>

  <?php require_once 'bootstrap.php'; ?>
</body>

</html>