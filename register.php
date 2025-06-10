<?php
session_start();

require_once "bootstrap.php";
require_once "classes/Customer.php";
require_once "classes/CustomerLogon.php";
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";

$db = new Database();
$db->connect();
$repo = new CustomerLogonRepository($db);

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

$firstName = $lastName = $address = $city = $region = $country = $postal = $phone = $email = $username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $postal = trim($_POST['postal'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $validPassword = preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password);

    if (!$lastName || !$city || !$address || !$country || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$validPassword) {
        $error = 'validation';
    } elseif ($repo->userExists($username)) {
        $error = 'exists';
    } else {
        $customerId = $repo->getNextCustomerId();
        $customer = new Customer($firstName, $lastName, $address, $city, $country, $postal, $email, $region, $phone);
        $repo->insertCustomer($customer, $customerId);

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $logon = new CustomerLogon($username, $hashed, 1, 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $customerId);
        $repo->insertLogon($logon);

        $db->disconnect();
        header("Location: register.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container mt-5">
    <?php require_once "navbar.php"; ?>
    <h1>Register</h1>

    <?php if ($error === 'validation'): ?>
        <div class="alert alert-danger">
            Please fill out all required fields correctly.<br>
            The password must contain at least 6 characters, one uppercase letter, one number, and one special character.
        </div>
    <?php elseif ($error === 'exists'): ?>
        <div class="alert alert-warning">Username already exists. Please choose another one.</div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">Registration successful! You can now <a href="login.php">log in</a>.</div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="form-group"><input name="firstName" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($firstName ?? '') ?>"></div>
        <div class="form-group"><input name="lastName" class="form-control" placeholder="Last Name*" required value="<?= htmlspecialchars($lastName ?? '') ?>"></div>
        <div class="form-group"><input name="address" class="form-control" placeholder="Address*" required value="<?= htmlspecialchars($address ?? '') ?>"></div>
        <div class="form-group"><input name="city" class="form-control" placeholder="City*" required value="<?= htmlspecialchars($city ?? '') ?>"></div>
        <div class="form-group"><input name="region" class="form-control" placeholder="Region (optional)" value="<?= htmlspecialchars($region ?? '') ?>"></div>
        <div class="form-group"><input name="country" class="form-control" placeholder="Country*" required value="<?= htmlspecialchars($country ?? '') ?>"></div>
        <div class="form-group"><input name="postal" class="form-control" placeholder="Postal Code" value="<?= htmlspecialchars($postal ?? '') ?>"></div>
        <div class="form-group"><input name="phone" class="form-control" placeholder="Phone (optional)" value="<?= htmlspecialchars($phone ?? '') ?>"></div>
        <div class="form-group"><input name="email" type="email" class="form-control" placeholder="Email*" required value="<?= htmlspecialchars($email ?? '') ?>"></div>

        <hr>
        <div class="form-group"><input name="username" class="form-control" placeholder="Username*" required value="<?= htmlspecialchars($username ?? '') ?>"></div>
        <div class="form-group"><input name="password" type="password" class="form-control" placeholder="Password (min. 6 chars)*" required></div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <?php require_once "bootstrap.php"; ?>
</body>

</html>