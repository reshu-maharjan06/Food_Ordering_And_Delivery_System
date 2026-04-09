<?php
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'menu') {
    try {
        $stmt = $pdo->prepare("
            SELECT m.*, c.name as category_name 
            FROM menu_items m 
            JOIN categories c ON m.category_id = c.id
            ORDER BY c.name, m.name
        ");
        $stmt->execute();
        $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($menu_items)) {
            echo json_encode([]);
            exit;
        }

        echo json_encode($menu_items);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
