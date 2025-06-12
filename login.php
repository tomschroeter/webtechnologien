<?php
require_once "bootstrap.php";
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$repo = new CustomerLogonRepository($db);

// Meldungen verarbeiten
$error = $_GET['error'] ?? null;
$logout = $_GET['logout'] ?? null;

// Verarbeitung bei Formular-POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        header("Location: login.php?error=missing");
        exit;
    }

    $user = $repo->getActiveUserByUsername($username);

    if (!$user || !password_verify($password, $user['Pass'])) {
        header("Location: login.php?error=invalid");
        exit;
    }

    $_SESSION['customerId'] = $user['CustomerID'];  // Fixed: Use CustomerID (uppercase D)
    $_SESSION['username'] = $user['UserName'];
    $_SESSION['isAdmin'] = $user['isAdmin'] ?? false;

    header("Location: index.php?login=success");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container">
    <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
    <h1 class="mt-3">Login</h1>

    <?php if ($error === 'missing'): ?>
        <div class="alert alert-warning">Please enter username and password.</div>
    <?php elseif ($error === 'invalid'): ?>
        <div class="alert alert-danger">Invalid username or password.</div>
    <?php elseif ($logout): ?>
        <div class="alert alert-success">You were logged out successfully.</div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="form-group">
            <label>Benutzername</label>
            <input name="username" class="form-control" placeholder="Benutzername" required>
        </div>
        <div class="form-group">
            <label>Passwort</label>
            <input name="password" type="password" class="form-control" placeholder="Passwort" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <?php require_once "bootstrap.php"; ?>
</body>

</html>