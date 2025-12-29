<?php

$DB_HOST = '127.0.0.1';
$DB_NAME = 'BookStore';
$DB_USER = 'root';
$DB_PASS = ''; // set your local MySQL password

$DB_DSN = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // For development show the error; for production log it and show friendly message.
    die('DB Connection failed: ' . $e->getMessage());
}

// Ensure session is started exactly once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}