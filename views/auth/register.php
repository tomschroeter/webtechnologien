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
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>First Name</label>
            <input name="firstName" class="form-control" placeholder="First Name">
        </div>
        <div class="form-group col-md-6">
            <label>Last Name</label>
            <input name="lastName" class="form-control" placeholder="Last Name*" required>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Address</label>
            <input name="address" class="form-control" placeholder="Address*" required>
        </div>
        <div class="form-group col-md-6">
            <label>City</label>
            <input name="city" class="form-control" placeholder="City*" required>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Region</label>
            <input name="region" class="form-control" placeholder="Region (optional)">
        </div>
        <div class="form-group col-md-4">
            <label>Country</label>
            <input
                name="country"
                class="form-control"
                placeholder="Country*"
                required
                minlength="4"
            >
        </div>
        <div class="form-group col-md-4">
            <label>Postal</label>
            <input
                name="postal"
                class="form-control"
                placeholder="Postal Code"
                inputmode="numeric"
                pattern="^\d{10}$|^\d{5}-\d{4}$"
                title="Please enter valid postal code. Postal codes can only be 10-digits long and must be numeric."
            >
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Phone</label>
            <input
                type="tel"
                name="phone"
                class="form-control"
                placeholder="Phone (optional)"
                pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}"
                title="Please enter a valid phone number."
            >
        </div>
        <div class="form-group col-md-6">
            <label>Email</label>
            <input name="email" type="email" class="form-control" placeholder="Email*" required>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <label>Username</label>
        <input
            name="username"
            class="form-control"
            placeholder="Username*"
            required
            minlength="3"
        >
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Password</label>
            <input
                name="password"
                type="password"
                class="form-control"
                placeholder="Password*"
                required
                minlength="6"
            >
        </div>
        <div class="form-group col-md-6">
            <label>Repeat Password</label>
            <input
                name="password2"
                type="password"
                class="form-control"
                placeholder="Repeat Password*"
                required
                minlength="6"
            >
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>
