<h1 class="mt-4">My Account</h1>

<!-- Display user's general information in a disabled form for viewing only -->
<h4 class="mt-2">General Information</h4>
<form class="mt-4 mb-4" action="/edit-account" method="get">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <!-- Disabled input to show first name (read-only) -->
            <input name="firstName" class="form-control" value="<?= htmlspecialchars($customer->getFirstName()) ?>"
                disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <!-- Disabled input to show last name (read-only) -->
            <input name="lastName" class="form-control" value="<?= htmlspecialchars($customer->getLastName()) ?>"
                disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Address</label>
            <!-- Disabled input to show address -->
            <input name="address" class="form-control" value="<?= htmlspecialchars($customer->getAddress()) ?>"
                disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">City</label>
            <!-- Disabled input to show city -->
            <input name="city" class="form-control" value="<?= htmlspecialchars($customer->getCity()) ?>" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Region</label>
            <!-- Disabled input to show region -->
            <input name="region" class="form-control" value="<?= htmlspecialchars($customer->getRegion()) ?>" disabled>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Country</label>
            <!-- Disabled input to show country -->
            <input name="country" class="form-control" value="<?= htmlspecialchars($customer->getCountry()) ?>"
                disabled>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Postal</label>
            <!-- Disabled input to show postal code -->
            <input name="postal" class="form-control" value="<?= htmlspecialchars($customer->getPostal()) ?>" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <!-- Disabled input to show phone number -->
            <input name="phone" class="form-control" value="<?= htmlspecialchars($customer->getPhone()) ?>" disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <!-- Disabled input to show email -->
            <input name="email" class="form-control" value="<?= htmlspecialchars($customer->getEmail()) ?>" disabled>
        </div>
    </div>
    <!-- Button to navigate to profile edit page -->
    <a href="/edit-profile" class="btn btn-primary">Edit Profile</a>
</form>

<!-- Account settings section displaying username and obscured password -->
<h4 class="mt-4">Account Settings</h4>
<form class="mt-4 mb-4" action="/change-password" method="get">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Username</label>
            <!-- Disabled input showing username -->
            <input class="form-control" value="<?= htmlspecialchars($user->getUserName()) ?>" disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
            <!-- Password field obscured, disabled -->
            <input class="form-control" value="••••••••" disabled>
        </div>
    </div>
    <!-- Button to navigate to password change page -->
    <button type="submit" class="btn btn-secondary">Change Password</button>
</form>

<!-- Quick links for navigation -->
<h4 class="mt-4">Quick Links</h4>
<div class="list-group mb-5">
    <!-- Link to user's favorites -->
    <a href="/favorites" class="list-group-item list-group-item-action">
        <strong>My Favorites</strong>
        <br>
        <small class="text-muted">View your favorite artworks</small>
    </a>
    <!-- Link to logout -->
    <a href="/logout" class="list-group-item list-group-item-action text-danger">
        <strong>Logout</strong>
        <br>
        <small class="text-muted">Sign out of your account</small>
    </a>
</div>