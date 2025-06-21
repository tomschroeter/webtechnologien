<h1 class="mt-4">Register</h1>

<form method="POST" action="/register" class="mt-4">
    <div class="row">
        <!-- First Name input (optional) -->
        <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input name="firstName" class="form-control" placeholder="First Name"
                value="<?= htmlspecialchars($formData['firstName'] ?? '') ?>">
        </div>
        <!-- Last Name input (required) -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Last Name*</label>
            <input name="lastName" class="form-control" placeholder="Last Name" required
                value="<?= htmlspecialchars($formData['lastName'] ?? '') ?>">
        </div>
    </div>

    <div class="row">
        <!-- Address input (required) -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Address*</label>
            <input name="address" class="form-control" placeholder="Address" required
                value="<?= htmlspecialchars($formData['address'] ?? '') ?>">
        </div>
        <!-- City input (required) -->
        <div class="col-md-6 mb-3">
            <label class="form-label">City*</label>
            <input name="city" class="form-control" placeholder="City" required
                value="<?= htmlspecialchars($formData['city'] ?? '') ?>">
        </div>
    </div>

    <div class="row">
        <!-- Region input (optional) -->
        <div class="col-md-4 mb-3">
            <label class="form-label">Region</label>
            <input name="region" class="form-control" placeholder="Region"
                value="<?= htmlspecialchars($formData['region'] ?? '') ?>">
        </div>
        <!-- Country input (required) -->
        <div class="col-md-4 mb-3">
            <label class="form-label">Country*</label>
            <input name="country" class="form-control" placeholder="Country" required
                value="<?= htmlspecialchars($formData['country'] ?? '') ?>">
        </div>
        <!-- Postal code input (optional) -->
        <div class="col-md-4 mb-3">
            <label class="form-label">Postal</label>
            <input name="postal" class="form-control" value="<?= htmlspecialchars($formData['postal'] ?? '') ?>">
        </div>
    </div>

    <div class="row">
        <!-- Phone input (optional) -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($formData['phone'] ?? '') ?>"
                class="form-control" placeholder="Phone (optional)" title="Please enter a valid phone number.">
        </div>
        <!-- Email input (required, validated as email) -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Email*</label>
            <input name="email" type="email" class="form-control" placeholder="Email" required
                value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
        </div>
    </div>

    <hr>

    <!-- Username input (required, min length 3) -->
    <div class="mb-3">
        <label class="form-label">Username*</label>
        <input name="username" class="form-control" value="<?= htmlspecialchars($formData['username'] ?? '') ?>"
            placeholder="Username" required minlength="3">
    </div>

    <div class="row">
        <!-- Password input (required, min length 6) -->
        <div class="col-md-6">
            <label class="form-label">Password*</label>
            <input name="password" type="password" class="form-control" placeholder="Password" required minlength="6">
        </div>
        <!-- Repeat password input (required, min length 6) -->
        <div class="col-md-6">
            <label class="form-label">Repeat Password*</label>
            <input name="password2" type="password" class="form-control" placeholder="Repeat Password" required
                minlength="6">
        </div>
    </div>

    <!-- Password requirements note -->
    <div class="form-text mb-3">
        Your password must be at least 6 characters, contain an uppercase letter, a digit, and a special character.
    </div>

    <!-- Submit button -->
    <button type="submit" class="btn btn-primary mb-4">Register</button>
</form>