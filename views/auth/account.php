<h1 class="mt-4">My Account</h1>

<h4 class="mt-2">General Information</h4>
<form class="mt-4 mb-4" action="/edit-account" method="get">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input name="firstName" class="form-control" value="<?= htmlspecialchars($customer->getFirstName()) ?>" disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input name="lastName" class="form-control" value="<?= htmlspecialchars($customer->getLastName()) ?>" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Address</label>
            <input name="address" class="form-control" value="<?= htmlspecialchars($customer->getAddress()) ?>" disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">City</label>
            <input name="city" class="form-control" value="<?= htmlspecialchars($customer->getCity()) ?>" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Region</label>
            <input name="region" class="form-control" value="<?= htmlspecialchars($customer->getRegion()) ?>" disabled>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Country</label>
            <input name="country" class="form-control" value="<?= htmlspecialchars($customer->getCountry()) ?>" disabled>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Postal</label>
            <input name="postal" class="form-control" value="<?= htmlspecialchars($customer->getPostal()) ?>" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" value="<?= htmlspecialchars($customer->getPhone()) ?>" disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input name="email" class="form-control" value="<?= htmlspecialchars($customer->getEmail()) ?>" disabled>
        </div>
    </div>
    <a href="/edit-profile" class="btn btn-primary">Edit Profile</a>
</form>

<h4 class="mt-4">Account Settings</h4>
<form class="mt-4 mb-4" action="/change-password" method="get">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" value="<?= htmlspecialchars($user->getUserName()) ?>" disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
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
