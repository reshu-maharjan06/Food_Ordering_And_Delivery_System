<?php
session_start();
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
$action = $_GET['action'] ?? '';

if ($action === 'admin_dashboard') {
    $totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM `order_`")->fetchColumn();
    $active      = (int)$pdo->query("SELECT COUNT(*) FROM `order_` WHERE status NOT IN ('delivered','cancelled')")->fetchColumn();
    $pending     = (int)$pdo->query("SELECT COUNT(*) FROM `order_` WHERE status='pending'")->fetchColumn();
    $todayDel    = (int)$pdo->query("SELECT COUNT(*) FROM `order_` WHERE status='delivered' AND DATE(placed_at)=CURDATE()")->fetchColumn();
    $revenue     = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM `order_` WHERE status='delivered' AND DATE(placed_at)=CURDATE()")->fetchColumn();
    $drvOnline   = (int)$pdo->query("SELECT COUNT(*) FROM user WHERE role='delivery' AND lat IS NOT NULL AND lat!=0")->fetchColumn();
    
    // Revenue Progress Data
    $targetRevenue = 10000;
    $percentage = ($revenue / $targetRevenue) * 100;
    if ($percentage > 100) $percentage = 100;

    $recent   = $pdo->query("SELECT o.id, cu.username as customer_name, o.total_amount, o.status, o.placed_at as created_at FROM `order_` o JOIN user cu ON o.customer_id=cu.id ORDER BY o.id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $weekly   = $pdo->query("
        SELECT DATE(placed_at) as day, COUNT(*) as count, DAYNAME(placed_at) as day_name,
               COALESCE(SUM(CASE WHEN status='delivered' THEN total_amount ELSE 0 END),0) as revenue
        FROM `order_`
        WHERE placed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(placed_at), DAYNAME(placed_at)
        ORDER BY day ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'total_orders'     => $totalOrders,
        'active_orders'    => $active,
        'pending_count'    => $pending,
        'delivered_today'  => $todayDel,
        'revenue_today'    => round($revenue),
        'target_revenue'   => $targetRevenue,
        'target_percentage'=> round($percentage, 1),
        'drivers_online'   => $drvOnline,
        'recent_orders'    => $recent,
        'activity'         => $recent,
        'weekly_data'      => $weekly,
    ]); exit;
}

if ($action === 'admin_menu') {
    echo json_encode($pdo->query("SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id ORDER BY category, m.name")->fetchAll(PDO::FETCH_ASSOC)); exit;
}

echo json_encode(['error' => 'Invalid action: ' . $action]);
?>
