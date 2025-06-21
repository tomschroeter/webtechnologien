<h1 class="mt-4">Change Password</h1>

<h4 class="mt-2">Password Security</h4>

<!-- Form for changing the user’s password -->
<form class="mt-4 mb-4" method="POST" action="/change-password">

  <div class="row">
    <!-- Current password input -->
    <div class="col-md-4">
      <label class="form-label">Current Password</label>
      <!-- Required field with autocomplete set to current-password for browser password managers -->
      <input name="oldPassword" type="password" class="form-control" autocomplete="current-password" required>
    </div>

    <!-- New password input -->
    <div class="col-md-4">
      <label class="form-label">New Password</label>
      <!-- Required field with autocomplete new-password -->
      <input name="newPassword1" type="password" class="form-control" autocomplete="new-password" required>
    </div>

    <!-- Confirm new password input -->
    <div class="col-md-4">
      <label class="form-label">Confirm New Password</label>
      <!-- Required field to confirm new password -->
      <input name="newPassword2" type="password" class="form-control" autocomplete="new-password" required>
    </div>
  </div>

  <!-- Password complexity requirements info for users -->
  <div class="form-text mb-3">
    New password must be at least 6 characters and contain an uppercase letter, a digit, and a special character.
  </div>

  <!-- Submit and cancel buttons -->
  <button type="submit" class="btn btn-primary">Change Password</button>
  <a href="/account" class="btn btn-secondary ms-2">Cancel</a>
</form>