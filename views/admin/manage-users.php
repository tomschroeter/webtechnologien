<h1 class="mt-3">User Management</h1>

<?php if ($error === 'lastadmin'): ?>
    <div class="alert alert-danger">Cannot demote or deactivate the last administrator. There must be at least one active admin.</div>
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
                    <a class="btn btn-sm btn-primary" href="/edit-user?id=<?= $user['CustomerID'] ?>">Edit</a>

                    <?php if (!$user['isAdmin']): ?>
                        <form method="POST" action="/manage-users" class="d-inline" onsubmit="return confirm('Are you sure you want to promote this user?')">
                            <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
                            <button name="action" value="promote" class="btn btn-sm btn-success">Promote</button>
                        </form>
                    <?php elseif ($user['isAdmin'] && !($user['isAdmin'] && $adminCount <= 1)): ?>
                        <form method="POST" action="/manage-users" class="d-inline" onsubmit="return confirm('Are you sure you want to demote this user?')">
                            <input type="hidden" name="customerId" value="<?= $user['CustomerID'] ?>">
                            <button name="action" value="demote" class="btn btn-sm btn-warning">Demote</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!($user['isAdmin'] && $adminCount <= 1)): ?>
                        <form method="POST" action="/manage-users" class="d-inline" onsubmit="return confirm('Are you sure you want to change status?')">
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
