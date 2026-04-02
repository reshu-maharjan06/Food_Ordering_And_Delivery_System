<?php
$host    = 'localhost';
$dbname  = 'sauniauth_db';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
} catch (PDOException $e) {
    http_response_code(500);
    die('DB connection failed: ' . $e->getMessage());
}
