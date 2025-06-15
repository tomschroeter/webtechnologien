<?php
require_once "bootstrap.php";
require_once "classes/Customer.php";
require_once "classes/CustomerLogon.php";
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['customerId'])) {
    header("Location: account");
    exit;
}

$db = new Database();
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
    $password2 = $_POST['password2'] ?? '';

    $validPhoneNumber = preg_match('/^\+?[0-9\s\-\(\)\.\/xXextEXT\*#]{5,30}$/', $phone);
    $validPassword = preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password);

    if (!$lastName || !$city || !$address || !$country || !$email || !$password) {
        $error = 'empty_field';
    } elseif (!$validPhoneNumber) {
        $error = 'invalid_phone_number';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'invalid_email';
    } elseif (!$validPassword) {
        $error = 'invalid_password';
    } elseif ($password !== $password2) {
        $error = 'password_mismatch';
    } elseif ($repo->userExists($username)) {
        $error = 'exists';
    } else {
        try {
            $customer = new Customer($firstName, $lastName, $address, $city, $country, $postal, $email, $region, $phone);

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $logon = new CustomerLogon($username, $hashed, 1, 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));

            // Use atomic registration method to prevent race conditions
            $customerId = $repo->registerCustomer($customer, $logon);

            // Automatically log the user in after successful registration
            $_SESSION['customerId'] = $customerId;  // This should be consistent
            $_SESSION['username'] = $username;
            $_SESSION['isAdmin'] = false; // New users are always regular users

            // Redirect to home page (logged in)
            header("Location: index.php?welcome=1");
            exit;
        } catch (Exception $e) {
            $error = 'database';
            echo $e;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container">
    <?php require_once "navbar.php"; ?>
    <h1 class="mt-3">Register</h1>

    <?php if ($error === 'empty_field'): ?>
        <div class="alert alert-danger">Please fill out all required fields.</div>
    <?php elseif ($error === 'invalid_phone_number'): ?>
        <div class="alert alert-danger">The phone number doesn't have a valid format.</div>
    <?php elseif ($error === 'invalid_email'): ?>
        <div class="alert alert-danger">The E-Mail doesn't have a valid format.</div>
    <?php elseif ($error === 'invalid_password'): ?>
        <div class="alert alert-danger">
            The password must contain at least 6 characters, one uppercase letter, one number, and one special character.
            . </div>
    <?php elseif ($error === 'exists'): ?>
        <div class="alert alert-warning">Username already exists. Please choose another one.</div>
    <?php elseif ($error === 'password_mismatch'): ?>
        <div class="alert alert-danger">Passwords do not match. Please try again.</div>
    <?php elseif ($error === 'database'): ?>
        <div class="alert alert-danger">Registration failed due to a database error. Please try again.</div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">Registration successful! You can now <a href="login.php">log in</a>.</div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>First Name</label>
                <input name="firstName" class="form-control" placeholder="First Name"
                    value="<?= htmlspecialchars($firstName ?? '') ?>">
            </div>
            <div class="form-group col-md-6">
                <label>Last Name</label>
                <input name="lastName" class="form-control" placeholder="Last Name*" required
                    value="<?= htmlspecialchars($lastName ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Address</label>
                <input name="address" class="form-control" placeholder="Address*" required
                    value="<?= htmlspecialchars($address ?? '') ?>">
            </div>
            <div class="form-group col-md-6">
                <label>City</label>
                <input name="city" class="form-control" placeholder="City*" required
                    value="<?= htmlspecialchars($city ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Region</label>
                <input name="region" class="form-control" placeholder="Region (optional)"
                    value="<?= htmlspecialchars($region ?? '') ?>">
            </div>
            <div class="form-group col-md-4">
                <label>Country</label>
                <input name="country" class="form-control" placeholder="Country*" required
                    value="<?= htmlspecialchars($country ?? '') ?>">
            </div>
            <div class="form-group col-md-4">
                <label>Postal</label>
                <input name="postal" class="form-control" placeholder="Postal Code"
                    value="<?= htmlspecialchars($postal ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Phone</label>
                <input name="phone" class="form-control" placeholder="Phone (optional)"
                    value="<?= htmlspecialchars($phone ?? '') ?>">
            </div>
            <div class="form-group col-md-6">
                <label>Email</label>
                <input name="email" type="email" class="form-control" placeholder="Email*" required
                    value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
        </div>
        <hr>
        <div class="form-group">
            <label>Username</label>
            <input name="username" class="form-control" placeholder="Username*" required
                value="<?= htmlspecialchars($username ?? '') ?>">
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Password</label>
                <input name="password" type="password" class="form-control" placeholder="Password*" required>
            </div>
            <div class="form-group col-md-6">
                <label>Repeat Password</label>
                <input name="password2" type="password" class="form-control" placeholder="Repeat Password*" required>
            </div>
        </div>
        <small class="form-text text-muted mb-3">Your password must be at least 6 characters, contain an uppercase
            letter, a digit, and a special character.</small>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <?php require_once "bootstrap.php"; ?>
</body>

</html>