<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "bootstrap.php";
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";

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

    $_SESSION['customerId'] = $user['CustomerId'];
    $_SESSION['username'] = $user['UserName'];
    $_SESSION['isAdmin'] = $user['isAdmin'] ?? false;

    header("Location: index.php?login=success");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container mt-5">
    <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
    <h1>Login</h1>

    <?php if ($error === 'missing'): ?>
        <div class="alert alert-warning">Bitte gib Benutzername und Passwort ein.</div>
    <?php elseif ($error === 'invalid'): ?>
        <div class="alert alert-danger">Ungültiger Benutzername oder Passwort.</div>
    <?php elseif ($logout): ?>
        <div class="alert alert-success">Du wurdest erfolgreich ausgeloggt.</div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="form-group">
            <input name="username" class="form-control" placeholder="Benutzername" required>
        </div>
        <div class="form-group">
            <input name="password" type="password" class="form-control" placeholder="Passwort" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <?php require_once "bootstrap.php"; ?>
</body>

</html>