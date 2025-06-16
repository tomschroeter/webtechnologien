<h1 class="mt-4">Login</h1>

<?php if ($error === 'missing'): ?>
    <div class="alert alert-warning">Please enter username and password.</div>
<?php elseif ($error === 'invalid'): ?>
    <div class="alert alert-danger">Invalid username or password.</div>
<?php elseif ($logout): ?>
    <div class="alert alert-success">You were logged out successfully.</div>
<?php endif; ?>

<form method="POST" action="/login" class="mt-4">
    <div class="form-group">
        <label>Username</label>
        <input name="username" class="form-control" placeholder="Username" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input name="password" type="password" class="form-control" placeholder="Password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
