<div class="d-flex align-items-center justify-content-center">
  <div id="carouselId" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#carouselId" data-slide-to="0" class="active"></li>
      <li data-target="#carouselId" data-slide-to="1"></li>
      <li data-target="#carouselId" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner" role="listbox">
      <?php
      $worksDir = 'assets/images/works/medium';
      $allFiles = glob($worksDir . '/*.jpg', GLOB_BRACE);

      $randomFiles = array_rand($allFiles, 3);
      foreach ($randomFiles as $index => $file) {
          $activeClass = $index === 0 ? 'active' : '';
          echo "<div class=\"carousel-item $activeClass\"><img src=\"$allFiles[$file]\" alt=\"$file\" class=\"d-block w-100 h-100 carousel-image\"></div>";
      }
      ?>
      <a class="carousel-control-prev" href="#carouselId" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselId" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </div>
</div>