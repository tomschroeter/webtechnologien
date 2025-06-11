<!DOCTYPE html>
<html lang="en">

<?php
require_once dirname(__DIR__) . "/src/head.php";
require_once dirname(__DIR__) . "/src/navbar.php";
require_once dirname(__DIR__) . "/src/classes/Artist.php";
require_once dirname(__DIR__) . "/src/classes/Artwork.php";
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/src/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/src/dtos/ArtworkWithArtistName.php";

$db = new Database();
$artistRepository = new ArtistRepository($db);
$artworkRepository = new ArtworkRepository($db);
$genreRepository = new GenreRepository($db);

$nationalities = $artistRepository->getArtistNationalities();
$genres = $genreRepository->getAllGenres();
$genreNames = [];
foreach ($genres as $genre) {
  $genreNames[] = $genre->getGenreName();
}

// Checks if user sets sort order for displayed artists
if (isset($_GET['filterBy'])) {
  $filterBy = $_GET['filterBy'];
} else {
  $filterBy = 'artist';
}

if (isset($_GET['artistNationality'])) {
  $selectedArtistNationality = $_GET['artistNationality'];
} else {
  $selectedArtistNationality = '';
}

if (isset($_GET['artworkGenre'])) {
  $selectedArtworkGenre = $_GET['artworkGenre'];
} else {
  $selectedArtworkGenre = '';
}
?>

<body class="container">
  <form method="get" action="/search.php" class="ml-2">
    <h2 class="flex-grow-1 mb-1 mt-3">Advanced Search</h2>
    <h5 class="row ml-4 mt-4">
      <label for="filterBy" class="mt-1">Filter By</label>
      <select name="filterBy" id="filterBy" class="form-control ml-2 w-25" onchange="toggleFilterSections()">
        <option value="artist" <?php echo $filterBy === 'artist' ? 'selected' : '' ?>>Artist</option>
        <option value="artwork" <?php echo $filterBy === 'artwork' ? 'selected' : '' ?>>Artwork</option>
      </select>
    </h5>

    <div class="col">
      <!-- Artist Filter Section -->
      <div id="artistFilters" style="display: <?php echo $filterBy === 'artist' ? 'block' : 'none' ?>;">
        <div class="col form-group w-50">
          <label for="artistName">Name</label>
          <input type="text" name="artistName"
            value="<?php echo isset($_GET['artistName']) ? htmlspecialchars($_GET['artistName']) : '' ?>"
            id="artistName" class="form-control w-50">
        </div>
        <div class="col w-25 mt-1 form-group">
          <label for="artistStartDate">Year Range</label>
          <div class="d-flex align-items-center">
            <input type="number" name="artistStartDate"
              value="<?php echo isset($_GET['artistStartDate']) ? htmlspecialchars($_GET['artistStartDate']) : '' ?>"
              class="w-50 form-control mr-1" id="artistStartDate"> -
            <input type="number" name="artistEndDate"
              value="<?php echo isset($_GET['artistEndDate']) ? htmlspecialchars($_GET['artistEndDate']) : '' ?>"
              class="w-50 form-control ml-1" id="artistEndDate">
          </div>
        </div>
        <div class="col mt-1 form-group w-50">
          <label for="artistNationality">Nationality</label>
          <select name="artistNationality" id="artistNationality" class="form-control w-50">
            <option value="" <?php echo $selectedArtistNationality === '' ? 'selected' : '' ?>>None</option>
            <?php foreach ($nationalities as $nationality) {
              echo "<option value=\"$nationality\" " . ($selectedArtistNationality === $nationality ? 'selected' : '') . ">$nationality</option>";
            } ?>
          </select>
        </div>
      </div>

      <!-- Artwork Filter Section -->
      <div id="artworkFilters" style="display: <?php echo $filterBy === 'artwork' ? 'block' : 'none' ?>;">
        <div class="col form-group w-50">
          <label for="artworkTitle">Title</label>
          <input type="text" name="artworkTitle"
            value="<?php echo isset($_GET['artworkTitle']) ? htmlspecialchars($_GET['artworkTitle']) : '' ?>"
            id="artworkTitle" class="form-control w-50">
        </div>
        <div class="col w-25 mt-1 form-group">
          <label for="artworkStartDate">Year Range</label>
          <div class="d-flex align-items-center">
            <input type="number" name="artworkStartDate"
              value="<?php echo isset($_GET['artworkStartDate']) ? htmlspecialchars($_GET['artworkStartDate']) : '' ?>"
              class="w-50 form-control mr-1" id="artworkStartDate"> -
            <input type="number" name="artworkEndDate"
              value="<?php echo isset($_GET['artworkEndDate']) ? htmlspecialchars($_GET['artworkEndDate']) : '' ?>"
              class="w-50 form-control ml-1" id="artworkEndDate">
          </div>
        </div>
        <div class="col mt-1 form-group w-50">
          <label for="artworkGenre">Genre</label>
          <select name="artworkGenre" id="artworkGenre" class="form-control w-50">
            <option value="" <?php echo $selectedArtworkGenre === '' ? 'selected' : '' ?>>None</option>
            <?php foreach ($genreNames as $genre) {
              echo "<option value=\"$genre\" " . ($selectedArtworkGenre === $genre ? 'selected' : '') . ">$genre</option>";
            } ?>
          </select>
        </div>
      </div>
    </div>

    <button class="btn btn-outline-success mt-4 ml-4" type="submit">Search</button>

    <?php require_once 'bootstrap.php'; ?>
  </form>

  <script>
    function toggleFilterSections() {
      const filterBy = document.getElementById('filterBy').value;
      const artistFilters = document.getElementById('artistFilters');
      const artworkFilters = document.getElementById('artworkFilters');

      if (filterBy === 'artist') {
        artistFilters.style.display = 'block';
        artworkFilters.style.display = 'none';
      } else if (filterBy === 'artwork') {
        artistFilters.style.display = 'none';
        artworkFilters.style.display = 'block';
      }
    }
  </script>
</body>

</html>