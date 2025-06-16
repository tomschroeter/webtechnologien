<h1 class="mt-4">My Account</h1>

<?php if ($success === 'info'): ?>
    <div class="alert alert-success">Profile updated successfully.</div>
<?php elseif ($success === 'password'): ?>
    <div class="alert alert-success">Password updated successfully.</div>
<?php endif; ?>

<h4 class="mt-2">General Information</h4>
<form class="mt-4 mb-4" action="/edit-account" method="get">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>First Name</label>
            <input name="firstName" class="form-control" value="<?= htmlspecialchars($customer['FirstName']) ?>" disabled>
        </div>
        <div class="form-group col-md-6">
            <label>Last Name</label>
            <input name="lastName" class="form-control" value="<?= htmlspecialchars($customer['LastName']) ?>" disabled>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Address</label>
            <input name="address" class="form-control" value="<?= htmlspecialchars($customer['Address']) ?>" disabled>
        </div>
        <div class="form-group col-md-6">
            <label>City</label>
            <input name="city" class="form-control" value="<?= htmlspecialchars($customer['City']) ?>" disabled>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Region</label>
            <input name="region" class="form-control" value="<?= htmlspecialchars($customer['Region']) ?>" disabled>
        </div>
        <div class="form-group col-md-4">
            <label>Country</label>
            <input name="country" class="form-control" value="<?= htmlspecialchars($customer['Country']) ?>" disabled>
        </div>
        <div class="form-group col-md-4">
            <label>Postal</label>
            <input name="postal" class="form-control" value="<?= htmlspecialchars($customer['Postal']) ?>" disabled>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Phone</label>
            <input name="phone" class="form-control" value="<?= htmlspecialchars($customer['Phone']) ?>" disabled>
        </div>
        <div class="form-group col-md-6">
            <label>Email</label>
            <input name="email" class="form-control" value="<?= htmlspecialchars($customer['Email']) ?>" disabled>
        </div>
    </div>
    <a href="/edit-profile" class="btn btn-primary">Edit Profile</a>
</form>

<h4 class="mt-4">Account Settings</h4>
<form class="mt-4 mb-4" action="/change-password" method="get">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Username</label>
            <input class="form-control" value="<?= htmlspecialchars($user['UserName']) ?>" disabled>
        </div>
        <div class="form-group col-md-6">
            <label>Password</label>
            <input class="form-control" value="••••••••" disabled>
        </div>
    </div>
    <button type="submit" class="btn btn-secondary">Change Password</button>
</form>

<h4 class="mt-4">Quick Links</h4>
<div class="list-group mb-5">
    <a href="/favorites" class="list-group-item list-group-item-action">
        <strong>My Favorites</strong>
        <br>
        <small class="text-muted">View your favorite artworks</small>
    </a>
    <a href="/logout" class="list-group-item list-group-item-action text-danger">
        <strong>Logout</strong>
        <br>
        <small class="text-muted">Sign out of your account</small>
    </a>
</div>
