<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['isAdmin'] = true; // temporär
?>
<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(__DIR__) . "/src/head.php"; ?>

<body class="container">
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>

  <!-- Login Modal (only visible to non-logged-in users) -->
  <?php if (!isset($_SESSION['username'])): ?>

    <!-- Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form method="POST" action="login.php" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="loginModalLabel">Login</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <input name="username" class="form-control" placeholder="Benutzername" required>
            </div>
            <div class="form-group">
              <input name="password" type="password" class="form-control" placeholder="Passwort" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Login</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
          </div>
        </form>
      </div>
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