<h1 class="mt-4">Register</h1>

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
                minlength="4"
                maxlength="10"
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
