<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in()) {
header('Location: login.php'); exit;
}

// remove from cart
if (isset($_POST['remove'])) {
    remove_from_cart((int)$_POST['book_id']);
}

//place order
if (isset($_POST['place_order'])) {
    $items = cart_items($pdo);
    if (empty($items)) 
        { $error = 'Cart is empty.'; }
    if (empty($error)) {
        $shipping = 5.00; // flat
        $tax_rate = 0.07; // 7%
        $subtotal = array_sum(array_column($items, 'subtotal'));
        $tax = $subtotal * $tax_rate;
        $total = $subtotal + $tax + $shipping;
        
        // create order
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, total,
        shipping_address, payment_method) VALUES (?, ?, ?, ?)');
        $stmt->execute([current_user_id(), $total, $_POST['shipping_address'],
        $_POST['payment_method']]);
        $order_id = $pdo->lastInsertId();
        
        // insert items and reduce stock
        foreach ($items as $it) {
        $pdo->prepare('INSERT INTO order_items (order_id, book_id, quantity, price) VALUES
        (?, ?, ?, ?)') ->execute([$order_id, $it['book']['id'], $it['quantity'], $it['book']['price']]);
        $pdo->prepare('UPDATE books SET stock = stock - ? WHERE id = ?')->execute([$it['quantity'], $it['book']['id']]);
        }
        
        clear_cart();
        header('Location: order_submitted.php'); exit;
    }
}

$items = cart_items($pdo);
include 'header.php';
?>

<h2>Your Cart</h2>
<?php if(!empty($error)) echo '<p class="error">'.htmlspecialchars($error).'</
p>'; 
?>

<?php if(empty($items)): ?>
    <p>Your cart is empty.</p>

    <?php else: ?>
    <table class="cart">
    <thead><tr><th>Book</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
            <?php foreach($items as $it): ?>
            <tr>
            <td><?= htmlspecialchars($it['book']['title']) ?></td>
            <td><?= $it['quantity'] ?></td>
            <td>$<?= number_format($it['book']['price'],2) ?></td>
            <td>$<?= number_format($it['subtotal'],2) ?></td>
            <td>
            <form method="post"><input type="hidden" name="book_id" value="<?=
            $it['book']['id'] ?>"><button name="remove">Remove</button></form>
            </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    
    <?php
    $subtotal = array_sum(array_column($items, 'subtotal'));
    $shipping = 5.00;
    $tax_rate = 0.07;
    $tax = $subtotal * $tax_rate;
    $total = $subtotal + $tax + $shipping;
    ?>
    
    <p>Subtotal: $<?= number_format($subtotal,2) ?></p>
    <p>Shipping: $<?= number_format($shipping,2) ?></p>
    <p>Tax (7%): $<?= number_format($tax,2) ?></p>
    <h3>Total: $<?= number_format($total,2) ?></h3>
    
    <h3>Checkout</h3>
    <form method="post">
        <label>Shipping address<textarea name="shipping_address" required></textarea></label>
        <label>Payment method
            <select name="payment_method">
                <option>Pay on delivery</option>
                <option>Credit Card</option>
                <option>Debit Card</option>
            </select>
        </label>
        <button name="place_order" type="submit">Place order</button>
    </form>
<?php endif; ?>
<?php include 'footer.php'; ?>