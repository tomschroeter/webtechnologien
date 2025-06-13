<br>
<h1><?php echo htmlspecialchars($subject->getSubjectName()) ?></h1>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-12">
      <h3>Artworks with this Subject</h3>
      
      <?php if (!empty($artworks)): ?>
        <div class="row mt-4">
          <?php 
          require_once dirname(dirname(__DIR__)) . "/components/artwork-card-list.php";
          renderArtworkCardList($artworks);
          ?>
        </div>
      <?php else: ?>
        <p>No artworks found with this subject.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
