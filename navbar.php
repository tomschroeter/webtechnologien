<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<div class="navbar-fullwidth">
  <nav class="navbar sticky-top navbar-expand-sm navbar-light" style="background-color:rgb(240, 243, 246)">
    <a class="navbar-brand" href="/">
</invoke>
      <img src="/assets/svgs/logo.svg" alt="Logo" style="height: 40px; width: 40px;">
    </a>
    <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId"
      aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavId">
      <?php
      // Get the current path from REQUEST_URI for MVC routing
      $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      $currentPath = trim($currentPath, '/'); // Remove leading/trailing slashes
      
      // Determine current route based on path
      if (empty($currentPath) || $currentPath === 'index') {
          $currentRoute = 'home';
      } elseif ($currentPath === 'about') {
          $currentRoute = 'about';
      } elseif (strpos($currentPath, 'artists') === 0) {
          $currentRoute = 'artists';
      } elseif (strpos($currentPath, 'artworks') === 0) {
          $currentRoute = 'artworks';
      } elseif (strpos($currentPath, 'genres') === 0) {
          $currentRoute = 'genres';
      } elseif (strpos($currentPath, 'subjects') === 0) {
          $currentRoute = 'subjects';
      } else {
          $currentRoute = '';
      }
      
      $browseRoutes = ['artists', 'artworks', 'genres', 'subjects'];
      $isBrowseActive = in_array($currentRoute, $browseRoutes);
      ?>
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item <?php echo ($currentRoute === 'home') ? 'active' : ''; ?>">
          <a class="nav-link" href="/">Home <?php if ($currentRoute === 'home'): ?><span class="sr-only">(current)</span><?php endif; ?></a>
        </li>
        <li class="nav-item <?php echo ($currentRoute === 'about') ? 'active' : ''; ?>">
          <a class="nav-link" href="/about">About <?php if ($currentRoute === 'about'): ?><span class="sr-only">(current)</span><?php endif; ?></a>
        </li>
        <li class="nav-item dropdown <?php echo $isBrowseActive ? 'active' : ''; ?>">
          <a class="nav-link dropdown-toggle" href="#" id="dropdownBrowse" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">Browse</a>
          <div class="dropdown-menu" aria-labelledby="dropdownBrowse">
            <a class="dropdown-item <?php echo ($currentRoute === 'artists') ? 'active' : ''; ?>" href="/artists">Artists</a>
            <a class="dropdown-item <?php echo ($currentRoute === 'artworks') ? 'active' : ''; ?>" href="/artworks">Artworks</a>
            <a class="dropdown-item <?php echo ($currentRoute === 'genres') ? 'active' : ''; ?>" href="/genres">Genres</a>
            <a class="dropdown-item <?php echo ($currentRoute === 'subjects') ? 'active' : ''; ?>" href="/subjects">Subjects</a>
          </div>
        </li>
      </ul>

      <form class="form-inline my-2 my-lg-0" action="/search" method="GET">
        <input class="form-control mr-sm-2" name="searchQuery" type="text" placeholder="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form>

      <ul class="navbar-nav mt-2 mt-lg-0 mx-4">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="dropdownProfile" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <img src="/assets/svgs/profile.svg" alt="Profile" style="height: 25px; width: 25px;">
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownProfile">
            <?php if (isset($_SESSION['username'])): ?>
              <span class="dropdown-item-text font-weight-bold text-dark">
                <?= htmlspecialchars($_SESSION['username']) ?>
              </span>
              <a class="dropdown-item" href="/account">My Account</a>
              <a class="dropdown-item" href="/favorites">Favorites</a>
              <?php if ($_SESSION['isAdmin'] ?? false): ?>
                <a class="dropdown-item" href="/manage-users">Manage Users</a>
              <?php endif; ?>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="/logout">Logout</a>
            <?php else: ?>
              <a class="dropdown-item" href="/register">Register</a>
              <a class="dropdown-item" href="/login">Login</a>
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</div>