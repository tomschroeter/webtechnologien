<div class="card mb-5 shadow-sm" style="margin-top: 90px;">
  <div class="card-body">
    <h2 class="flex-grow-1 mb-1">Advanced Search</h2>
    <hr>
    <form method="get" action="/advanced-search" class="form-inline ml-2 mt-3">
      <label for="filterBy" class="mr-2">Filter By</label>
      <select name="filterBy" id="filterBy" class="form-control" onchange="this.form.submit()">
        <option value="artist" <?= $filterBy === 'artist' ? 'selected' : '' ?>>Artist</option>
        <option value="artwork" <?= $filterBy === 'artwork' ? 'selected' : '' ?>>Artwork</option>
      </select>
    </form>
  </div>
  <form method="get" action="/search" class="ml-2">
    <input type="hidden" name="filterBy" value="<?= htmlspecialchars($filterBy) ?>">
    <div class="col">
      <!-- Artist Filter Section -->
      <?php if ($filterBy === 'artist'): ?>
        <div id="artistFilters">
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
              <?php foreach ($nationalities as $nationality): ?>
                <option value="<?php echo $nationality; ?>" <?php echo $selectedArtistNationality === $nationality ? 'selected' : '' ?>>
                  <?php echo $nationality; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      <?php endif; ?>

      <!-- Artwork Filter Section -->
      <?php if ($filterBy === 'artwork'): ?>
        <div id="artworkFilters">
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
              <?php foreach ($genreNames as $genre): ?>
                <option value="<?php echo $genre; ?>" <?php echo $selectedArtworkGenre === $genre ? 'selected' : '' ?>>
                  <?php echo $genre; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <button class="btn btn-outline-primary mt-4 ml-2 mb-4" type="submit">Search</button>
  </form>
</div>
</div>