<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    // Basic checks
    if (!$name || !$email || !$password) $error = 'Please fill all fields.';
    if (empty($error)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            header('Location: login.php'); exit;
        } catch (PDOException $e) {
            $error = 'Sign up failed: ' . $e->getMessage();
        }
    }
}
include 'header.php';
?>
<h2>Sign up</h2>
<?php if(!empty($error)) echo '<p class="error">'.htmlspecialchars($error).'</
p>'; ?>
<form method="post">
    <label>Name<input name="name" required></label>
    <label>Email<input name="email" type="email" required></label>
    <label>Password<input name="password" type="password" required></label>
    <button type="submit">Create account</button>
</form>
<?php include 'footer.php'; ?>