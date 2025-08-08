<?php
require_once 'includes/db.php';
require_once 'includes/header.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$title = $description = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading image.";
        } else {
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid() . '.' . $ext;
            $destination = __DIR__ . '/uploads/' . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $errors[] = "Failed to move uploaded image.";
            }
        }
    }

   
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO items (title, description, image) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $description, $imageName])) {
            echo "<div class='alert alert-success'>Item added successfully. <a href='index.php'>View all items</a>.</div>";
            $title = $description = "";
            $imageName = null;
        } else {
            $errors[] = "Database error: Could not add item.";
        }
    }
}
?>

<h2>Add New Item</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" action="add.php">
    <div class="mb-3">
        <label for="title" class="form-label">Title:</label>
        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($title) ?>">
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description:</label>
        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($description) ?></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image (optional):</label>
        <input type="file" name="image" accept="image/*" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Add Item</button>
</form>

<?php require_once 'includes/footer.php'; ?>
