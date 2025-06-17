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
  
  <div class="form-row">
    <div class="form-group col-md-4">
      <label>Current Password</label>
      <input name="oldPassword" type="password" class="form-control" autocomplete="current-password" required>
    </div>
    <div class="form-group col-md-4">
      <label>New Password</label>
      <input name="newPassword1" type="password" class="form-control" autocomplete="new-password" required>
    </div>
    <div class="form-group col-md-4">
      <label>Confirm New Password</label>
      <input name="newPassword2" type="password" class="form-control" autocomplete="new-password" required>
    </div>
  </div>
  
  <small class="form-text text-muted mb-3">
    New password must be at least 6 characters and contain an uppercase letter, a digit, and a special character.
  </small>
  
  <button type="submit" class="btn btn-primary">Change Password</button>
  <a href="/account" class="btn btn-secondary ml-2">Cancel</a>
</form>

<h4 class="mt-4">Account Information</h4>
<form class="mt-4 mb-4">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Username</label>
      <input class="form-control" value="<?= htmlspecialchars($user->getUserName()) ?>" disabled>
      <small class="form-text text-muted">Your username cannot be changed</small>
    </div>
    <div class="form-group col-md-6">
      <label>Email</label>
      <input class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>" disabled>
      <small class="form-text text-muted">To change your email, <a href="/edit-profile">edit your profile</a></small>
    </div>
  </div>
</form>
