<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$books = get_books($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
    if (is_logged_in()) {
        add_to_cart($book_id, 1);
        header('Location: order.php');
        exit;
    } else {
        header('Location: login.php?book_id=' . $book_id);
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bookstore - Home</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="index.php">Maison's Manuscripts</a>
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

    <h1>Our Collection</h1>
    <div class="books-grid">
      <?php foreach ($books as $b): ?>
        <div class="book-card">
          
          <h3><?php echo htmlspecialchars($b['title']); ?></h3>
          <p class="author"><?php echo htmlspecialchars($b['author']); ?></p>
          <p class="desc"><?php echo nl2br(htmlspecialchars($b['description'])); ?></p>
          <p class="stock">In stock: <?php echo (int)$b['stock']; ?></p>
          <p class="price">$<?php echo number_format($b['price'], 2); ?></p>
          
          <?php if ((int)$b['stock'] > 0): ?>
            <form method="post" style="margin-top:8px;">
              <input type="hidden" name="book_id" value="<?php echo (int)$b['id']; ?>">
              <button type="submit">Buy</button>
            </form>
          <?php else: ?>
            <p style="color: gray; font-weight: bold; margin-top:8px;">Out of Stock</p>
          <?php endif; ?>
        
        </div>
      <?php endforeach; ?>
    </div>
  </main>

</body>
</html>