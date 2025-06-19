<h1 class="mt-4">Change Password</h1>

<!-- Display validation errors -->
<?php
$validationErrors = $_SESSION['validation_errors'] ?? [];
if (!empty($validationErrors)) {
  unset($_SESSION['validation_errors']); // Clear after displaying
}
?>
<?php if (!empty($validationErrors)): ?>
  <div class="alert alert-danger">
    <strong>Please correct the following errors:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($validationErrors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<!-- Display URL error parameter -->
<?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?php if ($_GET['error'] === 'validation'): ?>
      Please correct the validation errors above.
    <?php elseif ($_GET['error'] === 'update'): ?>
      An error occurred while updating your password. Please try again.
    <?php else: ?>
      An error occurred. Please try again.
    <?php endif; ?>
  </div>
<?php endif; ?>

<h4 class="mt-2">Password Security</h4>
<form class="mt-4 mb-4" method="POST" action="/change-password">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">Current Password</label>
      <input name="oldPassword" type="password" class="form-control" autocomplete="current-password" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">New Password</label>
      <input name="newPassword1" type="password" class="form-control" autocomplete="new-password" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Confirm New Password</label>
      <input name="newPassword2" type="password" class="form-control" autocomplete="new-password" required>
    </div>
  </div>

  <div class="form-text mb-3">
    New password must be at least 6 characters and contain an uppercase letter, a digit, and a special character.
  </div>

  <button type="submit" class="btn btn-primary">Change Password</button>
  <a href="/account" class="btn btn-secondary ms-2">Cancel</a>
</form>