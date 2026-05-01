<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "deliver_db";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die(json_encode(["success" => false, "message" => "Error creating database: " . $conn->error]));
}

$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role ENUM('customer', 'delivery') NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    delivery_rider_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    delivery_address TEXT NOT NULL,
    dest_lat DECIMAL(10,8) NULL,
    dest_lng DECIMAL(11,8) NULL,
    note TEXT NULL,
    delivery_status ENUM('assigned', 'picked_up', 'delivered') NOT NULL DEFAULT 'assigned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (delivery_rider_id) REFERENCES users(id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";
$conn->query($sql);

$result = $conn->query("SELECT COUNT(*) AS count FROM users");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $conn->query("INSERT INTO users (id, name, role, email, password) VALUES 
        (1, 'Rider One', 'delivery', 'rider1@test.com', 'password123'),
        (2, 'John Customer', 'customer', 'customer@test.com', 'password123')");
    
    $conn->query("INSERT INTO orders (id, customer_id, delivery_rider_id, total_price, delivery_address, dest_lat, dest_lng, note, delivery_status) VALUES 
        (1, 2, 1, 35.50, 'Thamel, Kathmandu', 27.71500, 85.31200, 'Call me when you arrive.', 'assigned')");
    
    $conn->query("INSERT INTO order_items (order_id, food_name, quantity, price) VALUES 
        (1, 'Margherita Pizza', 1, 15.00),
        (1, 'Garlic Bread', 2, 5.00),
        (1, 'Coke', 2, 5.25)");
}

return $conn;
