<?php
function renderContributorCard($name, $githubUsername, $githubUrl, $profilePicture, $tickets)
{
  ?>
  <div class="card shadow-sm mx-3 mb-3" style="flex: 0 0 auto;">
    <div class="card-body d-flex align-items-center">
      <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile picture of <?= htmlspecialchars($name) ?>"
        style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
      <div>
        <h2 class="h5 card-title mb-1"><?= htmlspecialchars($name) ?></h2>
        <p class="mb-0">
          <strong>GitHub:</strong>
          <a href="<?= htmlspecialchars($githubUrl) ?>" target="_blank" rel="noopener noreferrer">
            <?= htmlspecialchars($githubUsername) ?>
          </a>
        </p>
        <p class="mt-2 mb-0"><strong>Tickets:</strong></p>
        <ul style="margin-top: 0;">
          <?php foreach ($tickets as $ticket): ?>
            <li><?= htmlspecialchars($ticket) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
  <?php
}
?>