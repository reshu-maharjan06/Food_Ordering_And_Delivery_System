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
if ($action === 'restaurant_info') {
    echo json_encode(['lat'=>RESTAURANT_LAT,'lng'=>RESTAURANT_LNG,'name'=>RESTAURANT_NAME]); exit;
}
if ($action === 'menu') {
    $cat = $_GET['cat'] ?? 'all';
    if ($cat !== 'all') { 
        $s=$pdo->prepare("SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id WHERE (ct.name=? OR c.name=?) AND (c.name NOT LIKE '%Combo%' AND c.name NOT LIKE '%Discount%') ORDER BY m.is_popular DESC"); 
        $s->execute([$cat, $cat]); 
    } else { 
        $s=$pdo->query("SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id WHERE c.name NOT LIKE '%Combo%' AND c.name NOT LIKE '%Discount%' ORDER BY m.is_popular DESC"); 
    }
    echo json_encode($s->fetchAll(PDO::FETCH_ASSOC)); exit;
}
if ($action === 'menu_categories') {
    echo json_encode(array_column($pdo->query("SELECT DISTINCT c.name as category FROM category c JOIN menu_item m ON m.category_id=c.id WHERE c.name NOT LIKE '%Combo%' AND c.name NOT LIKE '%Discount%' ORDER BY c.name")->fetchAll(PDO::FETCH_ASSOC),'category')); exit;
}
if ($action === 'search_place') {
    $q = $_GET['q'] ?? ''; if (!$q) { echo json_encode([]); exit; }
    $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&q='.urlencode($q.', Kathmandu');
    $ctx = stream_context_create(['http'=>['header'=>'User-Agent: SauniApp/1.0']]);
    echo file_get_contents($url,false,$ctx) ?: json_encode([]); exit;
}
if ($action === 'place_order') {
    $data = json_decode(file_get_contents('php://input'),true);
    if (!$uid||empty($data['items'])||!isset($data['lat'])) { echo json_encode(['error'=>'Invalid data']); exit; }
    $chk=$pdo->prepare("SELECT id FROM `order_` WHERE customer_id=? AND status!='delivered' AND status!='cancelled'");
    $chk->execute([$uid]);
    if ($chk->rowCount()>0) { echo json_encode(['error'=>'You already have an active order!']); exit; }
    try {
        $pdo->beginTransaction();
        $stmt=$pdo->prepare("INSERT INTO `order_` (customer_id,total_amount,dest_lat,dest_lng,delivery_address,note) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$uid,$data['total'],$data['lat'],$data['lng'],$data['address']??'Pinned on Map',$data['note']??'']);
        $oid = $pdo->lastInsertId();
        $istmt = $pdo->prepare("INSERT INTO order_item (order_id, menu_item_id, quantity, unit_price) VALUES (?,?,?,?)");
        foreach ($data['items'] as $it) {
            $istmt->execute([$oid, $it['id'] ?? null, $it['qty'], $it['price']]);
        }
        $pdo->commit();
        echo json_encode(['success'=>true,'order_id'=>$oid]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['error'=>'DB error: ' . $e->getMessage()]);
    }
    exit;
}
if ($action === 'customer_orders') {
    $stmt=$pdo->prepare("SELECT o.*, o.placed_at as created_at, u.username as driver_name, CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END as has_reviewed FROM `order_` o LEFT JOIN user u ON o.delivery_person_id=u.id LEFT JOIN review r ON o.id=r.order_id AND o.customer_id=r.customer_id WHERE o.customer_id=? ORDER BY o.id DESC");
    $stmt->execute([$uid]); 
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    attach_items($pdo, $orders);
    echo json_encode($orders); exit;
}
if ($action === 'active_order') {
    $stmt=$pdo->prepare("SELECT o.*, u.username as driver_name, u.lat as driver_lat, u.lng as driver_lng FROM `order_` o LEFT JOIN user u ON o.delivery_person_id=u.id WHERE o.customer_id=? AND o.status!='delivered' AND o.status!='cancelled' ORDER BY o.id DESC LIMIT 1");
    $stmt->execute([$uid]); 
    $order = $stmt->fetch(PDO::FETCH_ASSOC)?:null;
    if ($order) {
        $o = [$order];
        attach_items($pdo, $o);
        $order = $o[0];
    }
    echo json_encode($order); exit;
}
if ($action === 'check_review') {
    $order_id = (int)($_GET['order_id'] ?? 0);
    if (!$uid || !$order_id) { echo json_encode(['already_reviewed'=>false]); exit; }
    $chk = $pdo->prepare("SELECT id FROM review WHERE order_id=? AND customer_id=?");
    $chk->execute([$order_id, $uid]);
    echo json_encode(['already_reviewed' => $chk->rowCount() > 0]); exit;
}
if ($action === 'rate_order') {
    $data = json_decode(file_get_contents('php://input'),true);
    if (!$uid||!($data['order_id']??0)) { echo json_encode(['error'=>'Invalid']); exit; }
    $chk=$pdo->prepare("SELECT id FROM review WHERE order_id=? AND customer_id=?");
    $chk->execute([$data['order_id'],$uid]);
    if ($chk->rowCount()>0) { echo json_encode(['error'=>'Already rated']); exit; }
    $combined = round((int)($data['rating'] ?? 5));
    $stmt=$pdo->prepare("INSERT INTO review (order_id,customer_id,stars,comment) VALUES (?,?,?,?)");
    $stmt->execute([$data['order_id'],$uid,$combined,$data['comment']??'']);
    echo json_encode(['success'=>true]); exit;
}
if ($action === 'order_items') {
    $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
    if ($order_id <= 0) {
        echo json_encode(['error' => 'Valid Order ID is required']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("
            SELECT o.id as order_id, o.status, o.placed_at as order_date, 
                   i.id as order_item_id, m.name as item_name, i.quantity, i.unit_price as price
            FROM `order_` o
            JOIN order_item i ON o.id = i.order_id
            LEFT JOIN menu_item m ON i.menu_item_id = m.id
            WHERE o.id = ?
        ");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            echo json_encode(['error' => 'No items found for this order']);
            exit;
        }

        $result = [
            'order_id' => $items[0]['order_id'],
            'status' => $items[0]['status'],
            'order_date' => $items[0]['order_date'],
            'items' => array_map(function($row) {
                return [
                    'item_name' => $row['item_name'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price']
                ];
            }, $items)
        ];
        echo json_encode($result);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'past_orders') {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $status_filter = $_GET['status'] ?? '';
    $date_filter = $_GET['date'] ?? '';

    $query = "
        SELECT o.*, o.placed_at as created_at, 
               cu.username as customer_name, dr.username as driver_name
        FROM `order_` o
        JOIN user cu ON o.customer_id = cu.id
        LEFT JOIN user dr ON o.delivery_person_id = dr.id
        WHERE o.status IN ('delivered', 'cancelled')
    ";
    $params = [];

    if (!empty($status_filter)) {
        $query .= " AND o.status = ?";
        $params[] = $status_filter;
    }
    if (!empty($date_filter)) {
        $query .= " AND DATE(o.placed_at) = ?";
        $params[] = $date_filter;
    }

    $query .= " ORDER BY o.placed_at DESC LIMIT $limit OFFSET $offset";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        attach_items($pdo, $orders);

        echo json_encode([
            'page' => $page,
            'limit' => $limit,
            'data' => $orders
        ]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Database error']);
    }
    exit;
}

echo json_encode(['error'=>'Invalid action: '.$action]);
?>
