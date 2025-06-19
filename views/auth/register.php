<h1 class="mt-4">Register</h1>

<?php if ($error === 'empty_field'): ?>
    <div class="alert alert-danger">Please fill out all required fields.</div>
<?php elseif ($error === 'invalid_phone_number'): ?>
    <div class="alert alert-danger">The phone number doesn't have a valid format.</div>
<?php elseif ($error === 'invalid_email'): ?>
    <div class="alert alert-danger">The E-Mail doesn't have a valid format.</div>
<?php elseif ($error === 'invalid_password'): ?>
    <div class="alert alert-danger">
        The password must contain at least 6 characters, one uppercase letter, one number, and one special character.
    </div>
<?php elseif ($error === 'exists'): ?>
    <div class="alert alert-warning">Username already exists. Please choose another one.</div>
<?php elseif ($error === 'password_mismatch'): ?>
    <div class="alert alert-danger">Passwords do not match. Please try again.</div>
<?php elseif ($error === 'database'): ?>
    <div class="alert alert-danger">Registration failed due to a database error. Please try again.</div>
<?php elseif ($success): ?>
    <div class="alert alert-success">Registration successful! You can now <a href="/login">log in</a>.</div>
<?php endif; ?>

<form method="POST" action="/register" class="mt-4">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input name="firstName" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($formData['firstName'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input name="lastName" class="form-control" placeholder="Last Name*" required value="<?= htmlspecialchars($formData['lastName'] ?? '') ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Address</label>
            <input name="address" class="form-control" placeholder="Address*" required value="<?= htmlspecialchars($formData['address'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">City</label>
            <input name="city" class="form-control" placeholder="City*" required value="<?= htmlspecialchars($formData['city'] ?? '') ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Region</label>
            <input name="region" class="form-control" placeholder="Region (optional)" value="<?= htmlspecialchars($formData['region'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Country</label>
            <input name="country" class="form-control" placeholder="Country*" required value="<?= htmlspecialchars($formData['country'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Postal</label>
            <input name="postal" class="form-control" placeholder="Postal Code" value="<?= htmlspecialchars($formData['postal'] ?? '') ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" placeholder="Phone (optional)" value="<?= htmlspecialchars($formData['phone'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" placeholder="Email*" required value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
        </div>
    </div>
    <hr>
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" placeholder="Username*" required value="<?= htmlspecialchars($formData['username'] ?? '') ?>">
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" placeholder="Password*" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Repeat Password</label>
            <input name="password2" type="password" class="form-control" placeholder="Repeat Password*" required>
        </div>
    </div>
    <div class="form-text mb-3">Your password must be at least 6 characters, contain an uppercase letter, a digit, and a special character.</div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>
