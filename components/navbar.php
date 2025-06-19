<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<div class="fullwidth">
  <nav class="navbar fixed-top navbar-expand-sm navbar-light" style="background-color:rgb(240, 243, 246)">
    <a class="navbar-brand" href="/">
</invoke>
      <img src="/assets/svgs/logo.svg" alt="Logo" style="height: 40px; width: 40px; margin-left: 0.5rem;">
    </a>
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavId"
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
      <ul class="navbar-nav me-auto mt-2 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link<?php echo ($currentRoute === 'home') ? ' active fw-bold' : ''; ?>" aria-current="<?php echo ($currentRoute === 'home') ? 'page' : false; ?>" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?php echo ($currentRoute === 'about') ? ' active fw-bold' : ''; ?>" aria-current="<?php echo ($currentRoute === 'about') ? 'page' : false; ?>" href="/about">About</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle<?php echo $isBrowseActive ? ' active fw-bold' : ''; ?>" href="#" id="dropdownBrowse" data-bs-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">Browse</a>
          <ul class="dropdown-menu" aria-labelledby="dropdownBrowse">
        <li><a class="dropdown-item<?php echo ($currentRoute === 'artists') ? ' active fw-bold' : ''; ?>" href="/artists">Artists</a></li>
        <li><a class="dropdown-item<?php echo ($currentRoute === 'artworks') ? ' active fw-bold' : ''; ?>" href="/artworks">Artworks</a></li>
        <li><a class="dropdown-item<?php echo ($currentRoute === 'genres') ? ' active fw-bold' : ''; ?>" href="/genres">Genres</a></li>
        <li><a class="dropdown-item<?php echo ($currentRoute === 'subjects') ? ' active fw-bold' : ''; ?>" href="/subjects">Subjects</a></li>
          </ul>
        </li>
      </ul>

      <div class="d-flex align-items-center" style="gap: 0.5rem;">
        <form class="my-2 my-lg-0" action="/search" method="GET">
            <div class="input-group">
                <input
                    class="form-control"
                    name="searchQuery"
                    type="text"
                    placeholder="Search"
                    aria-label="Search field"
                    required
                    minlength="3"
                >
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </div>
        </form>

        <a href="/advanced-search" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="height: 38px; width: 38px;">
          <img src="/assets/svgs/search-advanced.svg" alt="Advanced Search" style="height: 24px; width: 24px;" />
        </a>
      </div>

      <ul class="navbar-nav mt-2 mt-lg-0 ms-lg-4" style="margin-right: 0.5rem !important;">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="dropdownProfile" data-bs-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">
            <img src="/assets/svgs/profile.svg" alt="Profile" style="height: 25px; width: 25px;">
          </a>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownProfile" style="right: 0; left: auto;">
        <?php if (isset($_SESSION['username'])): ?>
          <span class="dropdown-item-text" style="font-size: 1.1rem;">
            <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
          </span>
          <a class="dropdown-item" href="/account">My Account</a>
          <a class="dropdown-item" href="/favorites">View Favorites</a>
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