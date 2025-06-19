<?php
/**
 * @file Navigation bar component
 * 
 * This component renders the fixed-top site navigation using Bootstrap.
 * It dynamically determines the current route based on the request URI,
 * highlights the active link, and shows different options depending on user session state.
 */

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<div class="fullwidth">
  <nav class="navbar fixed-top navbar-expand-sm navbar-light" style="background-color:rgb(240, 243, 246)">

    <!-- Logo linking to homepage -->
    <a class="navbar-brand" href="/">
      <img src="/assets/svgs/logo.svg" alt="Logo" style="height: 40px; width: 40px; margin-left: 0.5rem;">
    </a>

    <!-- Hamburger menu button for mobile view -->
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavId"
      aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation links container -->
    <div class="collapse navbar-collapse" id="collapsibleNavId">
      <?php
      /**
       * Determine current route to apply "active" class to nav items.
       * Uses simple pattern matching on the request URI.
       */
      $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      $currentPath = trim($currentPath, '/');

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
        <!-- Home link -->
        <li class="nav-item">
          <a class="nav-link<?= $currentRoute === 'home' ? ' active fw-bold' : '' ?>"
            aria-current="<?= $currentRoute === 'home' ? 'page' : 'false' ?>" href="/">
            Home
          </a>
        </li>

        <!-- About link -->
        <li class="nav-item">
          <a class="nav-link<?= $currentRoute === 'about' ? ' active fw-bold' : '' ?>"
            aria-current="<?= $currentRoute === 'about' ? 'page' : 'false' ?>" href="/about">
            About
          </a>
        </li>

        <!-- Browse dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle<?= $isBrowseActive ? ' active fw-bold' : '' ?>" href="#"
            id="dropdownBrowse" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Browse
          </a>
          <ul class="dropdown-menu" aria-labelledby="dropdownBrowse">
            <li><a class="dropdown-item<?= $currentRoute === 'artists' ? ' active fw-bold' : '' ?>"
                href="/artists">Artists</a></li>
            <li><a class="dropdown-item<?= $currentRoute === 'artworks' ? ' active fw-bold' : '' ?>"
                href="/artworks">Artworks</a></li>
            <li><a class="dropdown-item<?= $currentRoute === 'genres' ? ' active fw-bold' : '' ?>"
                href="/genres">Genres</a></li>
            <li><a class="dropdown-item<?= $currentRoute === 'subjects' ? ' active fw-bold' : '' ?>"
                href="/subjects">Subjects</a></li>
          </ul>
        </li>
      </ul>

      <div class="d-flex align-items-center" style="gap: 0.5rem;">
        <!-- Basic search form -->
        <form class="my-2 my-lg-0" action="/search" method="GET">
          <div class="input-group">
            <input class="form-control" name="searchQuery" type="text" placeholder="Search" aria-label="Search field">
            <button class="btn btn-outline-primary" type="submit">Search</button>
          </div>
        </form>

        <!-- Link to advanced search page -->
        <a href="/advanced-search" class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
          style="height: 38px; width: 38px;">
          <img src="/assets/svgs/search-advanced.svg" alt="Advanced Search" style="height: 24px; width: 24px;" />
        </a>
      </div>

      <!-- Profile dropdown: shows login/register or user info -->
      <ul class="navbar-nav mt-2 mt-lg-0 ms-lg-4" style="margin-right: 0.5rem !important;">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="dropdownProfile" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <img src="/assets/svgs/profile.svg" alt="Profile" style="height: 25px; width: 25px;">
          </a>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownProfile">
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