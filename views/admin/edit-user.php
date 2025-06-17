<h1 class="mt-4">Edit User</h1>

<?php if ($error === 'invalidInput'): ?>
    <div class="alert alert-danger">Please provide a valid last name and email address.</div>
<?php endif; ?>

<form method="POST" action="/edit-user" class="mt-4">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user->getCustomerId()) ?>">
    
    <div class="form-group">
        <label for="firstName">First Name</label>
        <input name="firstName" id="firstName" class="form-control" value="<?= htmlspecialchars($user->getFirstName()) ?>">
    </div>
    
    <div class="form-group">
        <label for="lastName">Last Name</label>
        <input name="lastName" id="lastName" class="form-control" value="<?= htmlspecialchars($user->getLastName()) ?>" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
        <input name="email" id="email" type="email" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
    </div>
    
    <div class="form-group">
        <label for="isAdmin">Role</label>
        <select name="isAdmin" id="isAdmin" class="form-control">
            <option value="0" <?= !$user->getIsAdmin() ? 'selected' : '' ?>>User</option>
            <option value="1" <?= $user->getIsAdmin() ? 'selected' : '' ?>>Admin</option>
        </select>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="/manage-users" class="btn btn-secondary ml-2">Cancel</a>
    </div>
</form>
