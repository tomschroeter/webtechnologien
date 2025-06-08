<?php
session_start();

require_once "bootstrap.php";
require_once "Database.php";

$db = new Database();
$db->connect();

// Meldungen verarbeiten
$error = $_GET['error'] ?? null;
$logout = $_GET['logout'] ?? null;

// Verarbeitung bei Formular-POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic Validierung
    if (!$username || !$password) {
        header("Location: login.php?error=missing");
        exit;
    }

    // Nutzer aus DB laden
    $stmt = $db->prepareStatement("
        SELECT * FROM customerlogon WHERE UserName = :username AND State = 1
    ");
    $stmt->bindValue("username", $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        header("Location: login.php?error=invalid");
        exit;
    }

    $storedHash = $user['Pass'];

    if (!password_verify($password, $storedHash)) {
        header("Location: login.php?error=invalid");
        exit;
    }


    // === Login erfolgreich
    $_SESSION['customerId'] = $user['CustomerId'];
    $_SESSION['username'] = $user['UserName'];
    $_SESSION['isAdmin'] = $user['Type'] == 1;


    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container mt-5">
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