-- Run this in phpMyAdmin or via mysql CLI
CREATE DATABASE IF NOT EXISTS bookstore CHARACTER SET utf8mb4 COLLATE
utf8mb4_unicode_ci;
USE bookstore;

-- Users table
CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
email VARCHAR(255) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
is_admin TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books
CREATE TABLE books (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
author VARCHAR(255) DEFAULT '',
description TEXT,
price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
image VARCHAR(255) DEFAULT '',
stock INT NOT NULL DEFAULT 0
);

-- Orders
CREATE TABLE orders (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
total DECIMAL(10,2) NOT NULL,
shipping_address TEXT,
payment_method VARCHAR(50),
status VARCHAR(50) DEFAULT 'pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items
CREATE TABLE order_items (
id INT AUTO_INCREMENT PRIMARY KEY,
order_id INT NOT NULL,
book_id INT NOT NULL,
quantity INT NOT NULL DEFAULT 1,
price DECIMAL(10,2) NOT NULL,
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password_hash, is_admin)
VALUES ('Admin', 'admin@example.com', '$2y$10$xS/bCo9ECqEaJ2wXj9ASFeAWzZgMqXVzsBh2uu/z3UpOac30FTIgC', 1);

INSERT INTO books (title, author, description, price, image, stock) VALUES
('Frankenstein', 'Mary Shelley', 'First Edition of classic gothic Novel, Hardcover', 1256.80, 'frank.jpg', 1),
('Dracula', 'Bram Stoker', 'Collector edition replica, Hardcover', 150.50, 'drac.jpg', 8),
('Bible', 'Various Authors', '18th Century King James Version', 200.00, 'bible.jpg', 5);
('The Sun Also Rises', 'Earnest Hemingway', 'Modern Version of a Classic Book', 25.00, 'SAR.jpg', 100);
('East of Eden', 'John Steinbeck', 'Origional Manuscript with Author Notes', 10000.99, 'EoE.jpg', 1);
('A Portrait of Dorian Gray', 'Oscar Wilde', 'Early Mass Paperback Edition', 60.20, 'aPoDG.jpg', 50);
