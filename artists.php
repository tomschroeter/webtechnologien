<!DOCTYPE html>
<html lang="en">

<?php
  require_once dirname(__DIR__)."/src/head.php";
require_once dirname(__DIR__)."/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__)."/src/navbar.php";

$artistRepository = new ArtistRepository(new Database());

// Checks if user has submitted a valid option for changing the display order
if (isset($_GET['sort'])) {
    $sort = ($_GET['sort'] === 'descending');
} else {
    $sort = false;
}

$artists = $artistRepository->getAllArtists($sort);
?>

<body class="container">
  <!-- Form providing the ability to sort the order of displayed artists -->
  <div class="d-flex align-items-center mt-3 mb-3">
    <h1 class="flex-grow-1 mb-0">Künstler</h1>
    <form method="get">
      <select name="sort" onchange="this.form.submit()" class="form-select">
        <option value="ascending" <?php echo !$sort ? 'selected' : ''?>>Name (aufsteigend)</option>
        <option value="descending" <?php echo $sort ? 'selected' : ''?>>Name (absteigend)</option>
      </select>
    </form>
  </div>

  <!-- List to display all artists -->
  <ul class="list-group mb-5">
    <?php foreach ($artists as $artist): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="<?php echo route('artists', ['id' => $artist->getArtistId()])?>"
           class="d-flex justify-content-between align-items-center w-100 text-decoration-none text-dark">
          <span><?php echo $artist->getFirstName()?> <?php echo $artist->getLastName()?></span>
          <!-- Checks if artists' image exists -->
          <?php $imagePath =  "/assets/images/artists/square-thumb/".$artist->getArtistId().".jpg";
        $placeholderPath = "/assets/placeholder/artists/square-thumb/placeholder.svg";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            $correctImagePath = $imagePath;
        } else {
            $correctImagePath = $placeholderPath;
        }
        ?>
          <img src="<?php echo $correctImagePath?>" alt="Künsterbild" style="max-width: 100px; max-height: 100px; object-fit: cover;">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php require_once 'bootstrap.php'; ?>
</body>
</html>
