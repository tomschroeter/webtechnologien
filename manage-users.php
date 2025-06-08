<?php
session_start();

if (!($_SESSION['isAdmin'] ?? false)) {
  header("Location: error.php?error=unauthorized");
  exit;
}

require_once dirname(__DIR__) . "/src/bootstrap.php";
require_once dirname(__DIR__) . "/src/Database.php";

$db = new Database();
$db->connect();

// Handle user updates (promote/demote or activate/deactivate)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $customerId = (int)($_POST['customerId'] ?? 0);
  $action = $_POST['action'] ?? '';

  if ($customerId === (int)$_SESSION['customerId'] && $action === 'demote') {
    header("Location: manage-users.php?error=selfdemote");
    exit;
  }

  if ($customerId && in_array($action, ['promote', 'demote', 'deactivate', 'activate'])) {
    $typeUpdate = "";
    $stateUpdate = "";

    if ($action === 'promote') {
      $typeUpdate = "Type = 1";
    } elseif ($action === 'demote') {
      $typeUpdate = "Type = 0";
    } elseif ($action === 'deactivate') {
      $stateUpdate = "State = 0";
    } elseif ($action === 'activate') {
      $stateUpdate = "State = 1";
    }

    if ($typeUpdate) {
      $stmt = $db->prepareStatement("UPDATE customerlogon SET $typeUpdate WHERE CustomerID = :id");
      $stmt->bindValue("id", $customerId, PDO::PARAM_INT);
      $stmt->execute();
    }

    if ($stateUpdate) {
      $stmt = $db->prepareStatement("UPDATE customerlogon SET $stateUpdate WHERE CustomerID = :id");
      $stmt->bindValue("id", $customerId, PDO::PARAM_INT);
      $stmt->execute();
    }

    header("Location: manage-users.php");
    exit;
  }
}

// Load all users
$stmt = $db->prepareStatement("SELECT c.CustomerID, FirstName, LastName, Email, UserName, Type, State FROM customers c JOIN customerlogon cl ON c.CustomerID = cl.CustomerID ORDER BY LastName, FirstName");
$stmt->execute();
$users = $stmt->fetchAll();

$db->disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once dirname(__DIR__) . "/src/head.php"; ?>

<body class="container mt-5">
  <?php require_once dirname(__DIR__) . "/src/navbar.php"; ?>
  <h1>User Management</h1>

  <?php if (isset($_GET['error']) && $_GET['error'] === 'selfdemote'): ?>
    <div class="alert alert-warning">You cannot demote yourself as admin.</div>
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
          <td><?= $user['Type'] == 1 ? 'Admin' : 'User' ?></td>
          <td><?= $user['State'] == 1 ? 'Active' : 'Inactive' ?></td>
          <td>
            <form method="POST" class="d-inline">
              <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
              <?php if ($user['Type'] == 0): ?>
                <button name="action" value="promote" class="btn btn-sm btn-success">Promote</button>
              <?php elseif ($_SESSION['customerId'] != $user['CustomerID']): ?>
                <button name="action" value="demote" class="btn btn-sm btn-warning">Demote</button>
              <?php endif; ?>
              <?php if ($user['State'] == 1): ?>
                <button name="action" value="deactivate" class="btn btn-sm btn-secondary">Deactivate</button>
              <?php else: ?>
                <button name="action" value="activate" class="btn btn-sm btn-primary">Activate</button>
              <?php endif; ?>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php require_once dirname(__DIR__) . "/src/bootstrap.php"; ?>
</body>

</html>