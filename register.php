<?php
session_start();

require_once "bootstrap.php";
require_once "classes/Customer.php";
require_once "classes/CustomerLogon.php";
require_once "Database.php";

$db = new Database();
$db->connect();

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

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
        header("Location: register.php?error=validation");
        exit;
    }

    $stmt = $db->prepareStatement("SELECT COUNT(*) FROM customerlogon WHERE UserName = :username");
    $stmt->bindValue("username", $username);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        header("Location: register.php?error=exists");
        exit;
    }

    // === Manuell nächste CustomerId ermitteln
    $stmt = $db->prepareStatement("SELECT MAX(CustomerId) + 1 AS nextId FROM customers");
    $stmt->execute();
    $nextId = $stmt->fetchColumn();
    if (!$nextId) {
        $nextId = 1;
    }

    // === Insert in customers
    $customer = new Customer($firstName, $lastName, $address, $city, $country, $postal, $email, $region, $phone);
    $stmt = $db->prepareStatement("
        INSERT INTO customers (CustomerId, FirstName, LastName, Address, City, Region, Country, Postal, Phone, Email)
        VALUES (:id, :first, :last, :address, :city, :region, :country, :postal, :phone, :email)
    ");
    $stmt->bindValue("id", $nextId, PDO::PARAM_INT);
    $stmt->bindValue("first", $customer->getFirstName());
    $stmt->bindValue("last", $customer->getLastName());
    $stmt->bindValue("address", $customer->getAddress());
    $stmt->bindValue("city", $customer->getCity());
    $stmt->bindValue("region", $customer->getRegion());
    $stmt->bindValue("country", $customer->getCountry());
    $stmt->bindValue("postal", $customer->getPostal());
    $stmt->bindValue("phone", $customer->getPhone());
    $stmt->bindValue("email", $customer->getEmail());
    $stmt->execute();

    $customerId = $nextId;

    // === Passwort hash + salt
    $salt = bin2hex(random_bytes(8));
    $hashed = hash("sha256", $salt . $password);

    // === Insert in customerlogon
    $logon = new CustomerLogon($username, $hashed, $salt, 1, 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $customerId);
    $stmt = $db->prepareStatement("
        INSERT INTO customerlogon (CustomerId, UserName, Pass, Salt, State, Type, DateJoined, DateLastModified)
        VALUES (:id, :user, :pass, :salt, :state, :type, :joined, :modified)
    ");
    $stmt->bindValue("id", $logon->getCustomerId(), PDO::PARAM_INT);
    $stmt->bindValue("user", $logon->getUserName());
    $stmt->bindValue("pass", $logon->getPass());
    $stmt->bindValue("salt", $logon->getSalt());
    $stmt->bindValue("state", $logon->getState());
    $stmt->bindValue("type", $logon->getType());
    $stmt->bindValue("joined", $logon->getDateJoined());
    $stmt->bindValue("modified", $logon->getDateLastModified());
    $stmt->execute();

    $db->disconnect();
    header("Location: register.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>

<body class="container mt-5">
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
        <div class="form-group"><input name="firstName" class="form-control" placeholder="First Name"></div>
        <div class="form-group"><input name="lastName" class="form-control" placeholder="Last Name*" required></div>
        <div class="form-group"><input name="address" class="form-control" placeholder="Address*" required></div>
        <div class="form-group"><input name="city" class="form-control" placeholder="City*" required></div>
        <div class="form-group"><input name="region" class="form-control" placeholder="Region (optional)"></div>
        <div class="form-group"><input name="country" class="form-control" placeholder="Country*" required></div>
        <div class="form-group"><input name="postal" class="form-control" placeholder="Postal Code"></div>
        <div class="form-group"><input name="phone" class="form-control" placeholder="Phone (optional)"></div>
        <div class="form-group"><input name="email" type="email" class="form-control" placeholder="Email*" required></div>

        <hr>
        <div class="form-group"><input name="username" class="form-control" placeholder="Username*" required></div>
        <div class="form-group"><input name="password" type="password" class="form-control" placeholder="Password (min. 6 chars)*" required></div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <?php require_once "bootstrap.php"; ?>
</body>

</html>