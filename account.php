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

$id = (int)$_SESSION['customerId'];
$user = $repo->getUserDetailsById($id);
$customer = $repo->getCustomerById($id);

if (!$user || !$customer) {
    header("Location: /error.php?error=userNotFound");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>
<body class="container">
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
  <h1 class="mt-3">My Account</h1>
  <?php if (isset($_GET['success']) && $_GET['success'] === 'info'): ?>
    <div class="alert alert-success">Profile updated successfully.</div>
  <?php elseif (isset($_GET['success']) && $_GET['success'] === 'password'): ?>
    <div class="alert alert-success">Password updated successfully.</div>
  <?php endif; ?>

  <h4 class="mt-2">General Information</h4>
  <form class="mt-4 mb-4" action="<?php echo route('edit_account'); ?>" method="get">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>First Name</label>
        <input name="firstName" class="form-control" value="<?= htmlspecialchars($customer['FirstName']) ?>" disabled>
      </div>
      <div class="form-group col-md-6">
        <label>Last Name</label>
        <input name="lastName" class="form-control" value="<?= htmlspecialchars($customer['LastName']) ?>" disabled>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Address</label>
        <input name="address" class="form-control" value="<?= htmlspecialchars($customer['Address']) ?>" disabled>
      </div>
      <div class="form-group col-md-6">
        <label>City</label>
        <input name="city" class="form-control" value="<?= htmlspecialchars($customer['City']) ?>" disabled>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>Region</label>
        <input name="region" class="form-control" value="<?= htmlspecialchars($customer['Region']) ?>" disabled>
      </div>
      <div class="form-group col-md-4">
        <label>Country</label>
        <input name="country" class="form-control" value="<?= htmlspecialchars($customer['Country']) ?>" disabled>
      </div>
      <div class="form-group col-md-4">
        <label>Postal</label>
        <input name="postal" class="form-control" value="<?= htmlspecialchars($customer['Postal']) ?>" disabled>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Phone</label>
        <input name="phone" class="form-control" value="<?= htmlspecialchars($customer['Phone']) ?>" disabled>
      </div>
      <div class="form-group col-md-6">
        <label>Email</label>
        <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($customer['Email']) ?>" disabled>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Edit</button>
  </form>

  <hr>
  <h4>Change Password</h4>
  <form class="mt-4" action="<?php echo route('edit_password'); ?>" method="get">
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>Password</label>
        <input type="password" class="form-control" value="********" disabled>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Edit Password</button>
  </form>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
