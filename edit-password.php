<?php
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";
require_once "classes/Customer.php";
require_once dirname(__DIR__) . "/src/router/router.php";

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['customerId'])) {
  header("Location: /error.php?error=notLoggedIn");
  exit;
}

$db = new Database();
$repo = new CustomerLogonRepository($db);

$id = (int) $_SESSION['customerId'];
$user = $repo->getUserDetailsById($id);

if (!$user) {
  header("Location: /error.php?error=userNotFound");
  exit;
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: /error.php?error=csrf");
    exit;
  }
  $oldPassword = $_POST['oldPassword'] ?? '';
  $newPassword1 = $_POST['newPassword1'] ?? '';
  $newPassword2 = $_POST['newPassword2'] ?? '';
  $userLogon = $repo->getActiveUserByUsername($user['UserName']);
  if (!$userLogon || !password_verify($oldPassword, $userLogon['Pass'])) {
    $error = 'oldPasswordWrong';
  } elseif ($newPassword1 !== $newPassword2) {
    $error = 'passwordMismatch';
  } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $newPassword1)) {
    $error = 'passwordWeak';
  } else {
    $hashed = password_hash($newPassword1, PASSWORD_DEFAULT);
    // Check if the new password hash is the same as the old one
    if (password_verify($newPassword1, $userLogon['Pass'])) {
      $error = 'nochange';
    } else {
      $salt = substr($hashed, 7, 22);
      $repo->updateCustomerPassword($id, $hashed, $salt);
      header("Location: " . route('account') . "?success=password");
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container">
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
  <h1 class="mt-3">Edit Password</h1>
  <?php if ($error === 'oldPasswordWrong'): ?>
    <div class="alert alert-danger">Old password is incorrect.</div>
  <?php elseif ($error === 'passwordMismatch'): ?>
    <div class="alert alert-danger">New passwords do not match.</div>
  <?php elseif ($error === 'passwordWeak'): ?>
    <div class="alert alert-danger">New password must be at least 6 characters, contain an uppercase letter, a digit, and
      a special character.</div>
  <?php elseif ($error === 'nochange'): ?>
    <div class="alert alert-info">No changes detected. The new password is the same as the old one.</div>
  <?php endif; ?>
  <form method="POST" class="mt-4 mb-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>Old Password</label>
        <input name="oldPassword" type="password" class="form-control" autocomplete="current-password" required>
      </div>
      <div class="form-group col-md-4">
        <label>New Password</label>
        <input name="newPassword1" type="password" class="form-control" autocomplete="new-password" required>
      </div>
      <div class="form-group col-md-4">
        <label>Repeat New Password</label>
        <input name="newPassword2" type="password" class="form-control" autocomplete="new-password" required>
      </div>
    </div>
    <small class="form-text text-muted mb-3">To change your password, enter your old password and the new password
      twice. New password must be at least 6 characters, contain an uppercase letter, a digit, and a special
      character.</small>
    <button type="submit" class="btn btn-primary">Save Password</button>
    <a href="<?php echo route('account'); ?>" class="btn btn-danger ml-2">Cancel</a>
  </form>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>

</html>