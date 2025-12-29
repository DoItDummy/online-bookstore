<?php 
require_once __DIR__ . '/../includes/config.php'; 
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
    <title>Maison's Manuscripts</title>
</head>
<body>
<header class="site-header">
<a class="brand" href="index.php">Maison's Manuscripts</a>
    <nav>
        <?php if(is_logged_in()): ?>
            <a href="order.php">Cart</a>
            <?php if(is_admin()): ?> <a href="admin.php">Admin</a> <?php endif; ?>
            <a href="?logout=1">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign up</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
<?php
    if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>