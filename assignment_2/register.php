<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$username = $password = $confirm = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    // Validate input
    if (empty($username) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username already taken.";
        } else {
            // Hash the password and insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed])) {
                echo "<div class='alert alert-success'>Registration successful. <a href='login.php'>Login here</a>.</div>";
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<h2>Register</h2>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
<?php endif; ?>

<form method="POST" class="mb-4" action="register.php">
    <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($username) ?>">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="confirm" class="form-label">Confirm Password:</label>
        <input type="password" name="confirm" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php require_once 'includes/footer.php'; ?>
