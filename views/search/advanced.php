<div class="card mb-5 shadow-sm" style="margin-top: 90px;">
  <div class="card-body">
    <form method="get" action="/search" class="ml-2">
      <h2 class="flex-grow-1 mb-1">Advanced Search</h2>
      <hr>

      <h5 class="row ml-2 mt-3">
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

      <button class="btn btn-outline-success mt-4 ml-2" type="submit">Search</button>
    </form>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('form.ml-2');
    if (form) {
      form.addEventListener("submit", function (e) {
        const elements = form.querySelectorAll("input, select");
        elements.forEach(el => {
          if (!el.value.trim()) {
            el.removeAttribute("name");
          }
        });
      });
    }
  });

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
