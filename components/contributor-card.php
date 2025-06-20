<?php
/**
 * Renders a contributor card component displaying GitHub profile and assigned tickets.
 * This card includes:
 * - A circular profile picture
 * - Contributor's name
 * - GitHub username with link to their GitHub profile
 * - A list of associated ticket identifiers or descriptions
 *
 * @param string   $name            The full name of the contributor
 * @param string   $githubUsername  The GitHub username of the contributor
 * @param string   $githubUrl       The full URL to the contributor's GitHub profile
 * @param string   $profilePicture  URL to the contributor's profile image
 * @param string[] $tickets         Array of ticket strings (e.g., issue IDs or descriptions)
 *
 * @return void Outputs the card HTML directly
 */
function renderContributorCard($name, $githubUsername, $githubUrl, $profilePicture, $tickets): void
{
  ?>
  <div class="card shadow-sm mx-3 my-3" style="width: 24rem;">
    <div class="card-body text-center">
      <!-- Contributor profile image: circular and cropped to fit -->
      <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile picture of <?= htmlspecialchars($name) ?>"
        class="mb-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">

      <!-- Contributor's name -->
      <h2 class="h5 card-title mb-1"><?= htmlspecialchars($name) ?></h2>

      <!-- GitHub profile link -->
      <p class="mb-4">
        <strong>GitHub:</strong>
        <a href="<?= htmlspecialchars($githubUrl) ?>" target="_blank" rel="noopener noreferrer">
          <?= htmlspecialchars($githubUsername) ?>
        </a>
      </p>

      <!-- List of tickets associated with this contributor -->
      <?php foreach ($tickets as $ticket): ?>
        <?= htmlspecialchars($ticket) ?><br>
      <?php endforeach; ?>
    </div>
  </div>
  <?php
}
?>