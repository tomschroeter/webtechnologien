<?php
session_start();
require_once "bootstrap.php";
require_once "Database.php";
require_once "repositories/CustomerLogonRepository.php";

if (!($_SESSION['isAdmin'] ?? false)) {
    header("Location: /error.php?error=unauthorized");
    exit;
}

$db = new Database();
$db->connect();
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
    $type = $_POST['type'] ?? 0;

    if (!$last || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: edit-user.php?id=$id&error=invalidInput");
        exit;
    }

    $repo->updateCustomerBasicInfo((int)$id, $first, $last, $email);
    $repo->updateUserType((int)$id, (int)$type);

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
      <select name="type" class="form-control">
        <option value="0" <?= $user['Type'] == 0 ? 'selected' : '' ?>>User</option>
        <option value="1" <?= $user['Type'] == 1 ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="manage-users.php" class="btn btn-secondary ml-2">Cancel</a>
  </form>
</body>
</html>
