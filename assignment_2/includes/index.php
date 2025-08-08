<?php
require_once 'includes/db.php';
require_once 'includes/header.php';


$stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
$items = $stmt->fetchAll();
?>

<h2>All Items</h2>

<?php if (count($items) === 0): ?>
    <p>No items found. <a href="add.php">Add a new item</a>.</p>
<?php else: ?>
    <div class="row">
        <?php foreach ($items as $item): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($item['image']) && file_exists('uploads/' . $item['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" alt="No Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                    </div>
                    <div class="card-footer text-muted">
                        Created on <?= date('F j, Y, g:i a', strtotime($item['created_at'])) ?>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <div class="mt-2">
                                <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
