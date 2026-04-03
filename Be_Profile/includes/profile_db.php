<?php
$host = 'localhost'; 
$dbname = 'profile_db'; 
$user = 'root'; 
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    $pdo->exec("CREATE TABLE IF NOT EXISTS user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NULL,
        email VARCHAR(100) NULL UNIQUE,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('customer','admin','delivery') NOT NULL DEFAULT 'customer',
        phone VARCHAR(20) NULL,
        address VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch(PDOException $e) { 
    die("DB Connection Error: " . $e->getMessage()); 
}
?>
