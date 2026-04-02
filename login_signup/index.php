<?php
try {
    require_once __DIR__ . '/includes/db.php';

    if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . getRoleRedirect($_SESSION['role']));
        exit;
    }

    $error = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  (isset($_POST['ajax']) && $_POST['ajax'] === 'true');
        
        if (empty($username) || empty($password)) {
            $msg = 'Username and password are required.';
            $isAjax ? sendResponse(false, $msg) : ($error = $msg);
        } else {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM user WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                $redirect = getRoleRedirect($user['role']);
                $isAjax ? sendResponse(true, 'Login successful', ['redirect' => $redirect]) : (header("Location: $redirect") && exit);
            } else {
                $msg = 'Invalid username or password!';
                $isAjax ? sendResponse(false, $msg) : ($error = $msg);
            }
        }
    }
} catch (Throwable $e) {
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
              (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
              (isset($_POST['ajax']) && $_POST['ajax'] === 'true');
              
    $errorMsg = "Fatal Error: " . $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine();
    
    if ($isAjax) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'message' => $errorMsg]);
        exit;
    }
    $error = $errorMsg;
}

require_once __DIR__ . '/views/index.view.php';
