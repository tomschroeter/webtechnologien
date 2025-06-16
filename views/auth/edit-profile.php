<h1 class="mt-3"><?= $isAdminEdit ? 'Edit User Profile' : 'Edit My Profile' ?></h1>

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
      An error occurred while updating the profile. Please try again.
    <?php else: ?>
      An error occurred. Please try again.
    <?php endif; ?>
  </div>
<?php endif; ?>

<h4 class="mt-2">General Information</h4>
<form class="mt-4 mb-4" method="POST" action="<?= $isAdminEdit ? "/edit-profile/{$userId}" : "/edit-profile" ?>">
  <!-- User ID (Hidden) -->
  <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">
  
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>First Name</label>
      <input name="firstName" class="form-control" value="<?= htmlspecialchars($user['FirstName']) ?>" required>
    </div>
    <div class="form-group col-md-6">
      <label>Last Name</label>
      <input name="lastName" class="form-control" value="<?= htmlspecialchars($user['LastName']) ?>" required>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Email</label>
      <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($user['Email']) ?>" required>
    </div>
    <?php if ($isAdminEdit): ?>
    <div class="form-group col-md-6">
      <label>Role</label>
      <select name="isAdmin" class="form-control" required>
        <option value="0" <?= !$user['isAdmin'] ? 'selected' : '' ?>>User</option>
        <option value="1" <?= $user['isAdmin'] ? 'selected' : '' ?>>Administrator</option>
      </select>
      <?php if (isset($_SESSION['customerId']) && (int)$_SESSION['customerId'] === (int)$userId): ?>
        <small class="form-text text-muted">
          <strong>Warning:</strong> If you demote yourself from admin, you will lose admin privileges immediately.
        </small>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <button type="submit" class="btn btn-primary">Save Changes</button>
  <a href="<?= $isAdminEdit ? '/manage-users' : '/account' ?>" class="btn btn-secondary ml-2">Cancel</a>
</form>

<h4 class="mt-4">Account Settings</h4>
<form class="mt-4 mb-4">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Username</label>
      <input class="form-control" value="<?= htmlspecialchars($user['UserName']) ?>" disabled>
      <small class="form-text text-muted">Username cannot be changed</small>
    </div>
    <div class="form-group col-md-6">
      <label>Password</label>
      <input class="form-control" value="••••••••" disabled>
    </div>
  </div>
  <?php if (!$isAdminEdit): ?>
    <a href="/change-password" class="btn btn-secondary">Change Password</a>
  <?php endif; ?>
</form>
