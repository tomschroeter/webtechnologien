<?php
function renderContributorCard($name, $githubUsername, $githubUrl, $tickets)
{
  ?>
  <div class="card shadow-sm mb-2">
    <div class="card-body">
      <h2 class="h4 card-title"><?php echo $name ?></h2>
      <p><strong>GitHub:</strong> <a href=<?php echo $githubUrl ?>><?php echo $githubUsername ?></a></p>
      <div class="row ml-1">
        <p><strong>Tickets:</strong></p>&MediumSpace;
        <?php foreach ($tickets as $ticket): ?>
          <span class="col text-center"><?php echo $ticket ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php
}

?>