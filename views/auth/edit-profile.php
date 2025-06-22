<h1 class="mt-4"><?= $isAdminEdit ? 'Edit User Profile' : 'Edit My Profile' ?></h1>

<div>
  <h4 class="mt-2">General Information</h4>

  <!-- Profile edit form -->
  <form class="mt-4 mb-4" method="POST" action="<?= $isAdminEdit ? "/edit-profile/{$userId}" : "/edit-profile" ?>">

    <!-- Name inputs -->
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">First Name</label>
        <!-- First name input, no required attribute -->
        <input type="text" name="firstName" class="form-control" value="<?= htmlspecialchars($user->getFirstName()) ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Last Name*</label>
        <!-- Last name input, required field -->
        <input type="text" name="lastName" class="form-control" value="<?= htmlspecialchars($user->getLastName()) ?>"
          required>
      </div>
    </div>

    <!-- Address inputs -->
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Address*</label>
        <!-- Address input, required -->
        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user->getAddress()) ?>"
          required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">City*</label>
        <!-- City input, required -->
        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user->getCity()) ?>" required>
      </div>
    </div>

    <!-- Region, country, postal inputs -->
    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">Region</label>
        <!-- Region is optional -->
        <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($user->getRegion()) ?>">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Country*</label>
        <!-- Country input, required -->
        <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($user->getCountry()) ?>"
          required>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Postal</label>
        <!-- Postal code optional -->
        <input type="text" name="postal" class="form-control" value="<?= htmlspecialchars($user->getPostal()) ?>">
      </div>
    </div>

    <!-- Phone and Email inputs -->
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Phone</label>
        <!-- Phone number optional -->
        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user->getPhone()) ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Email*</label>
        <!-- Email input, required, but not validated by browser as type="email" doesn't allow characters like ł or ó -->
        <input name="email" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>"
          required>
      </div>
    </div>

    <!-- Submit and Cancel buttons -->
    <div class="d-flex justify-content-start gap-2"></div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <!-- Cancel button redirects depending on user/admin context -->
    <a href="<?= $isAdminEdit ? '/manage-users' : '/account' ?>" class="btn btn-secondary">Cancel</a>
</div>
</form>

<!-- Admin-only section: change user password -->
<?php if ($isAdminEdit): ?>
  <h4 class="mt-5">Edit User Password</h4>
  <form class="mt-4 mb-4" method="POST" action="<?= "/change-password/{$userId}" ?>">
    <!-- Hidden user ID for password change form -->
    <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">

    <div class="row">
      <div class="col-md-6">
        <label class="form-label">New Password</label>
        <!-- New password input, required, with autocomplete off -->
        <input name="newPassword1" type="password" class="form-control" autocomplete="new-password" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirm New Password</label>
        <!-- Confirm password input, required -->
        <input name="newPassword2" type="password" class="form-control" autocomplete="new-password" required>
      </div>
    </div>

    <!-- Password requirements reminder -->
    <div class="form-text mb-3">
      New password must be at least 6 characters and contain an uppercase letter, a digit, and a special character.
    </div>

    <!-- Password form buttons -->
    <button type="submit" class="btn btn-primary">Change Password</button>
    <a href="<?= $isAdminEdit ? '/manage-users' : '/account' ?>" class="btn btn-secondary">Cancel</a>
  </form>
<?php endif; ?>
</div>