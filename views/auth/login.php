<h1 class="mt-4">Login</h1>

<form method="POST" action="/login" class="mt-4">
    <!-- Username input (required, min length 3) -->
    <div class="form-group">
        <label>Username</label>
        <input name="username" class="form-control" value="<?= htmlspecialchars($formData['username'] ?? '') ?>"
            placeholder="Username" required minlength="3">
    </div>

    <!-- Password input (required, min length 6) -->
    <div class="form-group mt-4">
        <label>Password</label>
        <input name="password" type="password" class="form-control" placeholder="Password" required minlength="6">
    </div>

    <!-- Submit button -->
    <button type="submit" class="btn btn-primary mt-4">Login</button>
</form>