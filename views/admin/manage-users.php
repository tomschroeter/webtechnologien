<h1 class="mt-4">Manage Users</h1>

<!-- Users table with columns for name, email, username, role, status, and actions -->
<table class="table table-bordered mt-4 mb-5">
    <thead class="table-dark">
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
            <tr data-user-id="<?= $user->getCustomerId() ?>">
                <!-- Display user information -->
                <td><?= htmlspecialchars($user->getFullName()) ?></td>
                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                <td><?= htmlspecialchars($user->getUserName()) ?></td>
                <!-- Show role based on admin status -->
                <td class="role-cell"><?= $user->getIsAdmin() ? 'Admin' : 'User' ?></td>
                <!-- Show active/inactive status -->
                <td class="status-cell"><?= $user->getState() == 1 ? 'Active' : 'Inactive' ?></td>
                <td class="actions-cell">
                    <!-- Edit button links to edit profile page for user -->
                    <a class="btn btn-sm btn-primary" href="/edit-profile/<?= $user->getCustomerId() ?>">Edit</a>

                    <?php if (!$user->getIsAdmin()): ?>
                        <!-- Promote button (form submission) only for non-admins -->
                        <form method="POST" action="/manage-users" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to promote this user?')">
                            <input type="hidden" name="customerId" value="<?= $user->getCustomerId() ?>">
                            <button name="action" value="promote" class="btn btn-sm btn-success">Promote</button>
                        </form>
                    <?php elseif ($user->getIsAdmin() && !($user->getIsAdmin() && $adminCount <= 1)): ?>
                        <!-- Demote button for admins except when only one admin remains -->
                        <form method="POST" action="/manage-users" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to demote this user?')">
                            <input type="hidden" name="customerId" value="<?= $user->getCustomerId() ?>">
                            <button name="action" value="demote" class="btn btn-sm btn-warning">Demote</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!($user->getIsAdmin() && $adminCount <= 1)): ?>
                        <!-- Activate/Deactivate toggle button -->
                        <form method="POST" action="/manage-users" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to change this user\'s status?')">
                            <input type="hidden" name="customerId" value="<?= $user->getCustomerId() ?>">
                            <button name="action" value="<?= $user->getState() == 1 ? 'deactivate' : 'activate' ?>"
                                class="btn btn-sm btn-<?= $user->getState() == 1 ? 'secondary' : 'primary' ?>">
                                <?= $user->getState() == 1 ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>