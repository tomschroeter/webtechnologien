<h1 class="mt-4">User Management</h1>

<?php if ($error === 'lastadmin'): ?>
    <div class="alert alert-danger">Cannot demote or deactivate the last administrator. There must be at least one active admin.</div>
<?php endif; ?>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['message']) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
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
            <tr data-user-id="<?= $user['CustomerID'] ?>">
                <td><?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></td>
                <td><?= htmlspecialchars($user['Email']) ?></td>
                <td><?= htmlspecialchars($user['UserName']) ?></td>
                <td class="role-cell"><?= $user['isAdmin'] ? 'Admin' : 'User' ?></td>
                <td class="status-cell"><?= $user['State'] == 1 ? 'Active' : 'Inactive' ?></td>
                <td class="actions-cell">
                    <a class="btn btn-sm btn-primary" href="/edit-profile/<?= $user['CustomerID'] ?>">Edit</a>

                    <?php if (!$user['isAdmin']): ?>
                        <!-- Form for promoting user -->
                        <form method="POST" action="/manage-users" class="d-inline" onsubmit="return confirm('Are you sure you want to promote this user?')">
                            <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
                            <button name="action" value="promote" class="btn btn-sm btn-success">Promote</button>
                        </form>
                    <?php elseif ($user['isAdmin'] && !($user['isAdmin'] && $adminCount <= 1)): ?>
                        <!-- Form for demoting user -->
                        <form method="POST" action="/manage-users" class="d-inline" onsubmit="return confirm('Are you sure you want to demote this user?')">
                            <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
                            <button name="action" value="demote" class="btn btn-sm btn-warning">Demote</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!($user['isAdmin'] && $adminCount <= 1)): ?>
                        <!-- Form for changing user status -->
                        <form method="POST" action="/manage-users" class="d-inline" onsubmit="return confirm('Are you sure you want to change this user\'s status?')">
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
