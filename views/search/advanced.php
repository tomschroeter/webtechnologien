<div class="d-flex justify-content-center align-items-center">
  <div class="card mb-5 shadow-sm" style="margin-top: 90px; min-width: 700px; max-width: 900px;">
    <div class="card-body">
      <h2 class="mb-3">Advanced Search</h2>
      <hr>
      <form method="get" action="/advanced-search" class="d-flex align-items-center gap-3 mb-4">
        <label for="filterBy" class="form-label mb-0 ms-2">Filter By</label>
        <select name="filterBy" id="filterBy" class="form-select w-auto" onchange="this.form.submit()">
          <option value="artist" <?= $filterBy === 'artist' ? 'selected' : '' ?>>Artist</option>
          <option value="artwork" <?= $filterBy === 'artwork' ? 'selected' : '' ?>>Artwork</option>
        </select>
      </form>

      <form method="get" action="/search" style="max-width: 200px;">
        <input type="hidden" name="filterBy" value="<?= htmlspecialchars($filterBy) ?>">

        <!-- Artist Filters -->
        <?php if ($filterBy === 'artist'): ?>
          <div id="artistFilters" class="mb-3 ms-5">
            <div class="mb-3">
              <label for="artistName" class="form-label">Name</label>
              <input type="text" name="artistName" id="artistName" class="form-control"
                value="<?= isset($_GET['artistName']) ? htmlspecialchars($_GET['artistName']) : '' ?>">
            </div>

            <div class="mb-3">
              <label for="artistStartDate" class="form-label">Year Range</label>
              <div class="d-flex align-items-center gap-2">
                <input type="number" name="artistStartDate" id="artistStartDate" class="form-control"
                  value="<?= isset($_GET['artistStartDate']) ? htmlspecialchars($_GET['artistStartDate']) : '' ?>">
                <span>-</span>
                <input type="number" name="artistEndDate" id="artistEndDate" class="form-control"
                  value="<?= isset($_GET['artistEndDate']) ? htmlspecialchars($_GET['artistEndDate']) : '' ?>">
              </div>
            </div>

            <div class="mb-3">
              <label for="artistNationality" class="form-label">Nationality</label>
              <select name="artistNationality" id="artistNationality" class="form-select">
                <option value="" <?= $selectedArtistNationality === '' ? 'selected' : '' ?>>None</option>
                <?php foreach ($nationalities as $nationality): ?>
                  <option value="<?= htmlspecialchars($nationality) ?>" <?= $selectedArtistNationality === $nationality ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nationality) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        <?php endif; ?>

        <!-- Artwork Filters -->
        <?php if ($filterBy === 'artwork'): ?>
          <div id="artworkFilters" class="mb-3 ms-5">
            <div class="mb-3">
              <label for="artworkTitle" class="form-label">Title</label>
              <input type="text" name="artworkTitle" id="artworkTitle" class="form-control"
                value="<?= isset($_GET['artworkTitle']) ? htmlspecialchars($_GET['artworkTitle']) : '' ?>">
            </div>

            <div class="mb-3">
              <label for="artworkStartDate" class="form-label">Year Range</label>
              <div class="d-flex align-items-center gap-2">
                <input type="number" name="artworkStartDate" id="artworkStartDate" class="form-control"
                  value="<?= isset($_GET['artworkStartDate']) ? htmlspecialchars($_GET['artworkStartDate']) : '' ?>">
                <span>-</span>
                <input type="number" name="artworkEndDate" id="artworkEndDate" class="form-control"
                  value="<?= isset($_GET['artworkEndDate']) ? htmlspecialchars($_GET['artworkEndDate']) : '' ?>">
              </div>
            </div>

            <div class="mb-3">
              <label for="artworkGenre" class="form-label">Genre</label>
              <select name="artworkGenre" id="artworkGenre" class="form-select">
                <option value="" <?= $selectedArtworkGenre === '' ? 'selected' : '' ?>>None</option>
                <?php foreach ($genreNames as $genre): ?>
                  <option value="<?= htmlspecialchars($genre) ?>" <?= $selectedArtworkGenre === $genre ? 'selected' : '' ?>>
                    <?= htmlspecialchars($genre) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-outline-primary ms-2 mt-3 mb-3">Search</button>
      </form>
    </div>
  </div>
</div>