<?php
// Start session for authentication
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header with logo, navigation, and login form -->
    <header class="bg-primary text-white p-3">
        <div class="container">
            <div class="d-flex align-items-center">
                <!-- Logo -->
                <img src="images/logo.png" alt="Blog Logo" height="50" class="me-3">
                <!-- Navigation -->
                <nav>
                    <a href="index.php" class="text-white me-3">Home</a>
                    <a href="about.php" class="text-white me-3">About</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="users.php" class="text-white me-3">Users</a>
                        <a href="content.php" class="text-white">Content</a>
                    <?php endif; ?>
                </nav>
                <!-- Login/Logout section -->
                <div class="ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="me-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="logout.php" class="btn btn-outline-light">Logout</a>
                    <?php else: ?>
                        <form action="login.php" method="POST" class="d-inline">
                            <input type="email" name="email" placeholder="Email" required class="form-control d-inline-block w-auto me-2">
                            <input type="password" name="password" placeholder="Password" required class="form-control d-inline-block w-auto me-2">
                            <button type="submit" class="btn btn-light">Login</button>
                        </form>
                        <a href="register.php" class="btn btn-outline-light ms-2">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="container mt-4">