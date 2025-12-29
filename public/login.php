
<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$ADMIN_EMAIL = "admin@admin.com";
$ADMIN_PASSWORD = "admin123";

$selected_book = isset($_GET['book_id']) ? (int)$_GET['book_id'] : (isset($_POST['book_id']) ? (int)$_POST['book_id'] : null);

if (is_logged_in() && $selected_book) {
    add_to_cart($selected_book, 1);
    header('Location: order.php');
    exit;
}

$error = '';

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === $ADMIN_EMAIL && $password === $ADMIN_PASSWORD) {
        $_SESSION['user_id'] = -1; // artificial id for admin when not in DB
        $_SESSION['is_admin'] = 1;
        // If a book was posted along, add it to cart (admin probably won't order but ok)
        if (!empty($_POST['book_id']) && is_numeric($_POST['book_id'])) {
            add_to_cart((int)$_POST['book_id'], 1);
            header('Location: admin.php');
            exit;
        }
        header('Location: admin.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            // success
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['is_admin'] = !empty($user['is_admin']) ? 1 : 0;

            // If book was selected earlier, add to cart
            if (!empty($_POST['book_id']) && is_numeric($_POST['book_id'])) {
                add_to_cart((int)$_POST['book_id'], 1);
                header('Location: order.php');
                exit;
            }
            header('Location: index.php');
            exit;
        } else {
            $error = 'Login failed: incorrect email or password.';
        }
    } else {
        $error = 'Login failed: incorrect email or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - Bookstore</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="index.php">Bookstore</a>
    <nav>
      <?php if (is_logged_in()): ?>
        <a href="order.php">Cart</a>
        <?php if (is_admin()): ?><a href="admin.php">Admin</a><?php endif; ?>
        <a href="?logout=1">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign up</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container">
    <?php
    if (!empty($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit;
    }
    ?>

    <h2>Login</h2>

    <?php if (!empty($error)): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="login.php">
      <label>
        Email
        <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </label>
      <label>
        Password
        <input type="password" name="password" required>
      </label>

      <?php if ($selected_book): ?>
        <input type="hidden" name="book_id" value="<?php echo (int)$selected_book; ?>">
      <?php endif; ?>

      <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
  </main>

  <footer class="site-footer">&copy; Bookstore</footer>
</body>
</html>