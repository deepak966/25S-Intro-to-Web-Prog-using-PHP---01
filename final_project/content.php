<?php
require 'includes/header.php';
require 'includes/db_connect.php';

// Restrict access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle content creation/deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM content WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['delete_id'], $_SESSION['user_id']]);
    } elseif (isset($_POST['title'], $_POST['body'])) {
        $stmt = $pdo->prepare("INSERT INTO content (title, body, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['body'], $_SESSION['user_id']]);
    }
}

// Fetch user's content
$stmt = $pdo->prepare("SELECT c.*, u.username FROM content c JOIN users u ON c.user_id = u.id WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();
?>
<!-- Content management page -->
<h1>Manage Your Posts</h1>
<h2>Add New Post</h2>
<form method="POST">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" id="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="body" class="form-label">Content</label>
        <textarea name="body" id="body" class="form-control" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add Post</button>
</form>
<h2>Your Posts</h2>
<?php foreach ($posts as $post): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
            <form method="POST" class="d-inline">
                <input type="hidden" name="delete_id" value="<?php echo $post['id']; ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>