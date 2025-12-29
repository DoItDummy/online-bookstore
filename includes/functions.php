<?php
require_once __DIR__ . '/../includes/config.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function is_admin() {
    return !empty($_SESSION['is_admin']);
}

// Get all books
function get_books($pdo) {
    $stmt = $pdo->query('SELECT * FROM books ORDER BY id ASC');
    return $stmt->fetchAll();
}

// Get single book
function get_book($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

// Cart helpers stored in session
function add_to_cart($book_id, $quantity = 1) {
    $book_id = (int)$book_id;
    $quantity = max(1, (int)$quantity);
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (!isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id] = 0;
    }
    $_SESSION['cart'][$book_id] += $quantity;
}

function set_cart_item($book_id, $quantity) {
    $book_id = (int)$book_id;
    $quantity = max(0, (int)$quantity);
    if ($quantity === 0) {
        remove_from_cart($book_id);
        return;
    }
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][$book_id] = $quantity;
}

function remove_from_cart($book_id) {
    $book_id = (int)$book_id;
    if (isset($_SESSION['cart'][$book_id])) {
        unset($_SESSION['cart'][$book_id]);
    }
}

function clear_cart() {
    unset($_SESSION['cart']);
}

// Return array of cart items with book details, quantity, subtotal
function cart_items($pdo) {
    $items = [];
    if (empty($_SESSION['cart'])) return $items;
    $ids = array_keys($_SESSION['cart']);
    // build placeholders
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $books = $stmt->fetchAll();
    // map books by id for easy lookup
    $map = [];
    foreach ($books as $b) $map[$b['id']] = $b;
    foreach ($_SESSION['cart'] as $id => $qty) {
        if (!isset($map[$id])) continue; // book removed from DB
        $book = $map[$id];
        $subtotal = $qty * $book['price'];
        $items[] = [
            'book' => $book,
            'quantity' => $qty,
            'subtotal' => $subtotal
        ];
    }
    return $items;
}