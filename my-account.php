<?php
session_start();

if (!isset($_SESSION['customerId'])) {
    header("Location: /error.php?error=unauthorized");
    exit;
}

require_once "bootstrap.php";
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";

$db = new Database();
$db->connect();
$repo = new CustomerLogonRepository($db);

$customerId = $_SESSION['customerId'];
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['firstName'] ?? '');
    $last = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$last || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: my-account.php?error=validation");
        exit;
    }

    $repo->updateCustomerBasicInfo($customerId, $first, $last, $email);

    if (!empty($password)) {
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
            header("Location: my-account.php?error=weakPassword");
            exit;
        }
        $repo->updateCustomerPassword($customerId, $password);
    }

    header("Location: my-account.php?success=1");
    exit;
}

// Load user info
$user = $repo->getUserDetailsById($customerId);
if (!$user) {
    header("Location: /error.php?error=userNotFound");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container mt-5">
    <?php require_once "navbar.php"; ?>
    <h1>My Account</h1>

    <?php if ($error === 'validation'): ?>
        <div class="alert alert-danger">Please enter a valid last name and email address.</div>
    <?php elseif ($error === 'weakPassword'): ?>
        <div class="alert alert-warning">
            Password must have at least 6 characters, one uppercase letter, one number, and one special character.
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">Changes saved successfully!</div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="form-group">
            <label>First Name</label>
            <input name="firstName" class="form-control" value="<?= htmlspecialchars($user['FirstName']) ?>" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input name="lastName" class="form-control" value="<?= htmlspecialchars($user['LastName']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($user['Email']) ?>" required>
        </div>
        <div class="form-group">
            <label>New Password <small>(optional)</small></label>
            <input name="password" type="password" class="form-control" placeholder="New Password">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>

    <?php require_once "bootstrap.php"; ?>
</body>

</html>