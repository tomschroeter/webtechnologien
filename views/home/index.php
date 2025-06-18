<div class="fullwidth">
  <div style="width: 100%; box-sizing: border-box; margin-bottom: 24px; margin-top: 24px;">
    <?php require_once dirname(dirname(__DIR__)) . '/components/random-carousel.php' ?>
  </div>

  <div
    style="display: flex; gap: 40px; justify-content: center; flex-wrap: wrap; width: 100%; padding: 30px; box-sizing: border-box; margin-bottom: 24px;">
    <div
      style="flex: 1 1 45%; min-width: 300px; justify-content: center; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
      <h2 class="mx-auto pb-4" style="width: fit-content;">Top Rated Artworks</h2>
      <?php require_once dirname(dirname(__DIR__)) . '/components/top-rated.php'; ?>
    </div>

    <div
      style="flex: 1 1 45%; min-width: 300px; justify-content: center; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
      <h2 class="mx-auto pb-4" style="width: fit-content;">Most Reviewed Artists</h2>
      <?php require_once dirname(dirname(__DIR__)) . "/components/most-reviewed-artists.php"; ?>
    </div>
  </div>

  <div style="width: 100%; box-sizing: border-box; padding: 30px;">
    <div
      style="width: 100%; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 30px;">
      <h2 class="mx-auto pb-4" style="width: fit-content;">Recent Reviews</h2>
      <?php require_once dirname(dirname(__DIR__)) . '/components/recent-reviews.php'; ?>
    </div>
  </div>
</div>