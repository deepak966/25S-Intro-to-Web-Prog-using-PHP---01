<?php
require 'includes/header.php';
require 'includes/db_connect.php';

// Restrict access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle update/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
    } elseif (isset($_POST['update_id'], $_POST['username'])) {
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$_POST['username'], $_POST['update_id']]);
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>
<!-- Users management page -->
<h1>Manage Users</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Username</th>
            <th>Profile Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="update_id" value="<?php echo $user['id']; ?>">
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control d-inline-block w-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </form>
                </td>
                <td>
                    <?php if ($user['profile_image']): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" height="50">
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require 'includes/footer.php'; ?>