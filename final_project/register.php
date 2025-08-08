<?php
require 'includes/header.php';
require 'includes/db_connect.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$email) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check email uniqueness
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Email already registered.";
    }

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
            $errors[] = "Only JPEG and PNG images are allowed.";
        } elseif ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image size must be less than 2MB.";
        } else {
            $image_path = 'images/' . uniqid() . '_' . basename($_FILES['profile_image']['name']);
            if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Insert user if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Use bcrypt
        $stmt = $pdo->prepare("INSERT INTO users (email, username, password, profile_image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$email, $username, $hashed_password, $image_path]);
        header("Location: login.php");
        exit;
    }
}
?>
<!-- Register page content -->
<h1>Register</h1>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="profile_image" class="form-label">Profile Image (Optional)</label>
        <input type="file" name="profile_image" id="profile_image" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>
<?php require 'includes/footer.php'; ?>