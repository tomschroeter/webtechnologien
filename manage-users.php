<?php
require_once dirname(__DIR__) . "/src/Database.php";
require_once dirname(__DIR__) . "/src/repositories/CustomerLogonRepository.php";

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!($_SESSION['isAdmin'] ?? false)) {
  header("Location: error.php?error=unauthorized");
  exit;
}

$db = new Database();
$repo = new CustomerLogonRepository($db);

// Handle user updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $customerID = (int) ($_POST['customerId'] ?? 0);
  $action = $_POST['action'] ?? '';

  // Check if trying to demote the last admin
  if ($action === 'demote') {
    $adminCount = $repo->countActiveAdmins();
    if ($adminCount <= 1) {
      header("Location: manage-users.php?error=lastadmin");
      exit;
    }
  }

  // Check if trying to deactivate the last active admin
  if ($action === 'deactivate') {
    $user = $repo->getUserDetailsById($customerID);
    if ($user && $user['isAdmin']) {
      $adminCount = $repo->countActiveAdmins();
      if ($adminCount <= 1) {
        header("Location: manage-users.php?error=lastadmin");
        exit;
      }
    }
  }

  if ($customerID && in_array($action, ['promote', 'demote', 'deactivate', 'activate'])) {
    if ($action === 'promote') {
      $repo->updateUserAdmin($customerID, true);
    } elseif ($action === 'demote') {
      $repo->updateUserAdmin($customerID, false);

      // Check if admin is demoting themselves
      if (
        isset($_SESSION['customerId']) &&
        $customerID === (int) $_SESSION['customerId']
      ) {

        // Update session to reflect they're no longer admin
        $_SESSION['isAdmin'] = false;

        // Redirect to home page instead of manage-users
        header("Location: /index");
        exit;
      }
    } elseif ($action === 'activate') {
      $repo->updateUserState($customerID, 1);
    } elseif ($action === 'deactivate') {
      $repo->updateUserState($customerID, 0);
    }

    header("Location: manage-users.php");
    exit;
  }
}

$users = $repo->getAllUsersWithLogonData();
$adminCount = $repo->countActiveAdmins();
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once dirname(__DIR__) . "/src/head.php"; ?>

<body class="container">
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
  <h1 class="mt-3">User Management</h1>

  <?php if (isset($_GET['error']) && $_GET['error'] === 'lastadmin'): ?>
    <div class="alert alert-danger">Cannot demote or deactivate the last administrator. There must be at least one active
      admin.</div>
  <?php endif; ?>

  <table class="table table-bordered mt-4">
    <thead class="thead-dark">
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Username</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></td>
          <td><?= htmlspecialchars($user['Email']) ?></td>
          <td><?= htmlspecialchars($user['UserName']) ?></td>
          <td><?= $user['isAdmin'] ? 'Admin' : 'User' ?></td>
          <td><?= $user['State'] == 1 ? 'Active' : 'Inactive' ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="edit-user.php?id=<?= $user['CustomerID'] ?>">Edit</a>

            <?php if (!$user['isAdmin']): ?>
              <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to promote this user?')">
                <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
                <button name="action" value="promote" class="btn btn-sm btn-success">Promote</button>
              </form>
            <?php elseif ($user['isAdmin'] && !($user['isAdmin'] && $adminCount <= 1)): ?>
              <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to demote this user?')">
                <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
                <button name="action" value="demote" class="btn btn-sm btn-warning">Demote</button>
              </form>
            <?php endif; ?>

            <?php if (!($user['isAdmin'] && $adminCount <= 1)): ?>
              <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to change status?')">
                <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
                <button name="action" value="<?= $user['State'] == 1 ? 'deactivate' : 'activate' ?>"
                  class="btn btn-sm btn-<?= $user['State'] == 1 ? 'secondary' : 'primary' ?>">
                  <?= $user['State'] == 1 ? 'Deactivate' : 'Activate' ?>
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>

</html>