<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$username = $password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // it will Validate inputs
    if (empty($username) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        // Get the  user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login is successfull
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: index.php");      // Redirect after login
            exit;
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}
?>

<h2>Login</h2>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
<?php endif; ?>

<form method="POST" class="mb-4" action="login.php">
    <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($username) ?>">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<?php require_once 'includes/footer.php'; ?>
