<?php
// security.php - Core Security Helper 

// Ensure session is started for CSRF tokens
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generates and returns a CSRF token.
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Returns the hidden HTML input field containing the CSRF token.
 */
function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verifies a given CSRF token. Matches against POST or Header data.
 * Will terminate execution (or return false) if invalid.
 */
function verify_csrf() {
    // Determine the CSRF token from POST payload or Headers (for APIs)
    $token = $_POST['csrf_token'] ?? '';
    if (!$token) {
        $headers = getallheaders();
        // Check standard headers
        $token = $headers['X-CSRF-Token'] ?? $headers['x-csrf-token'] ?? '';
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

/**
 * XSS Helper to escape strings printed to HTML.
 */
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', false);
}
?>
