<?php
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!($_SESSION['isAdmin'] ?? false)) {
    header("Location: /error.php?error=unauthorized");
    exit;
}

$db = new Database();
$repo = new CustomerLogonRepository($db);

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: /error.php?error=invalidId");
    exit;
}

$user = $repo->getUserDetailsById((int)$id);

if (!$user) {
    header("Location: /error.php?error=userNotFound");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['firstName'] ?? '');
    $last = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $isAdmin = isset($_POST['isAdmin']) && $_POST['isAdmin'] === '1';

    if (!$last || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: edit-user.php?id=$id&error=invalidInput");
        exit;
    }

    $repo->updateCustomerBasicInfo((int)$id, $first, $last, $email);
    $repo->updateUserAdmin((int)$id, $isAdmin);

    // Check if admin is demoting themselves
    if (isset($_SESSION['customerId']) && 
        (int)$id === (int)$_SESSION['customerId'] && 
        !$isAdmin && 
        ($_SESSION['isAdmin'] ?? false)) {
        
        // Update session to reflect they're no longer admin
        $_SESSION['isAdmin'] = false;
        
        // Redirect to home page instead of manage-users
        header("Location: /index.php");
        exit;
    }

    header("Location: manage-users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "head.php"; ?>
<body class="container mt-5">
  <h1>Edit User</h1>

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
      <label>Role</label>
      <select name="isAdmin" class="form-control">
        <option value="0" <?= !$user['isAdmin'] ? 'selected' : '' ?>>User</option>
        <option value="1" <?= $user['isAdmin'] ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="manage-users.php" class="btn btn-secondary ml-2">Cancel</a>
  </form>
  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>
</html>
