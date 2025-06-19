<h1 class="mt-4"><?= $isAdminEdit ? 'Edit User Profile' : 'Edit My Profile' ?></h1>

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
  
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">First Name</label>
      <input type="text" name="firstName" class="form-control" value="<?= htmlspecialchars($user->getFirstName()) ?>" required>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Last Name*</label>
      <input type="text" name="lastName" class="form-control" value="<?= htmlspecialchars($user->getLastName()) ?>" required>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Address*</label>
      <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user->getAddress()) ?>">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">City*</label>
      <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user->getCity()) ?>">
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">Region</label>
      <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($user->getRegion()) ?>">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Country*</label>
      <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($user->getCountry()) ?>">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Postal</label>
      <input type="text" name="postal" class="form-control" value="<?= htmlspecialchars($user->getPostal()) ?>">
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Phone</label>
      <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user->getPhone()) ?>">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Email*</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
    </div>
  </div>

<?php if ($isAdminEdit): ?>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Role</label>
      <select name="isAdmin" class="form-select" required>
        <option value="0" <?= !$user->getIsAdmin() ? 'selected' : '' ?>>User</option>
        <option value="1" <?= $user->getIsAdmin() ? 'selected' : '' ?>>Administrator</option>
      </select>
      <?php if (isset($_SESSION['customerId']) && (int)$_SESSION['customerId'] === (int)$userId): ?>
        <div class="form-text text-muted">
          <strong>Warning:</strong> If you demote yourself from admin, you will lose admin privileges immediately.
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class="d-flex justify-content-start gap-2"></div>
  <button type="submit" class="btn btn-primary">Save Changes</button>
  <a href="<?= $isAdminEdit ? '/manage-users' : '/account' ?>" class="btn btn-secondary">Cancel</a>
</div>
</form>
