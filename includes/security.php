<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token) {
        $raw_input = file_get_contents('php://input');
        if (!empty($raw_input)) {
            $json_data = json_decode($raw_input, true);
            if (is_array($json_data)) {
                $token = $json_data['csrf_token'] ?? '';
            }
        }
    }
    if (!$token) {
        if (function_exists('getallheaders')) {
            $raw_headers = getallheaders();
            if (is_array($raw_headers)) {
                $headers = array_change_key_case($raw_headers, CASE_LOWER);
                $token = $headers['x-csrf-token'] ?? '';
            }
        }
        if (!$token) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_CSRF_token'] ?? '';
        }
    }
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        header('HTTP/1.1 403 Forbidden');
        if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            echo json_encode(['error' => 'CSRF token validation failed.']);
        } else {
            die('Access Denied: CSRF validation failed.');
        }
        exit;
    }
    return true;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', false);
}
