<?php
// Try MVC routing first
$mvcHandled = require_once dirname(__DIR__) . "/src/mvc_bootstrap.php";

// If MVC handled the request, we're done
if ($mvcHandled) {
    return;
}

// Otherwise, fall back to original behavior
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(__DIR__) . "/src/head.php"; ?>

<body class="container">
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

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Top Rated Artworks</u></h2>
  <?php // require_once 'components/top-rated.php'; 
  ?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Most Reviewed Artists</u></h2>
  <?php require_once dirname(__DIR__) . "/src/components/most-reviewed-artists.php"; ?>

  <hr class="break">
  <h2 class="mx-auto pb-4" style="width: fit-content;"><u>Recent Reviews</u></h2>
  <?php // require_once 'components/recent-reviews.php'; 
  ?>

  <?php require_once 'bootstrap.php'; ?>
</body>

</html>