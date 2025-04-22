<!DOCTYPE html>
<html lang="en">

<?php
  require_once dirname(__DIR__)."/src/head.php";
  require_once dirname(__DIR__)."/src/repositories/ArtistRepository.php";
  require_once dirname(__DIR__)."/src/navbar.php";

  $artistRepository = new ArtistRepository();

  // Checks if user has submitted a valid option for changing the display order
  if (isset($_GET['sort'])) {
    $sort = ($_GET['sort'] === 'descending');
  } else {
    $sort = false;
  }

  $artists = $artistRepository->getAllArtists($sort);
?>

<body class="container">
  <h1>Künstlerliste</h1>
  <br>
    <!-- Form providing the ability to sort the order of displayed artists -->
    <form method="get">
      <select name="sort" onchange="this.form.submit()">
          <option value="ascending" <?php echo $sort == "ascending" ? 'selected' : ''?>>Name (aufsteigend)</option>
          <option value="descending" <?php echo $sort == "descending" ? 'selected' : ''?>>Name (absteigend)</option>
      </select>
    </form>
  <br>

  <!-- List to display all artists -->
  <ul class="list-group">
    <?php foreach ($artists as $artist): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="/src/<?php echo route('artists', ['id' => $artist->getArtistId()]) ?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <span><?php echo $artist->getFirstName() ?> <?= $artist->getLastName() ?></span>
          <img src="/src/assets/images/artists/square-thumb/<?php echo $artist->getArtistId()?>.jpg" alt="Künsterbild">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php require_once 'bootstrap.php'; ?>
</body>
</html>
