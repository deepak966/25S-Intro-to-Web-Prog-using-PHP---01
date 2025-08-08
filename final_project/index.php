<?php
require 'includes/header.php';
require 'includes/db_connect.php';

// Select recent posts
$stmt = $pdo->query("SELECT c.*, u.username FROM content c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC LIMIT 5");
$posts = $stmt->fetchAll();
?>
<!-- Home page content -->
<h1>Welcome to Blog App</h1>
<p>Explore our latest blog posts below.</p>
<h2>Recent Posts</h2>
<?php foreach ($posts as $post): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
            <p class="card-text"><small>Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></small></p>
        </div>
    </div>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>