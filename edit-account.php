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
$customer = $repo->getCustomerById($id);

if (!$customer) {
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
    // Check if all info is the same as before
    $allSame = (
        $first === ($customer['FirstName'] ?? '') &&
        $last === ($customer['LastName'] ?? '') &&
        $address === ($customer['Address'] ?? '') &&
        $city === ($customer['City'] ?? '') &&
        $region === ($customer['Region'] ?? '') &&
        $country === ($customer['Country'] ?? '') &&
        $postal === ($customer['Postal'] ?? '') &&
        $phone === ($customer['Phone'] ?? '') &&
        $email === ($customer['Email'] ?? '')
    );
    if ($allSame) {
        $error = 'nochange';
    } elseif (!$last || !$city || !$address || !$country || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'invalidInput';
    } else {
        $repo->updateCustomerFullInfo($id, $first, $last, $address, $city, $region, $country, $postal, $phone, $email);
        header("Location: " . route('account') . "?success=info");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>
<body class="container">
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
  <h1 class="mt-3">Edit General Information</h1>
  <?php if ($success): ?>
    <div class="alert alert-success">Profile updated successfully.</div>
  <?php elseif ($error === 'invalidInput'): ?>
    <div class="alert alert-danger">Please fill in all required fields and provide a valid email.</div>
  <?php elseif ($error === 'nochange'): ?>
    <div class="alert alert-info">No changes detected. All information is the same.</div>
  <?php endif; ?>
  <form method="POST" class="mt-4 mb-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
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
    <a href="<?php echo route('account'); ?>" class="btn btn-danger ml-2">Cancel</a>
  </form>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
