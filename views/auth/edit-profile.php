<h1 class="mt-4"><?= $isAdminEdit ? 'Edit User Profile' : 'Edit My Profile' ?></h1>

<h4 class="mt-2">General Information</h4>
<form class="mt-4 mb-4" method="POST" action="<?= $isAdminEdit ? "/edit-profile/{$userId}" : "/edit-profile" ?>">
  <!-- User ID (Hidden) -->
  <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">
  
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>First Name</label>
      <input name="firstName" class="form-control" value="<?= htmlspecialchars($user->getFirstName()) ?>" required>
    </div>
    <div class="form-group col-md-6">
      <label>Last Name</label>
      <input name="lastName" class="form-control" value="<?= htmlspecialchars($user->getLastName()) ?>" required>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Email</label>
      <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
    </div>
    <?php if ($isAdminEdit): ?>
    <div class="form-group col-md-6">
      <label>Role</label>
      <select name="isAdmin" class="form-control" required>
        <option value="0" <?= !$user->getIsAdmin() ? 'selected' : '' ?>>User</option>
        <option value="1" <?= $user->getIsAdmin() ? 'selected' : '' ?>>Administrator</option>
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
      <input class="form-control" value="<?= htmlspecialchars($user->getUserName()) ?>" disabled>
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
