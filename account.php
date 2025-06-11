<?php
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";
require_once "classes/Customer.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['customerId'])) {
    header("Location: /error.php?error=notLoggedIn");
    exit;
}

$db = new Database();
$repo = new CustomerLogonRepository($db);

$id = (int)$_SESSION['customerId'];
$user = $repo->getUserDetailsById($id);
$customer = $repo->getCustomerById($id);

if (!$user || !$customer) {
    header("Location: /error.php?error=userNotFound");
    exit;
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: /error.php?error=csrf");
        exit;
    }
    $formType = $_POST['form_type'] ?? '';
    if ($formType === 'general') {
        // Collect and trim all fields for general info
        $first = trim($_POST['firstName'] ?? '');
        $last = trim($_POST['lastName'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $postal = trim($_POST['postal'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        // Validate required fields
        if (!$last || !$city || !$address || !$country || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'invalidInput';
        } else {
            $repo->updateCustomerFullInfo($id, $first, $last, $address, $city, $region, $country, $postal, $phone, $email);
            $success = true;
            $customer = $repo->getCustomerById($id);
        }
    } elseif ($formType === 'password') {
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
            $salt = substr($hashed, 7, 22);
            $repo->updateCustomerPassword($id, $hashed, $salt);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>
<body class="container">
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
  <h1 class="mt-3">My Account</h1>
  <?php if ($success): ?>
    <div class="alert alert-success">Profile updated successfully.</div>
  <?php elseif ($error === 'invalidInput'): ?>
    <div class="alert alert-danger">Please fill in all required fields and provide a valid email.</div>
  <?php elseif ($error === 'oldPasswordWrong'): ?>
    <div class="alert alert-danger">Old password is incorrect.</div>
  <?php elseif ($error === 'passwordMismatch'): ?>
    <div class="alert alert-danger">New passwords do not match.</div>
  <?php elseif ($error === 'passwordWeak'): ?>
    <div class="alert alert-danger">New password must be at least 6 characters, contain an uppercase letter, a digit, and a special character.</div>
  <?php endif; ?>

  <h4 class="mt-2">General Information</h4>
  <form method="POST" class="mt-4 mb-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="form_type" value="general">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>First Name</label>
        <input name="firstName" class="form-control" value="<?= htmlspecialchars($customer['FirstName']) ?>" required>
      </div>
      <div class="form-group col-md-6">
        <label>Last Name</label>
        <input name="lastName" class="form-control" value="<?= htmlspecialchars($customer['LastName']) ?>" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Address</label>
        <input name="address" class="form-control" value="<?= htmlspecialchars($customer['Address']) ?>" required>
      </div>
      <div class="form-group col-md-6">
        <label>City</label>
        <input name="city" class="form-control" value="<?= htmlspecialchars($customer['City']) ?>" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>Region</label>
        <input name="region" class="form-control" value="<?= htmlspecialchars($customer['Region']) ?>">
      </div>
      <div class="form-group col-md-4">
        <label>Country</label>
        <input name="country" class="form-control" value="<?= htmlspecialchars($customer['Country']) ?>" required>
      </div>
      <div class="form-group col-md-4">
        <label>Postal</label>
        <input name="postal" class="form-control" value="<?= htmlspecialchars($customer['Postal']) ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Phone</label>
        <input name="phone" class="form-control" value="<?= htmlspecialchars($customer['Phone']) ?>">
      </div>
      <div class="form-group col-md-6">
        <label>Email</label>
        <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($customer['Email']) ?>" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
  </form>

  <hr>
  <h4>Change Password</h4>
  <form method="POST" class="mt-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="form_type" value="password">
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>Old Password</label>
        <input name="oldPassword" type="password" class="form-control" autocomplete="current-password">
      </div>
      <div class="form-group col-md-4">
        <label>New Password</label>
        <input name="newPassword1" type="password" class="form-control" autocomplete="new-password">
      </div>
      <div class="form-group col-md-4">
        <label>Repeat New Password</label>
        <input name="newPassword2" type="password" class="form-control" autocomplete="new-password">
      </div>
    </div>
    <small class="form-text text-muted mb-3">To change your password, enter your old password and the new password twice. New password must be at least 6 characters, contain an uppercase letter, a digit, and a special character.</small>
    <button type="submit" class="btn btn-primary">Save Password</button>
  </form>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
