<?php
session_start();
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

define('RESTAURANT_LAT', 27.71534);
define('RESTAURANT_LNG', 85.31440);
define('RESTAURANT_NAME', 'Sauni Kitchen, Thamel');
$action = $_GET['action'] ?? '';
$uid    = $_SESSION['user_id'] ?? 0;
$role   = $_SESSION['role'] ?? '';

function handle_menu_upload($files, $existing = '') {
    if (empty($files['image_file']['name'])) return $existing;
    $target_dir = __DIR__ . '/assets/img/menu/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $ext = pathinfo($files['image_file']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('dish_') . '.' . $ext;
    $target_file = $target_dir . $filename;
    if (move_uploaded_file($files['image_file']['tmp_name'], $target_file)) {
        return 'assets/img/menu/' . $filename;
    }
    return $existing;
}

function attach_items($pdo, &$orders) {
    foreach ($orders as &$o) {
        $stmt = $pdo->prepare("
            SELECT m.id, m.name, m.price, oi.quantity as qty, m.image_url, ct.name as category 
            FROM order_item oi 
            JOIN menu_item m ON oi.menu_item_id = m.id 
            JOIN category c ON m.category_id = c.id
            JOIN cuisine_type ct ON c.cuisine_type_id = ct.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$o['id']]);
        $o['items_json'] = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

if ($action === 'menu_categories') {
    echo json_encode(array_column($pdo->query("SELECT DISTINCT c.name as category FROM category c JOIN menu_item m ON m.category_id=c.id WHERE c.name NOT LIKE '%Combo%' AND c.name NOT LIKE '%Discount%' ORDER BY c.name")->fetchAll(PDO::FETCH_ASSOC),'category')); exit;
}


if ($action === 'admin_orders_kanban') {
    $stmt=$pdo->query("SELECT o.*, o.placed_at as created_at, cu.username as customer_name, dr.username as driver_name FROM `order_` o JOIN user cu ON o.customer_id=cu.id LEFT JOIN user dr ON o.delivery_person_id=dr.id ORDER BY o.id DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    attach_items($pdo, $orders);
    echo json_encode($orders); exit;
}

if ($action === 'active_orders_board') {
    $stmt=$pdo->prepare("SELECT o.id, o.placed_at as created_at, cu.username as customer_name, dr.username as driver_name, o.total_amount, o.status FROM `order_` o JOIN user cu ON o.customer_id=cu.id LEFT JOIN user dr ON o.delivery_person_id=dr.id WHERE o.status IN ('pending', 'prepared') ORDER BY o.placed_at ASC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    attach_items($pdo, $orders);
    echo json_encode($orders); exit;
}

if ($action === 'mark_prepared') {
    $data=json_decode(file_get_contents('php://input'),true);
    $stmt=$pdo->prepare("UPDATE `order_` SET status='prepared' WHERE id=? AND status IN ('pending', 'confirmed')");
    $stmt->execute([$data['order_id']]);
    echo json_encode(['success'=>$stmt->rowCount()>0,'error'=>$stmt->rowCount()?null:'Not in pending/confirmed state']); exit;
}

if ($action === 'assign_driver') {
    $data=json_decode(file_get_contents('php://input'),true);
    if (!($data['order_id']??0)||!($data['driver_id']??0)) { echo json_encode(['error'=>'Missing data']); exit; }
    $stmt=$pdo->prepare("UPDATE `order_` SET delivery_person_id=?,status='assigned' WHERE id=? AND status='prepared'");
    $stmt->execute([$data['driver_id'],$data['order_id']]);
    echo json_encode(['success'=>$stmt->rowCount()>0,'error'=>$stmt->rowCount()?null:'Order must be in prepared state']); exit;
}

if ($action === 'admin_menu') { 
    echo json_encode($pdo->query("SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id ORDER BY category, m.name")->fetchAll(PDO::FETCH_ASSOC)); exit; 
}

if ($action === 'add_menu') {
    $data = $_POST;
    if (!($data['name']??'')||!($data['price']??0)) { echo json_encode(['error'=>'Fill required fields']); exit; }
    $cat = $data['category'] ?? 'Other';
    $cstmt = $pdo->prepare("SELECT id FROM category WHERE name=?"); $cstmt->execute([$cat]);
    $cid = $cstmt->fetchColumn();
    if (!$cid) {
        $pdo->prepare("INSERT INTO category (name) VALUES (?)")->execute([$cat]);
        $cid = $pdo->lastInsertId();
        $cst = $pdo->prepare("SELECT id FROM cuisine_type WHERE name=?"); $cst->execute([$cat]);
        $ctid = $cst->fetchColumn();
        if ($ctid) $pdo->prepare("UPDATE category SET cuisine_type_id=? WHERE id=?")->execute([$ctid, $cid]);
    }
    $img_url = handle_menu_upload($_FILES);
    $stmt=$pdo->prepare("INSERT INTO menu_item (name,category_id,price,description,image_url,is_popular) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$data['name'],$cid,$data['price'],$data['description']??'',$img_url,$data['is_popular']?1:0]);
    echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]); exit;
}

if ($action === 'edit_menu') {
    $data = $_POST;
    if (!($data['id']??0)) { echo json_encode(['error'=>'Missing ID']); exit; }
    $cat = $data['category'] ?? 'Other';
    $cstmt = $pdo->prepare("SELECT id FROM category WHERE name=?"); $cstmt->execute([$cat]);
    $cid = $cstmt->fetchColumn();
    if (!$cid) {
        $pdo->prepare("INSERT INTO category (name) VALUES (?)")->execute([$cat]);
        $cid = $pdo->lastInsertId();
        $cst = $pdo->prepare("SELECT id FROM cuisine_type WHERE name=?"); $cst->execute([$cat]);
        $ctid = $cst->fetchColumn();
        if ($ctid) $pdo->prepare("UPDATE category SET cuisine_type_id=? WHERE id=?")->execute([$ctid, $cid]);
    }
    $img_url = handle_menu_upload($_FILES, $data['existing_image'] ?? '');
    $stmt=$pdo->prepare("UPDATE menu_item SET name=?,category_id=?,price=?,description=?,image_url=?,is_popular=? WHERE id=?");
    $stmt->execute([$data['name'],$cid,$data['price'],$data['description'],$img_url,$data['is_popular']?1:0,$data['id']]);
    echo json_encode(['success'=>true]); exit;
}

if ($action === 'delete_menu') {
    $data=json_decode(file_get_contents('php://input'),true);
    $pdo->prepare("DELETE FROM menu_item WHERE id=?")->execute([$data['id']]);
    echo json_encode(['success'=>true]); exit;
}

if ($action === 'dishes') {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $search = $_GET['search'] ?? '';
        $cat = $_GET['cat'] ?? 'all';
        $offset = ($page - 1) * $limit;
        
        $where = ["1=1"];
        $params = [];
        if ($search) {
            $where[] = "(m.name LIKE ? OR m.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($cat !== 'all') {
            $where[] = "COALESCE(ct.name, c.name) = ?";
            $params[] = $cat;
        }
        $whereSql = implode(" AND ", $where);
        
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id WHERE $whereSql");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        
        $sql = "SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category 
                FROM menu_item m 
                JOIN category c ON m.category_id=c.id 
                LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id 
                WHERE $whereSql 
                ORDER BY m.id DESC 
                LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]);
        exit;
    } elseif ($method === 'POST') {
        $_GET['action'] = 'add_menu';
    } elseif ($method === 'PUT') {
        $_GET['action'] = 'edit_menu';
    } elseif ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $pdo->prepare("DELETE FROM menu_item WHERE id=?")->execute([$data['id']]);
        echo json_encode(['success' => true]); exit;
    }
}

if ($action === 'admin_drivers') {
    echo json_encode($pdo->query("SELECT id,username,lat,lng FROM user WHERE role='delivery'")->fetchAll(PDO::FETCH_ASSOC)); exit;
}

if ($action === 'admin_ratings') {
    echo json_encode($pdo->query("SELECT r.*, r.stars as rating, u.username, o.total_amount FROM review r JOIN user u ON r.customer_id=u.id JOIN `order_` o ON r.order_id=o.id ORDER BY r.id DESC")->fetchAll(PDO::FETCH_ASSOC)); exit;
}

if ($action === 'transition_order_status') {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = (int)($data['order_id'] ?? 0);
    $new_status = $data['status'] ?? '';
    
    $stmt = $pdo->prepare("SELECT status FROM `order_` WHERE id=?");
    $stmt->execute([$order_id]);
    $current_status = $stmt->fetchColumn();
    
    if (!$current_status) { echo json_encode(['error' => 'Order not found']); exit; }
    
    $allowed = [
        'pending' => ['prepared', 'cancelled'],
        'prepared' => ['assigned', 'cancelled'],
        'assigned' => ['picked_up'],
        'picked_up' => ['delivered'],
        'delivered' => [],
        'cancelled' => []
    ];
    
    if (!in_array($new_status, $allowed[$current_status] ?? [])) {
        echo json_encode(['error' => "Invalid transition from $current_status to $new_status"]); exit;
    }
    
    $stmt = $pdo->prepare("UPDATE `order_` SET status=?, status_updated_at=NOW() WHERE id=?");
    $stmt->execute([$new_status, $order_id]);
    
    echo json_encode(['success' => true, 'new_status' => $new_status]); exit;
}

echo json_encode(['error'=>'Invalid action: '.$action]);
?>
