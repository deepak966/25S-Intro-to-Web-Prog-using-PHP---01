<?php
require_once 'includes/db.php';
require_once 'includes/header.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid item ID.</div>";
    require_once 'includes/footer.php';
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT image FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo "<div class='alert alert-danger'>Item not found.</div>";
    require_once 'includes/footer.php';
    exit;
}

$image = $item['image'];
$stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
if ($stmt->execute([$id])) {
    if ($image && file_exists(__DIR__ . '/uploads/' . $image)) {
        unlink(__DIR__ . '/uploads/' . $image);
    }
    echo "<div class='alert alert-success'>Item deleted successfully. <a href='index.php'>Back to list</a></div>";
} else {
    echo "<div class='alert alert-danger'>Failed to delete item.</div>";
}

require_once 'includes/footer.php';
