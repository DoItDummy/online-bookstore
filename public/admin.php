<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: login.php'); exit;
}
// handle add book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $desc = $_POST['description'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image = $_POST['image'];
    $stmt = $pdo->prepare('INSERT INTO books (title, author, description, price, image, stock)
    VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$title, $author, $desc, $price, $image, $stock]);
}

// update book (title, author, description, price, stock, image)
if (!empty($_POST['update_book'])) {

    $id = (int)$_POST['id'];

    // Load existing row
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {

        // Update changed fields only
        $title       = $_POST['title']       !== '' ? $_POST['title']       : $book['title'];
        $author      = $_POST['author']      !== '' ? $_POST['author']      : $book['author'];
        $description = $_POST['description'] !== '' ? $_POST['description'] : $book['description'];
        $price       = $_POST['price']       !== '' ? (float)$_POST['price'] : $book['price'];
        $stock       = $_POST['stock']       !== '' ? (int)$_POST['stock']   : $book['stock'];
        $image       = $_POST['image']       !== '' ? $_POST['image']       : $book['image'];

        $stmt = $pdo->prepare("
            UPDATE books SET
                title = ?, 
                author = ?, 
                description = ?, 
                price = ?, 
                stock = ?, 
                image = ?
            WHERE id = ?
        ");

        $stmt->execute([$title, $author, $description, $price, $stock, $image, $id]);
    }
}


// change price
if (isset($_POST['change_price'])) {
    $book_id = (int)$_POST['book_id'];
    $new_price = (float)$_POST['new_price'];
    $pdo->prepare('UPDATE books SET price = ? WHERE id = ?')->execute([$new_price, $book_id]);
}

// remove book
if (isset($_POST['remove_book'])) {
    $book_id = (int)$_POST['book_id'];
    $pdo->prepare('DELETE FROM books WHERE id = ?')->execute([$book_id]);
}

// cancel order
if (isset($_POST['cancel_order'])) {
    $order_id = (int)$_POST['order_id'];
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);
}

$books = get_books($pdo);
$orders = $pdo->query('SELECT o.*, u.name as user_name FROM orders o JOIN users
u ON u.id = o.user_id ORDER BY o.created_at DESC')->fetchAll();

include 'header.php';
?>
<h2>Admin Panel</h2>
<section>
    <h3>Add Book</h3>
    <form method="post">
        <label>Title<input name="title" required></label>
        <label>Author<input name="author"></label>
        <label>Description<textarea name="description"></textarea></label>
        <label>Price<input name="price" type="number" step="0.01" required></label>
        <label>Stock<input name="stock" type="number" required></label>
        <button name="add_book" type="submit">Add</button>
    </form>
    </section>
    <section>
    <h3>Edit / Update Books</h3>
    <?php foreach($books as $b): ?>
        <div class="admin-book">
            
            <h4><?= htmlspecialchars($b['title']) ?></h4>

            <!-- UPDATE BOOK FORM -->
            <form method="post">
                <input type="hidden" name="update_book" value="1">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                <label>Title:
                    <input name="title" value="<?= htmlspecialchars($b['title']) ?>">
                </label>
                <label>Author:
                    <input name="author" value="<?= htmlspecialchars($b['author']) ?>">
                </label>
                <label>Description:
                    <textarea name="description"><?= htmlspecialchars($b['description']) ?></textarea>
                </label>
                <label>Price:
                    <input name="price" type="number" step="0.01" value="<?= $b['price'] ?>">
                </label>
                <label>Stock:
                    <input name="stock" type="number" value="<?= $b['stock'] ?>">
                </label>
                <button type="submit">Save Changes</button>
            </form>
            <!-- REMOVE BOOK -->
            <form method="post" style="display:inline-block;margin-top:10px;">
                <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                <button name="remove_book" onclick="return confirm('Remove this book?')">
                    Remove
                </button>
            </form>

        </div>
    <?php endforeach; ?>
</section>

<section>
    <h3>Orders</h3>
    <?php foreach($orders as $o): ?>
    <div class="order">
        <p>Order #<?= $o['id'] ?> by <?= htmlspecialchars($o['user_name']) ?> - 
        <?= htmlspecialchars($o['status']) ?> - $<?= $o['total'] ?></p>
        <form method="post">
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <button name="cancel_order">Cancel order</button>
        </form>
    </div>
    <?php endforeach; ?>
</section>
<?php include 'footer.php'; ?>