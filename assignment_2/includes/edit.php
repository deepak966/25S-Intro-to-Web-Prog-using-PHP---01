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

$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo "<div class='alert alert-danger'>Item not found.</div>";
    require_once 'includes/footer.php';
    exit;
}

$title = $item['title'];
$description = $item['description'];
$currentImage = $item['image'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading image.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newImageName = uniqid() . '.' . $ext;
            $destination = __DIR__ . '/uploads/' . $newImageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $errors[] = "Failed to move uploaded image.";
            }
        }
    } else {
        $newImageName = $currentImage;
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE items SET title = ?, description = ?, image = ? WHERE id = ?");
        if ($stmt->execute([$title, $description, $newImageName, $id])) {
            if (isset($newImageName) && $newImageName !== $currentImage && $currentImage && file_exists(__DIR__ . '/uploads/' . $currentImage)) {
                unlink(__DIR__ . '/uploads/' . $currentImage);
            }
            echo "<div class='alert alert-success'>Item updated successfully. <a href='index.php'>Back to list</a></div>";
            $currentImage = $newImageName; 
        } else {
            $errors[] = "Database error: could not update item.";
        }
    }
}
?>

<h2>Edit Item</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" action="edit.php?id=<?= $id ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Title:</label>
        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($title) ?>">
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description:</label>
        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($description) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Current Image:</label><br>
        <?php if ($currentImage && file_exists(__DIR__ . '/uploads/' . $currentImage)): ?>
            <img src="uploads/<?= htmlspecialchars($currentImage) ?>" alt="Current Image" style="max-width: 200px;">
        <?php else: ?>
            <p>No image uploaded.</p>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Replace Image (optional):</label>
        <input type="file" name="image" accept="image/*" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Update Item</button>
</form>

<?php require_once 'includes/footer.php'; ?>
