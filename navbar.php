<!-- Placeholder Navbar -->
<?php require_once dirname(__DIR__) . "/src/router/router.php"; ?>
<nav class="navbar sticky-top navbar-expand-sm navbar-light bg-light">
  <a class="navbar-brand" href=<?php echo route("home") ?>>
    <img src="/assets/svgs/logo.svg" alt="Logo" style="height: 40px; width: 40px;">
  </a>
  <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId"
    aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavId">
    <?php
    $currentScript = basename($_SERVER['SCRIPT_NAME'], '.php');
    $currentRoute = $currentScript === 'index' ? 'home' : $currentScript;
    ?>
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item <?php echo ($currentRoute === 'home') ? 'active' : ''; ?>">
        <a class="nav-link" href=<?php echo route("home") ?>>Home <?php if ($currentRoute === 'home'): ?><span
              class="sr-only">(current)</span><?php endif; ?></a>
      </li>
      <li class="nav-item <?php echo ($currentRoute === 'about') ? 'active' : ''; ?>">
        <a class="nav-link" href=<?php echo route("about") ?>>About <?php if ($currentRoute === 'about'): ?><span
              class="sr-only">(current)</span><?php endif; ?></a>
      </li>
      <?php
      $browseRoutes = ['artists', 'artworks', 'genres', 'subjects'];
      $isBrowseActive = in_array($currentRoute, $browseRoutes);
      ?>
      <li class="nav-item dropdown <?php echo $isBrowseActive ? 'active' : ''; ?>">
        <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true"
          aria-expanded="false">Browse</a>
        <div class="dropdown-menu" aria-labelledby="dropdownId">
          <a class="dropdown-item <?php echo ($currentRoute === 'artists') ? 'active' : ''; ?>" href=<?php echo route("artists") ?>>Artists</a>
          <a class="dropdown-item <?php echo ($currentRoute === 'artworks') ? 'active' : ''; ?>" href=<?php echo route("artworks") ?>>Artworks</a>
          <a class="dropdown-item <?php echo ($currentRoute === 'genres') ? 'active' : ''; ?>" href=<?php echo route("genres") ?>>Genres</a>
          <a class="dropdown-item <?php echo ($currentRoute === 'subjects') ? 'active' : ''; ?>" href=<?php echo route("subjects") ?>>Subjects</a>
        </div>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0" action="/search.php" method="GET">
      <input class="form-control mr-sm-2" name="searchQuery" type="text" placeholder="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    <ul class="navbar-nav mx-1 mt-2 mt-lg-0">
      <li class="nav-item mr-2">
        <a href=<?php echo route("advanced") ?>>
          <img src="/assets/svgs/search-advanced.svg" alt="Advanced Search" class="btn btn-outline-secondary nav-link"
            style="height: 35px; width: 35px;" />
        </a>
      </li>
    </ul>
    <ul class="navbar-nav mt-2 mt-lg-0 mx-4">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown" aria-haspopup="true"
          aria-expanded="false">
          <img src="/assets/svgs/profile.svg" alt="Profile" style="height: 25px; width: 25px;">
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownId">
          <!-- Unauthorized users -->
          <a class="dropdown-item" href=<?php echo route(name: "register") ?>>Register</a>
          <!-- Logged in user -->
          <a class="dropdown-item" href=<?php echo route("account") ?>>My Account</a>
          <a class="dropdown-item" href=<?php echo route("favorites") ?>>Favorite List</a>
          <a class="dropdown-item" href=<?php echo route("login") ?>>Login</a>
          <!-- Admin -->
          <a class="dropdown-item" href=<?php echo route("admin_users") ?>>Manage Users</a>
        </div>
      </li>
    </ul>
  </div>
</nav>