<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(__DIR__) . "/src/head.php"; ?>

<body>
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>

  <!-- Login success message -->
  <?php if (isset($_GET['login']) && $_GET['login'] === 'success' && isset($_SESSION['username'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <strong>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</strong>
      You have successfully logged in.
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  <?php endif; ?>

  <!-- Welcome message for newly registered users -->
  <?php if (isset($_GET['welcome']) && isset($_SESSION['username'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <strong>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</strong>
      Your registration was successful and you are now logged in. Enjoy exploring our art gallery!
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  <?php endif; ?>

  <div style="width: 100%; box-sizing: border-box; margin-bottom: 30px;">
    <?php require_once dirname(__DIR__) . '/src/components/random-carousel.php' ?>
  </div>

  <div
    style="display: flex; gap: 40px; justify-content: center; flex-wrap: wrap; width: 100%; padding: 30px; box-sizing: border-box; margin-bottom: 30px;">
    <div
      style="flex: 1 1 45%; min-width: 300px; justify-content: center; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
      <h2 class="mx-auto pb-4" style="width: fit-content;">Top Rated Artworks</h2>
      <?php require_once dirname(__DIR__) . '/src/components/top-rated.php'; ?>
    </div>

    <div
      style="flex: 1 1 45%; min-width: 300px; justify-content: center; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
      <h2 class="mx-auto pb-4" style="width: fit-content;">Most Reviewed Artists</h2>
      <?php require_once dirname(__DIR__) . "/src/components/most-reviewed-artists.php"; ?>
    </div>
  </div>

  <div style="width: 100%; box-sizing: border-box; padding: 30px;">
    <div
      style="width: 100%; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 30px;">
      <h2 class="mx-auto pb-4" style="width: fit-content;">Recent Reviews</h2>
      <?php require_once dirname(__DIR__) . '/src/components/recent-reviews.php'; ?>
    </div>
  </div>
  <?php require_once 'bootstrap.php'; ?>
</body>

</html>