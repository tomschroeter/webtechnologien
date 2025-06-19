<?php
function renderContributorCard($name, $githubUsername, $githubUrl, $profilePicture, $tickets)
{
  ?>
  <div class="card shadow-sm mx-3 my-3" style="width: 24rem;">
    <div class="card-body text-center">
      <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile picture of <?= htmlspecialchars($name) ?>"
        class="mb-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
      <h2 class="h5 card-title mb-1"><?= htmlspecialchars($name) ?></h2>
      <p class="mb-4">
        <strong>GitHub:</strong>
        <a href="<?= htmlspecialchars($githubUrl) ?>" target="_blank" rel="noopener noreferrer">
          <?= htmlspecialchars($githubUsername) ?>
        </a>
      </p>
        <?php foreach ($tickets as $ticket): ?>
          <?= htmlspecialchars($ticket) ?><br>
        <?php endforeach; ?>
    </div>
  </div>
  <?php
}
?>