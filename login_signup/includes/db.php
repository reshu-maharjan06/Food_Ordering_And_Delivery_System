<?php
ob_start();
session_start();
require_once __DIR__ . '/../config/database.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    if ($e->getCode() == 1049) {
        try {
            $dsn_no_db = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn_no_db, DB_USER, DB_PASS, $options);
        } catch (PDOException $inner_e) {
            $msg = "Connection Error: " . $inner_e->getMessage();
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) sendResponse(false, $msg);
            die($msg);
        }
    } else {
        $msg = "Database Connection Error: " . $e->getMessage();
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) sendResponse(false, $msg);
        die($msg);
    }
}

function sendResponse(bool $ok, string $message, array $extra = []) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $response = array_merge(['ok' => $ok, 'message' => $message], $extra);
    echo json_encode($response);
    exit;
}

function getRoleRedirect(string $role) {
    return match ($role) {
        'admin'    => 'admin/dashboard.php',
        'delivery' => 'delivery/dashboard.php',
        default    => 'landing.php',
    };
}
