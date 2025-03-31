<!DOCTYPE html>
<html lang="en">

<?php include 'head.php'; ?>

<body>
  <?php
  include 'db.php';
  include 'navbar.php';
  ?>

  <!-- Test Title -->
  <h1 class="p-2">Home Page</h1>

  <?php include 'components/random-carousel.php'; ?>
  <hr class="break">
  <h2 class="mx-auto" style="width: fit-content;">Top Rated Artworks</h2>
  <?php include 'components/top-rated.php'; ?>

  <?php include 'bootstrap.php'; ?>
</body>

</html>