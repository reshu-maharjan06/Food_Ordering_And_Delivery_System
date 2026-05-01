<?php
$host = $_ENV['DB_HOST'] ?? 'localhost'; 
$dbname = $_ENV['DB_NAME'] ?? 'sauni_db'; 
$user = $_ENV['DB_USER'] ?? 'root'; 
$pass = $_ENV['DB_PASS'] ?? '';
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
} catch(PDOException $e) { 
    die("DB Connection Error: " . $e->getMessage()); 
}

require_once __DIR__ . '/security.php';
?>
